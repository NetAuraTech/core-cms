<?php

namespace Netauratech\CoreCms\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Netauratech\CoreCms\Tests\TestCase;
use Spatie\Permission\Models\Role;

class ImpersonateControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_guest_cannot_impersonate(): void
    {
        $targetUser = $this->createUser();

        $response = $this->get(route('admin.user.impersonate', $targetUser));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_admin_can_impersonate_user(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser([
            'username' => 'targetuser',
            'email' => 'target@example.com',
        ]);

        $response = $this->get(route('admin.user.impersonate', $targetUser));

        $response->assertRedirect(route('profile.index'));

        $this->assertEquals($targetUser->id, Auth::id());

        $this->assertEquals($admin->id, session('impersonate'));
    }

    /** @test */
    public function test_cannot_impersonate_twice(): void
    {
        $admin = $this->actingAsUser(1);
        $firstTarget = $this->createUser(['username' => 'first']);
        $secondTarget = $this->createUser(['username' => 'second']);

        $this->get(route('admin.user.impersonate', $firstTarget));

        $response = $this->get(route('admin.user.impersonate', $secondTarget));

        $response->assertStatus(403);

        $this->get(route('admin.user.impersonate.leave'));

        $firstTarget->assignRole(Role::find(1));

        $this->get(route('admin.user.impersonate', $firstTarget));

        $response = $this->get(route('admin.user.impersonate', $secondTarget));

        $response->assertRedirect(route('profile.index'));

        $this->assertEquals($firstTarget->id, Auth::id());
    }

    /** @test */
    public function test_can_leave_impersonation(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser();

        $this->get(route('admin.user.impersonate', $targetUser));
        $this->assertEquals($targetUser->id, Auth::id());

        $response = $this->get(route('admin.user.impersonate.leave'));

        $response->assertRedirect(route('admin.user.index'));

        $this->assertEquals($admin->id, Auth::id());

        $this->assertFalse(session()->has('impersonate'));
    }

    /** @test */
    public function test_leave_impersonation_without_active_session(): void
    {
        $admin = $this->actingAsUser(1);

        $response = $this->get(route('admin.user.impersonate.leave'));

        $response->assertRedirect(route('admin.user.index'));

        $this->assertEquals($admin->id, Auth::id());
    }
}