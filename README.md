# Core CMS Package

A comprehensive Laravel package providing the core foundation, services, and administration interface for content management systems.

## Description

This package provides a complete foundation for building content management systems on Laravel. It offers essential CMS functionality including user authentication, asset management, backup systems, captcha challenges, shortcode parsing, and an extensible admin interface. The package is designed to be modular and easily extendable.

## Features

- ✅ Complete authentication system with Laravel Breeze integration
- ✅ Dynamic asset management with Vite integration
- ✅ Comprehensive backup system with automatic cleanup
- ✅ Puzzle-based CAPTCHA security system
- ✅ Flexible options management with database caching
- ✅ Admin dashboard with customizable widgets
- ✅ Shortcode system for dynamic content
- ✅ Multi-language support with translation management
- ✅ Social authentication (OAuth integration)
- ✅ Permission-based access control with Spatie Permission
- ✅ Cache management with LiteSpeed support
- ✅ Content management (pages, templates, articles)
- ✅ Taxonomies (categories, tags)
- ✅ Media management integration
- ✅ Form handling with CAPTCHA protection
- ✅ SEO features (sitemap.xml, robots.txt)

## Requirements

- PHP ^8.1
- Laravel ^12.0

## Installation

### Via Composer (recommended)

```bash
composer require netauratech/core-cms
```

### Manual Installation

1. Clone the repository into your Laravel project
2. Add the dependency to your `composer.json`
3. Run `composer install`

## Configuration

### 1. Service Provider

The service provider is automatically registered thanks to Laravel's automatic discovery. If you want to register it manually, add it to `config/app.php`:

```php
'providers' => [
    // ...
    Netauratech\CoreCms\CoreCmsServiceProvider::class,
],
```

### 2. Publishing Configuration Files

Publish the configuration files to customize the package:

```bash
php artisan vendor:publish --tag=core-cms-config
```

This will publish:
- `config/core-cms.php` - Main CMS configuration
- `config/auth.php` - Authentication configuration
- `config/backup.php` - Backup system configuration
- `config/lscache.php` - LiteSpeed cache configuration
- `config/permission.php` - Permission system configuration

### 3. Configuration Files

#### core-cms.php

Main CMS configuration:

```php
return [
    'admin' => [
        'middleware' => [
            'auth',
            'web',
            'lscache:no-cache',
            ThemeMiddlewareInterface::class,
            BackupSessionForEsi::class,
            SmartCacheControlMiddleware::class
        ],
        'prefix' => 'admin',        // Admin URL prefix
        'name' => 'admin.',         // Route name prefix
    ],
    'media' => [
        'model' => null             // Custom media model (optional)
    ]
];
```

#### auth.php

Authentication configuration with remember me duration:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
        'remember' => 10080  // Remember me duration in minutes (7 days)
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => env('AUTH_MODEL', Netauratech\CoreCms\Models\User::class),
    ],
],
```

#### backup.php

Backup system configuration (powered by Spatie Backup):

```php
return [
    'backup' => [
        'name' => env('BACKUP_LOCATION_FOLDER', 'backup'),
        
        'source' => [
            'files' => [
                'include' => [base_path()],
                'exclude' => [
                    base_path('vendor'),
                    base_path('node_modules'),
                ],
            ],
            'databases' => [env('DB_CONNECTION', 'mysql')],
        ],
        
        'destination' => [
            'filename_prefix' => '',
            'disks' => ['local'],
        ],
    ],
    
    'cleanup' => [
        'default_strategy' => [
            'keep_all_backups_for_days' => 7,
            'keep_daily_backups_for_days' => 16,
            'keep_weekly_backups_for_weeks' => 8,
            'keep_monthly_backups_for_months' => 4,
            'keep_yearly_backups_for_years' => 2,
        ],
    ],
];
```

#### lscache.php

LiteSpeed Cache configuration:

```php
return [
    'esi' => env('LSCACHE_ESI_ENABLED', false),
    'default_ttl' => env('LSCACHE_DEFAULT_TTL', 0),
    'default_cacheability' => env('LSCACHE_DEFAULT_CACHEABILITY', 'no-cache'),
    'guest_only' => env('LSCACHE_GUEST_ONLY', false),
];
```

#### permission.php

Spatie Permission configuration:

```php
return [
    'models' => [
        'permission' => Spatie\Permission\Models\Permission::class,
        'role' => Spatie\Permission\Models\Role::class,
    ],
    
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],
    
    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'spatie.permission.cache',
        'store' => 'default',
    ],
];
```

### 4. Environment Variables

Add these variables to your `.env` file:

```env
# Authentication
AUTH_GUARD=web
AUTH_MODEL=Netauratech\CoreCms\Models\User

# Backup
BACKUP_LOCATION_FOLDER=backup
BACKUP_ARCHIVE_PASSWORD=null

# LiteSpeed Cache
LSCACHE_ESI_ENABLED=false
LSCACHE_DEFAULT_TTL=0
LSCACHE_DEFAULT_CACHEABILITY=no-cache
LSCACHE_GUEST_ONLY=false
```

### 5. Publishing Assets

Publish the package assets:

```bash
php artisan vendor:publish --tag=core-cms-assets
```

### 6. Database Setup

Run the migrations to create the necessary database tables:

```bash
php artisan migrate
```

### 7. Complete Installation

Run the installation command to set up the CMS:

```bash
php artisan cms:install
```

This command will:
- Run all package migrations
- Execute database seeders (creates admin user, roles, permissions, default content)
- Publish package assets

**Default Admin Credentials:**
- Email: `admin@example.com`
- Password: `password`

⚠️ **Important:** Change these credentials immediately after first login!

## Usage

### Asset Management

The package provides a comprehensive asset management system:

#### Registering Assets

In your service provider:

```php
use Netauratech\CoreCms\Services\AssetManager;

public function boot(AssetManager $assetManager)
{
    // Register JavaScript assets
    $assetManager->registerAppJs('path/to/app.js');
    $assetManager->registerAdminJs('path/to/admin.js');
    
    // Register CSS assets
    $assetManager->registerCss('path/to/styles.css');
    
    // Register translations
    $assetManager->registerTranslationPath('my-package', __DIR__.'/lang');
}
```

#### Discovering Assets

Generate dynamic asset entry points:

```bash
php artisan assets:discover
```

### Admin Interface

#### Dashboard Management

Add widgets to the admin dashboard:

```php
use Netauratech\CoreCms\Services\Admin\DashboardManager;

public function boot(DashboardManager $dashboardManager)
{
    $dashboardManager->addWidget(MyCustomWidget::class);
}
```

#### Menu Management

Register menu items in the admin interface:

```php
use Netauratech\CoreCms\Services\Admin\MenuManager;

public function boot(MenuManager $menuManager)
{
    $menuManager->registerMenuItem('my-item', [
        'label' => 'My Menu Item',
        'icon' => 'icon-name',
        'route' => 'admin.my-route',
        'can' => 'permission-name'
    ]);
}
```

### Shortcode System

#### Registering Shortcodes

Create and register custom shortcodes:

```php
use Netauratech\CoreCms\Services\Shortcode\ShortcodeRegistry;

public function boot(ShortcodeRegistry $shortcodeRegistry)
{
    $shortcodeRegistry->register('my-shortcode', function($attrs, $context) {
        $url = $attrs['url'] ?? '#';
        $text = $attrs['text'] ?? 'Default text';
        
        return "<a href=\"{$url}\">{$text}</a>";
    });
}
```

#### Using Shortcodes

In Blade templates:

```blade
@shortcode('[button url="/contact" text="Contact Us"]')
@shortcode('[my-shortcode url="/about" text="Learn More"]')
```

#### Built-in Shortcodes

The package includes several built-in shortcodes:

- `[button url="/path" type="primary" text="Click me"]` - Creates styled buttons
- `[option key="site_name"]` - Retrieves option values
- `[template id=3]` - Includes template content

### Backup Management

#### Programmatic Backups

```php
use Netauratech\CoreCms\Contracts\BackupProviderInterface;

$backupProvider = app(BackupProviderInterface::class);
$backupProvider->run(['--only-db' => true], ['--disable-notifications' => true]);
```

#### Artisan Commands

```bash
# Full backup
php artisan core-cms:backup

# Database only backup
php artisan core-cms:backup --only-db

# Disable notifications
php artisan core-cms:backup --disable-notifications
```

### CAPTCHA System

#### Generating Challenges

```php
// Generate a challenge key
$challengeKey = generate_challenge();
```

#### In Forms

```html
<puzzle-captcha
  name="{{ $name }}"
  width="350"
  height="200"
  piece-width="80"
  piece-height="50"
  src="{{ route('captcha.image', ['key' => $challengeKey]) }}"
>
  <input type="hidden" name="captcha-challenge" id="captcha-challenge" value="{{ $challengeKey }}">
  <input type="hidden" name="captcha-answer" id="captcha-answer">
</puzzle-captcha>
```

#### Verification

```php
use Netauratech\CoreCms\Contracts\ChallengeInterface;

$challenge = app(ChallengeInterface::class);
$isValid = $challenge->verify($request->challenge, $request->answer);

if ($isValid) {
    // Process form submission
} else {
    // Handle invalid captcha
}
```

### Content Management

The package provides a flexible content management system:

```php
use Netauratech\CoreCms\Contracts\ContentProviderInterface;

$contentProvider = app(ContentProviderInterface::class);

// Get published content
$pages = $contentProvider->getContents('page', 10);
$articles = $contentProvider->getContents('article', 20);

// Get content by slug
$page = $contentProvider->getContentBySlug('about-us');

// Get content by category
$categoryArticles = $contentProvider->getContentsByCategory('article', 'news', 10);
```

### Form Registry

Register custom form fields dynamically:

```php
use Netauratech\CoreCms\Form\FormRegistry;

public function boot(FormRegistry $formRegistry)
{
    $formRegistry->registerFormFields('content_form', [
        'custom_field' => [
            'type' => 'text',
            'label' => 'Custom Field',
        ],
    ]);
    
    $formRegistry->registerValidationRules('content_form', [
        'custom_field' => ['required', 'string', 'max:255'],
    ]);
}
```

### Helper Functions

The package provides several utility functions:

```php
// Generate SVG icons
echo icon('home');

// Check active menu state
echo menu_active(route('admin.dashboard'));

// Image handling
echo image_url($mediaId, 300, 200);
echo image_tag($mediaId, 'Alt text', 200);

// Generate CAPTCHA challenge
$key = generate_challenge();

// Time formatting
echo ago($carbonDate, 'Created');

// Exception handling
echo shortened_exception($exceptionMessage);
```

## Translations

### Publishing Translation Files

To customize the translation messages:

```bash
php artisan vendor:publish --tag=core-cms-translations
```

This will copy translation files to `lang/vendor/core-cms/` in your Laravel application.

### Available Translation Files

- `admin.php` - Admin interface translations
- `auth.php` - Authentication messages
- `core.php` - Core system messages
- `mail.php` - Email notifications

### Supported Languages

- English (en)
- French (fr)

## Customization

### Extending Content Providers

Implement the `ContentProviderInterface`:

```php
use Netauratech\CoreCms\Contracts\ContentProviderInterface;

class MyContentProvider implements ContentProviderInterface
{
    public function getContents(string $type, ?int $perPage): LengthAwarePaginator
    {
        // Return paginated content
    }
    
    public function getContentBySlug(string $slug): ?object
    {
        // Return content by slug
    }
    
    // ... implement other required methods
}
```

Register in your service provider:

```php
$this->app->bind(ContentProviderInterface::class, MyContentProvider::class);
```

### Custom Media Providers

Implement media management:

```php
use Netauratech\CoreCms\Contracts\MediaProviderInterface;

class MyMediaProvider implements MediaProviderInterface
{
    public function getImageUrl(string|int $id, ?int $width = null, ?int $height = null): string
    {
        // Generate image URL with optional resizing
    }
    
    // ... implement other methods
}
```

Register the provider:

```php
$this->app->bind(MediaProviderInterface::class, MyMediaProvider::class);
```

### Asset Sources

Create custom asset resolution:

```php
use Netauratech\CoreCms\Contracts\AssetSourceInterface;

class MyAssetSource implements AssetSourceInterface
{
    public function resolve(string $path, ?string $theme): BinaryFileResponse|Response|null
    {
        // Custom asset resolution logic
        return response()->file($resolvedPath);
    }
}
```

Tag your asset source:

```php
$this->app->tag(MyAssetSource::class, 'cms.asset.sources');
```

### Cache Purge Providers

Implement custom cache purge logic:

```php
use Netauratech\CoreCms\Contracts\PurgeUrlProviderInterface;
use Illuminate\Database\Eloquent\Model;

class MyPurgeProvider implements PurgeUrlProviderInterface
{
    public function getUrlsToPurge(Model $content): array
    {
        // Return URLs to purge when content is updated
        return ["/my-page/{$content->slug}"];
    }
    
    public function getAllManagedUrls(): array
    {
        // Return all URLs managed by this provider
        return ['/my-page/1', '/my-page/2'];
    }
}
```

Tag the provider:

```php
$this->app->tag(MyPurgeProvider::class, 'content_purge_providers');
```

## File Structure

```
src/
├── Console/                        # Artisan commands
│   ├── BackupCmsCommand.php
│   ├── BackupCommand.php
│   ├── CleanupCommand.php
│   ├── DiscoverAssetsCommand.php
│   └── InstallCommand.php
├── Contracts/                      # Service interfaces
│   ├── AssetSourceInterface.php
│   ├── BackupProviderInterface.php
│   ├── CacheServiceInterface.php
│   ├── ChallengeGeneratorInterface.php
│   ├── ChallengeInterface.php
│   ├── CommentableInterface.php
│   ├── ContentProviderInterface.php
│   ├── MediaProviderInterface.php
│   ├── PurgeUrlProviderInterface.php
│   └── ThemeMiddlewareInterface.php
├── Events/                         # Event classes
│   ├── CacheCleared.php
│   ├── ContentSaved.php
│   └── OptionUpdated.php
├── Form/                          # Form management
│   └── FormRegistry.php
├── Helpers/                       # Helper functions
│   └── helpers.php
├── Http/
│   ├── Controllers/               # Package controllers
│   │   ├── Admin/                # Admin controllers
│   │   ├── Api/                  # API controllers
│   │   └── Auth/                 # Authentication controllers
│   ├── Middlewares/              # HTTP middlewares
│   └── Requests/                 # Form requests
├── Jobs/                         # Queue jobs
│   └── PrecacheContent.php
├── Listeners/                    # Event listeners
│   └── ClearOptionCache.php
├── Mail/                        # Mailable classes
│   └── GenericFormMail.php
├── Models/                      # Eloquent models
│   ├── Category.php
│   ├── Content.php
│   ├── FailedJob.php
│   ├── Option.php
│   ├── Tag.php
│   └── User.php
├── Notifications/               # Notification classes
├── Observers/                   # Model observers
│   └── ContentObserver.php
├── Services/                    # Core services
│   ├── Admin/
│   │   ├── DashboardManager.php
│   │   └── MenuManager.php
│   ├── Captcha/
│   │   ├── PuzzleChallenge.php
│   │   └── PuzzleGenerator.php
│   ├── Shortcode/
│   │   ├── ButtonShortcode.php
│   │   ├── OptionShortcode.php
│   │   ├── ShortcodeParser.php
│   │   ├── ShortcodeRegistry.php
│   │   └── TemplateShortcode.php
│   ├── AbstractCmsServiceProvider.php
│   ├── AssetManager.php
│   ├── BackupProvider.php
│   ├── CacheService.php
│   ├── ContentProvider.php
│   ├── ContentPurgeProvider.php
│   ├── NullContentProvider.php
│   ├── NullMediaProvider.php
│   └── StorageAssetSource.php
├── Widgets/                     # Dashboard widgets
│   └── TasksWidget.php
├── resources/
│   ├── views/                   # Blade views
│   └── assets/                  # Static assets (images, icons)
├── lang/                        # Translation files
│   ├── en/
│   └── fr/
├── database/
│   ├── migrations/              # Database migrations
│   ├── seeders/                 # Database seeders
│   └── factories/               # Model factories
├── routes/                      # Package routes
│   ├── admin.php               # Admin routes
│   ├── api.php                 # API routes
│   ├── auth.php                # Authentication routes
│   └── web.php                 # Public routes
└── CoreCmsServiceProvider.php  # Main service provider
```

## API Routes

### CAPTCHA Endpoints

```
GET  /api/captcha/generate          # Generate new challenge
GET  /captcha/{key}                 # Get challenge image
POST /api/captcha/check             # Verify response
```

### Utility Endpoints

```
GET  /api/csrf                      # Get CSRF token
GET  /api/flash-messages            # Get flash messages
GET  /api/{type}/search             # Search taxonomies (auth required)
```

### Asset Endpoints

```
GET  /assets/{path}                 # Serve assets with caching
GET  /js/translations.js            # Frontend translations
```

### Admin Interface

Access the admin interface at `/admin` (configurable prefix).

## Artisan Commands

### Installation Commands
- `cms:install` - Complete CMS installation

### Asset Commands
- `assets:discover` - Discover and generate asset entry points

### Backup Commands
- `core-cms:backup` - Run backup with options
- `core-cms:backup-run` - Execute backup process
- `core-cms:backup-clean` - Clean old backups

## Events

The package dispatches several events you can listen to:

- `CacheCleared` - When cache is cleared
- `ContentSaved` - When content is saved
- `OptionUpdated` - When system options are updated

## Middleware

### Available Middleware

- `BackupSessionForEsi` - Backup flash messages for ESI compatibility
- `SmartCacheControlMiddleware` - Intelligent cache control based on page content
- `ThemeMiddlewareInterface` - Theme resolution middleware (can be implemented)

## Development

### Contributing

Contributions are welcome! Please:

1. Fork the project
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Testing

Run the package tests:

```bash
composer test
```

## License

This package is open-source software licensed under the [MIT license](LICENSE).

## Support

For support or questions:
- Email: contact@netauratech.fr
- Create an issue on GitHub

## Changelog

### v1.0.0
- Initial release
- Complete authentication system with social login
- Asset management with Vite integration
- Backup functionality with Spatie Backup
- Admin interface with dashboard and widgets
- Shortcode system with built-in shortcodes
- CAPTCHA integration with puzzle challenge
- Content management (pages, templates, articles)
- Taxonomy system (categories, tags)
- Permission system with Spatie Permission
- Form registry for dynamic form fields
- LiteSpeed cache support
- SEO features (sitemap, robots.txt)
- Multi-language support (EN, FR)

## Authors

- **NetAuraTech** - *Initial work* - [NetAuraTech](mailto:contact@netauratech.fr)

---

© 2025 NetAuraTech. All rights reserved.