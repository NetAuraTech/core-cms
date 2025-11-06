<?php

namespace Netauratech\CoreCms\Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Services\Shortcode\ButtonShortcode;
use Netauratech\CoreCms\Services\Shortcode\OptionShortcode;
use Netauratech\CoreCms\Services\Shortcode\ShortcodeParser;
use Netauratech\CoreCms\Services\Shortcode\ShortcodeRegistry;
use Netauratech\CoreCms\Services\Shortcode\TemplateShortcode;
use Netauratech\CoreCms\Tests\TestCase;

class ShortcodeTest extends TestCase
{
    use DatabaseMigrations;

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
    public function test_it_returns_null_for_unregistered_shortcode(): void
    {
        $callback = $this->registry->get('nonexistent');

        $this->assertNull($callback);
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
    public function test_it_parses_shortcode_with_single_quotes(): void
    {
        $this->registry->register('greet', function($attrs, $context) {
            return 'Hello ' . ($attrs['name'] ?? 'Guest');
        });

        $result = $this->parser->parse("[greet name='John']");

        $this->assertEquals('Hello John', $result);
    }

    /** @test */
    public function test_it_parses_shortcode_with_multiple_attributes(): void
    {
        $this->registry->register('person', function($attrs, $context) {
            $name = $attrs['name'] ?? '';
            $age = $attrs['age'] ?? '';
            return "$name is $age years old";
        });

        $result = $this->parser->parse('[person name="John" age="30"]');

        $this->assertEquals('John is 30 years old', $result);
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
    public function test_it_handles_empty_shortcode_content(): void
    {
        $this->registry->register('bold', function($attrs, $context) {
            return '<strong>' . ($attrs['content'] ?? 'empty') . '</strong>';
        });

        $result = $this->parser->parse('[bold][/bold]');

        $this->assertEquals('<strong></strong>', $result);
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
    public function test_button_shortcode_with_content(): void
    {
        $this->registry->register('button', new ButtonShortcode());
        $result = $this->parser->parse('[button url="/test"]Click me[/button]');

        $this->assertStringContainsString('href="/test"', $result);
        $this->assertStringContainsString('Click me', $result);
    }

    /** @test */
    public function test_button_shortcode_with_type(): void
    {
        $this->registry->register('button', new ButtonShortcode());
        $result = $this->parser->parse('[button url="/test" type="secondary" text="Click"]');

        $this->assertStringContainsString('data-type="secondary"', $result);
    }

    /** @test */
    public function test_button_shortcode_defaults(): void
    {
        $this->registry->register('button', new ButtonShortcode());
        $result = $this->parser->parse('[button]');

        $this->assertStringContainsString('href="#"', $result);
        $this->assertStringContainsString('data-type="primary"', $result);
    }

    /** @test */
    public function test_button_shortcode_escapes_html(): void
    {
        $this->registry->register('button', new ButtonShortcode());
        $result = $this->parser->parse('[button url="/test" text="<script>alert(1)</script>"]');

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
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
    public function test_option_shortcode_with_format_upper(): void
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
    public function test_option_shortcode_with_format_lower(): void
    {
        $context = [
            'options' => [
                'site_name' => 'MY SITE'
            ]
        ];

        $this->registry->register('option', new OptionShortcode());
        $result = $this->parser->parse('[option key="site_name" format="lower"]', $context);

        $this->assertEquals('my site', $result);
    }

    /** @test */
    public function test_option_shortcode_with_format_ucfirst(): void
    {
        $context = [
            'options' => [
                'site_name' => 'my awesome site'
            ]
        ];

        $this->registry->register('option', new OptionShortcode());
        $result = $this->parser->parse('[option key="site_name" format="ucfirst"]', $context);

        $this->assertEquals('My Awesome Site', $result);
    }

    /** @test */
    public function test_option_shortcode_with_format_number(): void
    {
        $context = [
            'options' => [
                'price' => '1234.56'
            ]
        ];

        $this->registry->register('option', new OptionShortcode());
        $result = $this->parser->parse('[option key="price" format="number"]', $context);

        $this->assertEquals('1 235', $result);
    }

    /** @test */
    public function test_option_shortcode_from_content(): void
    {
        $content = new \stdClass();
        $content->title = 'Test Title';

        $context = ['content' => $content];

        $this->registry->register('option', new OptionShortcode());
        $result = $this->parser->parse('[option from="content" property="title"]', $context);

        $this->assertEquals('Test Title', $result);
    }

    /** @test */
    public function test_option_shortcode_nested_object(): void
    {
        $nestedObject = new \stdClass();
        $nestedObject->title = 'Nested Title';

        $context = [
            'options' => [
                'footer' => $nestedObject
            ]
        ];

        $this->registry->register('option', new OptionShortcode());
        $result = $this->parser->parse('[option key="footer" property="title"]', $context);

        $this->assertEquals('Nested Title', $result);
    }

    /** @test */
    public function test_template_shortcode_renders_template(): void
    {
        $template = Content::find(1);

        $this->registry->register('template', new TemplateShortcode());
        $result = $this->parser->parse("[template id={$template->id}]", []);

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function test_template_shortcode_with_default_syntax(): void
    {
        $template = Content::find(1);

        $this->registry->register('template', new TemplateShortcode());
        $result = $this->parser->parse("[template ={$template->id}]", []);

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function test_template_shortcode_returns_empty_for_nonexistent(): void
    {
        $this->registry->register('template', new TemplateShortcode());
        $result = $this->parser->parse('[template id=99999]', []);

        $this->assertEquals('', $result);
    }

    /** @test */
    public function test_template_shortcode_returns_empty_for_non_template_type(): void
    {
        $page = Content::create([
            'title' => 'Test Page',
            'slug' => 'test-page',
            'type' => 'page',
            'status' => 'published',
            'content' => json_encode([]),
        ]);

        $this->registry->register('template', new TemplateShortcode());
        $result = $this->parser->parse("[template id={$page->id}]", []);

        $this->assertEquals('', $result);
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

    /** @test */
    public function test_it_parses_same_shortcode_multiple_times(): void
    {
        $this->registry->register('test', function() { return 'X'; });

        $result = $this->parser->parse('[test] and [test] and [test]');

        $this->assertEquals('X and X and X', $result);
    }

    /** @test */
    public function test_shortcode_with_no_attributes(): void
    {
        $this->registry->register('simple', function($attrs) {
            return count($attrs) === 1 && isset($attrs['content']) ? 'OK' : 'NOK';
        });

        $result = $this->parser->parse('[simple]');

        $this->assertEquals('OK', $result);
    }

    /** @test */
    public function test_context_is_passed_to_shortcode(): void
    {
        $this->registry->register('contextual', function($attrs, $context) {
            return $context['test'] ?? 'no context';
        });

        $result = $this->parser->parse('[contextual]', ['test' => 'has context']);

        $this->assertEquals('has context', $result);
    }

    /** @test */
    public function test_shortcode_can_use_class_as_callback(): void
    {
        $this->registry->register('button', new ButtonShortcode());

        $result = $this->parser->parse('[button url="/test" text="Test"]');

        $this->assertStringContainsString('Test', $result);
    }

    /** @test */
    public function test_special_characters_in_attributes_are_parsed(): void
    {
        $this->registry->register('test', function($attrs) {
            return $attrs['url'] ?? '';
        });

        $result = $this->parser->parse('[test url="/path?param=value&other=123"]');

        $this->assertEquals('/path?param=value&other=123', $result);
    }

    /** @test */
    public function test_shortcode_with_spaces_in_content(): void
    {
        $this->registry->register('wrap', function($attrs) {
            return '>' . $attrs['content'] . '<';
        });

        $result = $this->parser->parse('[wrap]  content with spaces  [/wrap]');

        $this->assertEquals('>  content with spaces  <', $result);
    }

    /** @test */
    public function test_empty_attribute_values_are_handled(): void
    {
        $this->registry->register('test', function($attrs) {
            return isset($attrs['empty']) ? 'has empty' : 'no empty';
        });

        $result = $this->parser->parse('[test empty=""]');

        $this->assertEquals('has empty', $result);
    }

    /** @test */
    public function test_numeric_attribute_values_are_parsed(): void
    {
        $this->registry->register('test', function($attrs) {
            return $attrs['number'] ?? 'none';
        });

        $result = $this->parser->parse('[test number="123"]');

        $this->assertEquals('123', $result);
    }
}