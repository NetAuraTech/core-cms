<?php

namespace Netauratech\CoreCms\Tests\Unit;

use Netauratech\CoreCms\Services\AssetManager;
use Netauratech\CoreCms\Tests\TestCase;

class AssetManagerTest extends TestCase
{
    protected AssetManager $assetManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assetManager = new AssetManager();
    }

    /** @test */
    public function test_it_can_register_app_js(): void
    {
        $this->assetManager->registerAppJs('path/to/app.js');

        $assets = $this->assetManager->getAppJsAssets();

        $this->assertContains('path/to/app.js', $assets);
    }

    /** @test */
    public function test_it_can_register_multiple_app_js(): void
    {
        $this->assetManager->registerAppJs(['path/to/app1.js', 'path/to/app2.js']);

        $assets = $this->assetManager->getAppJsAssets();

        $this->assertCount(2, $assets);
        $this->assertContains('path/to/app1.js', $assets);
        $this->assertContains('path/to/app2.js', $assets);
    }

    /** @test */
    public function test_it_prevents_duplicate_app_js(): void
    {
        $this->assetManager->registerAppJs('path/to/app.js');
        $this->assetManager->registerAppJs('path/to/app.js');

        $assets = $this->assetManager->getAppJsAssets();

        $this->assertCount(1, $assets);
    }

    /** @test */
    public function test_it_can_register_admin_js(): void
    {
        $this->assetManager->registerAdminJs('path/to/admin.js');

        $assets = $this->assetManager->getAdminJsAssets();

        $this->assertContains('path/to/admin.js', $assets);
    }

    /** @test */
    public function test_it_can_register_css(): void
    {
        $this->assetManager->registerCss('path/to/styles.css');

        $assets = $this->assetManager->getCssAssets();

        $this->assertContains('path/to/styles.css', $assets);
    }

    /** @test */
    public function test_it_can_register_translation_path(): void
    {
        $this->assetManager->registerTranslationPath('my-package', '/path/to/lang');

        $paths = $this->assetManager->getTranslationPaths();

        $this->assertArrayHasKey('my-package', $paths);
        $this->assertEquals('/path/to/lang', $paths['my-package']);
    }

    /** @test */
    public function test_it_can_register_view(): void
    {
        $this->assetManager->registerView('my-package::view');

        $views = $this->assetManager->getViewAssets();

        $this->assertContains('my-package::view', $views);
    }

    /** @test */
    public function test_it_handles_empty_asset_lists(): void
    {
        $this->assertEmpty($this->assetManager->getAppJsAssets());
        $this->assertEmpty($this->assetManager->getAdminJsAssets());
        $this->assertEmpty($this->assetManager->getCssAssets());
        $this->assertEmpty($this->assetManager->getTranslationPaths());
        $this->assertEmpty($this->assetManager->getViewAssets());
    }
}