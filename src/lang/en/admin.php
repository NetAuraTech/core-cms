<?php

return [
    'actions' => 'Actions',
    'add' => 'Add',
    'cache' => [
        'clear' => 'Clear the cache',
        'cleared' => 'The cache has been cleared.',
    ],
    'create' => 'Create',
    'dashboard' => 'Dashboard',
    'date' => 'Date',
    'delete' => [
        'confirm' => 'Do you really want to delete this content?',
        'unable' => 'Unable to delete',
        'value' => 'Delete',
    ],
    'edit' => 'Edit',
    'job' => [
        'date' => 'Date',
        'delete' => [
            'confirm' => 'Do you really want to delete the task?',
            'confirmed' => 'The task has been deleted.',
        ],
        'failed' => 'Failed tasks',
        'relaunch' => [
            'value' => 'Relaunch',
            'confirmed' => 'The task has been relaunched.',
        ],
        'message' => 'Message',
        'value' => '{0}Tasks|[1,1]task|[2,*]tasks',
    ],
    'manage' => 'Manage',
    'option' => [
        'created' => 'The option has been created.',
        'updated' => 'The option has been updated.',
        'deleted' => 'The option has been deleted.',
        'cannot_deleted' => 'The option cannot be deleted.',
        'category' => [
            'general' => 'General',
            'branding' => 'Branding',
            'content_settings' => 'Content Settings',
            'contact_emails' => 'Contact Emails',
            'social_media' => 'Social Media',
            'security' => 'Security',
            'schedule' => 'Schedules',
            'legals' => 'Legals',
            'seo' => 'Search Engine Optimization',
            'theme' => 'Theme',
            'custom' => 'Custom',
        ],
        'content' => [
            'article' => 'Article',
            'post' => 'Post',
        ],
        'value' => '{0}Options|[1,1]option|[2,*]options',
        'key' => 'Key',
        'keys' => [
            // General
            'site_name' => 'Site Name',
            'description' => 'Site Description',

            // Branding
            'favicon' => 'Favicon',
            'logo' => 'Logo',

            // Content Settings
            'header' => 'Header',
            'footer' => 'footer',
            'homepage' => 'Homepage',

            // Contact Emails
            'contact-email' => 'Contact Email',
            'noreply-email' => 'No-Reply Email',
            'sav-email' => 'Customer Service Email',

            // Social Media
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'linkedin' => 'LinkedIn',
            'twitter' => 'Twitter',
            'youtube' => 'YouTube',

            // Security
            'spam_words' => 'Spam Words',

            // SEO
            'address' => 'Address',
            'address_city' => 'City',
            'address_country' => 'Country',
            'address_latitude' => 'Latitude',
            'address_longitude' => 'Longitude',
            'address_postal-code' => 'Postal Code',
            'address_region' => 'Region',
            'area_served' => 'Service Area',
            'phone' => 'Phone',

            // Schedules
            'schedule_monday' => 'Monday',
            'schedule_tuesday' => 'Tuesday',
            'schedule_wednesday' => 'Wednesday',
            'schedule_thursday' => 'Thursday',
            'schedule_friday' => 'Friday',
            'schedule_saturday' => 'Saturday',
            'schedule_sunday' => 'Sunday',

            // Legals
            'privacy-policy' => 'Privacy Policy',
            'host-address' => 'Host address',
            'host-name' => 'Host name',
            'host-phone' => 'Host phone',
            'host-website' => 'Host website',
        ],
        'type' => [
            'value' => 'Type',
            'text' =>  'Text',
            'number' => 'Number',
            'boolean' => 'Boolean',
            'media' => 'Media',
            'content' => 'Content',
            'template' => 'Template',
        ],
        'schedule' => [
            'format_hint' => 'Format: HH:MM-HH:MM or HH:MM-HH:MM/HH:MM-HH:MM',
            'examples' => 'Examples:',
            'continuous' => 'Continuous day: 09:00-18:00',
            'with_break' => 'With lunch break: 09:00-12:00/14:00-18:00',
            'closed' => 'Closed: leave empty',
        ]
    ],
    'save' => 'Save',
    'send' => 'Send',
    'statut' => 'Status',
    'value' => 'Value',
    'permission' => 'Permissions',
    'role' => [
        'name' => 'Name',
        'value' => '{0}Roles|[1,1]role|[2,*]roles',
        'created' => 'The role has been created.',
        'updated' => 'The role has been updated.',
        'deleted' => 'The role has been deleted.',
    ],
    'user' => [
        'created' => 'The user has been created.',
        'updated' => 'The user has been updated.',
        'deleted' => 'The user has been deleted.',
        'email' => 'Email',
        'ban' => [
            'confirm' => 'Do you really want to ban this user?',
            'confirmed' => 'The user has been banned!',
            'value' => 'Ban the user',
        ],
        'confirm' => 'Confirm user account',
        'confirmed' => 'The user has been confirmed!',
        'impersonate' => [
            'info' => 'You are in impersonation mode.',
            'leave' => 'Leave',
            'value' => 'Take control of the account',
        ],
        'password' => [
            'confirm' => 'Confirm password',
            'value' => 'Password',
        ],
        'registration' => 'Registration',
        'unban' => [
            'confirm' => 'Do you really want to unban this user?',
            'confirmed' => 'The user has been unbanned!',
            'value' => 'Unban the user',
        ],
        'username' => 'Username',
        'value' => '{0}Users|[1,1]user|[2,*]users',
    ],
];