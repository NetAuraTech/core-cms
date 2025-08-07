<?php

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
        $iconPath = asset('vendor/core-cms/sprite.svg') . "#{$name}";
        $sizeClass = $size ? "{$size}" : '';

        return <<<HTML
            <svg class="icon {$sizeClass} icon-{$name}">
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
        // On récupère le chemin de l'URL courante
        $currentPath = url()->current();

        // On compare les deux chemins
        if ($currentPath === $path) {
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
     * @param string|int|object|null $entity The ID of the attachment, the file name, or the Attachment object itself.
     * @param int|null $width The desired width for the image.
     * @param int|null $height The desired height for the image.
     * @return string The URL of the image.
     */
    function image_url(string|int|object|null $entity, ?int $width = null, ?int $height = null): string
    {
        $mediaProvider = app(MediaProviderInterface::class);

        return $mediaProvider->getImageUrl($entity, $width, $height);
    }
}