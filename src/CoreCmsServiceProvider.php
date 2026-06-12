<?php

namespace Netauratech\CoreCms;

use Database\Seeders\ContentTableSeeder;
use Database\Seeders\OptionsSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Netauratech\CoreCms\Console\BackupCmsCommand;
use Netauratech\CoreCms\Console\BackupCommand;
use Netauratech\CoreCms\Console\CleanupCommand;
use Netauratech\CoreCms\Console\DiscoverAssetsCommand;
use Netauratech\CoreCms\Console\InstallCommand;
use Netauratech\CoreCms\Contracts\BackupProviderInterface;
use Netauratech\CoreCms\Contracts\CacheServiceInterface;
use Netauratech\CoreCms\Contracts\ChallengeGeneratorInterface;
use Netauratech\CoreCms\Contracts\ChallengeInterface;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;
use Netauratech\CoreCms\Contracts\MediaProviderInterface;
use Netauratech\CoreCms\Contracts\ThemeMiddlewareInterface;
use Netauratech\CoreCms\Events\ContentSaved;
use Netauratech\CoreCms\Events\OptionUpdated;
use Netauratech\CoreCms\Form\FormRegistry;
use Netauratech\CoreCms\Http\Controllers\AssetController;
use Netauratech\CoreCms\Listeners\ClearOptionCache;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Observers\ContentObserver;
use Netauratech\CoreCms\Services\AbstractCmsServiceProvider;
use Netauratech\CoreCms\Services\Admin\DashboardManager;
use Netauratech\CoreCms\Services\Admin\MenuManager;
use Netauratech\CoreCms\Services\AssetManager;
use Netauratech\CoreCms\Services\BackupProvider;
use Netauratech\CoreCms\Services\CacheService;
use Netauratech\CoreCms\Services\Captcha\PuzzleChallenge;
use Netauratech\CoreCms\Services\Captcha\PuzzleGenerator;
use Netauratech\CoreCms\Services\ContentProvider;
use Netauratech\CoreCms\Services\ContentPurgeProvider;
use Netauratech\CoreCms\Services\NullContentProvider;
use Netauratech\CoreCms\Services\NullMediaProvider;
use Netauratech\CoreCms\Services\Shortcode\ButtonShortcode;
use Netauratech\CoreCms\Services\Shortcode\OptionShortcode;
use Netauratech\CoreCms\Services\Shortcode\ShortcodeParser;
use Netauratech\CoreCms\Services\Shortcode\ShortcodeRegistry;
use Netauratech\CoreCms\Services\Shortcode\TemplateShortcode;
use Netauratech\CoreCms\Services\StorageAssetSource;
use Netauratech\CoreCms\Widgets\TasksWidget;
use Spatie\Permission\Middleware\PermissionMiddleware;

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

    protected function getSeeders(): array
    {
        return [
            OptionsSeeder::class,
            RolesAndPermissionsSeeder::class,
            ContentTableSeeder::class,
        ];
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/core-cms.php', 'core-cms'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/auth.php', 'auth'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/backup.php', 'backup'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/lscache.php', 'lscache'
        );

        $this->mergeConfigFrom(
            __DIR__.'/../config/permission.php', 'permission'
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

        $this->app->bindIf(ContentProviderInterface::class, ContentProvider::class);
        $this->app->bindIf(MediaProviderInterface::class, NullMediaProvider::class);
        $this->app->bindIf(ChallengeInterface::class, PuzzleChallenge::class);
        $this->app->bindIf(ChallengeGeneratorInterface::class, PuzzleGenerator::class);
        $this->app->bindIf(BackupProviderInterface::class, BackupProvider::class);
        $this->app->bindIf(CacheServiceInterface::class, CacheService::class);

        if (!$this->app->bound(ThemeMiddlewareInterface::class)) {
            $this->app->bind(ThemeMiddlewareInterface::class, function() {
                return new class implements ThemeMiddlewareInterface {
                    public function handle($request, \Closure $next) {
                        return $next($request);
                    }
                };
            });
        }

        $this->app->tag(StorageAssetSource::class, 'cms.asset.sources');

        $this->app->bind(AssetController::class, function ($app) {
            $assetSources = iterator_to_array($app->tagged('cms.asset.sources'));
            return new AssetController($assetSources);
        });

        $this->app['router']->aliasMiddleware('permission', PermissionMiddleware::class);

        $this->app->tag(ContentPurgeProvider::class, 'content_purge_providers');
    }

    /**
     * @throws BindingResolutionException
     */
    public function boot(MenuManager $menuManager, ShortcodeRegistry $shortcodeRegistry, DashboardManager $dashboardManager): void
    {
        $this->bootstrapPackage();

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Publish configs
        $this->publishes([
            __DIR__.'/../config/auth.php' => config_path('auth.php'),
        ], 'core-cms-config');

        $this->publishes([
            __DIR__.'/../config/backup.php' => config_path('backup.php'),
        ], 'core-cms-config');

        $this->publishes([
            __DIR__.'/../config/lscache.php' => config_path('lscache.php'),
        ], 'core-cms-config');

        $this->publishes([
            __DIR__.'/../config/permission.php' => config_path('permission.php'),
        ], 'core-cms-config');

        // Publish views
        $this->publishes([
            __DIR__.'/resources/views/mail' => resource_path('views/vendor/mail'),
        ], 'core-cms-views');

        $this->publishes([
            __DIR__.'/resources/views/notifications' => resource_path('views/vendor/notifications'),
        ], 'core-cms-views');

        // Share options avec les vues UNIQUEMENT si la table existe
        if (!$this->app->runningInConsole()){
            $hasOptionsTable = Cache::rememberForever('schema_has_options', function () {
                return Schema::hasTable('options');
            });

            if ($hasOptionsTable) {
                $this->shareOptionsWithViews();
            }
        }

        // Blade directives
        Blade::directive('shortcode', function ($expression) {
            return "<?php echo app(" . ShortcodeParser::class . "::class)->parse($expression); ?>";
        });

        // Register shortcodes
        $shortcodeRegistry->register('button', new ButtonShortcode());
        $shortcodeRegistry->register('option', new OptionShortcode());
        $shortcodeRegistry->register('template', new TemplateShortcode());

        // Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                DiscoverAssetsCommand::class,
                BackupCmsCommand::class,
                BackupCommand::class,
                CleanupCommand::class
            ]);
        }

        // Events
        $this->app->events->listen(
            OptionUpdated::class,
            ClearOptionCache::class
        );

        if (!app()->environment('testing')) {
            Content::observe(ContentObserver::class);

            Event::listen(ContentSaved::class, function (ContentSaved $event) {
                if ($event->content->type === "template") {
                    Cache::store('database')->forget('options');
                }
            });
        }

        // Dashboard & Menu
        $dashboardManager->addWidget(TasksWidget::class);

        $menuManager->registerMenuItem('option', [
            'label' => trans_choice('core-cms::admin.option.value', 0),
            'icon' => 'option',
            'route' => 'admin.option.index',
            'can' => 'option-list'
        ]);

        $menuManager->registerMenuItem('users', [
            'label' => trans_choice('core-cms::admin.user.value', 0),
            'icon'  => 'users',
            'children' => [
                [
                    'label' => trans_choice('core-cms::admin.user.value', 0),
                    'icon'  => 'users',
                    'route' => 'admin.user.index',
                    'can'   => 'user-list'
                ],
                [
                    'label' => trans_choice('core-cms::admin.role.value', 0),
                    'icon'  => 'role',
                    'route' => 'admin.role.index',
                    'can'   => 'role-list'
                ]
            ]
        ]);

        $menuManager->registerMenuItem('content-management', [
            'label' => __('core-cms::admin.content.value'),
            'children' => [
                [
                    'label' => trans_choice('core-cms::admin.content.page.value', 0),
                    'icon'  => 'page',
                    'route' => 'admin.contents.index',
                    'params' => ['type' => 'page'],
                    'can'   => 'content-list'
                ],
                [
                    'label' => trans_choice('core-cms::admin.content.template.value', 0),
                    'icon'  => 'template',
                    'route' => 'admin.contents.index',
                    'params' => ['type' => 'template'],
                    'can'   => 'content-list'
                ]
            ]
        ]);

        $menuManager->registerMenuItem('taxonomies', [
            'label' => __('core-cms::admin.taxonomy'),
            'children' => [
                [
                    'label' => trans_choice('core-cms::admin.content.category.value', 0),
                    'icon'  => 'category',
                    'route' => 'admin.categories.index',
                    'can'   => 'category-list'
                ],
                [
                    'label' => trans_choice('core-cms::admin.content.tag.value', 0),
                    'icon'  => 'tag',
                    'route' => 'admin.tags.index',
                    'can'   => 'tag-list'
                ],
            ]
        ]);
    }

    /**
     * @throws BindingResolutionException
     */
    protected function shareOptionsWithViews(): void
    {
        View::composer(['core-cms::base', 'core-cms::front/page', 'portfolio-manager::front/portfolio.show', 'core-cms::auth/*', 'core-cms::profile/*', 'core-cms::admin.base', 'theme::*'], function ($view) {
            $cache = Cache::getFacadeRoot();
            $ret = $cache->remember('options_optimized', 3600, function () {
                $opts = Option::all();
                $data = [];
                $contentProvider = $this->app->make(ContentProviderInterface::class);
                $mediaProvider = $this->app->make(MediaProviderInterface::class);
                $theme = null;

                foreach ($opts as $option) {
                    $valueToStore = $option->value ?? '';

                    if (($option->type === 'content' || $option->type === 'template') && $option->value !== "") {
                        $valueToStore = $contentProvider->getContentById($option->value);
                    }

                    if ($option->type === 'theme') {
                        $theme = $option;
                    }

                    $data[$option->key] = $valueToStore;
                }

                $favicon = (isset($data['favicon']) && $data['favicon']) ? image_url($data['favicon'], 128) : null;
                $ogLogo = (isset($data['logo']) && $data['logo']) ? $mediaProvider->get($data['logo']) : null;
                $cacheBuster = isset($theme->updated_at) ? substr(md5(json_encode($theme->updated_at)), 0, 8) : 'dev';

                return [
                    "options"        => $data,
                    "theme"          => $theme,
                    "favicon"        => $favicon,
                    "openGraphLogo"  => $ogLogo,
                    "cacheBuster"    => $cacheBuster
                ];
            });

            $view->with('options', $ret['options']);
            $view->with('favicon', $ret['favicon']);
            $view->with('openGraphLogo', $ret['openGraphLogo']);
            $view->with('cacheBuster', $ret['cacheBuster']);
        });
    }
}
