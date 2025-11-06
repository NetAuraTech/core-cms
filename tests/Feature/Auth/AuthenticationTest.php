<?php

namespace Netauratech\CoreCms\Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Netauratech\CoreCms\Models\User;
use Netauratech\CoreCms\Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertViewIs('core-cms::auth.login');
    }

    /** @test */
    public function test_users_can_authenticate_with_valid_credentials(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'status' => 1,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_users_cannot_authenticate_with_invalid_email(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_login_requires_email(): void
    {
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function test_login_requires_password(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    /** @test */
    public function test_login_validates_email_format(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'not-an-email',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function test_users_can_logout(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /** @test */
    public function test_login_with_remember_me(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
            'remember' => true,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));

        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    /** @test */
    public function test_login_without_remember_me(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
            'remember' => false,
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function test_login_is_rate_limited_after_multiple_failed_attempts(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        $errors = session('errors');
        $this->assertStringContainsString('Too many', $errors->first('email'));
    }

    /** @test */
    public function test_rate_limiter_clears_after_successful_login(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        for ($i = 0; $i < 3; $i++) {
            $this->post(route('login'), [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        $this->post(route('logout'));

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
    }

    /** @test */
    public function test_banned_user_cannot_login(): void
    {
        $user = $this->createUser([
            'email' => 'banned@example.com',
            'password' => Hash::make('password'),
            'status' => 0,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'banned@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function test_session_is_regenerated_after_login(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $oldSessionId = session()->getId();

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $newSessionId = session()->getId();

        $this->assertNotEquals($oldSessionId, $newSessionId);
    }

    /** @test */
    public function test_authenticated_user_is_redirected_from_login_page(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('login'));

        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function test_logout_invalidates_session(): void
    {
        $user = $this->actingAsUser(1);

        session(['test_key' => 'test_value']);

        $response = $this->post(route('logout'));

        $this->assertNull(session('test_key'));
    }

    /** @test */
    public function test_logout_regenerates_csrf_token(): void
    {
        $user = $this->actingAsUser(1);

        $oldToken = csrf_token();

        $this->post(route('logout'));

        $newToken = csrf_token();

        $this->assertNotEquals($oldToken, $newToken);
    }

    /** @test */
    public function test_guest_is_redirected_to_login_when_accessing_protected_route(): void
    {
        $response = $this->get(route('profile.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function test_login_redirects_to_intended_url(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->get(route('profile.index'));

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('profile.index'));
    }

    /** @test */
    public function test_case_insensitive_email_login(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => 'TEST@EXAMPLE.COM',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }
}