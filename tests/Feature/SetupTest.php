<?php

namespace Netauratech\CoreCms\Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Tests\TestCase;

class SetupTest extends TestCase
{
    /** @test */
    public function test_database_is_properly_configured(): void
    {
        $this->assertNotNull(config('database.default'));
        $this->assertEquals('testing', config('database.default'));
    }

    /** @test */
    public function test_migrations_are_executed(): void
    {
        $this->assertTrue(Schema::hasTable('options'));
        $this->assertTrue(Schema::hasTable('users'));
        $this->assertTrue(Schema::hasTable('sessions'));
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
}