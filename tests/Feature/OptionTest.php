<?php

namespace Netauratech\CoreCms\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Tests\TestCase;

class OptionTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function test_it_can_create_an_option(): void
    {
        $option = Option::create([
            'key' => 'test_option',
            'value' => 'test_value',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $this->assertDatabaseHas('options', [
            'key' => 'test_option',
            'value' => 'test_value',
        ]);
    }

    /** @test */
    public function test_it_can_update_an_option(): void
    {
        $option = Option::create([
            'key' => 'test_option',
            'value' => 'original_value',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $option->update(['value' => 'updated_value']);

        $this->assertDatabaseHas('options', [
            'key' => 'test_option',
            'value' => 'updated_value',
        ]);
    }

    /** @test */
    public function test_it_can_delete_custom_option(): void
    {
        $option = Option::create([
            'key' => 'custom_option',
            'value' => 'test',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $option->delete();

        $this->assertDatabaseMissing('options', [
            'key' => 'custom_option',
        ]);
    }

    /** @test */
    public function test_it_uses_key_as_primary_key(): void
    {
        $option = Option::create([
            'key' => 'primary_test',
            'value' => 'test',
            'type' => 'text',
            'category' => 'custom',
        ]);

        $found = Option::find('primary_test');

        $this->assertNotNull($found);
        $this->assertEquals('primary_test', $found->key);
    }
}