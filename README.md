# BOA Theme

Customizable Laravel design system extracted from BOA PDF: brand mark, semantic color tokens, typography, and Tailwind CSS v4 utilities.

**Repository:** [github.com/p3iyaji/boa-theme](https://github.com/p3iyaji/boa-theme)

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13
- Tailwind CSS v4 in the host application

Current package version: **0.1.1** (adds Laravel 13 illuminate component support).

## Install

```bash
composer require boa/theme
```

If Composer cannot find the package yet (not on Packagist), add a VCS repository:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/p3iyaji/boa-theme.git"
    }
  ],
  "require": {
    "boa/theme": "^0.1"
  }
}
```

### Local development (path repo)

While developing the theme, symlink it from a Herd/Laravel app instead of installing from GitHub:

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

```bash
composer update boa/theme
```

Edits in the theme repo appear immediately in the host app via the symlink.

## Quick start

**1. Layout head** — inject CSS variables + Google Fonts:

```blade
<x-boa-theme::styles />
```

**2. App CSS** (Tailwind v4) — bridge tokens to utilities:

```css
@import 'tailwindcss';
@import '../../vendor/boa/theme/resources/css/theme.css';
```

**3. Brand mark / lockup:**

```blade
<x-boa-theme::mark size="lg" />
<x-boa-theme::brand size="md" :show-tagline="true" />
```

## Customize

Publish config:

```bash
php artisan vendor:publish --tag=boa-theme-config
```

```php
// config/boa-theme.php
'preset' => 'solar-stele', // solar-stele | midnight | coastal | ember | null
'colors' => [
    'brand' => '#0f766e',   // primary
    'accent' => '#d97706',  // CTAs / highlights
    'canvas' => '#78716c',  // page stone
],
'fonts' => [
    'sans' => 'Source Sans 3',
    'display' => 'Cinzel',
],
```

Or via `.env`:

```
BOA_THEME_PRESET=solar-stele
BOA_THEME_BRAND=#0f766e
BOA_THEME_ACCENT=#d97706
BOA_THEME_NAME="My App"
BOA_THEME_TAGLINE="Your library, illuminated"
```

## Semantic utilities

After importing `theme.css`, use:

| Token | Examples |
|-------|----------|
| brand | `bg-brand-950`, `text-brand-100`, `border-brand-800` |
| accent | `bg-accent-500`, `text-accent-700`, `ring-accent-500` |
| canvas | `bg-canvas-50`, `text-canvas-900` |
| danger / success | `bg-danger-50`, `text-success-700` |
| fonts | `font-sans`, `font-display` |
| radius | `rounded-boa-lg` |

Prefer semantic tokens over raw `teal-*` / `amber-*` so rebranding stays a config change.

## Package layout

Edit in this repository — not under `vendor/`:

| What | Where |
|------|--------|
| Colors / fonts / presets | `config/boa-theme.php`, `src/Support/Presets.php` |
| Palette logic | `src/Theme.php`, `src/Support/Color.php` |
| Tailwind bridge | `resources/css/theme.css` |
| Brand mark / lockup | `resources/views/components/*.blade.php` |
| Styles component | `src/View/Components/Styles.php` |

After config/PHP changes in the host app:

```bash
php artisan config:clear
php artisan view:clear
```

CSS changes need a Vite rebuild in the host app (`npm run dev` / `npm run build`).

## Accessibility

```php
use Boa\Theme\Facades\BoaTheme;

BoaTheme::accessibilityReport();
BoaTheme::contrast('brand', 50, 'brand', 950);
```

```bash
php artisan boa-theme:inspect
```

## PHP API

```php
BoaTheme::color('brand', 600);
BoaTheme::palette('accent');
BoaTheme::cssVariables();
```
