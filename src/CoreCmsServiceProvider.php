<?php

namespace Netauratech\CoreCms;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Netauratech\CoreCms\Console\InstallCommand;
use Netauratech\CoreCms\Services\Admin\DashboardManager;
use Netauratech\CoreCms\Services\Admin\MenuManager;

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
    }
    public function boot(): void
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

        // Lang
        $this->loadTranslationsFrom(__DIR__.'/lang', 'core-cms');

        // Allows you to publish translations of the package
        $this->publishes([
            __DIR__.'/lang' => $this->app->langPath('vendor/core-cms'),
        ], 'core-cms-translations');

        // Command registration Artisan
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
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
    }
}