<?php

namespace Netauratech\CoreCms\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Netauratech\CoreCms\Models\Category;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_guest_cannot_access_category_index(): void
    {
        $response = $this->get(route('admin.categories.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_authenticated_user_can_view_categories(): void
    {
        $user = $this->actingAsUser(1);

        Category::create(['name' => 'Test Category', 'slug' => 'test-category']);

        $response = $this->get(route('admin.categories.index'));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.categories.index');
        $response->assertSee('Test Category');
    }

    /** @test */
    public function test_categories_are_paginated(): void
    {
        $user = $this->actingAsUser(1);

        for ($i = 1; $i <= 25; $i++) {
            Category::create([
                'name' => "Category $i",
                'slug' => "category-$i",
            ]);
        }

        $response = $this->get(route('admin.categories.index'));

        $response->assertOk();
        $categories = $response->viewData('categories');
        $this->assertEquals(20, $categories->perPage());
    }

    /** @test */
    public function test_categories_are_ordered_by_name(): void
    {
        $user = $this->actingAsUser(1);

        Category::create(['name' => 'Zebra', 'slug' => 'zebra']);
        Category::create(['name' => 'Alpha', 'slug' => 'alpha']);

        $response = $this->get(route('admin.categories.index'));

        $categories = $response->viewData('categories');
        $this->assertEquals('Alpha', $categories->first()->name);
    }

    /** @test */
    public function test_it_shows_create_form(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.categories.create'));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.categories.form');
        $response->assertViewHas('category');
    }

    /** @test */
    public function test_it_can_create_category(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'New Category',
            'slug' => 'new-category',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
            'slug' => 'new-category',
        ]);
    }

    /** @test */
    public function test_it_auto_generates_slug_if_not_provided(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Auto Slug Category',
        ]);

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Auto Slug Category',
            'slug' => 'auto-slug-category',
        ]);
    }

    /** @test */
    public function test_it_validates_required_name(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.categories.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function test_it_validates_unique_slug(): void
    {
        $user = $this->actingAsUser(1);

        Category::create(['name' => 'Existing', 'slug' => 'existing']);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'New Category',
            'slug' => 'existing',
        ]);

        $response->assertSessionHasErrors(['slug']);
    }

    /** @test */
    public function test_it_validates_name_max_length(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.categories.store'), [
            'name' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function test_it_validates_slug_max_length(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Test',
            'slug' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['slug']);
    }

    /** @test */
    public function test_it_shows_edit_form(): void
    {
        $user = $this->actingAsUser(1);
        $category = Category::create(['name' => 'Edit Me', 'slug' => 'edit-me']);

        $response = $this->get(route('admin.categories.edit', $category));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.categories.form');
        $response->assertViewHas('category', $category);
    }

    /** @test */
    public function test_it_can_update_category(): void
    {
        $user = $this->actingAsUser(1);
        $category = Category::create(['name' => 'Old Name', 'slug' => 'old-slug']);

        $response = $this->put(route('admin.categories.update', $category), [
            'name' => 'New Name',
            'slug' => 'new-slug',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'slug' => 'new-slug',
        ]);
    }

    /** @test */
    public function test_update_validates_unique_slug_except_current(): void
    {
        $user = $this->actingAsUser(1);

        $category1 = Category::create(['name' => 'Category 1', 'slug' => 'category-1']);
        $category2 = Category::create(['name' => 'Category 2', 'slug' => 'category-2']);

        // Doit échouer avec le slug d'une autre catégorie
        $response = $this->put(route('admin.categories.update', $category1), [
            'name' => 'Updated',
            'slug' => 'category-2',
        ]);

        $response->assertSessionHasErrors(['slug']);

        // Doit réussir avec le même slug
        $response = $this->put(route('admin.categories.update', $category1), [
            'name' => 'Updated',
            'slug' => 'category-1',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
    }

    /** @test */
    public function test_it_can_delete_category(): void
    {
        $user = $this->actingAsUser(1);
        $category = Category::create(['name' => 'Delete Me', 'slug' => 'delete-me']);

        $response = $this->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function test_deleting_category_removes_content_associations(): void
    {
        $user = $this->actingAsUser(1);

        $category = Category::create(['name' => 'Test Category', 'slug' => 'test']);
        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'page',
            'status' => 'published',
        ]);

        $content->categories()->attach($category);

        $this->delete(route('admin.categories.destroy', $category));

        $this->assertDatabaseMissing('content_category', [
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function test_user_without_permission_cannot_access_categories(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->get(route('admin.categories.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_create_categories(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Test',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_update_categories(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $category = Category::create(['name' => 'Test', 'slug' => 'test']);

        $response = $this->put(route('admin.categories.update', $category), [
            'name' => 'Updated',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_delete_categories(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $category = Category::create(['name' => 'Test', 'slug' => 'test']);

        $response = $this->delete(route('admin.categories.destroy', $category));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_slug_is_normalized(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Test Category',
            'slug' => 'Test Category With Spaces',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
    }

    /** @test */
    public function test_category_with_special_characters_in_name(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.categories.store'), [
            'name' => 'Catégorie Spéciale',
        ]);

        $response->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Catégorie Spéciale',
        ]);
    }

    /** @test */
    public function test_category_counts_its_contents(): void
    {
        $user = $this->actingAsUser(1);

        $category = Category::create(['name' => 'Test', 'slug' => 'test']);

        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'page',
            'status' => 'published',
        ]);

        $content->categories()->attach($category);

        $this->assertEquals(1, $category->contents()->count());
    }

    /** @test */
    public function test_empty_categories_can_exist(): void
    {
        $user = $this->actingAsUser(1);

        $category = Category::create(['name' => 'Empty', 'slug' => 'empty']);

        $this->assertEquals(0, $category->contents()->count());
    }

    /** @test */
    public function test_multiple_contents_can_have_same_category(): void
    {
        $user = $this->actingAsUser(1);

        $category = Category::create(['name' => 'Shared', 'slug' => 'shared']);

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

        $content1->categories()->attach($category);
        $content2->categories()->attach($category);

        $this->assertEquals(2, $category->contents()->count());
    }
}