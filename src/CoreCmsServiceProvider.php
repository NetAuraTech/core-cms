<?php

namespace Netauratech\CoreCms;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
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
use Netauratech\CoreCms\Events\OptionUpdated;
use Netauratech\CoreCms\Form\FormRegistry;
use Netauratech\CoreCms\Http\Controllers\AssetController;
use Netauratech\CoreCms\Listeners\ClearOptionCache;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Services\AbstractCmsServiceProvider;
use Netauratech\CoreCms\Services\Admin\DashboardManager;
use Netauratech\CoreCms\Services\Admin\MenuManager;
use Netauratech\CoreCms\Services\AssetManager;
use Netauratech\CoreCms\Services\BackupProvider;
use Netauratech\CoreCms\Services\Captcha\PuzzleChallenge;
use Netauratech\CoreCms\Services\Captcha\PuzzleGenerator;
use Netauratech\CoreCms\Services\NullContentProvider;
use Netauratech\CoreCms\Services\NullMediaProvider;
use Netauratech\CoreCms\Services\Shortcode\ButtonShortcode;
use Netauratech\CoreCms\Services\Shortcode\ShortcodeParser;
use Netauratech\CoreCms\Services\Shortcode\ShortcodeRegistry;
use Netauratech\CoreCms\Services\StorageAssetSource;
use Netauratech\CoreCms\Widgets\TasksWidget;

class CoreCmsServiceProvider extends AbstractCmsServiceProvider
{
    protected function getPackageName(): string
    {
        return 'core-cms';
    }

    protected function getBootstrapConfig(): array
    {
        $config = parent::getBootstrapConfig();

        return $config;
    }

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

        $this->app->singleton(ShortcodeRegistry::class, fn () => new ShortcodeRegistry());
        $this->app->singleton(ShortcodeParser::class, function ($app) {
            return new ShortcodeParser($app->make(ShortcodeRegistry::class));
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
    public function boot(MenuManager $menuManager, ShortcodeRegistry $shortcodeRegistry, DashboardManager $dashboardManager): void
    {
        $this->bootstrapPackage();

        // Publish the configuration file
        $this->publishes([
            __DIR__.'/../config/auth.php' => config_path('auth.php'),
        ], 'core-cms');

        $this->publishes([
            __DIR__.'/../config/backup.php' => config_path('backup.php'),
        ], 'core-cms');

        $this->publishes([
            __DIR__.'/resources/views/mail' => resource_path('views/vendor/mail'),
        ], 'core-cms');

        $this->publishes([
            __DIR__.'/resources/views/notifications' => resource_path('views/vendor/notifications'),
        ], 'core-cms');

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

        Blade::directive('shortcode', function ($expression) {
            return "<?php echo app(" . ShortcodeParser::class . "::class)->parse($expression); ?>";
        });

        $shortcodeRegistry->register('button', new ButtonShortcode());

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

        $dashboardManager->addWidget(TasksWidget::class);

        $menuManager->registerMenuItem('option', [
            'label' => trans_choice('core-cms::admin.option.value', 0),
            'icon' => 'option',
            'route' => 'admin.option.index',
            'can' => 'option-list'
        ]);
    }
}