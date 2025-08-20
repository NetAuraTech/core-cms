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
- ✅ Permission-based access control
- ✅ Media management integration
- ✅ Cache management with LiteSpeed support

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

### 3. Publishing Assets

Publish the package assets:

```bash
php artisan vendor:publish --tag=core-cms-assets
```

### 4. Database Setup

Run the migrations to create the necessary database tables:

```bash
php artisan migrate
```

### 5. Complete Installation

Run the installation command to set up the CMS:

```bash
php artisan cms:install
```

This command will:
- Run all package migrations
- Execute database seeders
- Publish package assets

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

### Available Translation Keys

Edit the files in `lang/vendor/core-cms/{locale}/`:

- `admin.php` - Admin interface translations
- `auth.php` - Authentication messages
- `core.php` - Core system messages

### Multi-language Support

The package supports all languages configured in your Laravel application. Translation files are automatically loaded and cached for performance.

## Customization

### Extending Content Providers

Implement the `ContentProviderInterface`:

```php
use Netauratech\CoreCms\Contracts\ContentProviderInterface;

class MyContentProvider implements ContentProviderInterface
{
    public function getArticles(int $perPage = 10): LengthAwarePaginator
    {
        // Return paginated articles
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

## File Structure

```
src/
├── Console/                        # Artisan commands
│   ├── BackupCmsCommand.php
│   ├── DiscoverAssetsCommand.php
│   └── InstallCommand.php
├── Contracts/                      # Service interfaces
│   ├── AssetSourceInterface.php
│   ├── ContentProviderInterface.php
│   └── MediaProviderInterface.php
├── Http/
│   ├── Controllers/                # Package controllers
│   └── Requests/                   # Form requests
├── Models/                         # Eloquent models
│   ├── User.php
│   └── Option.php
├── Services/                       # Core services
│   ├── Admin/
│   ├── AssetManager.php
│   └── BackupProvider.php
├── resources/
│   ├── views/                      # Package views
│   └── assets/                     # Static assets
├── lang/                          # Translation files
├── database/
│   ├── migrations/                 # Database migrations
│   └── seeders/                    # Database seeders
├── routes/                        # Package routes
│   ├── admin.php
│   ├── auth.php
│   └── web.php
└── CoreCmsServiceProvider.php      # Main service provider
```

## API Routes

### CAPTCHA Endpoints

```
GET  /api/captcha/generate          # Generate new challenge
GET  /captcha/{key}                 # Get challenge image
POST /captcha/check                 # Verify response
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

- `LangLoaded` - When language files are loaded
- `OptionUpdated` - When system options are updated
- `ContentSaved` - When content is saved
- `CacheCleared` - When cache is cleared

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
- Complete authentication system
- Asset management
- Backup functionality
- Admin interface
- Shortcode system
- CAPTCHA integration

## Authors

- **NetAuraTech** - *Initial work* - [NetAuraTech](mailto:contact@netauratech.fr)

---

© 2025 NetAuraTech. All rights reserved.