<?php

namespace Netauratech\CoreCms\Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
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
}