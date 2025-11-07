<?php

namespace Netauratech\CoreCms\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Tests\TestCase;

class PageControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_homepage_displays_correctly(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewIs('core-cms::front.page');
        $response->assertViewHas('isHomepage', true);
    }

    /** @test */
    public function test_homepage_displays_configured_content(): void
    {
        // Le seeder crée déjà une homepage pointant vers 'accueil'
        $homepage = Content::where('slug', 'accueil')->first();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('content', $homepage);
    }

    /** @test */
    public function test_homepage_shows_welcome_message(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Bienvenue');
    }

    /** @test */
    public function test_homepage_returns_404_if_not_configured(): void
    {
        // Supprimer l'option homepage
        Option::where('key', 'homepage')->delete();

        $response = $this->get(route('home'));

        $response->assertStatus(404);
    }

    /** @test */
    public function test_homepage_returns_404_if_content_not_found(): void
    {
        // Pointer vers un contenu inexistant
        Option::where('key', 'homepage')->update(['value' => '99999']);

        $response = $this->get(route('home'));

        $response->assertStatus(404);
    }

    /** @test */
    public function test_page_route_displays_content(): void
    {
        // Le seeder crée déjà 'mentions-legales'
        $response = $this->get('/mentions-legales');

        $response->assertOk();
        $response->assertViewIs('core-cms::front.page');
        $response->assertViewHas('isHomepage', false);
    }

    /** @test */
    public function test_page_shows_correct_content(): void
    {
        $page = Content::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
            'type' => 'page',
            'status' => 'published',
            'content' => json_encode([
                ['_name' => 'section', 'title' => 'Test Section', 'content' => 'Test content']
            ]),
        ]);

        $response = $this->get('/test-page');

        $response->assertOk();
        $response->assertSee('Test Page');
    }

    /** @test */
    public function test_unpublished_page_returns_404(): void
    {
        Content::create([
            'title' => 'Draft Page',
            'slug' => 'draft-page',
            'type' => 'page',
            'status' => 'draft',
        ]);

        $response = $this->get('/draft-page');

        $response->assertStatus(404);
    }

    /** @test */
    public function test_archived_page_returns_404(): void
    {
        Content::create([
            'title' => 'Archived Page',
            'slug' => 'archived-page',
            'type' => 'page',
            'status' => 'archived',
        ]);

        $response = $this->get('/archived-page');

        $response->assertStatus(404);
    }

    /** @test */
    public function test_non_page_type_returns_404(): void
    {
        Content::create([
            'title' => 'Template',
            'slug' => 'test-template',
            'type' => 'template',
            'status' => 'published',
        ]);

        $response = $this->get('/test-template');

        $response->assertStatus(404);
    }

    /** @test */
    public function test_nonexistent_page_returns_404(): void
    {
        $response = $this->get('/nonexistent-page');

        $response->assertStatus(404);
    }

    /** @test */
    public function test_page_with_special_characters_in_slug(): void
    {
        Content::create([
            'title' => 'Special Page',
            'slug' => 'page-with-special-chars',
            'type' => 'page',
            'status' => 'published',
        ]);

        $response = $this->get('/page-with-special-chars');

        $response->assertOk();
    }

    /** @test */
    public function test_page_slug_is_case_sensitive(): void
    {
        Content::create([
            'title' => 'Case Test',
            'slug' => 'test-page',
            'type' => 'page',
            'status' => 'published',
        ]);

        // Laravel routes sont case-sensitive par défaut
        $response = $this->get('/test-page');
        $response->assertOk();

        $response = $this->get('/Test-Page');
        $response->assertStatus(404);
    }

    /** @test */
    public function test_homepage_has_metas_data(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertViewHas('metas');
    }

    /** @test */
    public function test_page_has_metas_data(): void
    {
        $response = $this->get('/mentions-legales');

        $response->assertOk();
        $response->assertViewHas('metas');
    }

    /** @test */
    public function test_page_content_is_passed_to_view(): void
    {
        $page = Content::create([
            'title' => 'Content Test',
            'slug' => 'content-test',
            'type' => 'page',
            'status' => 'published',
            'content' => json_encode([
                ['_name' => 'section', 'title' => 'Section Title']
            ]),
        ]);

        $response = $this->get('/content-test');

        $response->assertOk();
        $response->assertViewHas('content');

        $viewContent = $response->viewData('content');
        $this->assertEquals('Content Test', $viewContent->title);
    }

    /** @test */
    public function test_multiple_pages_can_exist(): void
    {
        Content::create([
            'title' => 'Page 1',
            'slug' => 'page-1',
            'type' => 'page',
            'status' => 'published',
        ]);

        Content::create([
            'title' => 'Page 2',
            'slug' => 'page-2',
            'type' => 'page',
            'status' => 'published',
        ]);

        $response1 = $this->get('/page-1');
        $response1->assertOk();

        $response2 = $this->get('/page-2');
        $response2->assertOk();
    }

    /** @test */
    public function test_page_with_long_slug(): void
    {
        $longSlug = str_repeat('a', 200);

        Content::create([
            'title' => 'Long Slug Page',
            'slug' => $longSlug,
            'type' => 'page',
            'status' => 'published',
        ]);

        $response = $this->get('/' . $longSlug);

        $response->assertOk();
    }

    /** @test */
    public function test_page_with_numbers_in_slug(): void
    {
        Content::create([
            'title' => 'Numbered Page',
            'slug' => 'page-123-test',
            'type' => 'page',
            'status' => 'published',
        ]);

        $response = $this->get('/page-123-test');

        $response->assertOk();
    }

    /** @test */
    public function test_page_with_hyphens_in_slug(): void
    {
        Content::create([
            'title' => 'Hyphenated Page',
            'slug' => 'this-is-a-long-slug-with-hyphens',
            'type' => 'page',
            'status' => 'published',
        ]);

        $response = $this->get('/this-is-a-long-slug-with-hyphens');

        $response->assertOk();
    }

    /** @test */
    public function test_homepage_option_value_is_numeric(): void
    {
        $homepageOption = Option::where('key', 'homepage')->first();

        $this->assertNotNull($homepageOption);
        $this->assertIsNumeric($homepageOption->value);
    }

    /** @test */
    public function test_seeded_pages_are_accessible(): void
    {
        // Test des pages créées par le seeder
        $seedPages = ['accueil', 'mentions-legales', 'politique-de-confidentialite'];

        foreach ($seedPages as $slug) {
            $response = $this->get('/' . $slug);
            $response->assertOk();
        }
    }

    /** @test */
    public function test_page_displays_with_empty_content(): void
    {
        Content::create([
            'title' => 'Empty Page',
            'slug' => 'empty-page',
            'type' => 'page',
            'status' => 'published',
            'content' => json_encode([]),
        ]);

        $response = $this->get('/empty-page');

        $response->assertOk();
    }

    /** @test */
    public function test_page_displays_with_null_content(): void
    {
        Content::create([
            'title' => 'Null Content Page',
            'slug' => 'null-content',
            'type' => 'page',
            'status' => 'published',
            'content' => null,
        ]);

        $response = $this->get('/null-content');

        $response->assertOk();
    }
}