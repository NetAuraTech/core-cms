<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Netauratech\CoreCms\Models\Option;
use Netauratech\CoreCms\Models\User;

class CmsOptionsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!Schema::hasTable('options')) {
            return;
        }

        Option::firstOrCreate(
            ['key' => 'site_name'],
            [
                'value' => 'Mon site',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'description'],
            [
                'value' => 'La description de mon site',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'logo'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'image',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'favicon'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'image',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'theme'],
            [
                'value' => 'default',
                'used_by_cms' => true,
                'type' => 'theme',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'homepage'],
            [
                'value' => '3',
                'used_by_cms' => true,
                'type' => 'content',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'privacy-policy'],
            [
                'value' => '5',
                'used_by_cms' => true,
                'type' => 'content',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'contact-email'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'noreply-email'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'sav-email'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'spam_words'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'facebook'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'instagram'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'linkedin'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'twitter'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'youtube'],
            [
                'value' => '',
                'used_by_cms' => true,
                'type' => 'text',
            ]
        );
    }
}
