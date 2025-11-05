<?php

return [
    'actions' => 'Actions',
    'add' => 'Add',
    'cache' => [
        'clear' => 'Clear the cache',
        'cleared' => 'The cache has been cleared.',
    ],
    'create' => 'Create',

    'content' => [
        'edit' => 'Edit content',
        'created' => 'The content has been created.',
        'updated' => 'The content has been updated.',
        'deleted' => 'The content has been deleted.',
        'description' => 'Description',
        'name' => 'Name',
        'title' => 'Title',
        'value' => 'Content',
        'slug' => 'Slug',
        'article' => [
            'value' => '{0}Articles|[1,1]article|[2,*]articles'
        ],
        'category' => [
            'value' => '{0}Categories|[1,1]category|[2,*]categories',
            'created' => 'The category has been created.',
            'updated' => 'The category has been updated.',
            'deleted' => 'The category has been deleted.',
        ],
        'tag' => [
            'value' => '{0}Tags|[1,1]tag|[2,*]tags',
            'created' => 'The tag has been created.',
            'updated' => 'The tag has been updated.',
            'deleted' => 'The tag has been deleted.',
        ],
        'page' => [
            'value' => '{0}Pages|[1,1]page|[2,*]pages'
        ],
        'template' => [
            'value' => '{0}Templates|[1,1]template|[2,*]templates'
        ],
        'status' => [
            'archived' => 'Archived',
            'draft' => 'Draft',
            'published' => 'Published',
            'value' => 'Status',
        ],
        'published_at' => 'Published at'
    ],
    'editor' => [
        'category' => [
            'layout' => 'Layout',
            'template' => 'Template',
        ],
        'item' => [
            'delete' => [
                'confirmed' => 'The component has been deleted.',
            ],
        ],
        'parse' => [
            'error' => 'Unable to parse visual editor data.',
        ],
        'sidebar' => [
            'action' => [
                'copy' => [
                    'component' => 'Copy component',
                    'instructions' => 'You can paste the component on another page (CTRL + V).',
                    'page' => 'Copy page code',
                    'success' => 'The code has been copied.',
                ],
            ],
            'close' => 'Close',
            'component' => [
                'add' => 'Add a component',
                'all' => 'All components',
                'delete' => 'Delete a component',
                'search' => 'Search for a component',
                'unknown' => 'Unknown component',
            ],
            'empty' => 'You don’t have any content yet',
            'field' => [
                'htmltext' => [
                    'alignment' => [
                        'center' => 'Align center',
                        'justify' => 'Justify text',
                        'left' => 'Align left',
                        'right' => 'Align right',
                        'unset' => 'Reset text alignment',
                    ],
                    'bold' => 'Bold',
                    'color' => 'Color',
                    'formatting' => [
                        'remove' => 'Remove all formatting',
                    ],
                    'heading' => 'Heading :nr',
                    'highlight' => 'Highlight',
                    'italic' => 'Italic',
                    'link' => [
                        'unlink' => 'Remove link',
                        'value' => 'Link',
                    ],
                    'list' => [
                        'lift' => 'Move list item down',
                        'sink' => 'Move list item up',
                        'value' => 'List',
                    ],
                    'redo' => 'Redo',
                    'strike' => 'Strikethrough',
                    'underline' => 'Underline',
                    'undo' => 'Undo',
                    'video' => 'Video',
                ],
            ],
            'item' => 'Items',
            'mode' => [
                'responsive' => 'Responsive view',
            ],
            'tabs' => [
                'animation' => [
                    'delay' => 'Delay',
                    'general' => 'General',
                    'value' => 'Animations',
                    'view-transition-name' => 'Nom de transition de la vue',
                ],
                'appearance' => 'Appearance',
                'automatic-gallery' => [
                    'row' => [
                        'height' => 'Row height',
                    ],
                    'value' => 'Automatic gallery',
                ],
                'background' => [
                    'color' => 'Background color',
                    'image' => [
                        'position' => [
                            'bottom' => 'Bottom',
                            'center' => 'Center',
                            'left' => 'Left',
                            'right' => 'Right',
                            'top' => 'Top',
                            'x' => 'Position (X)',
                            'y' => 'Position (Y)',
                        ],
                        'repeat' => [
                            'no' => 'No repeat',
                            'value' => 'Repeat',
                        ],
                        'size' => [
                            'auto' => 'Original',
                            'contain' => 'Contain',
                            'cover' => 'Cover',
                            'value' => 'Image size',
                        ],
                        'opacity' => 'Opacity',
                        'value' => 'Background image',
                    ],
                    'value' => 'Background',
                ],
                'border' => [
                    'color' => 'Border color',
                    'radius' => [
                        'bottomleft' => 'Bottom Left',
                        'bottomright' => 'Bottom Right',
                        'topleft' => 'Top Left',
                        'topright' => 'Top Right',
                        'value' => 'Border radius',
                    ],
                    'line' => [
                        'blink' => 'Blink',
                        'line-through' => 'Line-through',
                        'underline' => 'Underline',
                        'overline' => 'Overline',
                        'value' => 'Border line',
                    ],
                    'style' => [
                        'dashed' => 'Dashed',
                        'dotted' => 'Dotted',
                        'solid' => 'Solid',
                        'wavy' => 'Wavy',
                        'value' => 'Border style',
                    ],
                ],
                'carousel' => [
                    'items-per-page' => 'Items per page',
                    'value' => 'Carousel',
                ],
                'contact' => [
                    'subject' => [
                        'option' => 'Option',
                        'value' => 'Subject',
                    ],
                    'value' => 'Contact form',
                ],
                'content' => 'Content',
                'ctas' => 'Appels à l\'action',
                'even-columns' => 'Columns',
                'form' => [
                    'value' => 'Form',
                    'sections' => [
                        'value' => 'Sections',
                        'visible' => 'Visible',
                    ],
                    'fields' => [
                        'value' => 'Fields',
                        'type' => 'Type',
                        'options' => 'Options',
                        'label' => 'Label',
                        'help' => 'Help'
                    ]
                ],
                'grid' => [
                    'gap' => 'Gap',
                    'item' => [
                        'size' => [
                            'min' => 'Minimum item size',
                        ],
                    ],
                    'value' => 'Grid',
                ],
                'header' => 'Header',
                'hero' => 'Hero',
                'media' => [
                    'alt' => 'Alt',
                    'width' => [
                        'help' => 'Leave empty for automatic width.',
                        'value' => 'Width',
                    ],
                    'value' => 'Media',
                ],
                'medias' => 'Medias',
                'label' => [
                    'help' => 'Leave empty to keep the page name.',
                    'value' => 'Label',
                ],
                'link' => [
                    'cta' => [
                        'type' => 'Button type',
                        'primary' => 'Primary',
                        'accent' => 'Accent',
                        'outline' => 'Outline'
                    ],
                    'home' => 'Homepage',
                    'blog' => 'Articles',
                    'login' => 'Log in',
                    'profile' => 'Profile',
                    'type' => [
                        'external' => 'External link',
                        'internal' => 'Internal link',
                        'value' => 'Link type',
                    ],
                    'url' => 'URL',
                    'value' => 'Link',
                ],
                'links' => 'List of links',
                'padding' => [
                    'block' => 'Vertical spacing',
                    'inline' => 'Horizontal spacing',
                ],
                'section' => 'Section',
                'card' => 'Card',
                'title' => [
                    'color' => 'Color',
                    'level' => 'Level',
                    'value' => 'Title',
                ],
            ],
            'template' => [
                'choose' => 'Choose a template',
                'use' => 'Use a template',
            ],
        ],
    ],
    'taxonomy' => 'Taxonomies',
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