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
            'email' => $user->email, // Same email
        ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertEquals($verifiedAt->timestamp, $user->email_verified_at->timestamp);
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

        // 2 users: Admin user & this test user
        $this->assertDatabaseCount('users', 2);
    }

    /** @test */
  /*  public function test_user_can_clean_notifications(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->delete(route('profile.clean-notification'));

        $response->assertRedirect(route('profile.index'));
        $response->assertSessionHas('status', 'notification-deleted');
    }*/
}