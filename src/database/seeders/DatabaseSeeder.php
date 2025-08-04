<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Netauratech\CoreCms\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'username' => 'Test User',
            'email' => 'test@example.com',
        ]);

        if (class_exists(RolesAndPermissionsSeeder::class)) {
            $this->call(RolesAndPermissionsSeeder::class);
        }

        if (class_exists(CmsOptionsSeeder::class)) {
            $this->call(CmsOptionsSeeder::class);
        }
    }
}
