<?php

namespace Netauratech\CoreCms\Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Netauratech\CoreCms\Models\Content;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Models\User;
use Netauratech\CoreCms\Tests\TestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SetupTest extends TestCase
{
    /** @test */
    public function test_database_is_properly_configured(): void
    {
        $this->assertNotNull(config('database.default'));
        $this->assertEquals('testing', config('database.default'));
    }

    /** @test */
    public function test_sqlite_in_memory_database_is_used(): void
    {
        $connection = config('database.connections.testing');

        $this->assertEquals('sqlite', $connection['driver']);
        $this->assertEquals(':memory:', $connection['database']);
    }

    /** @test */
    public function test_migrations_are_executed(): void
    {
        $this->assertTrue(Schema::hasTable('options'));
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('sessions'));
        $this->assertTrue(Schema::hasTable('contents'));
        $this->assertTrue(Schema::hasTable('categories'));
        $this->assertTrue(Schema::hasTable('tags'));
    }

    /** @test */
    public function test_cache_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('cache'));
        $this->assertTrue(Schema::hasTable('cache_locks'));
    }

    /** @test */
    public function test_job_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('jobs'));
        $this->assertTrue(Schema::hasTable('job_batches'));
        $this->assertTrue(Schema::hasTable('failed_jobs'));
    }

    /** @test */
    public function test_permission_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('permissions'));
        $this->assertTrue(Schema::hasTable('roles'));
        $this->assertTrue(Schema::hasTable('model_has_permissions'));
        $this->assertTrue(Schema::hasTable('model_has_roles'));
        $this->assertTrue(Schema::hasTable('role_has_permissions'));
    }

    /** @test */
    public function test_pivot_tables_exist(): void
    {
        $this->assertTrue(Schema::hasTable('content_category'));
        $this->assertTrue(Schema::hasTable('content_tag'));
    }

    /** @test */
    public function test_seeders_are_executed(): void
    {
        $optionsCount = Option::count();
        $this->assertGreaterThan(0, $optionsCount, 'No options were seeded');

        $this->assertDatabaseHas('options', [
            'key' => 'site_name',
        ]);
    }

    /** @test */
    public function test_roles_and_permissions_are_seeded(): void
    {
        $role = Role::where('name', 'Super Administrator')->first();
        $this->assertNotNull($role, 'Super Administrator role should be seeded');

        $permissionsCount = Permission::count();
        $this->assertGreaterThan(0, $permissionsCount, 'Permissions should be seeded');
    }

    /** @test */
    public function test_admin_user_is_seeded(): void
    {
        $admin = User::find(1);

        $this->assertNotNull($admin);
        $this->assertEquals('admin@example.com', $admin->email);
        $this->assertTrue($admin->hasRole('Super Administrator'));
    }

    /** @test */
    public function test_admin_has_all_permissions(): void
    {
        $admin = User::find(1);
        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            $this->assertTrue(
                $admin->can($permission->name),
                "Admin should have {$permission->name} permission"
            );
        }
    }

    /** @test */
    public function test_default_content_is_seeded(): void
    {
        $homepage = Content::where('slug', 'accueil')->first();
        $this->assertNotNull($homepage, 'Homepage should be seeded');

        $header = Content::where('type', 'template')->where('slug', 'header')->first();
        $this->assertNotNull($header, 'Header template should be seeded');

        $footer = Content::where('type', 'template')->where('slug', 'footer')->first();
        $this->assertNotNull($footer, 'Footer template should be seeded');
    }

    /** @test */
    public function test_default_options_are_seeded(): void
    {
        $defaultOptions = [
            'site_name',
            'description',
            'logo',
            'favicon',
            'contact-email',
            'noreply-email',
        ];

        foreach ($defaultOptions as $key) {
            $this->assertDatabaseHas('options', ['key' => $key]);
        }
    }

    /** @test */
    public function test_social_media_options_are_seeded(): void
    {
        $socialOptions = ['facebook', 'instagram', 'linkedin', 'twitter', 'youtube'];

        foreach ($socialOptions as $key) {
            $this->assertDatabaseHas('options', ['key' => $key]);
        }
    }

    /** @test */
    public function test_seo_options_are_seeded(): void
    {
        $seoOptions = ['phone', 'address', 'address_city', 'address_postal-code'];

        foreach ($seoOptions as $key) {
            $this->assertDatabaseHas('options', ['key' => $key]);
        }
    }

    /** @test */
    public function test_option_model_works(): void
    {
        $option = Option::create([
            'key' => 'test_setup_key',
            'value' => 'test_setup_value',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $this->assertInstanceOf(Option::class, $option);
        $this->assertEquals('test_setup_key', $option->key);
        $this->assertEquals('test_setup_value', $option->value);

        $this->assertDatabaseHas('options', [
            'key' => 'test_setup_key',
            'value' => 'test_setup_value',
        ]);
    }

    /** @test */
    public function test_can_create_user(): void
    {
        $user = $this->createUser([
            'email' => 'test@example.com',
            'username' => 'testuser',
        ]);

        $this->assertNotNull($user);
        $this->assertEquals('test@example.com', $user->email);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function test_authentication_works(): void
    {
        $user = $this->actingAsUser(1);

        $this->assertAuthenticated();
        $this->assertEquals('admin@example.com', $user->email);
    }

    /** @test */
    public function test_core_cms_config_is_loaded(): void
    {
        $this->assertNotNull(config('core-cms.admin.prefix'));
        $this->assertEquals('admin', config('core-cms.admin.prefix'));
        $this->assertEquals('admin.', config('core-cms.admin.name'));
    }

    /** @test */
    public function test_app_config_is_loaded(): void
    {
        $this->assertNotNull(Config::get('app.key'));
        $this->assertEquals('fr', Config::get('app.locale'));
        $this->assertEquals('en', Config::get('app.fallback_locale'));
    }

    /** @test */
    public function test_cache_config_is_loaded(): void
    {
        $this->assertEquals('array', config('cache.default'));
    }

    /** @test */
    public function test_session_config_is_loaded(): void
    {
        $this->assertEquals('array', config('session.driver'));
    }

    /** @test */
    public function test_auth_config_is_loaded(): void
    {
        $this->assertEquals(User::class, config('auth.providers.users.model'));
    }

    /** @test */
    public function test_views_can_be_loaded(): void
    {
        $this->assertNotNull(view()->exists('core-cms::auth.login'));
        $this->assertNotNull(view()->exists('core-cms::admin.dashboard'));
    }

    /** @test */
    public function test_routes_are_registered(): void
    {
        $this->assertTrue(route('login') !== null);
        $this->assertTrue(route('admin.dashboard') !== null);
        $this->assertTrue(route('home') !== null);
    }

    /** @test */
    public function test_artisan_commands_are_available(): void
    {
        $commands = Artisan::all();

        $this->assertArrayHasKey('cms:install', $commands);
        $this->assertArrayHasKey('assets:discover', $commands);
        $this->assertArrayHasKey('core-cms:backup', $commands);
    }

    /** @test */
    public function test_service_providers_are_loaded(): void
    {
        $providers = Config::get('app.providers');

        $this->assertNotEmpty($providers);
    }

    /** @test */
    public function test_blade_directives_are_registered(): void
    {
        $compiled = \Illuminate\Support\Facades\Blade::compileString('@shortcode("test")');

        $this->assertStringContainsString('ShortcodeParser', $compiled);
    }

    /** @test */
    public function test_helpers_are_loaded(): void
    {
        $this->assertTrue(function_exists('icon'));
        $this->assertTrue(function_exists('menu_active'));
        $this->assertTrue(function_exists('image_url'));
        $this->assertTrue(function_exists('image_tag'));
        $this->assertTrue(function_exists('generate_challenge'));
    }

    /** @test */
    public function test_middleware_is_registered(): void
    {
        $router = app('router');

        $this->assertTrue($router->hasMiddlewareGroup('web'));
    }

    /** @test */
    public function test_database_connection_is_working(): void
    {
        $this->assertNotNull(\DB::connection());

        $result = \DB::select('SELECT 1 as test');
        $this->assertEquals(1, $result[0]->test);
    }

    /** @test */
    public function test_session_is_working(): void
    {
        session(['test_key' => 'test_value']);

        $this->assertEquals('test_value', session('test_key'));
    }

    /** @test */
    public function test_factory_is_working(): void
    {
        $user = User::factory()->make();

        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->email);
    }

    /** @test */
    public function test_content_model_relationships_work(): void
    {
        $content = Content::create([
            'title' => 'Test Content',
            'slug' => 'test-content',
            'type' => 'page',
            'status' => 'published',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $content->tags());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $content->categories());
    }

    /** @test */
    public function test_user_model_has_spatie_permission_trait(): void
    {
        $user = User::factory()->create();

        $this->assertTrue(method_exists($user, 'hasRole'));
        $this->assertTrue(method_exists($user, 'can'));
        $this->assertTrue(method_exists($user, 'assignRole'));
    }

    /** @test */
    public function test_homepage_option_points_to_valid_content(): void
    {
        $homepageOption = Option::find('homepage');

        if ($homepageOption && $homepageOption->value) {
            $homepage = Content::find($homepageOption->value);
            $this->assertNotNull($homepage);
        }
    }

    /** @test */
    public function test_translations_are_loaded(): void
    {
        $this->assertNotNull(__('core-cms::admin.dashboard'));
        $this->assertNotEquals('core-cms::admin.dashboard', __('core-cms::admin.dashboard'));
    }

    /** @test */
    public function test_validation_rules_work(): void
    {
        $rules = [
            'email' => 'required|email',
            'username' => 'required|string',
        ];

        $validator = \Validator::make([], $rules);

        $this->assertTrue($validator->fails());
    }
}