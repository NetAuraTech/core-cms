<?php

namespace Netauratech\CoreCms\Services\Shortcode;

class ShortcodeRegistry
{
    protected array $shortcodes = [];

    /**
     * Registers a shortcode callback under a given name.
     *
     * @param string   $name     The name of the shortcode (e.g., 'button').
     * @param callable $callback The callback to execute when the shortcode is parsed.
     *                           Receives two parameters:
     *                           - array $attrs: shortcode attributes + 'content'
     *                           - array $context: optional context passed by the parser
     *
     * @return void
     */
    public function register(string $name, callable $callback): void
    {
        $this->shortcodes[$name] = $callback;
    }

    /**
     * Retrieves the callback for a given shortcode name.
     *
     * @param string $name The shortcode name to retrieve.
     *
     * @return callable|null The registered callback, or null if not found.
     */
    public function get(string $name): ?callable
    {
        return $this->shortcodes[$name] ?? null;
    }
}