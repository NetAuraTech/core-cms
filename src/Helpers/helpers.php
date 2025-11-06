<?php

use Netauratech\CoreCms\Contracts\ChallengeInterface;
use Netauratech\CoreCms\Contracts\MediaProviderInterface;

if (!function_exists('icon')) {
    /**
     * Generates an SVG tag for an icon.
     *
     * @param string $name The name of the icon.
     * @param string|null $size The size of the icon.
     * @return string
     */
    function icon(string $name, ?string $size = null): string
    {
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        $iconPath = '/vendor/core-cms/sprite.svg' . "#{$safeName}";

        $sizeClass = $size ? htmlspecialchars($size, ENT_QUOTES, 'UTF-8') : '';

        return <<<HTML
            <svg class="icon {$sizeClass} icon-{$safeName}">
              <use xlink:href="{$iconPath}"></use>
            </svg>
        HTML;
    }
}

if (!function_exists('menu_active')) {
    /**
     * Adds the ‘active’ class if the current URL matches the menu path.
     *
     * @param string $path The path of the menu link (usually via route()).
     * @return string
     */
    function menu_active(string $path): string
    {
        $currentPathRelative = request()->path();

        $pathRelative = parse_url($path, PHP_URL_PATH);

        $currentPathClean = trim($currentPathRelative, '/');
        $pathClean = trim($pathRelative, '/');

        if ($currentPathClean === $pathClean) {
            return 'aria-current=page';
        }

        return '';
    }
}

if (!function_exists('generateNameVariants')) {
    function generateNameVariants(string $site_name): array
    {
        $variants = [];

        $base = trim($site_name);
        $lower = strtolower($base);
        $ucfirst = ucfirst($lower);
        $variants[] = $base;
        $variants[] = $lower;
        $variants[] = $ucfirst;

        $spaced = preg_replace('/(?<=\p{Ll})(?=\p{Lu})/u', ' ', $base);
        if (!in_array($spaced, $variants)) $variants[] = $spaced;

        $spacedLower = strtolower($spaced);
        $spacedUcwords = ucwords($spacedLower);

        foreach ([$spacedLower, $spacedUcwords] as $variant) {
            if (!in_array($variant, $variants)) {
                $variants[] = $variant;
            }
        }

        $parts = explode(' ', $spaced);
        $count = count($parts);

        if ($count > 1) {
            for ($i = 1; $i < $count; $i++) {
                $combined1 = implode('', array_slice($parts, 0, $i)) . ' ' . implode('', array_slice($parts, $i));
                $combined2 = implode('', array_slice($parts, 0, $i)) . ' ' . implode(' ', array_slice($parts, $i));

                foreach ([$combined1, strtolower($combined1), ucwords($combined1),
                             $combined2, strtolower($combined2), ucwords($combined2)] as $variant) {
                    if (!in_array($variant, $variants)) {
                        $variants[] = $variant;
                    }
                }
            }
        }

        return array_unique($variants);
    }
}

if (!function_exists('image_url')) {
    /**
     * Generates a URL for an image, potentially resized.
     * Uses MediaProviderInterface to delegate the logic.
     *
     * @param string|int $id The ID of the Media.
     * @param int|null $width The desired width for the image.
     * @param int|null $height The desired height for the image.
     * @return string The URL of the image.
     */
    function image_url(string|int $id, ?int $width = null, ?int $height = null): string
    {
        $mediaProvider = app(MediaProviderInterface::class);

        return $mediaProvider->getImageUrl($id, $width, $height);
    }
}

if (!function_exists('image_tag')) {
    /**
     * Generates an HTML <img> tag to display an image.
     *
     * This function takes an image entity or path and constructs an <img> tag
     * with options for alternative text, height, CSS transitions,
     * additional classes, and preloading.
     *
     * @param string $entity The image entity (e.g., a file path, an ID, or an image object).
     * @param string|null $alt The alternative text for the image, for accessibility. Defaults to null.
     * @param int|null $height The height of the image in pixels. Defaults to null.
     * @param string|null $transitionName A CSS transition name (e.g., for frontend animations). Defaults to null.
     * @param string|null $classes Additional CSS classes to apply to the <img> tag. Defaults to null.
     * @param array $styles Additional styles to apply to the <img> tag. Defaults to [].
     * @return string|null The generated HTML <img> tag as a string, or null if the image cannot be generated.
     */
    function image_tag(string $entity, ?string $alt = null, ?int $height = null, ?string $transitionName = null, ?string $classes = null, array $styles = []): ?string
    {
        $mediaProvider = app(MediaProviderInterface::class);

        return $mediaProvider->image_tag($entity, $alt, $height, $transitionName, $classes, $styles);
    }
}

if (!function_exists('generate_challenge')) {
    /**
     * Generates and returns a unique key for a new captcha challenge.
     *
     * @return string The key for the generated challenge.
     */
    function generate_challenge(): string
    {
        $challenge = app(ChallengeInterface::class);
        $key = $challenge->generateKey();

        return $key;
    }
}

if (!function_exists('ago')) {
    function ago(Carbon\Carbon $date, string $prefix = ''): string
    {
        $prefixAttribute = !empty($prefix) ? " prefix=\"{$prefix}\"" : '';

        return "<time-ago time=\"{$date->getTimestamp()}\"$prefixAttribute></time-ago>";
    }
}

if (!function_exists('shortened_exception')) {
    function shortened_exception(string $exception): string
    {
        if (preg_match('/(\w+Exception.*?)(\n|$)/', $exception, $matches)) {
            return $matches[1];
        }
        return substr($exception, 0, 200) . (strlen($exception) > 200 ? '...' : '');
    }
}