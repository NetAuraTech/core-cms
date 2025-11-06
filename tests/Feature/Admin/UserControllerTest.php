<?php

namespace Netauratech\CoreCms\Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Netauratech\CoreCms\Models\User;
use Netauratech\CoreCms\Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_guest_cannot_access_user_index(): void
    {
        $response = $this->get(route('admin.user.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function test_authenticated_user_can_view_users(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser([
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);

        $response = $this->get(route('admin.user.index'));

        $response->assertOk();
        $response->assertSee('testuser');
        $response->assertSee('test@example.com');
    }

    /** @test */
    public function test_it_shows_create_form(): void
    {
        $user = $this->actingAsUser(1);
        $response = $this->get(route('admin.user.create'));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.users.form');
    }

    /** @test */
    public function test_it_can_create_user_with_password(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.user.store'), [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'new_password' => 'password123',
            'new_password_confirmation' => 'password123',
            'role' => [],
        ]);

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
        ]);

        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue(Hash::check('password123', $newUser->password));
    }

    /** @test */
    public function test_it_can_create_user_with_roles(): void
    {
        $user = $this->actingAsUser(1);
        $role1 = Role::create(['name' => 'Editor']);
        $role2 = Role::create(['name' => 'Moderator']);

        $response = $this->post(route('admin.user.store'), [
            'username' => 'newuser',
            'email' => 'newuser@example.com',
            'new_password' => 'password123',
            'new_password_confirmation' => 'password123',
            'role' => [$role1->id, $role2->id],
        ]);

        $response->assertRedirect(route('admin.user.index'));

        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue($newUser->hasRole('Editor'));
        $this->assertTrue($newUser->hasRole('Moderator'));
    }

    /** @test */
    public function test_it_validates_required_fields(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.user.store'), [
            'username' => '',
            'email' => '',
        ]);

        $response->assertSessionHasErrors(['username', 'email']);
    }

    /** @test */
    public function test_it_validates_unique_email(): void
    {
        $user = $this->actingAsUser(1);
        $existingUser = $this->createUser(['email' => 'existing@example.com']);

        $response = $this->post(route('admin.user.store'), [
            'username' => 'newuser',
            'email' => 'existing@example.com',
            'new_password' => 'password123',
            'new_password_confirmation' => 'password123',
            'role' => [],
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_it_validates_unique_username(): void
    {
        $user = $this->actingAsUser(1);
        $existingUser = $this->createUser(['username' => 'existinguser']);

        $response = $this->post(route('admin.user.store'), [
            'username' => 'existinguser',
            'email' => 'new@example.com',
            'new_password' => 'password123',
            'new_password_confirmation' => 'password123',
            'role' => [],
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    /** @test */
    public function test_it_validates_password_confirmation(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.user.store'), [
            'username' => 'newuser',
            'email' => 'new@example.com',
            'new_password' => 'password123',
            'new_password_confirmation' => 'differentpassword',
            'role' => [],
        ]);

        $response->assertSessionHasErrors(['new_password']);
    }

    /** @test */
    public function test_it_validates_email_format(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('admin.user.store'), [
            'username' => 'newuser',
            'email' => 'not-a-valid-email',
            'new_password' => 'password123',
            'new_password_confirmation' => 'password123',
            'role' => [],
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_it_shows_edit_form(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser();

        $response = $this->get(route('admin.user.edit', $testUser));

        $response->assertOk();
        $response->assertViewIs('core-cms::admin.users.form');
        $response->assertViewHas('user', $testUser);
    }

    /** @test */
    public function test_it_can_update_user(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser([
            'username' => 'oldusername',
            'email' => 'old@example.com',
        ]);

        $response = $this->put(route('admin.user.update', $testUser), [
            'username' => 'newusername',
            'email' => 'new@example.com',
            'role' => [],
        ]);

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $testUser->id,
            'username' => 'newusername',
            'email' => 'new@example.com',
        ]);
    }

    /** @test */
    public function test_it_can_update_user_password(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser();

        $response = $this->put(route('admin.user.update', $testUser), [
            'username' => $testUser->username,
            'email' => $testUser->email,
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
            'role' => [],
        ]);

        $response->assertRedirect(route('admin.user.index'));

        $testUser->refresh();
        $this->assertTrue(Hash::check('newpassword123', $testUser->password));
    }

    /** @test */
    public function test_it_can_update_user_roles(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser();
        $oldRole = Role::create(['name' => 'Old Role']);
        $newRole = Role::create(['name' => 'New Role']);

        $testUser->assignRole($oldRole);

        $response = $this->put(route('admin.user.update', $testUser), [
            'username' => $testUser->username,
            'email' => $testUser->email,
            'role' => [$newRole->id],
        ]);

        $testUser->refresh();
        $this->assertFalse($testUser->hasRole('Old Role'));
        $this->assertTrue($testUser->hasRole('New Role'));
    }

    /** @test */
    public function test_update_validates_unique_email_except_current(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser(['email' => 'test@example.com']);
        $otherUser = $this->createUser(['email' => 'other@example.com']);

        $response = $this->put(route('admin.user.update', $testUser), [
            'username' => $testUser->username,
            'email' => 'other@example.com',
            'role' => [],
        ]);

        $response->assertSessionHasErrors(['email']);

        $response = $this->put(route('admin.user.update', $testUser), [
            'username' => $testUser->username,
            'email' => 'test@example.com',
            'role' => [],
        ]);

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionDoesntHaveErrors(['email']);
    }

    /** @test */
    public function test_update_validates_unique_username_except_current(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser(['username' => 'testuser']);
        $otherUser = $this->createUser(['username' => 'otheruser']);

        $response = $this->put(route('admin.user.update', $testUser), [
            'username' => 'otheruser',
            'email' => $testUser->email,
            'role' => [],
        ]);

        $response->assertSessionHasErrors(['username']);

        $response = $this->put(route('admin.user.update', $testUser), [
            'username' => 'testuser',
            'email' => $testUser->email,
            'role' => [],
        ]);

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionDoesntHaveErrors(['username']);
    }

    /** @test */
    public function test_it_can_delete_user(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser();

        $response = $this->delete(route('admin.user.destroy', $testUser));

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $testUser->id]);
    }

    /** @test */
    public function test_it_can_ban_user(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser(['status' => 1]);

        $response = $this->post(route('admin.user.ban', $testUser));

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionHas('success');

        $testUser->refresh();
        $this->assertEquals(0, $testUser->status);
    }

    /** @test */
    public function test_it_can_unban_user(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser(['status' => 0]);

        $response = $this->post(route('admin.user.unban', $testUser));

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionHas('success');

        $testUser->refresh();
        $this->assertEquals(1, $testUser->status);
    }

    /** @test */
    public function test_it_can_confirm_user_email(): void
    {
        $user = $this->actingAsUser(1);
        $testUser = $this->createUser(['email_verified_at' => null]);

        $response = $this->post(route('admin.user.confirm', $testUser));

        $response->assertRedirect(route('admin.user.index'));
        $response->assertSessionHas('success');

        $testUser->refresh();
        $this->assertNotNull($testUser->email_verified_at);
    }

    /** @test */
    public function test_users_are_paginated(): void
    {
        $user = $this->actingAsUser(1);

        for ($i = 1; $i <= 25; $i++) {
            $this->createUser([
                'username' => "user$i",
                'email' => "user$i@example.com",
            ]);
        }

        $response = $this->get(route('admin.user.index'));

        $response->assertOk();
        $response->assertViewHas('users');

        $users = $response->viewData('users');
        $this->assertEquals(20, $users->perPage());
        $this->assertGreaterThan(1, $users->lastPage());
    }

    /** @test */
    public function test_pagination_can_navigate_to_next_page(): void
    {
        $user = $this->actingAsUser(1);

        for ($i = 1; $i <= 25; $i++) {
            $this->createUser([
                'username' => "user$i",
                'email' => "user$i@example.com",
            ]);
        }

        $response = $this->get(route('admin.user.index', ['page' => 2]));

        $response->assertOk();
        $users = $response->viewData('users');
        $this->assertEquals(2, $users->currentPage());
    }

    /** @test */
    public function test_users_are_ordered_by_creation_date_desc(): void
    {
        $user = $this->actingAsUser(1);

        $oldUser = $this->createUser([
            'username' => 'olduser',
            'email' => 'old@example.com',
            'created_at' => now()->subDays(5),
        ]);

        $response = $this->get(route('admin.user.index'));

        $users = $response->viewData('users');

        $this->assertEquals($user->username, $users->first()->username);
    }

    /** @test */
    public function test_user_without_permission_cannot_access_user_management(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->get(route('admin.user.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function test_user_without_permission_cannot_create_users(): void
    {
        $regularUser = $this->createUser();
        $this->actingAs($regularUser);

        $response = $this->post(route('admin.user.store'), [
            'username' => 'newuser',
            'email' => 'new@example.com',
            'new_password' => 'password123',
            'new_password_confirmation' => 'password123',
            'role' => [],
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function test_banned_user_status_is_displayed(): void
    {
        $user = $this->actingAsUser(1);
        $bannedUser = $this->createUser([
            'username' => 'banneduser',
            'status' => 0,
        ]);

        $response = $this->get(route('admin.user.index'));

        $response->assertOk();
        $response->assertSee('banneduser');
    }

    /** @test */
    public function test_unverified_email_status_is_displayed(): void
    {
        $user = $this->actingAsUser(1);
        $unverifiedUser = $this->createUser([
            'username' => 'unverified',
            'email_verified_at' => null,
        ]);

        $response = $this->get(route('admin.user.index'));

        $response->assertOk();
        $response->assertSee('unverified');
    }
}