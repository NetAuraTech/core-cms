<?php

namespace Netauratech\CoreCms\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Netauratech\CoreCms\Services\AssetManager;
use Illuminate\Filesystem\Filesystem;
class DiscoverAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:discover';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Discovers and generates dynamic asset entry points for Vite.';

    /**
     * @var AssetManager
     */
    protected AssetManager $assetManager;

    /**
     * @var Filesystem
     */
    protected Filesystem $files;

    /**
     * Create a new command instance.
     *
     * @param AssetManager $assetManager
     * @param Filesystem $files
     */
    public function __construct(AssetManager $assetManager, Filesystem $files)
    {
        parent::__construct();
        $this->assetManager = $assetManager;
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Discovering package assets...');

        $generatedDir = base_path('resources/ts');
        $appPackagesJsPath = $generatedDir . '/app.ts';
        $adminPackagesJsPath = $generatedDir . '/admin.ts';
        $viteInputsJsonPath = base_path('bootstrap/cache/vite_inputs.json');

        $this->files->ensureDirectoryExists($generatedDir);
        $this->files->ensureDirectoryExists(dirname($viteInputsJsonPath));

        $appJsImports = array_map(function ($projectRelativePath) {
            return "import '../../{$projectRelativePath}';";
        }, $this->assetManager->getAppJsAssets());
        $this->files->put($appPackagesJsPath, implode("\n", $appJsImports));
        $this->info("Generated app JS entry point: {$appPackagesJsPath}");

        $adminJsImports = array_map(function ($projectRelativePath) {
            return "import '../../{$projectRelativePath}';";
        }, $this->assetManager->getAdminJsAssets());
        $this->files->put($adminPackagesJsPath, implode("\n", $adminJsImports));
        $this->info("Generated admin JS entry point: {$adminPackagesJsPath}");

        $viteInputs = array_merge(
            [$appPackagesJsPath, $adminPackagesJsPath],
            $this->assetManager->getCssAssets()
        );

        $finalViteInputs = array_map(function($path) {
            return str_replace('\\', '/', Str::after($path, base_path() . DIRECTORY_SEPARATOR));
        }, $viteInputs);

        $this->files->put($viteInputsJsonPath, json_encode($finalViteInputs, JSON_PRETTY_PRINT));
        $this->info("Generated Vite inputs list: {$viteInputsJsonPath}");

        $this->info('Asset discovery complete.');

        return self::SUCCESS;
    }
}