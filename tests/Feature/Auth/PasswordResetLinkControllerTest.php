<?php

namespace Netauratech\CoreCms\Tests\Feature\Auth;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Netauratech\CoreCms\Models\User;
use Netauratech\CoreCms\Notifications\ResetPasswordNotification;
use Netauratech\CoreCms\Tests\TestCase;

class PasswordResetLinkControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertOk();
        $response->assertViewIs('core-cms::auth.forgot-password');
    }

    /** @test */
    public function test_authenticated_user_is_redirected_from_reset_link_page(): void
    {
        $user = $this->actingAsUser(1);

        $response = $this->get(route('password.request'));

        $response->assertRedirect(route('home'));
    }

    /** @test */
    public function test_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = $this->createUser(['email' => 'test@example.com']);

        $response = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHas('status', 'verification-link-instruction');
    }

    /** @test */
    public function test_reset_link_requires_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => '',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_reset_link_validates_email_format(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_reset_link_for_nonexistent_email(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_reset_link_is_throttled_after_first_request(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);

        $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response2 = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response2->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_reset_link_notification_is_sent(): void
    {
        Notification::fake();

        $user = $this->createUser(['email' => 'test@example.com']);

        $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class
        );
    }

    /** @test */
    public function test_reset_link_with_trimmed_email(): void
    {
        Notification::fake();

        $user = $this->createUser(['email' => 'test@example.com']);

        $response = $this->post(route('password.email'), [
            'email' => '  test@example.com  ',
        ]);

        $response->assertSessionHas('status');
    }

    /** @test */
    public function test_reset_link_with_uppercase_email(): void
    {
        Notification::fake();

        $user = $this->createUser(['email' => 'test@example.com']);

        $response = $this->post(route('password.email'), [
            'email' => 'TEST@EXAMPLE.COM',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_multiple_users_can_request_reset_links(): void
    {
        Notification::fake();

        $user1 = $this->createUser(['email' => 'user1@example.com']);
        $user2 = $this->createUser(['email' => 'user2@example.com']);

        $this->post(route('password.email'), ['email' => 'user1@example.com']);
        $this->post(route('password.email'), ['email' => 'user2@example.com']);

        Notification::assertSentTo($user1, ResetPasswordNotification::class);
        Notification::assertSentTo($user2, ResetPasswordNotification::class);
    }

    /** @test */
    public function test_reset_link_email_is_normalized(): void
    {
        Notification::fake();

        $user = $this->createUser(['email' => 'test@example.com']);

        $response = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response->assertSessionHas('status');
    }

    /** @test */
    public function test_banned_user_can_still_request_reset_link(): void
    {
        Notification::fake();

        $user = $this->createUser([
            'email' => 'banned@example.com',
            'status' => 0,
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'banned@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /** @test */
    public function test_unverified_user_can_request_reset_link(): void
    {
        Notification::fake();

        $user = $this->createUser([
            'email' => 'unverified@example.com',
            'email_verified_at' => null,
        ]);

        $response = $this->post(route('password.email'), [
            'email' => 'unverified@example.com',
        ]);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /** @test */
    public function test_reset_link_request_creates_token(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);

        $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function test_rate_limiting_prevents_spam(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);

       $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response2 = $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $response2->assertSessionHasErrors('email');
        $errors = session('errors');
        $this->assertStringContainsString('wait', strtolower($errors->first('email')));
    }

    /** @test */
    public function test_token_is_hashed_in_database(): void
    {
        $user = $this->createUser(['email' => 'test@example.com']);

        $this->post(route('password.email'), [
            'email' => 'test@example.com',
        ]);

        $tokenRecord = \DB::table('password_reset_tokens')
            ->where('email', 'test@example.com')
            ->first();

        $this->assertNotNull($tokenRecord);
        $this->assertStringStartsWith('$2y$', $tokenRecord->token);
    }

    /** @test */
    public function test_reset_link_validates_email_max_length(): void
    {
        $response = $this->post(route('password.email'), [
            'email' => str_repeat('a', 250) . '@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }
}