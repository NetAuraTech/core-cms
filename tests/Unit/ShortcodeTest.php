<?php

namespace Netauratech\CoreCms\Tests\Unit;

use Netauratech\CoreCms\Services\Shortcode\ButtonShortcode;
use Netauratech\CoreCms\Services\Shortcode\OptionShortcode;
use Netauratech\CoreCms\Services\Shortcode\ShortcodeParser;
use Netauratech\CoreCms\Services\Shortcode\ShortcodeRegistry;
use Netauratech\CoreCms\Tests\TestCase;

class ShortcodeTest extends TestCase
{
    protected ShortcodeRegistry $registry;
    protected ShortcodeParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registry = new ShortcodeRegistry();
        $this->parser = new ShortcodeParser($this->registry);
    }

    /** @test */
    public function test_it_can_register_a_shortcode(): void
    {
        $this->registry->register('test', function($attrs, $context) {
            return 'TEST';
        });

        $callback = $this->registry->get('test');

        $this->assertIsCallable($callback);
    }

    /** @test */
    public function test_it_parses_simple_shortcode(): void
    {
        $this->registry->register('hello', function($attrs, $context) {
            return 'Hello World';
        });

        $result = $this->parser->parse('[hello]');

        $this->assertEquals('Hello World', $result);
    }

    /** @test */
    public function test_it_parses_shortcode_with_attributes(): void
    {
        $this->registry->register('greet', function($attrs, $context) {
            return 'Hello ' . ($attrs['name'] ?? 'Guest');
        });

        $result = $this->parser->parse('[greet name="John"]');

        $this->assertEquals('Hello John', $result);
    }

    /** @test */
    public function test_it_parses_shortcode_with_content(): void
    {
        $this->registry->register('bold', function($attrs, $context) {
            return '<strong>' . ($attrs['content'] ?? '') . '</strong>';
        });

        $result = $this->parser->parse('[bold]This is bold[/bold]');

        $this->assertEquals('<strong>This is bold</strong>', $result);
    }

    /** @test */
    public function test_it_parses_button_shortcode(): void
    {
        $this->registry->register('button', new ButtonShortcode());
        $result = $this->parser->parse('[button url="/test" text="Click me"]');

        $this->assertStringContainsString('href="/test"', $result);
        $this->assertStringContainsString('Click me', $result);
        $this->assertStringContainsString('class="button"', $result);
    }

    /** @test */
    public function test_button_shortcode_with_type(): void
    {
        $this->registry->register('button', new ButtonShortcode());
        $result = $this->parser->parse('[button url="/test" type="secondary" text="Click"]');

        $this->assertStringContainsString('data-type="secondary"', $result);
    }

    /** @test */
    public function test_it_parses_option_shortcode(): void
    {
        $context = [
            'options' => [
                'site_name' => 'My Site'
            ]
        ];

        $this->registry->register('option', new OptionShortcode());
        $result = $this->parser->parse('[option key="site_name"]', $context);

        $this->assertEquals('My Site', $result);
    }

    /** @test */
    public function test_option_shortcode_with_default_value(): void
    {
        $context = ['options' => []];

        $this->registry->register('option', new OptionShortcode());
        $result = $this->parser->parse('[option key="missing" default="Default Value"]', $context);

        $this->assertEquals('Default Value', $result);
    }

    /** @test */
    public function test_option_shortcode_with_format(): void
    {
        $context = [
            'options' => [
                'site_name' => 'my site'
            ]
        ];

        $this->registry->register('option', new OptionShortcode());
        $result = $this->parser->parse('[option key="site_name" format="upper"]', $context);

        $this->assertEquals('MY SITE', $result);
    }

    /** @test */
    public function test_it_leaves_unknown_shortcodes_intact(): void
    {
        $input = '[unknown_shortcode]';

        $result = $this->parser->parse($input);

        $this->assertEquals($input, $result);
    }

    /** @test */
    public function test_it_parses_multiple_shortcodes(): void
    {
        $this->registry->register('a', function() { return 'AAA'; });
        $this->registry->register('b', function() { return 'BBB'; });

        $result = $this->parser->parse('Start [a] middle [b] end');

        $this->assertEquals('Start AAA middle BBB end', $result);
    }
}