<?php

namespace Netauratech\CoreCms\Tests\Unit;

use Carbon\Carbon;
use Netauratech\CoreCms\Tests\TestCase;

class HelperTest extends TestCase
{
    /** @test */
    public function test_icon_helper_generates_svg(): void
    {
        $result = icon('home');

        $this->assertStringContainsString('<svg', $result);
        $this->assertStringContainsString('icon-home', $result);
        $this->assertStringContainsString('use xlink:href', $result);
    }

    /** @test */
    public function test_icon_helper_with_size(): void
    {
        $result = icon('home', 'large');

        $this->assertStringContainsString('large', $result);
    }

    /** @test */
    public function test_menu_active_returns_active_for_matching_path(): void
    {
        $this->get('/test-path');

        $result = menu_active(url('/test-path'));

        $this->assertEquals('aria-current=page', $result);
    }

    /** @test */
    public function test_menu_active_returns_empty_for_non_matching_path(): void
    {
        $this->get('/current-path');

        $result = menu_active(url('/other-path'));

        $this->assertEquals('', $result);
    }

    /** @test */
    public function test_generate_challenge_returns_string(): void
    {
        $key = generate_challenge();

        $this->assertIsString($key);
        $this->assertNotEmpty($key);
    }

    /** @test */
    public function test_ago_helper_generates_time_element(): void
    {
        $date = Carbon::now()->subHours(2);

        $result = ago($date, 'Created');

        $this->assertStringContainsString('<time-ago', $result);
        $this->assertStringContainsString('time="' . $date->getTimestamp() . '"', $result);
        $this->assertStringContainsString('prefix="Created"', $result);
    }

    /** @test */
    public function test_shortened_exception_shortens_long_messages(): void
    {
        $longException = str_repeat('A', 300);

        $result = shortened_exception($longException);

        $this->assertLessThanOrEqual(203, strlen($result));
        $this->assertStringEndsWith('...', $result);
    }

    /** @test */
    public function test_shortened_exception_extracts_exception_name(): void
    {
        $exception = "InvalidArgumentException: Something went wrong\nStack trace...";

        $result = shortened_exception($exception);

        $this->assertStringContainsString('InvalidArgumentException', $result);
        $this->assertStringNotContainsString('Stack trace', $result);
    }

    /** @test */
    public function test_generate_name_variants_creates_variations(): void
    {
        $variants = generateNameVariants('TestSite');

        $this->assertContains('TestSite', $variants);
        $this->assertContains('testsite', $variants);
        $this->assertContains('Testsite', $variants);
    }

    /** @test */
    public function test_generate_name_variants_handles_camel_case(): void
    {
        $variants = generateNameVariants('MyCoolSite');

        $this->assertContains('My Cool Site', $variants);
        $this->assertContains('my cool site', $variants);
    }
}