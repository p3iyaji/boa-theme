# BOA Theme

Customizable Laravel design system extracted from BOA PDF: brand mark, semantic color tokens, typography, and Tailwind CSS v4 utilities.

## Install

```bash
composer require boa/theme
```

Path repository (local monorepo):

```json
{
  "repositories": [
    { "type": "path", "url": "packages/boa-theme", "options": { "symlink": true } }
  ],
  "require": {
    "boa/theme": "*"
  }
}
```

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

## Accessibility

```php
use Boa\Theme\Facades\BoaTheme;

BoaTheme::accessibilityReport(); // WCAG AA/AAA checks for key pairs
BoaTheme::contrast('brand', 50, 'brand', 950);
```

## PHP API

```php
BoaTheme::color('brand', 600);
BoaTheme::palette('accent');
BoaTheme::cssVariables();
```
