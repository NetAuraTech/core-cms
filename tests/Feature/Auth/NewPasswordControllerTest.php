<?php

namespace Netauratech\CoreCms\Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Netauratech\CoreCms\Models\User;
use Netauratech\CoreCms\Tests\TestCase;

class NewPasswordControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_reset_password_screen_can_be_rendered(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->get(route('password.reset', ['token' => $token]));

        $response->assertOk();
        $response->assertViewIs('core-cms::auth.reset-password');
        $response->assertViewHas('request');
    }

    /** @test */
    public function test_password_can_be_reset_with_valid_token(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'password-reseted');

        $user->refresh();
        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    /** @test */
    public function test_password_reset_requires_token(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);

        $response = $this->post(route('password.store'), [
            'token' => '',
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('token');
    }

    /** @test */
    public function test_password_reset_requires_email(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => '',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_password_reset_validates_email_format(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'not-an-email',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_password_reset_requires_password(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_password_reset_requires_password_confirmation(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_password_reset_validates_password_minimum_length(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /** @test */
    public function test_password_cannot_be_reset_with_invalid_token(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);

        $response = $this->post(route('password.store'), [
            'token' => 'invalid-token',
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_password_cannot_be_reset_with_wrong_email(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'wrong@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_password_reset_token_is_deleted_after_use(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function test_remember_token_is_regenerated_after_reset(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'remember_token' => 'old-token',
        ]);

        $token = Password::createToken($user);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $user->refresh();
        $this->assertNotEquals('old-token', $user->remember_token);
    }

    /** @test */
    public function test_password_reset_event_is_dispatched(): void
    {
        \Event::fake();

        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        \Event::assertDispatched(\Illuminate\Auth\Events\PasswordReset::class);
    }

    /** @test */
    public function test_password_reset_redirects_to_login_with_status(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'password-reseted');
    }

    /** @test */
    public function test_user_can_login_with_new_password_after_reset(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $loginResponse = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'new-password',
        ]);

        $this->assertAuthenticated();
    }

    /** @test */
    public function test_user_cannot_login_with_old_password_after_reset(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $token = Password::createToken($user);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $loginResponse = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'old-password',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function test_banned_user_can_reset_password(): void
    {
        $user = $this->createUser([
            'email' => 'banned@example.com',
            'password' => Hash::make('old-password'),
            'status' => 0,
        ]);

        $token = Password::createToken($user);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'banned@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('new-password', $user->password));
    }

    /** @test */
    public function test_token_cannot_be_reused(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);
        $token = Password::createToken($user);

        $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => 'test@example.com',
            'password' => 'another-password',
            'password_confirmation' => 'another-password',
        ]);

        $response->assertSessionHasErrors('email');
    }
}