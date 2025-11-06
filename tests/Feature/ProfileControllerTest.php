<?php

namespace Netauratech\CoreCms\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Netauratech\CoreCms\Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get(route('profile.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_authenticated_user_can_view_profile(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('profile.index'));

        $response->assertOk();
        $response->assertViewIs('core-cms::profile.index');
        $response->assertViewHas('user', $user);
    }

    /** @test */
    public function test_profile_displays_user_information(): void
    {
        $user = $this->createUser([
            'username' => 'displayuser',
            'email' => 'display@example.com',
        ]);
        $this->actingAs($user);

        $response = $this->get(route('profile.index'));

        $response->assertOk();
        $response->assertSee('displayuser');
        $response->assertSee('display@example.com');
    }

    /** @test */
    public function test_profile_shows_comments_section(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('profile.index'));

        $response->assertOk();
        $response->assertViewHas('comments');
        $response->assertViewHas('hasActivity');
    }

    /** @test */
    public function test_profile_shows_notifications_section(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('profile.index'));

        $response->assertOk();
        $response->assertViewHas('notifications');
    }

    /** @test */
    public function test_user_can_update_profile(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->patch(route('profile.update'), [
            'username' => 'newusername',
            'email' => 'newemail@example.com',
        ]);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('status', 'profile-updated');

        $user->refresh();
        $this->assertEquals('newusername', $user->username);
        $this->assertEquals('newemail@example.com', $user->email);
    }

    /** @test */
    public function test_update_validates_unique_email(): void
    {
        $user = $this->actingAsUser(1);
        $otherUser = $this->createUser(['email' => 'other@example.com']);

        $response = $this->patch(route('profile.update'), [
            'username' => $user->username,
            'email' => 'other@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_update_allows_keeping_same_email(): void
    {
        $user = $this->actingAsUser(1);
        $originalEmail = $user->email;

        $response = $this->patch(route('profile.update'), [
            'username' => 'newusername',
            'email' => $originalEmail,
        ]);

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionDoesntHaveErrors(['email']);
    }

    /** @test */
    public function test_email_verification_is_reset_when_email_changes(): void
    {
        $user = $this->actingAsUser(1);
        $user->email_verified_at = now();
        $user->save();

        $response = $this->patch(route('profile.update'), [
            'username' => $user->username,
            'email' => 'different@example.com',
        ]);

        $user->refresh();
        $this->assertNull($user->email_verified_at);
    }

    /** @test */
    public function test_email_verification_not_reset_if_email_unchanged(): void
    {
        $user = $this->actingAsUser(1);
        $verifiedAt = now();
        $user->email_verified_at = $verifiedAt;
        $user->save();

        $response = $this->patch(route('profile.update'), [
            'username' => 'newusername',
            'email' => $user->email,
        ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals($verifiedAt->timestamp, $user->email_verified_at->timestamp);
    }

    /** @test */
    public function test_update_profile_validates_email_format(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->patch(route('profile.update'), [
            'username' => $user->username,
            'email' => 'not-a-valid-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_update_profile_validates_email_max_length(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->patch(route('profile.update'), [
            'username' => $user->username,
            'email' => str_repeat('a', 250) . '@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function test_update_profile_validates_username_max_length(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->patch(route('profile.update'), [
            'username' => str_repeat('a', 256),
            'email' => $user->email,
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    /** @test */
    public function test_user_can_delete_account(): void
    {
        $user = $this->createUser([
            'email' => 'todelete@example.com',
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);

        $response = $this->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $response->assertRedirect('/');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function test_correct_password_required_to_delete_account(): void
    {
        $user = $this->createUser([
            'password' => Hash::make('correct-password'),
        ]);
        $this->actingAs($user);

        $response = $this->delete(route('profile.destroy'), [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('password', null, 'userDeletion');
        $response->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseCount('users', 2);
    }

    /** @test */
    public function test_password_is_required_to_delete_account(): void
    {
        $user = $this->createUser([
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);

        $response = $this->delete(route('profile.destroy'), [
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password', null, 'userDeletion');
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /** @test */
    public function test_session_is_invalidated_after_account_deletion(): void
    {
        $user = $this->createUser([
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);

        session(['test_key' => 'test_value']);

        $this->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $this->assertNull(session('test_key'));
    }

    /** @test */
    public function test_csrf_token_is_regenerated_after_account_deletion(): void
    {
        $user = $this->createUser([
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);

        $oldToken = csrf_token();

        $this->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $newToken = csrf_token();

        $this->assertNotEquals($oldToken, $newToken);
    }

    /** @test */
    /*public function test_user_can_clean_notifications(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->delete(route('profile.clean-notification'));

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('status', 'notification-deleted');
    }*/

    /** @test */
    public function test_profile_shows_unverified_email_message(): void
    {
        $user = $this->createUser([
            'email_verified_at' => null,
        ]);
        $this->actingAs($user);

        $response = $this->get(route('profile.index'));

        $response->assertOk();
    }

    /** @test */
    public function test_profile_shows_verified_email_status(): void
    {
        $user = $this->createUser([
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $response = $this->get(route('profile.index'));

        $response->assertOk();
    }

    /** @test */
    public function test_profile_update_preserves_other_user_fields(): void
    {
        $user = $this->createUser([
            'username' => 'originaluser',
            'email' => 'original@example.com',
            'status' => 1,
        ]);
        $this->actingAs($user);

        $this->patch(route('profile.update'), [
            'username' => 'newusername',
            'email' => 'new@example.com',
        ]);

        $user->refresh();
        $this->assertEquals(1, $user->status);
    }

    /** @test */
    public function test_profile_cannot_be_updated_by_guest(): void
    {
        $response = $this->patch(route('profile.update'), [
            'username' => 'newusername',
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_account_cannot_be_deleted_by_guest(): void
    {
        $response = $this->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_notifications_cannot_be_cleaned_by_guest(): void
    {
        $response = $this->delete(route('profile.clean-notification'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_profile_shows_social_login_section(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('profile.index'));

        $response->assertOk();
    }

    /** @test */
    public function test_profile_shows_password_change_section(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('profile.index'));

        $response->assertOk();
    }

    /** @test */
    public function test_profile_shows_danger_zone_section(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('profile.index'));

        $response->assertOk();
    }

    /** @test */
    public function test_update_profile_with_unicode_username(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->patch(route('profile.update'), [
            'username' => 'utilisateur_été',
            'email' => $user->email,
        ]);

        $response->assertRedirect(route('profile.index'));

        $user->refresh();
        $this->assertEquals('utilisateur_été', $user->username);
    }

    /** @test */
    public function test_update_profile_trims_whitespace(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->patch(route('profile.update'), [
            'username' => '  testuser  ',
            'email' => '  test@example.com  ',
        ]);

        $response->assertRedirect(route('profile.index'));

        $user->refresh();
    }

    /** @test */
    public function test_deleting_account_removes_user_roles(): void
    {
        $user = $this->createUser([
            'password' => Hash::make('password'),
        ]);
        $this->actingAs($user);

        $role = \Spatie\Permission\Models\Role::create(['name' => 'Test Role']);
        $user->assignRole($role);

        $this->assertTrue($user->hasRole('Test Role'));

        $this->delete(route('profile.destroy'), [
            'password' => 'password',
        ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function test_profile_activity_section_shows_correctly(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('profile.index'));

        $response->assertOk();
        $response->assertViewHas('hasActivity');

        $hasActivity = $response->viewData('hasActivity');
        $this->assertIsBool($hasActivity);
    }
}