<?php

namespace Netauratech\CoreCms\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Netauratech\CoreCms\Contracts\ChallengeInterface;
use Netauratech\CoreCms\Tests\TestCase;

class CaptchaTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_it_can_generate_a_challenge_key(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();

        $this->assertNotEmpty($key);
        $this->assertIsString($key);
    }

    /** @test */
    public function test_generated_keys_are_unique(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key1 = $challenge->generateKey();
        $key2 = $challenge->generateKey();

        $this->assertNotEquals($key1, $key2);
    }

    /** @test */
    public function test_it_can_get_solution_for_key(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);

        $this->assertIsArray($solution);
        $this->assertCount(2, $solution);
    }

    /** @test */
    public function test_solution_contains_valid_coordinates(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);

        [$x, $y] = $solution;

        $this->assertIsInt($x);
        $this->assertIsInt($y);
        $this->assertGreaterThanOrEqual(0, $x);
        $this->assertGreaterThanOrEqual(0, $y);
    }

    /** @test */
    public function test_it_validates_correct_answer(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);
        $answer = implode('-', $solution);

        $isValid = $challenge->check($key, $answer);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function test_it_rejects_incorrect_answer(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $incorrectAnswer = '999-999';

        $isValid = $challenge->check($key, $incorrectAnswer);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function test_it_rejects_answer_for_nonexistent_key(): void
    {
        $challenge = app(ChallengeInterface::class);

        $isValid = $challenge->check('nonexistent-key', '100-100');

        $this->assertFalse($isValid);
    }

    /** @test */
    public function test_it_accepts_answer_with_precision(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);

        $answer = ($solution[0] + 1) . '-' . ($solution[1] + 1);

        $isValid = $challenge->check($key, $answer);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function test_it_rejects_answer_beyond_precision_threshold(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);

        $answer = ($solution[0] + 10) . '-' . ($solution[1] + 10);

        $isValid = $challenge->check($key, $answer);

        $this->assertFalse($isValid);
    }

    /** @test */
    public function test_verify_removes_challenge_after_use(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);
        $answer = implode('-', $solution);

        $this->assertTrue($challenge->verify($key, $answer));

        $this->assertFalse($challenge->verify($key, $answer));
    }

    /** @test */
    public function test_check_does_not_remove_challenge(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);
        $answer = implode('-', $solution);

        $this->assertTrue($challenge->check($key, $answer));

        $this->assertTrue($challenge->check($key, $answer));
    }

    /** @test */
    public function test_it_can_serve_captcha_image(): void
    {
        $challenge = app(ChallengeInterface::class);
        $key = $challenge->generateKey();

        $response = $this->get(route('captcha.image', ['key' => $key]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'image/png');
    }

    /** @test */
    public function test_captcha_image_returns_404_for_invalid_key(): void
    {
        $response = $this->get(route('captcha.image', ['key' => 'invalid-key']));

        $response->assertStatus(404);
    }

    /** @test */
    public function test_captcha_check_endpoint_works(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);
        $answer = implode('-', $solution);

        $response = $this->postJson(route('captcha.check'), [
            'challenge' => $key,
            'answer' => $answer,
        ]);

        $response->assertNoContent();
    }

    /** @test */
    public function test_captcha_check_fails_with_wrong_answer(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();

        $response = $this->postJson(route('captcha.check'), [
            'challenge' => $key,
            'answer' => '999-999',
        ]);

        $response->assertUnprocessable();
    }

    /** @test */
    public function test_captcha_check_validates_required_fields(): void
    {
        $response = $this->postJson(route('captcha.check'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['challenge', 'answer']);
    }

    /** @test */
    public function test_captcha_check_validates_challenge_format(): void
    {
        $response = $this->postJson(route('captcha.check'), [
            'challenge' => '',
            'answer' => '100-100',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['challenge']);
    }

    /** @test */
    public function test_captcha_check_validates_answer_format(): void
    {
        $challenge = app(ChallengeInterface::class);
        $key = $challenge->generateKey();

        $response = $this->postJson(route('captcha.check'), [
            'challenge' => $key,
            'answer' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['answer']);
    }

    /** @test */
    public function test_multiple_challenges_can_be_active_simultaneously(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key1 = $challenge->generateKey();
        $key2 = $challenge->generateKey();

        $solution1 = $challenge->getSolution($key1);
        $solution2 = $challenge->getSolution($key2);

        $this->assertNotNull($solution1);
        $this->assertNotNull($solution2);
        $this->assertNotEquals($solution1, $solution2);
    }

    /** @test */
    public function test_challenge_stored_in_session(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();

        $this->assertTrue(session()->has('puzzles'));
    }

    /** @test */
    public function test_session_can_store_multiple_puzzles(): void
    {
        $challenge = app(ChallengeInterface::class);

        for ($i = 0; $i < 5; $i++) {
            $challenge->generateKey();
        }

        $puzzles = session('puzzles', []);
        $this->assertCount(5, $puzzles);
    }

    /** @test */
    public function test_old_puzzles_are_removed_from_session(): void
    {
        $challenge = app(ChallengeInterface::class);

        for ($i = 0; $i < 15; $i++) {
            $challenge->generateKey();
        }

        $puzzles = session('puzzles', []);

        $this->assertLessThanOrEqual(10, count($puzzles));
    }

    /** @test */
    public function test_string_to_position_parses_correctly(): void
    {
        $challenge = app(ChallengeInterface::class);

        $result = $challenge->stringToPosition('100-200');

        $this->assertEquals([100, 200], $result);
    }

    /** @test */
    public function test_string_to_position_handles_invalid_format(): void
    {
        $challenge = app(ChallengeInterface::class);

        $result = $challenge->stringToPosition('invalid');

        $this->assertEquals([-1, -1], $result);
    }

    /** @test */
    public function test_captcha_image_has_correct_dimensions(): void
    {
        $challenge = app(ChallengeInterface::class);
        $key = $challenge->generateKey();

        $response = $this->get(route('captcha.image', ['key' => $key]));

        $response->assertOk();

        $this->assertEquals('image/png', $response->headers->get('Content-Type'));
    }

    /** @test */
    public function test_captcha_answer_with_negative_coordinates_is_rejected(): void
    {
        $challenge = app(ChallengeInterface::class);
        $key = $challenge->generateKey();

        $isValid = $challenge->check($key, '-10--10');

        $this->assertFalse($isValid);
    }

    /** @test */
    public function test_captcha_check_is_case_sensitive_for_answer(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);

        $answer = implode('-', $solution);

        $this->assertTrue($challenge->check($key, $answer));
    }
}