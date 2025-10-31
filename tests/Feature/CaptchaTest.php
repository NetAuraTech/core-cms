<?php

namespace Netauratech\CoreCms\Tests\Feature;

use Netauratech\CoreCms\Contracts\ChallengeInterface;
use Netauratech\CoreCms\Tests\TestCase;

class CaptchaTest extends TestCase
{
    /** @test */
    public function test_it_can_generate_a_challenge_key(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();

        $this->assertNotEmpty($key);
        $this->assertIsString($key);
    }

    /** @test */
    public function test_it_can_get_solution_for_key(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);

        $this->assertIsArray($solution);
        $this->assertCount(2, $solution); // [x, y]
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
    public function test_it_accepts_answer_with_precision(): void
    {
        $challenge = app(ChallengeInterface::class);

        $key = $challenge->generateKey();
        $solution = $challenge->getSolution($key);

        // Ajoute une petite différence dans la précision autorisée
        $answer = ($solution[0] + 1) . '-' . ($solution[1] + 1);

        $isValid = $challenge->check($key, $answer);

        $this->assertTrue($isValid);
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
}