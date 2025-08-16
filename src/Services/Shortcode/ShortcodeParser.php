<?php

namespace Netauratech\CoreCms\Services\Shortcode;

class ShortcodeParser
{
    protected ShortcodeRegistry $registry;

    public function __construct(ShortcodeRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Parses the given content and replaces all registered shortcodes with
     * their rendered output.
     *
     * Supports shortcodes in the format:
     *   [shortcode attr1="value1" attr2='value2']Inner content[/shortcode]
     * or self-closing:
     *   [shortcode attr1="value1"]
     *
     * For each shortcode:
     *   - The corresponding callback from ShortcodeRegistry is called.
     *   - Attributes are parsed into an associative array.
     *   - Inner content is passed as $attrs['content'].
     *
     * Unknown shortcodes are left intact in the content.
     *
     * @param string $content The raw content containing shortcodes.
     * @param array  $context Optional context passed to the shortcode callbacks.
     *                        Example: ['content' => $content]
     *
     * @return string Content with all recognized shortcodes replaced by their rendered output.
     */
    public function parse(string $content, array $context = []): string
    {
        $pattern = '/\[(\w+)([^\]]*)\](?:([^[]*?)\[\/\1\])?/';

        return preg_replace_callback($pattern, function ($matches) use ($context) {
            [$full, $tag, $rawAttrs, $innerContent] = $matches + ['', '', '', ''];

            $callback = $this->registry->get($tag);
            if (!$callback) {
                return $full;
            }

            $attrs = $this->parseAttributes(trim($rawAttrs));
            $attrs['content'] = $innerContent;

            return call_user_func($callback, $attrs, $context);
        }, $content);
    }

    /**
     * Parses a shortcode attribute string into an associative array.
     *
     * Supports attributes in the following formats:
     *   - key="value"
     *   - key='value'
     *   - key=value
     *   - =value          (defaults to 'default' key)
     *
     * Examples:
     *   [button url="/contact" type="primary" text="Click me"]
     *   -> ['url' => '/contact', 'type' => 'primary', 'text' => 'Click me']
     *
     *   [template =3]
     *   -> ['default' => '3']
     *
     * @param string $text Raw attribute string from the shortcode
     *
     * @return array Associative array of parsed attributes
     */
    protected static function parseAttributes(string $text): array
    {
        $attrs = [];

        // Split by whitespace, but ignore spaces inside quotes
        $parts = preg_split('/\s+(?=(?:[^"\']|"[^"]*"|\'[^\']*\')*$)/', trim($text));

        foreach ($parts as $part) {
            if (str_contains($part, '=')) {
                [$key, $val] = explode('=', $part, 2);
                $key = $key === '' ? 'default' : $key;
                $attrs[$key] = trim($val, "\"'");
            } elseif (str_starts_with($part, '=')) {
                // case: "=value" -> default key
                $attrs['default'] = ltrim($part, '=');
            }
        }

        return $attrs;
    }
}