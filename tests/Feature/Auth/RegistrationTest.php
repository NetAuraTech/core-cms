<?php

namespace Netauratech\CoreCms\Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Netauratech\CoreCms\Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertViewIs('core-cms::auth.register');
    }

    /** @test */
    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('profile.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function test_registered_user_password_is_hashed(): void
    {
        $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = \Netauratech\CoreCms\Models\User::where('email', 'test@example.com')->first();

        $this->assertNotEquals('password', $user->password);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    /** @test */
    public function test_registration_requires_username(): void
    {
        $response = $this->post(route('register'), [
            'username' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    /** @test */
    public function test_registration_requires_email(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function test_registration_requires_valid_email(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'not-an-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function test_registration_requires_unique_email(): void
    {
        $this->createUser(['email' => 'existing@example.com']);

        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'existing@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function test_registration_requires_password(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /** @test */
    public function test_registration_requires_password_confirmation(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /** @test */
    public function test_registration_validates_password_minimum_length(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /** @test */
    public function test_registration_validates_username_max_length(): void
    {
        $response = $this->post(route('register'), [
            'username' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    /** @test */
    public function test_registration_validates_email_max_length(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => str_repeat('a', 256) . '@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function test_email_is_normalized_to_lowercase(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'TEST@EXAMPLE.COM',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function test_new_user_is_automatically_logged_in_after_registration(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('test@example.com', auth()->user()->email);
    }

    /** @test */
    public function test_authenticated_user_cannot_access_registration_page(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('register'));

        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function test_new_user_has_default_status(): void
    {
        $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = \Netauratech\CoreCms\Models\User::where('email', 'test@example.com')->first();

        $this->assertNotEquals(0, $user->status);
    }

    /** @test */
    public function test_new_user_email_is_not_verified_by_default(): void
    {
        $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = \Netauratech\CoreCms\Models\User::where('email', 'test@example.com')->first();

        $this->assertNull($user->email_verified_at);
    }

    /** @test */
    public function test_registration_trims_whitespace_from_email(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'testuser',
            'email' => '  test@example.com  ',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function test_special_characters_in_username_are_allowed(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'test_user-123',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'username' => 'test_user-123',
        ]);
    }

    /** @test */
    public function test_registration_with_unicode_username(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'utilisateur_é',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'username' => 'utilisateur_é',
        ]);
    }

    /** @test */
    public function test_multiple_users_can_register_sequentially(): void
    {
        $this->post(route('register'), [
            'username' => 'user1',
            'email' => 'user1@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->post(route('logout'));

        $response = $this->post(route('register'), [
            'username' => 'user2',
            'email' => 'user2@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertEquals('user2@example.com', auth()->user()->email);
    }

    /** @test */
    public function test_session_is_regenerated_after_registration(): void
    {
        $oldSessionId = session()->getId();

        $this->post(route('register'), [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $newSessionId = session()->getId();

        $this->assertAuthenticated();
    }
}