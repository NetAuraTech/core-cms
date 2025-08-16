<?php

namespace Netauratech\CoreCms\Services\Shortcode;

class ButtonShortcode
{
    /**
     * Renders a `[button]` shortcode into an HTML link styled as a button.
     *
     * Supports both `[button text="..."]` and `[button]Inner content[/button]` syntaxes.
     *
     * @param array $attrs   Shortcode attributes.
     *                       - url:  Destination link (default: "#").
     *                       - type: Button style type (default: "primary").
     *                       - text: Button label (optional, falls back to inner content).
     * @param array $context Additional context passed from Blade or the caller.
     *
     * @return string Rendered `<a>` HTML element representing the button.
     *
     * @example
     * // Using text attribute:
     * // [button url="/contact" type="secondary" text="Contact us"]
     *
     * // Using inner content:
     * // [button url="/signup" type="primary"]Sign up now[/button]
     *
     * // In Blade:
     * // @shortcode($block['content'], ['content' => $content])
     *
     * // Output:
     * // <a href="/signup" class="button" data-type="primary">Sign up now</a>
     */
    public function __invoke(array $attrs, array $context): string
    {
        $url  = $attrs['url'] ?? '#';
        $type = $attrs['type'] ?? 'primary';

        // Prefer "text" attribute, fallback to shortcode inner content
        $text = $attrs['text'] ?? ($attrs['content'] ?? '');

        // Escape values for security
        $url  = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        $type = htmlspecialchars($type, ENT_QUOTES, 'UTF-8');
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

        return "<a href=\"{$url}\" class=\"button\" data-type=\"{$type}\">{$text}</a>";
    }
}