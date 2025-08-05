<?php

namespace Netauratech\CoreCms\Services;

class AssetManager
{
    /**
     * List of registered JavaScript/TypeScript app asset paths.
     * @var array
     */
    protected array $appJsAssets = [];

    /**
     * List of registered JavaScript/TypeScript administration asset paths.
     * @var array
     */
    protected array $adminJsAssets = [];

    /**
     * List of registered CSS asset paths (not imported via JS).
     * @var array
     */
    protected array $cssAssets = [];

    /**
     * List of paths to saved translation files.
     * The key is the translation namespace (e.g., ‘core-cms’), and the value is the absolute path to the language folder.
     * @var array
     */
    protected array $translationFiles = [];

    /**
     * Saves one or more paths for JavaScript/TypeScript app assets.
     *
     * @param string|array $assets The paths of assets to be saved.
     * @return void
     */
    public function registerAppJs(string|array $assets): void
    {
        foreach ((array) $assets as $asset) {
            if (!in_array($asset, $this->appJsAssets)) {
                $this->appJsAssets[] = $asset;
            }
        }
    }

    /**
     * Saves one or more paths for JavaScript/TypeScript administration assets.
     *
     * @param string|array $assets The paths of assets to be saved.
     * @return void
     */
    public function registerAdminJs(string|array $assets): void
    {
        foreach ((array) $assets as $asset) {
            if (!in_array($asset, $this->adminJsAssets)) {
                $this->adminJsAssets[] = $asset;
            }
        }
    }

    /**
     * Saves one or more CSS asset paths (for CSS that is not imported via JS).
     *
     * @param string|array $assets The paths of assets to be saved.
     * @return void
     */
    public function registerCss(string|array $assets): void
    {
        foreach ((array) $assets as $asset) {
            if (!in_array($asset, $this->cssAssets)) {
                $this->cssAssets[] = $asset;
            }
        }
    }

    /**
     * Registers a translation folder for a given namespace.
     *
     * @param string $namespace The namespace of the translation (e.g., ‘core-cms’).
     * @param string $path The absolute path to the package's language folder (e.g., __DIR__.‘/../lang’).
     * @return void
     */
    public function registerTranslationPath(string $namespace, string $path): void
    {
        // Stocke le chemin du dossier de langue pour ce namespace.
        // Nous nous attendons à trouver des sous-dossiers de langue (fr, en, etc.) à cet endroit.
        $this->translationFiles[$namespace] = $path;
    }

    /**
     * Retrieves all paths of JavaScript/TypeScript app assets that have been saved.
     *
     * @return array
     */
    public function getAppJsAssets(): array
    {
        return $this->appJsAssets;
    }

    /**
     * Retrieves all registered JavaScript/TypeScript administration asset paths.
     *
     * @return array
     */
    public function getAdminJsAssets(): array
    {
        return $this->adminJsAssets;
    }

    /**
     * Retrieves all saved CSS asset paths.
     *
     * @return array
     */
    public function getCssAssets(): array
    {
        return $this->cssAssets;
    }

    /**
     * Retrieves all paths of saved translation folders.
     *
     * @return array An associative array where the key is the namespace and the value is the folder path.
     */
    public function getTranslationPaths(): array
    {
        return $this->translationFiles;
    }
}