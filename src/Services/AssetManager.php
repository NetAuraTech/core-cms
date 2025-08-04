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
}