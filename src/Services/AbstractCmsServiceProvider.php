<?php

namespace Netauratech\CoreCms\Services;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Netauratech\CoreCms\Events\LangLoaded;
abstract class AbstractCmsServiceProvider extends ServiceProvider
{
    protected string $packageName;
    protected array $config = [];

    public function __construct($app)
    {
        parent::__construct($app);
        $this->packageName = $this->getPackageName();
        $this->config = $this->getBootstrapConfig();
    }

    abstract protected function getPackageName(): string;

    protected function getBootstrapConfig(): array
    {
        return [
            'views' => true,
            'translations' => true,
            'assets' => true,
            'migrations' => true,
            'seeders' => true,
            'routes' => [
                'admin' => true,
                'web' => true,
                'api' => true,
                'auth' => true
            ],
            'publishes' => [
                'migrations' => true,
                'seeders' => true,
                'translations' => true,
                'config' => true,
                'assets' => true
            ]
        ];
    }

    protected function bootstrapPackage(): void
    {
        $this->registerViews();
        $this->registerTranslations();
        $this->registerAssets();
        $this->loadMigrations();
        $this->registerPublishes();
        $this->registerRoutes();

        if ($this->config['translations']) {
            LangLoaded::dispatch($this->packageName);
        }
    }

    protected function registerViews(): void
    {
        if (!$this->config['views']) {
            return;
        }

        $viewsPath = $this->getPackagePath() . '/resources/views';
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, $this->packageName);
        }
    }

    protected function registerTranslations(): void
    {
        if (!$this->config['translations']) {
            return;
        }

        $langPath = $this->getPackagePath() . '/lang';
        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->packageName);
            app(AssetManager::class)->registerTranslationPath($this->packageName, $langPath);
        }
    }

    protected function registerAssets(): void
    {
        if (!$this->config['assets']) {
            return;
        }

        $packageBasePath = realpath($this->getPackagePath() . '/../');
        $composerJsonPath = $packageBasePath . '/composer.json';

        if (file_exists($composerJsonPath)) {
            $composerJsonContent = json_decode(file_get_contents($composerJsonPath), true);
            if (isset($composerJsonContent['name'])) {
                $composerPackageName = $composerJsonContent['name'];
                $assetManager = app(AssetManager::class);
                $assetManager->registerAppJs("vendor/{$composerPackageName}/src/resources/ts/app.ts");
                $assetManager->registerAdminJs("vendor/{$composerPackageName}/src/resources/ts/admin.ts");
            }
        }
    }

    protected function loadMigrations(): void
    {
        if (!$this->config['migrations']) {
            return;
        }

        $migrationsPath = $this->getPackagePath() . '/database/migrations';
        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    protected function registerPublishes(): void
    {
        $publishes = [];

        if ($this->config['publishes']['migrations'] && $this->config['migrations']) {
            $migrationsPath = $this->getPackagePath() . '/database/migrations/';
            if (is_dir($migrationsPath)) {
                $publishes[$migrationsPath] = database_path('migrations');
            }
        }

        if ($this->config['publishes']['seeders'] && $this->config['seeders']) {
            $seedersPath = $this->getPackagePath() . '/database/seeders/';
            if (is_dir($seedersPath)) {
                $publishes[$seedersPath] = database_path('seeders');
            }
        }

        if ($this->config['publishes']['translations'] && $this->config['translations']) {
            $langPath = $this->getPackagePath() . '/lang';
            if (is_dir($langPath)) {
                $publishes[$langPath] = $this->app->langPath("vendor/{$this->packageName}");
            }
        }

        if ($this->config['publishes']['config']) {
            $configPath = $this->getPackagePath() . '/../config/' . $this->packageName . '.php';
            if (file_exists($configPath)) {
                $publishes[$configPath] = config_path($this->packageName . '.php');
            }
        }

        if ($this->config['publishes']['assets']) {
            $assetsPath = $this->getPackagePath() . '/resources/assets';
            if (is_dir($assetsPath)) {
                $publishes[$assetsPath] = public_path("vendor/{$this->packageName}");
            }
        }

        if (!empty($publishes)) {
            $this->publishes($publishes, 'core-cms');
        }
    }

    protected function registerRoutes(): void
    {
        if ($this->config['routes']['admin']) {
            $adminRoutesPath = $this->getPackagePath() . '/routes/admin.php';
            if (file_exists($adminRoutesPath)) {
                Route::group([
                    'middleware' => config('core-cms.admin.middleware'),
                    'prefix' => config('core-cms.admin.prefix'),
                    'as' => config('core-cms.admin.name'),
                ], function () use ($adminRoutesPath) {
                    $this->loadRoutesFrom($adminRoutesPath);
                });
            }
        }

        if ($this->config['routes']['web']) {
            $webRoutesPath = $this->getPackagePath() . '/routes/web.php';
            if (file_exists($webRoutesPath)) {
                Route::group([
                    'middleware' => ['web'],
                ], function () use ($webRoutesPath) {
                    $this->loadRoutesFrom($webRoutesPath);
                });
            }
        }

        if ($this->config['routes']['api']) {
            $apiRoutesPath = $this->getPackagePath() . '/routes/api.php';
            if (file_exists($apiRoutesPath)) {
                Route::group([
                    'prefix' => 'api',
                    'as' => 'api.',
                    'middleware' => ['web'],
                ], function () use ($apiRoutesPath) {
                    $this->loadRoutesFrom($apiRoutesPath);
                });
            }
        }

        if ($this->config['routes']['auth']) {
            $authRoutesPath = $this->getPackagePath() . '/routes/auth.php';
            if (file_exists($authRoutesPath)) {
                Route::group([], function () use ($authRoutesPath) {
                    $this->loadRoutesFrom($authRoutesPath);
                });
            }
        }
    }

    protected function getPackagePath(): string
    {
        return dirname((new \ReflectionClass($this))->getFileName());
    }
}