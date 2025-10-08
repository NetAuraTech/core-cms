<?php

namespace Netauratech\CoreCms\Services\Shortcode;

use Illuminate\Support\Arr;

class OptionShortcode
{
    /**
     * Renders a *`[option]`* shortcode to retrieve and display a value from context or database.
     *
     * Prioritizes context data (options, content) before querying the database.
     *
     * @param array $attrs   Shortcode attributes.
     *                       - key:      Direct key from context['options'] (quick access).
     *                       - from:     Context source: "options", "content" (default: "options").
     *                       - property: Property to access on object (e.g., "title", "slug").
     *                       - table:    Database table name (fallback if not in context).
     *                       - column:   Column to retrieve (fallback).
     *                       - where:    Column name for WHERE clause (default: "id").
     *                       - value:    Value to match in WHERE clause.
     *                       - default:  Fallback value if nothing is found (default: "").
     *                       - cache:    Cache duration in minutes (optional, 0 = no cache).
     *                       - format:   Optional formatting: "upper", "lower", "ucfirst", "number" (default: none).
     *
     * @param array $context Context data (options, content, etc.).
     *
     * @return string The retrieved value, or the default value.
     *
     * @example
     * // Quick access from context options:
     * // [option key="site_name"]
     * // [option key="description"]
     *
     * // Access from content object:
     * // [option from="content" property="title"]
     * // [option from="content" property="slug"]
     *
     * // Access nested option object:
     * // [option key="footer" property="title"]
     *
     * // With formatting:
     * // [option key="site_name" format="upper"]
     *
     * // Fallback to database (if not in context):
     * // [option table="users" column="email" where="id" value="1" default="no-email@example.com"]
     *
     * // With default value:
     * // [option key="unknown_key" default="Default value"]
     */
    public function __invoke(array $attrs, array $context): string
    {
        $key = $attrs['key'] ?? null;
        $from = $attrs['from'] ?? 'options';
        $property = $attrs['property'] ?? null;
        $default = $attrs['default'] ?? '';
        $format = $attrs['format'] ?? null;

        $result = null;

        if ($key !== null && isset($context[$from])) {
            $result = Arr::get($context[$from], $key);

            if (is_object($result) && $property !== null) {
                $result = $result->{$property} ?? null;
            }
        }

        if ($result === null && $from === 'content' && isset($context['content']) && $property !== null) {
            $result = $context['content']->{$property} ?? null;
        }

        if ($result === null) {
            $result = $default;
        }

        return $this->formatValue($result, $format);
    }

    /**
     * Formats the retrieved value based on the format attribute.
     *
     * @param mixed       $value  The value to format.
     * @param string|null $format The format type.
     *
     * @return string Formatted and escaped value.
     */
    protected function formatValue(mixed $value, ?string $format): string
    {
        if (is_object($value) && method_exists($value, '__toString')) {
            $value = (string) $value;
        } elseif (is_object($value)) {
            $value = '';
        }

        $value = (string) $value;

        switch ($format) {
            case 'upper':
                $value = mb_strtoupper($value);
                break;

            case 'lower':
                $value = mb_strtolower($value);
                break;

            case 'ucfirst':
                $value = mb_convert_case($value, MB_CASE_TITLE);
                break;

            case 'number':
                $value = number_format((float) $value, 0, ',', ' ');
                break;

            default:
                break;
        }

        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}