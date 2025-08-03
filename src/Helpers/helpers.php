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