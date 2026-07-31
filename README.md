# BOA Theme

Customizable Laravel design system — brand mark, semantic color tokens, typography, Tailwind CSS v4 utilities, and an admin **theme settings drawer**.

**Repository:** [github.com/p3iyaji/boa-theme](https://github.com/p3iyaji/boa-theme)

Current version: **0.2.1**

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13
- Authenticated users (for the settings panel)

---

## Important: how this package works

Composer installs package code into **`vendor/boa/theme`**.  
Those folders do **not** appear under your app’s `app/` or `src/`. That is normal.

What you *do* add to the host app:

1. Publish config + migrate (once)
2. Define an authorisation gate (required)
3. Include `<x-boa-theme::styles />` in your layout `<head>`
4. Include `<x-boa-theme::settings-link />` where admins can open the panel
5. Use **semantic theme classes** in your Blade/CSS so saved colours actually show

If your views only use hardcoded classes like `bg-blue-500` / `text-gray-800`, changing brand colours in the panel will **not** restyle those elements. Use tokens such as `bg-brand-600`, `text-canvas-950`, `bg-accent-500`, `font-sans`, `font-display`.

---

## Install (new project)

```bash
composer require boa/theme
```

If the package is not on Packagist yet, use VCS:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/p3iyaji/boa-theme.git"
    }
  ],
  "require": {
    "boa/theme": "^0.2"
  }
}
```

Then:

```bash
php artisan boa-theme:settings:install
php artisan migrate
php artisan storage:link
```

### 1. Authorisation (required)

The panel **denies access by default**. Without this gate, `/admin/theme` returns **403** and the settings link stays hidden.

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('manage-boa-theme-settings', function ($user) {
        return $user->is_admin === true; // your real admin check
    });
}
```

### 2. Layout head (required for theme to apply)

```blade
<head>
    {{-- If you use Tailwind CDN, keep styles AFTER the CDN script OR use Vite + theme.css --}}
    <x-boa-theme::styles />
</head>
```

This injects:

- CSS variables (`--boa-brand-600`, fonts, radius, …)
- A **runtime CSS bridge** so `bg-brand-*` / `text-accent-*` work even without rebuilding Tailwind
- Document base styles (page background, body font, link colour) when enabled

### 3. Open the settings drawer

```blade
{{-- Anywhere in an authenticated layout (sidebar, header, …) --}}
<x-boa-theme::settings-link />
```

This opens a **right-side drawer** on the current page (does not navigate away).  
Click **Done** / backdrop / Escape to close. **Save** applies tokens to the live page, then closes.

You can also visit `/admin/theme` directly.

### 4. Use semantic classes in your UI

```blade
<body class="bg-canvas-50 text-canvas-950 font-sans">
    <button class="bg-brand-800 text-on-brand rounded-boa-md px-4 py-2">Primary</button>
    <a class="text-link" href="#">Link</a>
</body>
```

---

## Upgrade (existing project already using boa/theme)

You do **not** uninstall. Update in place:

```bash
# Ensure constraint allows 0.2 (tags matter for VCS installs)
# "boa/theme": "^0.2"

composer clear-cache
composer update boa/theme -W

php artisan boa-theme:settings:install
php artisan migrate
php artisan storage:link
php artisan config:clear
php artisan view:clear
php artisan boa-theme:settings:clear-cache
```

Then complete the same host steps as a new install:

1. Define `manage-boa-theme-settings` gate  
2. Keep/add `<x-boa-theme::styles />`  
3. Add `<x-boa-theme::settings-link />`  
4. Prefer semantic `brand` / `accent` / `canvas` classes in views  

### VCS “nothing to install or update”?

Composer resolves **git tags**, not only `main`.  
If `v0.2.x` is not tagged on GitHub, `composer update` will stay on `0.1.x`.  
Require `"boa/theme": "^0.2"` and ensure a `v0.2.0` (or newer) tag exists.

---

## Tailwind setup options

### Option A — Vite + Tailwind v4 (recommended)

```css
@import 'tailwindcss';
@import '../../vendor/boa/theme/resources/css/theme.css';
```

Still include `<x-boa-theme::styles />` for live CSS variables from saved settings.

### Option B — Tailwind CDN

The package runtime bridge emits utility CSS for theme tokens, so `bg-brand-600` works without `@theme`.  
Hardcoded `bg-blue-*` / `bg-gray-*` still will not follow your brand settings.

---

## Settings panel behaviour

| Feature | Default |
|---------|---------|
| Right-side drawer | On (`settings.features.drawer`) |
| Apply document base styles | On (`settings.features.apply_document_styles`) |
| Logo / favicon uploads | On |
| Import / export JSON | On |
| Custom CSS / JS / head | **Off** (enable explicitly + authorise) |

Panel URL prefix: `/admin/theme` (configurable).

Publish tags:

```bash
php artisan vendor:publish --tag=boa-theme-config
php artisan vendor:publish --tag=boa-theme-migrations
php artisan vendor:publish --tag=boa-theme-views
php artisan vendor:publish --tag=boa-theme-css
php artisan vendor:publish --tag=boa-theme
```

---

## Troubleshooting: “I saved settings but the app looks the same”

1. **Confirm styles are loaded** — view source for `#boa-theme-vars` and `#boa-theme-bridge`.
2. **Confirm your markup uses tokens** — search for `bg-brand`, `text-canvas`, `bg-accent`. Hardcoded blues/greys will not change.
3. **Clear caches** — `php artisan config:clear && php artisan boa-theme:settings:clear-cache`
4. **Confirm gate + login** — otherwise you cannot open/save the panel.
5. **Confirm DB rows** — table `boa_theme_settings` should contain keys after save.
6. **Published old config** — merge new `settings` / `features` keys from the package config if you published before 0.2.

Quick check:

```bash
php artisan route:list --name=boa-theme
ls vendor/boa/theme/src
php artisan boa-theme:inspect
```

---

## PHP API

```php
use Boa\Theme\Facades\BoaTheme;
use Boa\Theme\Services\ThemeManager;

BoaTheme::color('brand', 600);
BoaTheme::cssVariables();
app(ThemeManager::class)->get('brand.name');
```

Blade:

```blade
@themeSetting('brand.name')
<x-boa-theme::brand size="md" :show-tagline="true" />
<x-boa-theme::mark size="lg" />
```

---

## Artisan

```bash
php artisan boa-theme:settings:install
php artisan boa-theme:settings:reset
php artisan boa-theme:settings:export
php artisan boa-theme:settings:clear-cache
php artisan boa-theme:inspect
```

---

## Security notes

- Settings routes require `auth` + package authorisation middleware.
- Custom CSS/JS/head are disabled by default.
- Uploads are validated and stored with generated filenames.
- CSS values and body classes are sanitised.
- Import accepts known keys only.
