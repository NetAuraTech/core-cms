<?php

namespace Netauratech\CoreCms\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Models\Tag;
use Netauratech\CoreCms\Tests\TestCase;

class TagControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_guest_cannot_access_tag_index(): void
    {
        $response = $this->get(route('admin.tags.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_authenticated_user_can_view_tags(): void
    {
        $user = $this->actingAsUser(1);

        Tag::create(['name' => 'Test Tag', 'slug' => 'test-tag']);

        $response = $this->get(route('admin.tags.index'));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.tags.index');
        $response->assertSee('Test Tag');
    }

    /** @test */
    public function test_tags_are_paginated(): void
    {
        $user = $this->actingAsUser(1);

        for ($i = 1; $i <= 25; $i++) {
            Tag::create([
                'name' => "Tag $i",
                'slug' => "tag-$i",
            ]);
        }

        $response = $this->get(route('admin.tags.index'));

        $response->assertOk();
        $tags = $response->viewData('tags');
        $this->assertEquals(20, $tags->perPage());
    }

    /** @test */
    public function test_tags_are_ordered_by_name(): void
    {
        $user = $this->actingAsUser(1);

        Tag::create(['name' => 'Zebra', 'slug' => 'zebra']);
        Tag::create(['name' => 'Alpha', 'slug' => 'alpha']);

        $response = $this->get(route('admin.tags.index'));

        $tags = $response->viewData('tags');
        $this->assertEquals('Alpha', $tags->first()->name);
    }

    /** @test */
    public function test_it_shows_create_form(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.tags.create'));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.tags.form');
        $response->assertViewHas('tag');
    }

    /** @test */
    public function test_it_can_create_tag(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.tags.store'), [
            'name' => 'New Tag',
            'slug' => 'new-tag',
        ]);

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tags', [
            'name' => 'New Tag',
            'slug' => 'new-tag',
        ]);
    }

    /** @test */
    public function test_it_auto_generates_slug_if_not_provided(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.tags.store'), [
            'name' => 'Auto Slug Tag',
        ]);

        $response->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseHas('tags', [
            'name' => 'Auto Slug Tag',
            'slug' => 'auto-slug-tag',
        ]);
    }

    /** @test */
    public function test_it_validates_required_name(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.tags.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function test_it_validates_unique_slug(): void
    {
        $user = $this->actingAsUser(1);

        Tag::create(['name' => 'Existing', 'slug' => 'existing']);

        $response = $this->post(route('admin.tags.store'), [
            'name' => 'New Tag',
            'slug' => 'existing',
        ]);

        $response->assertSessionHasErrors(['slug']);
    }

    /** @test */
    public function test_it_validates_name_max_length(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.tags.store'), [
            'name' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function test_it_shows_edit_form(): void
    {
        $user = $this->actingAsUser(1);
        $tag = Tag::create(['name' => 'Edit Me', 'slug' => 'edit-me']);

        $response = $this->get(route('admin.tags.edit', $tag));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.tags.form');
        $response->assertViewHas('tag', $tag);
    }

    /** @test */
    public function test_it_can_update_tag(): void
    {
        $user = $this->actingAsUser(1);
        $tag = Tag::create(['name' => 'Old Name', 'slug' => 'old-slug']);

        $response = $this->put(route('admin.tags.update', $tag), [
            'name' => 'New Name',
            'slug' => 'new-slug',
        ]);

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'New Name',
            'slug' => 'new-slug',
        ]);
    }

    /** @test */
    public function test_update_validates_unique_slug_except_current(): void
    {
        $user = $this->actingAsUser(1);

        $tag1 = Tag::create(['name' => 'Tag 1', 'slug' => 'tag-1']);
        $tag2 = Tag::create(['name' => 'Tag 2', 'slug' => 'tag-2']);

        // Doit échouer avec le slug d'un autre tag
        $response = $this->put(route('admin.tags.update', $tag1), [
            'name' => 'Updated',
            'slug' => 'tag-2',
        ]);

        $response->assertSessionHasErrors(['slug']);

        // Doit réussir avec le même slug
        $response = $this->put(route('admin.tags.update', $tag1), [
            'name' => 'Updated',
            'slug' => 'tag-1',
        ]);

        $response->assertRedirect(route('admin.tags.index'));
    }

    /** @test */
    public function test_it_can_delete_tag(): void
    {
        $user = $this->actingAsUser(1);
        $tag = Tag::create(['name' => 'Delete Me', 'slug' => 'delete-me']);

        $response = $this->delete(route('admin.tags.destroy', $tag));

        $response->assertRedirect(route('admin.tags.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    /** @test */
    public function test_deleting_tag_removes_content_associations(): void
    {
        $user = $this->actingAsUser(1);

        $tag = Tag::create(['name' => 'Test Tag', 'slug' => 'test']);
        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'page',
            'status' => 'published',
        ]);

        $content->tags()->attach($tag);

        $this->delete(route('admin.tags.destroy', $tag));

        $this->assertDatabaseMissing('content_tag', [
            'tag_id' => $tag->id,
        ]);
    }

    /** @test */
    public function test_user_without_permission_cannot_access_tags(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->get(route('admin.tags.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_create_tags(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->post(route('admin.tags.store'), [
            'name' => 'Test',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_update_tags(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $tag = Tag::create(['name' => 'Test', 'slug' => 'test']);

        $response = $this->put(route('admin.tags.update', $tag), [
            'name' => 'Updated',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_delete_tags(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $tag = Tag::create(['name' => 'Test', 'slug' => 'test']);

        $response = $this->delete(route('admin.tags.destroy', $tag));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_tag_with_special_characters_in_name(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.tags.store'), [
            'name' => 'Tag Spécial',
        ]);

        $response->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseHas('tags', [
            'name' => 'Tag Spécial',
        ]);
    }

    /** @test */
    public function test_tag_counts_its_contents(): void
    {
        $user = $this->actingAsUser(1);

        $tag = Tag::create(['name' => 'Test', 'slug' => 'test']);

        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'page',
            'status' => 'published',
        ]);

        $content->tags()->attach($tag);

        $this->assertEquals(1, $tag->contents()->count());
    }

    /** @test */
    public function test_empty_tags_can_exist(): void
    {
        $user = $this->actingAsUser(1);

        $tag = Tag::create(['name' => 'Empty', 'slug' => 'empty']);

        $this->assertEquals(0, $tag->contents()->count());
    }

    /** @test */
    public function test_multiple_contents_can_have_same_tag(): void
    {
        $user = $this->actingAsUser(1);

        $tag = Tag::create(['name' => 'Shared', 'slug' => 'shared']);

        $content1 = Content::create([
            'title' => 'Content 1',
            'slug' => 'content-1',
            'type' => 'page',
            'status' => 'published',
        ]);

        $content2 = Content::create([
            'title' => 'Content 2',
            'slug' => 'content-2',
            'type' => 'page',
            'status' => 'published',
        ]);

        $content1->tags()->attach($tag);
        $content2->tags()->attach($tag);

        $this->assertEquals(2, $tag->contents()->count());
    }

    /** @test */
    public function test_content_can_have_multiple_tags(): void
    {
        $user = $this->actingAsUser(1);

        $tag1 = Tag::create(['name' => 'Tag 1', 'slug' => 'tag-1']);
        $tag2 = Tag::create(['name' => 'Tag 2', 'slug' => 'tag-2']);

        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'page',
            'status' => 'published',
        ]);

        $content->tags()->attach([$tag1->id, $tag2->id]);

        $this->assertEquals(2, $content->tags()->count());
    }

    /** @test */
    public function test_tag_slug_with_numbers(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.tags.store'), [
            'name' => 'Tag 123',
        ]);

        $response->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseHas('tags', [
            'slug' => 'tag-123',
        ]);
    }

    /** @test */
    public function test_tag_slug_with_hyphens(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.tags.store'), [
            'name' => 'Multi-Word Tag',
        ]);

        $response->assertRedirect(route('admin.tags.index'));

        $this->assertDatabaseHas('tags', [
            'slug' => 'multi-word-tag',
        ]);
    }
}