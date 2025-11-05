<?php

namespace Netauratech\CoreCms\Services\Shortcode;

use Netauratech\CoreCms\Models\Content;

class TemplateShortcode
{
    /**
     * Replaces a `[template id=...]` shortcode with the rendered output
     * of a Content model of type "template".
     *
     * ⚠️ If the ID is missing, the content does not exist, or the content
     * type is not "template", the method will return an empty string.
     *
     * @param array $attrs   Attributes passed to the shortcode.
     *                       Example: ['id' => 3]
     * @param array $context Additional context passed by Blade or the caller.
     *                       Example: ['content' => $content]
     *
     * @return string Rendered HTML of the view `core-cms::shortcodes.template`,
     *                or an empty string if the content is invalid.
     *
     * @example
     * // Inside stored content:
     * // "Welcome to my site! [template id=3] Thank you for visiting."
     *
     * // In Blade:
     * // @shortcode($block['content'], ['content' => $content])
     *
     * // Result: the shortcode is replaced by the rendered view
     * // `core-cms::shortcodes.template` with $template = Content::find(3).
     */
    public function __invoke(array $attrs, array $context): string
    {
        $id = $attrs['id'] ?? $attrs['default'] ?? null;
        if (!$id) return '';

        $template = Content::find($id);

        if (!$template || $template->type !== 'template') {
            return '';
        }

        return view('core-cms::shortcodes.template', [
                'template' => $template,
            ] + $context)->render();
    }
}