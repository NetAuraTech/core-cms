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
    public function test_impersonation_stores_original_user_id_in_session(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser();

        $this->get(route('admin.user.impersonate', $targetUser));

        $this->assertTrue(session()->has('impersonate'));
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

        $this->assertEquals($firstTarget->id, Auth::id());
    }

    /** @test */
    public function test_admin_with_role_can_impersonate_multiple_times(): void
    {
        $admin = $this->actingAsUser(1);
        $firstTarget = $this->createUser(['username' => 'first']);
        $secondTarget = $this->createUser(['username' => 'second']);

        $this->get(route('admin.user.impersonate', $firstTarget));
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

    /** @test */
    public function test_leave_impersonation_clears_session(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser();

        $this->get(route('admin.user.impersonate', $targetUser));
        $this->assertTrue(session()->has('impersonate'));

        $this->get(route('admin.user.impersonate.leave'));

        $this->assertFalse(session()->has('impersonate'));
    }

    /** @test */
    public function test_user_without_permission_cannot_impersonate(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $targetUser = $this->createUser();

        $response = $this->get(route('admin.user.impersonate', $targetUser));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_impersonated_user_can_access_their_profile(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser([
            'username' => 'targetuser',
            'email' => 'target@example.com',
        ]);

        $this->get(route('admin.user.impersonate', $targetUser));

        $response = $this->get(route('profile.index'));

        $response->assertOk();
        $response->assertSee('targetuser');
    }

    /** @test */
    public function test_impersonated_user_has_their_permissions(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser();

        $role = Role::create(['name' => 'Limited Role']);
        $targetUser->assignRole($role);

        $this->get(route('admin.user.impersonate', $targetUser));

        $this->assertTrue(Auth::user()->hasRole('Limited Role'));
        $this->assertFalse(Auth::user()->hasRole('Super Administrator'));
    }

    /** @test */
    public function test_admin_cannot_impersonate_themselves(): void
    {
        $admin = $this->actingAsUser(1);

        $response = $this->get(route('admin.user.impersonate', $admin));

        $response->assertRedirect();
    }

    /** @test */
    public function test_impersonation_persists_across_requests(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser();

        $this->get(route('admin.user.impersonate', $targetUser));

        $response = $this->get(route('profile.index'));

        $response->assertOk();
        $this->assertEquals($targetUser->id, Auth::id());
    }

    /** @test */
    public function test_leaving_impersonation_redirects_to_user_index(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser();

        $this->get(route('admin.user.impersonate', $targetUser));

        $response = $this->get(route('admin.user.impersonate.leave'));

        $response->assertRedirect(route('admin.user.index'));
    }

    /** @test */
    public function test_original_admin_retains_their_permissions_after_leaving(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser();

        $this->get(route('admin.user.impersonate', $targetUser));
        $this->get(route('admin.user.impersonate.leave'));

        $this->assertTrue(Auth::user()->hasRole('Super Administrator'));
    }

    /** @test */
    public function test_impersonate_route_requires_user_id(): void
    {
        $admin = $this->actingAsUser(1);

        $response = $this->get('/admin/user/999999/impersonate');

        $response->assertStatus(404);
    }

    /** @test */
    public function test_banned_user_can_still_be_impersonated(): void
    {
        $admin = $this->actingAsUser(1);
        $bannedUser = $this->createUser(['status' => 0]);

        $response = $this->get(route('admin.user.impersonate', $bannedUser));

        $response->assertRedirect(route('profile.index'));
        $this->assertEquals($bannedUser->id, Auth::id());
    }

    /** @test */
    public function test_unverified_user_can_be_impersonated(): void
    {
        $admin = $this->actingAsUser(1);
        $unverifiedUser = $this->createUser(['email_verified_at' => null]);

        $response = $this->get(route('admin.user.impersonate', $unverifiedUser));

        $response->assertRedirect(route('profile.index'));
        $this->assertEquals($unverifiedUser->id, Auth::id());
    }

    /** @test */
    public function test_impersonation_works_with_remember_token(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser();
        $targetUser->remember_token = 'test-token';
        $targetUser->save();

        $response = $this->get(route('admin.user.impersonate', $targetUser));

        $response->assertRedirect(route('profile.index'));
        $this->assertEquals($targetUser->id, Auth::id());
    }

    /** @test */
    public function test_multiple_admins_cannot_interfere_with_each_others_impersonation(): void
    {
        $admin1 = $this->actingAsUser(1);
        $targetUser1 = $this->createUser(['username' => 'target1']);

        $this->get(route('admin.user.impersonate', $targetUser1));
        $session1Id = session()->getId();

        $this->get(route('admin.user.impersonate.leave'));

        $admin2 = $this->createUser(['username' => 'admin2']);
        $admin2->assignRole(Role::find(1));
        $this->actingAs($admin2);

        $targetUser2 = $this->createUser(['username' => 'target2']);

        $this->get(route('admin.user.impersonate', $targetUser2));

        $this->assertEquals($targetUser2->id, Auth::id());
    }

    /** @test */
    public function test_impersonation_session_data_is_isolated(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser();

        session(['admin_data' => 'admin_value']);

        $this->get(route('admin.user.impersonate', $targetUser));

        $this->assertEquals('admin_value', session('admin_data'));
    }

    /** @test */
    public function test_leaving_impersonation_preserves_admin_session_data(): void
    {
        $admin = $this->actingAsUser(1);
        $targetUser = $this->createUser();

        session(['admin_data' => 'admin_value']);

        $this->get(route('admin.user.impersonate', $targetUser));
        $this->get(route('admin.user.impersonate.leave'));

        $this->assertEquals('admin_value', session('admin_data'));
    }
}