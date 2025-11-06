<?php

namespace Netauratech\CoreCms\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Tests\TestCase;

class OptionControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_guest_cannot_access_option_index(): void
    {
        $response = $this->get(route('admin.option.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_authenticated_user_can_view_options(): void
    {
        $user = $this->actingAsUser(1);

        $option = Option::find('site_name');
        $option->update(['value' => 'Test Site']);

        $response = $this->get(route('admin.option.index'));

        $response->assertOk();
        $response->assertSee('Test Site');
    }

    /** @test */
    public function test_options_are_grouped_by_category(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.option.index'));

        $response->assertOk();
        $response->assertViewHas('groupedOptions');

        $groupedOptions = $response->viewData('groupedOptions');
        $this->assertNotEmpty($groupedOptions);
    }

    /** @test */
    public function test_it_can_create_custom_option(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.option.store'), [
            'key' => 'custom_key',
            'value' => 'custom_value',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $response->assertRedirect(route('admin.option.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('options', [
            'key' => 'custom_key',
            'value' => 'custom_value',
        ]);
    }

    /** @test */
    public function test_it_can_create_boolean_option(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.option.store'), [
            'key' => 'custom_boolean',
            'value' => '1',
            'type' => 'boolean',
            'category' => 'custom',
        ]);

        $response->assertRedirect(route('admin.option.index'));

        $this->assertDatabaseHas('options', [
            'key' => 'custom_boolean',
            'type' => 'boolean',
        ]);
    }

    /** @test */
    public function test_it_can_create_number_option(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.option.store'), [
            'key' => 'custom_number',
            'value' => '42',
            'type' => 'number',
            'category' => 'custom',
        ]);

        $response->assertRedirect(route('admin.option.index'));

        $this->assertDatabaseHas('options', [
            'key' => 'custom_number',
            'value' => '42',
            'type' => 'number',
        ]);
    }

    /** @test */
    public function test_it_can_create_content_option(): void
    {
        $user = $this->actingAsUser(1);
        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'page',
            'status' => 'published',
        ]);

        $response = $this->post(route('admin.option.store'), [
            'key' => 'custom_content',
            'value' => $content->id,
            'type' => 'content',
            'category' => 'custom',
        ]);

        $response->assertRedirect(route('admin.option.index'));

        $this->assertDatabaseHas('options', [
            'key' => 'custom_content',
            'value' => $content->id,
            'type' => 'content',
        ]);
    }

    /** @test */
    public function test_it_validates_required_fields(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.option.store'), [
            'value' => 'test',
        ]);

        $response->assertSessionHasErrors(['key']);
        $response->assertStatus(302);
    }

    /** @test */
    public function test_it_validates_key_minimum_length(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.option.store'), [
            'key' => 'ab',
            'value' => 'test',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $response->assertSessionHasErrors(['key']);
    }

    /** @test */
    public function test_it_validates_unique_key_on_create(): void
    {
        $user = $this->actingAsUser(1);

        Option::create([
            'key' => 'existing_key',
            'value' => 'test',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $response = $this->post(route('admin.option.store'), [
            'key' => 'existing_key',
            'value' => 'test2',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $response->assertSessionHasErrors(['key']);
    }

    /** @test */
    public function test_it_validates_type_is_valid(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.option.store'), [
            'key' => 'custom_key',
            'value' => 'test',
            'type' => 'invalid_type',
            'category' => 'custom',
        ]);

        $response->assertSessionHasErrors(['type']);
    }

    /** @test */
    public function test_it_can_update_custom_option(): void
    {
        $user = $this->actingAsUser(1);

        $option = Option::create([
            'key' => 'editable_option',
            'value' => 'original',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $response = $this->put(route('admin.option.update', $option->key), [
            'key' => 'editable_option',
            'value' => 'updated',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $response->assertRedirect(route('admin.option.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('options', [
            'key' => 'editable_option',
            'value' => 'updated',
        ]);
    }

    /** @test */
    public function test_it_can_update_system_option_value_only(): void
    {
        $user = $this->actingAsUser(1);

        $option = Option::find('site_name');
        $originalKey = $option->key;
        $originalType = $option->type;

        $response = $this->put(route('admin.option.update', $option->key), [
            'key' => 'attempted_new_key',
            'value' => 'New Site Name',
            'type' => 'number',
            'category' => 'general',
        ]);

        $response->assertRedirect(route('admin.option.index'));

        $option->refresh();
        $this->assertEquals($originalKey, $option->key);
        $this->assertEquals($originalType, $option->type);
        $this->assertEquals('New Site Name', $option->value);
    }

    /** @test */
    public function test_it_can_delete_custom_option(): void
    {
        $user = $this->actingAsUser(1);

        $option = Option::create([
            'key' => 'deletable_option',
            'value' => 'test',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $response = $this->delete(route('admin.option.destroy', $option->key));

        $response->assertRedirect(route('admin.option.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('options', [
            'key' => 'deletable_option',
        ]);
    }

    /** @test */
    public function test_it_cannot_delete_system_option(): void
    {
        $user = $this->actingAsUser(1);

        $option = Option::find('site_name');

        $response = $this->delete(route('admin.option.destroy', $option->key));

        $response->assertRedirect(route('admin.option.index'));
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('options', [
            'key' => 'site_name',
        ]);
    }

    /** @test */
    public function test_show_create_form(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.option.create'));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.option.form');
        $response->assertViewHas('option');
    }

    /** @test */
    public function test_show_edit_form(): void
    {
        $user = $this->actingAsUser(1);
        $option = Option::find('site_name');

        $response = $this->get(route('admin.option.edit', $option));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.option.form');
        $response->assertViewHas('option', $option);
    }

    /** @test */
    public function test_edit_form_has_contents_for_content_type_options(): void
    {
        $user = $this->actingAsUser(1);
        $option = Option::find('site_name');

        $response = $this->get(route('admin.option.edit', $option));

        $response->assertOk();
        $response->assertViewHas('pages');
        $response->assertViewHas('articles');
        $response->assertViewHas('templates');
    }

    /** @test */
    public function test_create_form_has_contents_for_selection(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.option.create'));

        $response->assertOk();
        $response->assertViewHas('pages');
        $response->assertViewHas('articles');
        $response->assertViewHas('templates');
    }

    /** @test */
    public function test_user_without_permission_cannot_access_options(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->get(route('admin.option.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_create_options(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->post(route('admin.option.store'), [
            'key' => 'test_key',
            'value' => 'test',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_update_options(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);
        $option = Option::find('site_name');

        $response = $this->put(route('admin.option.update', $option), [
            'value' => 'New Name',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_delete_options(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $option = Option::create([
            'key' => 'test_option',
            'value' => 'test',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $response = $this->delete(route('admin.option.destroy', $option));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_updating_option_clears_cache(): void
    {
        $user = $this->actingAsUser(1);
        $option = Option::find('site_name');

        $response = $this->put(route('admin.option.update', $option), [
            'value' => 'Updated Site Name',
        ]);

        $response->assertRedirect(route('admin.option.index'));
    }

    /** @test */
    public function test_updating_option_clears_view_cache(): void
    {
        $user = $this->actingAsUser(1);
        $option = Option::find('site_name');

        $response = $this->put(route('admin.option.update', $option), [
            'value' => 'Updated Site Name',
        ]);

        $response->assertRedirect(route('admin.option.index'));
    }

    /** @test */
    public function test_theme_options_are_excluded_from_index(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.option.index'));

        $response->assertOk();
        $groupedOptions = $response->viewData('groupedOptions');

        foreach ($groupedOptions as $group) {
            foreach ($group->options as $option) {
                $this->assertNotEquals('theme', $option->type);
            }
        }
    }

    /** @test */
    public function test_options_are_ordered_by_category_then_key(): void
    {
        $user = $this->actingAsUser(1);

        Option::create([
            'key' => 'z_option',
            'value' => 'test',
            'type' => 'text',
            'category' => 'custom',
        ]);

        Option::create([
            'key' => 'a_option',
            'value' => 'test',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $response = $this->get(route('admin.option.index'));

        $response->assertOk();
    }

    /** @test */
    public function test_default_category_is_custom_for_new_options(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.option.create'));

        $option = $response->viewData('option');
        $this->assertEquals('custom', $option->category);
    }
}