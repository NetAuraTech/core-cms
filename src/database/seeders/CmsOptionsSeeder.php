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
                'category' => 'general',
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'description'],
            [
                'value' => 'La description de mon site',
                'category' => 'general',
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'logo'],
            [
                'value' => '',
                'category' => 'branding',
                'type' => 'image',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'favicon'],
            [
                'value' => '',
                'category' => 'branding',
                'type' => 'image',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'theme'],
            [
                'value' => 'default',
                'category' => 'theme',
                'type' => 'theme',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'homepage'],
            [
                'value' => '3',
                'category' => 'content_settings',
                'type' => 'content',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'privacy-policy'],
            [
                'value' => '5',
                'category' => 'content_settings',
                'type' => 'content',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'contact-email'],
            [
                'value' => '',
                'category' => 'contact_emails',
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'noreply-email'],
            [
                'value' => '',
                'category' => 'contact_emails',
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'sav-email'],
            [
                'value' => '',
                'category' => 'contact_emails',
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'spam_words'],
            [
                'value' => '',
                'category' => 'security',
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'facebook'],
            [
                'value' => '',
                'category' => 'social_media',
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'instagram'],
            [
                'value' => '',
                'category' => 'social_media',
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'linkedin'],
            [
                'value' => '',
                'category' => 'social_media',
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'twitter'],
            [
                'value' => '',
                'category' => 'social_media',
                'type' => 'text',
            ]
        );

        Option::firstOrCreate(
            ['key' => 'youtube'],
            [
                'value' => '',
                'category' => 'social_media',
                'type' => 'text',
            ]
        );
    }
}
