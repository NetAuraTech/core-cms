<?php

if (!function_exists('icon')) {
    /**
     * Génère une balise SVG pour une icône.
     *
     * @param string $name Le nom de l'icône.
     * @param string|null $size La taille de l'icône.
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
     * Ajoute la classe 'active' si l'URL courante correspond au chemin du menu.
     *
     * @param string $path Le chemin du lien de menu (généralement via route()).
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
    function generateNameVariants(string $sitename): array
    {
        $variants = [];

        $base = trim($sitename);
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