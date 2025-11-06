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
        $this->assertStringContainsString('icon-home', $result);
    }

    /** @test */
    public function test_icon_helper_without_size(): void
    {
        $result = icon('user');

        $this->assertStringContainsString('<svg', $result);
        $this->assertStringContainsString('icon-user', $result);
    }

    /** @test */
    public function test_icon_helper_with_different_icons(): void
    {
        $icons = ['home', 'user', 'settings', 'dashboard'];

        foreach ($icons as $iconName) {
            $result = icon($iconName);
            $this->assertStringContainsString("icon-{$iconName}", $result);
        }
    }

    /** @test */
    public function test_icon_helper_path_is_correct(): void
    {
        $result = icon('test');

        $this->assertStringContainsString('/vendor/core-cms/sprite.svg#test', $result);
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
    public function test_menu_active_is_case_sensitive(): void
    {
        $this->get('/Test-Path');

        $result = menu_active(url('/test-path'));

        $this->assertEquals('', $result);
    }

    /** @test */
    public function test_menu_active_handles_query_strings(): void
    {
        $this->get('/path?param=value');

        $result = menu_active(url('/path'));

        $this->assertEquals('aria-current=page', $result);
    }

    /** @test */
    public function test_generate_challenge_returns_string(): void
    {
        $key = generate_challenge();

        $this->assertIsString($key);
        $this->assertNotEmpty($key);
    }

    /** @test */
    public function test_generate_challenge_returns_unique_keys(): void
    {
        $key1 = generate_challenge();
        $key2 = generate_challenge();

        $this->assertNotEquals($key1, $key2);
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
    public function test_ago_helper_without_prefix(): void
    {
        $date = Carbon::now()->subHours(2);

        $result = ago($date);

        $this->assertStringContainsString('<time-ago', $result);
        $this->assertStringContainsString('time="' . $date->getTimestamp() . '"', $result);
        $this->assertStringNotContainsString('prefix=', $result);
    }

    /** @test */
    public function test_ago_helper_with_empty_prefix(): void
    {
        $date = Carbon::now()->subHours(2);

        $result = ago($date, '');

        $this->assertStringContainsString('<time-ago', $result);
        $this->assertStringNotContainsString('prefix=', $result);
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
    public function test_shortened_exception_keeps_short_messages(): void
    {
        $shortException = 'Short error message';

        $result = shortened_exception($shortException);

        $this->assertEquals($shortException, $result);
        $this->assertStringNotContainsString('...', $result);
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
    public function test_shortened_exception_handles_different_exception_types(): void
    {
        $exceptions = [
            "RuntimeException: Error occurred",
            "LogicException: Invalid logic",
            "BadMethodCallException: Method not found",
        ];

        foreach ($exceptions as $exception) {
            $result = shortened_exception($exception);
            $this->assertStringContainsString('Exception', $result);
        }
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

    /** @test */
    public function test_generate_name_variants_handles_single_word(): void
    {
        $variants = generateNameVariants('Site');

        $this->assertContains('Site', $variants);
        $this->assertContains('site', $variants);
    }

    /** @test */
    public function test_generate_name_variants_removes_duplicates(): void
    {
        $variants = generateNameVariants('test');

        $this->assertEquals(count($variants), count(array_unique($variants)));
    }

    /** @test */
    public function test_generate_name_variants_handles_spaces(): void
    {
        $variants = generateNameVariants('My Site Name');

        $this->assertContains('My Site Name', $variants);
        $this->assertContains('my site name', $variants);
    }

    /** @test */
    public function test_generate_name_variants_handles_special_characters(): void
    {
        $variants = generateNameVariants('Test-Site_Name');

        $this->assertIsArray($variants);
        $this->assertNotEmpty($variants);
    }

    /** @test */
    public function test_image_url_helper_returns_string(): void
    {
        $this->assertTrue(function_exists('image_url'));
    }

    /** @test */
    public function test_image_tag_helper_returns_string(): void
    {
        $this->assertTrue(function_exists('image_tag'));
    }

    /** @test */
    public function test_all_helpers_are_available(): void
    {
        $helpers = [
            'icon',
            'menu_active',
            'generateNameVariants',
            'image_url',
            'image_tag',
            'generate_challenge',
            'ago',
            'shortened_exception',
        ];

        foreach ($helpers as $helper) {
            $this->assertTrue(
                function_exists($helper),
                "Helper function {$helper} should exist"
            );
        }
    }

    /** @test */
    public function test_ago_helper_accepts_carbon_instance(): void
    {
        $date = Carbon::now();

        $result = ago($date);

        $this->assertIsString($result);
        $this->assertStringContainsString('<time-ago', $result);
    }

    /** @test */
    public function test_shortened_exception_handles_empty_string(): void
    {
        $result = shortened_exception('');

        $this->assertEquals('', $result);
    }

    /** @test */
    public function test_shortened_exception_handles_multiline_exception(): void
    {
        $exception = "Exception: Error\nLine 2\nLine 3\nLine 4";

        $result = shortened_exception($exception);

        $this->assertStringContainsString('Exception', $result);
    }

    /** @test */
    public function test_icon_helper_escapes_icon_name(): void
    {
        $result = icon('test<script>');

        $this->assertStringNotContainsString('<script>', $result);
    }

    /** @test */
    public function test_generate_name_variants_with_numbers(): void
    {
        $variants = generateNameVariants('Site123');

        $this->assertIsArray($variants);
        $this->assertNotEmpty($variants);
        $this->assertContains('Site123', $variants);
    }

    /** @test */
    public function test_generate_name_variants_with_unicode(): void
    {
        $variants = generateNameVariants('SitéWeb');

        $this->assertIsArray($variants);
        $this->assertNotEmpty($variants);
    }

    /** @test */
    public function test_menu_active_with_trailing_slash(): void
    {
        $this->get('/path/');

        $result1 = menu_active(url('/path/'));
        $result2 = menu_active(url('/path'));

        $this->assertIsString($result1);
        $this->assertIsString($result2);
    }

    /** @test */
    public function test_icon_size_classes_are_applied(): void
    {
        $sizes = ['small', 'medium', 'large', 'xl'];

        foreach ($sizes as $size) {
            $result = icon('test', $size);
            $this->assertStringContainsString($size, $result);
        }
    }
}