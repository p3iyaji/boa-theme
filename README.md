# BOA Theme

Customizable Laravel design system — brand mark, semantic color tokens, typography, Tailwind CSS v4 utilities, and an admin **theme settings panel**.

**Repository:** [github.com/p3iyaji/boa-theme](https://github.com/p3iyaji/boa-theme)

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13
- Tailwind CSS v4 in the host application

Current package version: **0.2.0**

## Install

```bash
composer require boa/theme
php artisan boa-theme:settings:install
php artisan migrate
php artisan storage:link
```

### Authorisation (required)

The settings panel **denies access by default**. Define the gate (or a config callback):

```php
// AppServiceProvider::boot()
use Illuminate\Support\Facades\Gate;

Gate::define('manage-boa-theme-settings', function ($user) {
    return $user->is_admin === true; // your rule
});
```

Or in `config/boa-theme.php`:

```php
'settings' => [
    'authorization' => [
        'callback' => fn ($user) => $user->can('manage theme settings'),
    ],
],
```

Panel URL (default): `/admin/theme`

Navigation link (only renders when enabled + authorised):

```blade
<x-boa-theme::settings-link />
```

## Quick start (theme tokens)

**1. Layout head** — CSS variables, fonts, optional favicon / custom code:

```blade
<x-boa-theme::styles />
```

**2. App CSS** (Tailwind v4):

```css
@import 'tailwindcss';
@import '../../vendor/boa/theme/resources/css/theme.css';
```

**3. Brand mark / lockup:**

```blade
<x-boa-theme::mark size="lg" />
<x-boa-theme::brand size="md" :show-tagline="true" />
```

## Settings panel features

| Section | What you can change |
|---------|---------------------|
| General | Display label, colour mode, preset, rounded/shadows/animations, density, content width, body class |
| Brand | Name, tagline, logo / dark logo / favicon uploads, brand·accent·canvas·link·success·warning·danger·info colours |
| Typography | Controlled font list, base size, weights, line height, letter spacing |
| Components | Button/card radius, form control style (CSS variables) |
| Custom code | CSS / JS / head — **off by default**, separate authorisation |
| Preview | Near-live representative UI |
| Reset / import / export | Per-section reset, reset all, JSON import/export |

### Omitted layout options

Sidebar position, sticky header/footer, breadcrumbs, and similar shell options are **not** included. This package is a design-token / branding theme, not an application layout framework.

## Configuration

```bash
php artisan vendor:publish --tag=boa-theme-config
php artisan vendor:publish --tag=boa-theme-migrations
```

Publish tags: `boa-theme-config`, `boa-theme-migrations`, `boa-theme-views`, `boa-theme-css`, `boa-theme-translations`, or all via `boa-theme`.

Important `config/boa-theme.php` keys:

```php
'settings' => [
    'enabled' => true,
    'driver' => env('BOA_THEME_SETTINGS_DRIVER', 'database'), // database|array
    'cache' => true,
    'route' => [
        'prefix' => 'admin/theme',
        'name' => 'boa-theme.settings.',
        'middleware' => ['web', 'auth', 'boa-theme.authorize'],
    ],
    'storage' => [
        'disk' => 'public',
        'directory' => 'theme-assets',
    ],
    'features' => [
        'live_preview' => true,
        'custom_css' => false,
        'custom_javascript' => false,
        'custom_head' => false,
        'import_export' => true,
        'uploads' => true,
    ],
],
```

Existing config (`preset`, `colors`, `fonts`, `name`, …) remains the default source when no database settings exist.

## Reading settings in code

```php
use Boa\Theme\Facades\BoaTheme;
use Boa\Theme\Services\ThemeManager;

BoaTheme::color('brand', 600);
BoaTheme::cssVariables();

app(ThemeManager::class)->get('brand.colors.accent');
```

Blade:

```blade
@themeSetting('brand.name')
```

## Artisan commands

```bash
php artisan boa-theme:settings:install
php artisan boa-theme:settings:reset
php artisan boa-theme:settings:export
php artisan boa-theme:settings:clear-cache
php artisan boa-theme:inspect
```

## Upgrade from 0.1.x

1. Update the package to `^0.2`.
2. Run `php artisan boa-theme:settings:install` and migrate.
3. Register the authorisation gate or callback.
4. No breaking changes to `Theme`, Blade components (`styles`, `brand`, `mark`), or existing config keys — settings overlay config when present.

## Local path development

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../boa-theme",
      "options": { "symlink": true }
    }
  ],
  "require": {
    "boa/theme": "@dev"
  }
}
```

## Testing

```bash
composer install
composer test
```

## Security notes

- Every settings route uses `auth` + package authorisation middleware.
- Custom CSS/JS/head are disabled by default and require elevated access when enabled.
- Uploads are validated, stored on a configured disk with generated filenames, and replaced files are removed only inside the package directory.
- CSS variable output is sanitised; body classes are restricted to safe tokens.
- Import only accepts known setting keys.
