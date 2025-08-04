<?php

namespace Netauratech\CoreCms;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Netauratech\CoreCms\Console\DiscoverAssetsCommand;
use Netauratech\CoreCms\Console\InstallCommand;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Services\Admin\DashboardManager;
use Netauratech\CoreCms\Services\Admin\MenuManager;
use Netauratech\CoreCms\Services\AssetManager;
use Netauratech\CoreCms\Services\NullContentProvider;

class CoreCmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/core-cms.php', 'core-cms'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/auth.php', 'auth'
        );

        $this->app->singleton(MenuManager::class, function () {
            return new MenuManager();
        });

        $this->app->singleton(DashboardManager::class, function () {
            return new DashboardManager();
        });

        $this->app->singleton(AssetManager::class, function ($app) {
            return new AssetManager();
        });

        $this->app->bind(ContentProviderInterface::class, NullContentProvider::class);
    }
    public function boot(MenuManager $menuManager, AssetManager $assetManager): void
    {
        // Publish the configuration file
        $this->publishes([
            __DIR__.'/../config/core-cms.php' => config_path('core-cms.php'),
        ], 'core-cms-config');

        $this->publishes([
            __DIR__.'/../config/auth.php' => config_path('auth.php'),
        ], 'core-cms-config');

        $this->publishes([
            __DIR__.'/resources/assets' => public_path('vendor/core-cms'),
        ], 'core-cms-assets');

        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations'),
        ], 'core-cms-migrations');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        $this->publishes([
            __DIR__.'/database/seeders/' => database_path('seeders')
        ], 'core-cms-seeders');

        // Load all views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'core-cms');

        // Register Assets
        $packageBasePath = realpath(__DIR__ . '/../');
        $composerJsonPath = $packageBasePath . '/composer.json';

        if (file_exists($composerJsonPath)) {
            $composerJsonContent = json_decode(file_get_contents($composerJsonPath), true);
            if (isset($composerJsonContent['name'])) {
                $packageName = $composerJsonContent['name'];
            }
            $assetManager->registerAppJs("vendor/{$packageName}/src/resources/ts/app.ts");
        }

        // Lang
        $this->loadTranslationsFrom(__DIR__.'/lang', 'core-cms');

        // Allows you to publish translations of the package
        $this->publishes([
            __DIR__.'/lang' => $this->app->langPath('vendor/core-cms'),
        ], 'core-cms-translations');

        // Share all CMS options with views
        if (Schema::hasTable('options')) {
            $cache = Cache::store('database');
            $ret = $cache->remember('options', 60 * 60, function () {
                $opts = Option::all();
                $data = [];
                $contentProvider = $this->app->make(ContentProviderInterface::class);

                foreach ($opts as $option) {
                    $valueToStore = $option->value ?? '';

                    if ($option->type === 'content') {
                        $contentItem = $contentProvider->getContentById($option->value);
                        $valueToStore = $contentItem;
                    }
                    $data[$option->key] = $valueToStore;
                }
                return $data;
            });

            View::composer('*', function ($view) use ($ret) {
                $view->with('options', $ret);
            });
        }

        // Command registration Artisan
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                DiscoverAssetsCommand::class,
            ]);
        }

        // Routes admin
        Route::group([
            'middleware' => config('core-cms.admin.middleware'),
            'prefix' => config('core-cms.admin.prefix'),
            'as' => config('core-cms.admin.name'),
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/routes/admin.php');
        });

        //Route Auth
        Route::group([
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/routes/auth.php');
        });

        //Route Web
        Route::group([
            'middleware' => ['web'],
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        });

        $menuManager->registerMenuItem('option', [
            'label' => trans_choice('core-cms::admin.option.value', 0),
            'icon' => 'option',
            'route' => 'admin.option.index',
            'can' => 'option-list'
        ]);
    }
}