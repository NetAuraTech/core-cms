<?php

namespace Netauratech\CoreCms\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
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
        $this->assertDatabaseHas('options', [
            'key' => 'custom_key',
            'value' => 'custom_value',
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
        $this->assertDatabaseHas('options', [
            'key' => 'editable_option',
            'value' => 'updated',
        ]);
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
        $this->assertDatabaseHas('options', [
            'key' => 'site_name',
        ]);
    }
}