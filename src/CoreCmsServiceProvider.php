<?php

namespace Netauratech\CoreCms;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Netauratech\CoreCms\Console\BackupCmsCommand;
use Netauratech\CoreCms\Console\BackupCommand;
use Netauratech\CoreCms\Console\CleanupCommand;
use Netauratech\CoreCms\Console\DiscoverAssetsCommand;
use Netauratech\CoreCms\Console\InstallCommand;
use Netauratech\CoreCms\Contracts\BackupProviderInterface;
use Netauratech\CoreCms\Contracts\ChallengeGeneratorInterface;
use Netauratech\CoreCms\Contracts\ChallengeInterface;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;
use Netauratech\CoreCms\Contracts\MediaProviderInterface;
use Netauratech\CoreCms\Events\LangLoaded;
use Netauratech\CoreCms\Events\OptionUpdated;
use Netauratech\CoreCms\Form\FormRegistry;
use Netauratech\CoreCms\Http\Controllers\AssetController;
use Netauratech\CoreCms\Listeners\ClearOptionCache;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Services\Admin\DashboardManager;
use Netauratech\CoreCms\Services\Admin\MenuManager;
use Netauratech\CoreCms\Services\AssetManager;
use Netauratech\CoreCms\Services\BackupProvider;
use Netauratech\CoreCms\Services\Captcha\PuzzleChallenge;
use Netauratech\CoreCms\Services\Captcha\PuzzleGenerator;
use Netauratech\CoreCms\Services\NullContentProvider;
use Netauratech\CoreCms\Services\NullMediaProvider;
use Netauratech\CoreCms\Services\StorageAssetSource;

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

        Paginator::defaultView('core-cms::shared.partials.paginator');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');

        $this->app->singleton(MenuManager::class, function () {
            return new MenuManager();
        });

        $this->app->singleton(DashboardManager::class, function () {
            return new DashboardManager();
        });

        $this->app->singleton(AssetManager::class, function ($app) {
            return new AssetManager();
        });

        $this->app->singleton(FormRegistry::class, function ($app) {
            return new FormRegistry();
        });

        $this->app->bindIf(ContentProviderInterface::class, NullContentProvider::class);
        $this->app->bindIf(MediaProviderInterface::class, NullMediaProvider::class);
        $this->app->bindIf(ChallengeInterface::class, PuzzleChallenge::class);
        $this->app->bindIf(ChallengeGeneratorInterface::class, PuzzleGenerator::class);
        $this->app->bindIf(BackupProviderInterface::class, BackupProvider::class);

        $this->app->tag(StorageAssetSource::class, 'cms.asset.sources');
        $this->app->bind(AssetController::class, function ($app) {
            $assetSources = iterator_to_array($app->tagged('cms.asset.sources'));
            return new AssetController(
                $assetSources
            );
        });
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
            __DIR__.'/../config/backup.php' => config_path('backup.php'),
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

        $this->publishes([
            __DIR__.'/resources/views/mail' => resource_path('views/vendor/mail'),
        ], 'core-cms-assets');

        $this->publishes([
            __DIR__.'/resources/views/notifications' => resource_path('views/vendor/notifications'),
        ], 'core-cms-assets');

        // Register Assets
        $packageBasePath = realpath(__DIR__ . '/../');
        $composerJsonPath = $packageBasePath . '/composer.json';

        $assetManager->registerTranslationPath('core-cms', __DIR__.'/lang');

        if (file_exists($composerJsonPath)) {
            $composerJsonContent = json_decode(file_get_contents($composerJsonPath), true);
            if (isset($composerJsonContent['name'])) {
                $packageName = $composerJsonContent['name'];
            }
            $assetManager->registerAppJs("vendor/{$packageName}/src/resources/ts/app.ts");
            $assetManager->registerAdminJs("vendor/{$packageName}/src/resources/ts/admin.ts");
        }

        // Lang
        $this->loadTranslationsFrom(__DIR__.'/lang', 'core-cms');
        LangLoaded::dispatch('core-cms');

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

                $theme = null;

                foreach ($opts as $option) {
                    $valueToStore = $option->value ?? '';

                    if (($option->type === 'content' || $option->type === 'template') && $option->value !== "") {
                        $contentItem = $contentProvider->getContentById($option->value);
                        $valueToStore = $contentItem;
                    }
                    if ($option->type === 'theme') {
                        $theme = $option;
                    }
                    $data[$option->key] = $valueToStore;
                }
                return ["options" => $data, "theme" => $theme];
            });

            View::composer('*', function ($view) use ($ret) {
                $view->with('options', $ret['options']);
                $view->with('favicon', $ret['options']['favicon'] ? image_url($ret['options']['favicon'], 128) : "");
                $view->with('openGraphLogo', $ret['options']['logo'] ? image_url($ret['options']['logo']) : "");
                $view->with('cacheBuster', substr(md5(json_encode($ret['theme']->updated_at)), 0, 8));
            });
        }

        // Command registration Artisan
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                DiscoverAssetsCommand::class,
                BackupCmsCommand::class,
                BackupCommand::class,
                CleanupCommand::class
            ]);
        }

        $this->app->events->listen(
            OptionUpdated::class,
            ClearOptionCache::class
        );

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

        //Route Api
        Route::group([
            'prefix' => 'api',
            'as' => 'api.',
            'middleware' => ['web'],
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        });

        $menuManager->registerMenuItem('option', [
            'label' => trans_choice('core-cms::admin.option.value', 0),
            'icon' => 'option',
            'route' => 'admin.option.index',
            'can' => 'option-list'
        ]);
    }
}