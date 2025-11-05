<?php

namespace Netauratech\CoreCms\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Netauratech\CoreCms\Models\FailedJob;
use Netauratech\CoreCms\Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.dashboard');
        $response->assertViewHas('widgets');
    }

    /** @test */
    public function test_dashboard_displays_widgets(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();

        $widgets = $response->viewData('widgets');
        $this->assertIsArray($widgets);
    }

    /** @test */
    public function test_it_can_clear_cache(): void
    {
        $user = $this->actingAsUser(1);

        Cache::put('test_key', 'test_value', 60);
        $this->assertEquals('test_value', Cache::get('test_key'));

        $response = $this->delete(route('admin.cache'));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function test_guest_cannot_clear_cache(): void
    {
        $response = $this->delete(route('admin.cache'));

        $response->assertStatus(403);
    }
}