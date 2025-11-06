<?php

namespace Netauratech\CoreCms\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
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
        $this->assertNotEmpty($widgets, 'Dashboard should have at least one widget registered');
    }

    /** @test */
    public function test_dashboard_displays_failed_jobs(): void
    {
        $user = $this->actingAsUser(1);

        FailedJob::create([
            'uuid' => 'test-uuid-123',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['job' => 'TestJob']),
            'exception' => 'Test exception',
            'failed_at' => now(),
        ]);

        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSeeText('test-uuid-123');
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
    public function test_clear_cache_clears_view_cache(): void
    {
        $user = $this->actingAsUser(1);

        Artisan::call('view:cache');

        $response = $this->delete(route('admin.cache'));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success', __('core-cms::admin.cache.cleared'));
    }

    /** @test */
    public function test_guest_cannot_clear_cache(): void
    {
        $response = $this->delete(route('admin.cache'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_it_can_retry_failed_job(): void
    {
        $user = $this->actingAsUser(1);

        $job = FailedJob::create([
            'uuid' => 'retry-test-uuid',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['job' => 'RetryTestJob']),
            'exception' => 'Test exception for retry',
            'failed_at' => now(),
        ]);

        Queue::fake();

        $response = $this->post(route('admin.retry_job', $job));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success', __('core-cms::admin.job.relaunch.confirmed'));
    }

    /** @test */
    public function test_it_can_destroy_failed_job(): void
    {
        $user = $this->actingAsUser(1);

        $job = FailedJob::create([
            'uuid' => 'destroy-test-uuid',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['job' => 'DestroyTestJob']),
            'exception' => 'Test exception for destroy',
            'failed_at' => now(),
        ]);

        $response = $this->delete(route('admin.destroy_job', $job));

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success', __('core-cms::admin.job.delete.confirmed'));
    }

    /** @test */
    public function test_guest_cannot_retry_job(): void
    {
        $job = FailedJob::create([
            'uuid' => 'guest-retry-uuid',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['job' => 'GuestRetryJob']),
            'exception' => 'Test exception',
            'failed_at' => now(),
        ]);

        $response = $this->post(route('admin.retry_job', $job));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_guest_cannot_destroy_job(): void
    {
        $job = FailedJob::create([
            'uuid' => 'guest-destroy-uuid',
            'connection' => 'database',
            'queue' => 'default',
            'payload' => json_encode(['job' => 'GuestDestroyJob']),
            'exception' => 'Test exception',
            'failed_at' => now(),
        ]);

        $response = $this->delete(route('admin.destroy_job', $job));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_access_dashboard(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_dashboard_shows_no_failed_jobs_when_empty(): void
    {
        $user = $this->actingAsUser(1);

        FailedJob::query()->delete();

        $response = $this->get(route('admin.dashboard'));

        $response->assertOk();
    }
}