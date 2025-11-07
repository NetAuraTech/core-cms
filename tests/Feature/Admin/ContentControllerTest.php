<?php

namespace Netauratech\CoreCms\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Netauratech\CoreCms\Models\Category;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Models\Tag;
use Netauratech\CoreCms\Tests\TestCase;

class ContentControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_guest_cannot_access_content_index(): void
    {
        $response = $this->get(route('admin.contents.index', ['type' => 'page']));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_authenticated_user_can_view_pages(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.contents.index', ['type' => 'page']));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.contents.index');
        $response->assertViewHas('contentType', 'page');

        // Les pages seedées devraient être visibles (accueil, mentions-legales, etc.)
        $response->assertSee('Accueil');
    }

    /** @test */
    public function test_authenticated_user_can_view_templates(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.contents.index', ['type' => 'template']));

        $response->assertOk();
        $response->assertViewHas('contentType', 'template');

        // Les templates seedés (header, footer) devraient être visibles
        $response->assertSee('Header');
        $response->assertSee('Footer');
    }

    /** @test */
    public function test_contents_are_paginated(): void
    {
        $user = $this->actingAsUser(1);

        // Créer 25 pages en plus des pages seedées
        for ($i = 1; $i <= 25; $i++) {
            Content::create([
                'title' => "Page $i",
                'slug' => "page-$i",
                'type' => 'page',
                'status' => 'published',
            ]);
        }

        $response = $this->get(route('admin.contents.index', ['type' => 'page']));

        $response->assertOk();
        $contents = $response->viewData('contents');
        $this->assertEquals(20, $contents->perPage());
    }

    /** @test */
    public function test_it_shows_create_form(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.contents.create', ['type' => 'page']));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.contents.form');
        $response->assertViewHas('content');
        $response->assertViewHas('contentType', 'page');
        $response->assertViewHas('formFields');
    }

    /** @test */
    public function test_create_form_has_articles_and_pages(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.contents.create', ['type' => 'page']));

        $response->assertOk();
        $response->assertViewHas('articles');
        $response->assertViewHas('pages');
    }

    /** @test */
    public function test_it_can_create_page(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => 'New Page',
            'slug' => 'new-page',
            'description' => 'Test description',
            'content' => json_encode([]),
            'type' => 'page',
            'status' => 'published',
            'published_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('admin.contents.index', ['type' => 'page']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contents', [
            'title' => 'New Page',
            'slug' => 'new-page',
            'type' => 'page',
        ]);
    }

    /** @test */
    public function test_it_auto_generates_slug_if_not_provided(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => 'Auto Slug Page',
            'type' => 'page',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.contents.index', ['type' => 'page']));

        $this->assertDatabaseHas('contents', [
            'title' => 'Auto Slug Page',
            'slug' => 'auto-slug-page',
        ]);
    }

    /** @test */
    public function test_it_validates_required_title(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => '',
            'type' => 'page',
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    /** @test */
    public function test_it_validates_unique_slug(): void
    {
        $user = $this->actingAsUser(1);

        // 'accueil' existe déjà via le seeder
        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => 'Test',
            'slug' => 'accueil',
            'type' => 'page',
            'status' => 'draft',
        ]);

        $response->assertSessionHasErrors(['slug']);
    }

    /** @test */
    public function test_it_validates_status_values(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => 'Test',
            'type' => 'page',
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    /** @test */
    public function test_it_can_create_draft_content(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => 'Draft Page',
            'type' => 'page',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.contents.index', ['type' => 'page']));

        $this->assertDatabaseHas('contents', [
            'title' => 'Draft Page',
            'status' => 'draft',
        ]);
    }

    /** @test */
    public function test_it_shows_edit_form(): void
    {
        $user = $this->actingAsUser(1);

        // Utiliser une page seedée (homepage = accueil)
        $homepage = Content::where('slug', 'accueil')->first();

        $response = $this->get(route('admin.contents.edit', $homepage));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.contents.form');
        $response->assertViewHas('content', $homepage);
    }

    /** @test */
    public function test_it_can_update_content(): void
    {
        $user = $this->actingAsUser(1);

        $content = Content::create([
            'title' => 'Old Title',
            'slug' => 'old-slug',
            'type' => 'page',
            'status' => 'draft',
        ]);

        $response = $this->put(route('admin.contents.update', $content), [
            'title' => 'New Title',
            'slug' => 'new-slug',
            'type' => 'page',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.contents.index', ['type' => 'page']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contents', [
            'id' => $content->id,
            'title' => 'New Title',
            'slug' => 'new-slug',
            'status' => 'published',
        ]);
    }

    /** @test */
    public function test_update_validates_unique_slug_except_current(): void
    {
        $user = $this->actingAsUser(1);

        $content1 = Content::create([
            'title' => 'Content 1',
            'slug' => 'content-1',
            'type' => 'page',
            'status' => 'published',
        ]);

        // 'accueil' existe déjà
        $response = $this->put(route('admin.contents.update', $content1), [
            'title' => 'Updated',
            'slug' => 'accueil',
            'type' => 'page',
            'status' => 'published',
        ]);

        $response->assertSessionHasErrors(['slug']);

        // Doit réussir avec le même slug
        $response = $this->put(route('admin.contents.update', $content1), [
            'title' => 'Updated',
            'slug' => 'content-1',
            'type' => 'page',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.contents.index', ['type' => 'page']));
    }

    /** @test */
    public function test_it_can_delete_content(): void
    {
        $user = $this->actingAsUser(1);

        $content = Content::create([
            'title' => 'Delete Me',
            'slug' => 'delete-me',
            'type' => 'page',
            'status' => 'draft',
        ]);

        $response = $this->delete(route('admin.contents.destroy', $content));

        $response->assertRedirect(route('admin.contents.index', ['type' => 'page']));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('contents', ['id' => $content->id]);
    }

    /** @test */
    public function test_deleting_content_removes_category_associations(): void
    {
        $user = $this->actingAsUser(1);

        $content = Content::create([
            'title' => 'Test',
            'slug' => 'test-delete',
            'type' => 'page',
            'status' => 'published',
        ]);

        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-cat']);
        $content->categories()->attach($category);

        $this->delete(route('admin.contents.destroy', $content));

        $this->assertDatabaseMissing('content_category', [
            'content_id' => $content->id,
        ]);
    }

    /** @test */
    public function test_deleting_content_removes_tag_associations(): void
    {
        $user = $this->actingAsUser(1);

        $content = Content::create([
            'title' => 'Test',
            'slug' => 'test-tag-delete',
            'type' => 'page',
            'status' => 'published',
        ]);

        $tag = Tag::create(['name' => 'Test Tag', 'slug' => 'test-tag']);
        $content->tags()->attach($tag);

        $this->delete(route('admin.contents.destroy', $content));

        $this->assertDatabaseMissing('content_tag', [
            'content_id' => $content->id,
        ]);
    }

    /** @test */
    public function test_user_without_permission_cannot_access_contents(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->get(route('admin.contents.index', ['type' => 'page']));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_create_contents(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => 'Test',
            'type' => 'page',
            'status' => 'draft',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_it_can_create_template(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.contents.store', ['type' => 'template']), [
            'title' => 'New Template',
            'slug' => 'new-template',
            'type' => 'template',
            'status' => 'published',
        ]);

        $response->assertRedirect(route('admin.contents.index', ['type' => 'template']));

        $this->assertDatabaseHas('contents', [
            'title' => 'New Template',
            'type' => 'template',
        ]);
    }

    /** @test */
    public function test_it_can_create_archived_content(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => 'Archived Page',
            'type' => 'page',
            'status' => 'archived',
        ]);

        $response->assertRedirect(route('admin.contents.index', ['type' => 'page']));

        $this->assertDatabaseHas('contents', [
            'title' => 'Archived Page',
            'status' => 'archived',
        ]);
    }

    /** @test */
    public function test_content_stores_json_content(): void
    {
        $user = $this->actingAsUser(1);

        $jsonContent = json_encode([
            ['_name' => 'section', 'title' => 'Test Section']
        ]);

        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => 'JSON Content',
            'type' => 'page',
            'status' => 'draft',
            'content' => $jsonContent,
        ]);

        $response->assertRedirect(route('admin.contents.index', ['type' => 'page']));

        $content = Content::where('title', 'JSON Content')->first();
        $this->assertJson($content->content);
    }

    /** @test */
    public function test_preview_endpoint_works(): void
    {
        $user = $this->actingAsUser(1);

        $data = [
            ['_name' => 'section', 'title' => 'Preview Test']
        ];

        $response = $this->json('POST', route('admin.contents.preview', ['type' => 'content']), $data);

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.contents.preview');
    }

    /** @test */
    public function test_preview_for_template_hides_header_footer(): void
    {
        $user = $this->actingAsUser(1);

        $data = [
            ['_name' => 'section', 'title' => 'Template Preview']
        ];

        $response = $this->json('POST', route('admin.contents.preview', ['type' => 'template']), $data);

        $response->assertOk();
        $response->assertViewHas('hideHeaderFooter', true);
    }

    /** @test */
    public function test_content_with_special_characters(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => 'Contenu Spécial: été & hiver',
            'type' => 'page',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.contents.index', ['type' => 'page']));

        $this->assertDatabaseHas('contents', [
            'title' => 'Contenu Spécial: été & hiver',
        ]);
    }

    /** @test */
    public function test_index_only_shows_contents_of_specified_type(): void
    {
        $user = $this->actingAsUser(1);

        Content::create([
            'title' => 'Test Page',
            'slug' => 'test-page-type',
            'type' => 'page',
            'status' => 'published',
        ]);

        Content::create([
            'title' => 'Test Template',
            'slug' => 'test-template-type',
            'type' => 'template',
            'status' => 'published',
        ]);

        $response = $this->get(route('admin.contents.index', ['type' => 'page']));

        $contents = $response->viewData('contents');

        foreach ($contents as $content) {
            $this->assertEquals('page', $content->type);
        }
    }

    /** @test */
    public function test_published_at_can_be_set(): void
    {
        $user = $this->actingAsUser(1);

        $publishDate = now()->addDays(7);

        $response = $this->post(route('admin.contents.store', ['type' => 'page']), [
            'title' => 'Future Publication',
            'type' => 'page',
            'status' => 'draft',
            'published_at' => $publishDate->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('admin.contents.index', ['type' => 'page']));

        $content = Content::where('title', 'Future Publication')->first();
        $this->assertNotNull($content->published_at);
    }
}