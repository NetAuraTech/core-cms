<?php

namespace Netauratech\CoreCms\Tests;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Intervention\Image\ImageServiceProvider;
use Litespeed\LSCache\LSCacheServiceProvider;
use Netauratech\CoreCms\Contracts\ContentProviderInterface;
use Netauratech\CoreCms\Contracts\ThemeMiddlewareInterface;
use Netauratech\CoreCms\CoreCmsServiceProvider;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Models\User;
use Netauratech\CoreCms\Tests\Stubs\NullThemeMiddleware;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Backup\BackupServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(ThemeMiddlewareInterface::class, NullThemeMiddleware::class);

        $this->artisan('migrate', ['--database' => 'testing']);

        $this->seed(DatabaseSeeder::class);

        $this->shareDefaultViewVariables();
    }

    /**
     * Loads the necessary service providers
     * ⚠️ IMPORTANT ORDER: External dependencies BEFORE your package
     */
    protected function getPackageProviders($app): array
    {
        return [
            BackupServiceProvider::class,
            PermissionServiceProvider::class,
            LSCacheServiceProvider::class,
            ImageServiceProvider::class,

            CoreCmsServiceProvider::class,
        ];
    }

    /**
     * Configures the test environment
     */
    protected function getEnvironmentSetUp($app): void
    {
        // In-memory database
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Cache configuration for testing
        $app['config']->set('cache.default', 'array');
        $app['config']->set('cache.stores.database', [
            'driver' => 'array',
        ]);

        // Session configuration
        $app['config']->set('session.driver', 'array');

        // Auth configuration
        $app['config']->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => User::class,
        ]);

        // App configuration
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        $app['config']->set('app.locale', 'fr');
        $app['config']->set('app.fallback_locale', 'en');

        // CMS configuration
        $app['config']->set('core-cms.admin.prefix', 'admin');
        $app['config']->set('core-cms.admin.name', 'admin.');
        $app['config']->set('core-cms.admin.middleware', ['web']);

        // Backup configuration (to avoid errors)
        $app['config']->set('backup.backup.name', 'test');
        $app['config']->set('backup.backup.source.files.include', []);
        $app['config']->set('backup.backup.source.databases', []);
        $app['config']->set('backup.backup.destination.disks', ['local']);

        // Configuring lscache
        $app['config']->set('lscache.esi', false);
        $app['config']->set('lscache.default_ttl', 0);

        // Disable Vite checks for tests
        $app['config']->set('app.asset_url', '');

        $app->usePublicPath(base_path('public'));

        Blade::directive('vite', function ($expression) {
            return "<?php echo ''; ?>";
        });
    }

    /**
     * Shares the necessary variables with all views
     */
    protected function shareDefaultViewVariables(): void
    {
        $contentProvider = $this->app->make(ContentProviderInterface::class);
        $options = [];

        foreach (Option::all() as $option) {
            $valueToStore = $option->value ?? '';

            if (($option->type === 'content' || $option->type === 'template') && $option->value !== "") {
                $contentItem = $contentProvider->getContentById($option->value);
                $valueToStore = $contentItem;
            }
            if ($option->type === 'theme') {
                $theme = $option;
            }
            $options[$option->key] = $valueToStore;
        }

        view()->share([
            'options' => $options,
            'favicon' => '',
            'openGraphLogo' => null,
            'cacheBuster' => 'test',
        ]);
    }

    /**
     * Creates a test user
     */
    protected function createUser(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    /**
     * find a user
     */
    protected function getUser(int $id): User
    {
        return User::find($id);
    }

    /**
     * Authenticates a user
     */
    protected function actingAsUser(int $id): User
    {
        $user = $this->getUser($id);
        $this->actingAs($user);
        return $user;
    }
}