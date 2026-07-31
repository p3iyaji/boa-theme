<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Active preset
    |--------------------------------------------------------------------------
    |
    | Built-in presets: solar-stele (default BOA branding), midnight, coastal,
    | ember. Set to null to use only the colors / fonts below.
    |
    */

    'preset' => env('BOA_THEME_PRESET', 'solar-stele'),

    /*
    |--------------------------------------------------------------------------
    | Brand copy
    |--------------------------------------------------------------------------
    */

    'name' => env('BOA_THEME_NAME', env('APP_NAME', 'BOA')),

    'tagline' => env('BOA_THEME_TAGLINE', 'Your library, illuminated'),

    /*
    |--------------------------------------------------------------------------
    | Seed colors (override preset)
    |--------------------------------------------------------------------------
    |
    | Provide hex seeds; the package expands each into a 50–950 Tailwind-like
    | scale. Leave a key null to keep the preset value.
    |
    | - brand: primary surfaces, nav, headings
    | - accent: CTAs, highlights, focus rings
    | - canvas: page backgrounds / stone family
    | - danger / success / warning / info: feedback
    | - link: interactive text (single hex, not a full scale)
    |
    */

    'colors' => [
        'brand' => env('BOA_THEME_BRAND'),
        'accent' => env('BOA_THEME_ACCENT'),
        'canvas' => env('BOA_THEME_CANVAS'),
        'danger' => env('BOA_THEME_DANGER'),
        'success' => env('BOA_THEME_SUCCESS'),
        'warning' => env('BOA_THEME_WARNING'),
        'info' => env('BOA_THEME_INFO'),
        'link' => env('BOA_THEME_LINK'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Typography
    |--------------------------------------------------------------------------
    |
    | Google Fonts families (or any CSS font-family stack). Empty string skips
    | the Google Fonts stylesheet link.
    |
    */

    'fonts' => [
        'sans' => env('BOA_THEME_FONT_SANS', 'Source Sans 3'),
        'display' => env('BOA_THEME_FONT_DISPLAY', 'Cinzel'),
        'google' => env('BOA_THEME_GOOGLE_FONTS', true),
        'base_size' => '16px',
        'heading_weight' => '700',
        'body_weight' => '400',
        'line_height' => '1.5',
        'letter_spacing' => '0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Radius & motion
    |--------------------------------------------------------------------------
    */

    'radius' => [
        'sm' => '0.5rem',
        'md' => '0.75rem',
        'lg' => '1rem',
        'xl' => '1.5rem',
    ],

    /*
    |--------------------------------------------------------------------------
    | Dark mode
    |--------------------------------------------------------------------------
    |
    | When true (or color_mode is dark), :root.dark / .dark overrides flip brand
    | surfaces for dark UI. Prefer settings.color_mode when using the panel.
    |
    */

    'dark_mode' => env('BOA_THEME_DARK_MODE', false),

    'color_mode' => env('BOA_THEME_COLOR_MODE', 'system'),

    /*
    |--------------------------------------------------------------------------
    | Appearance toggles
    |--------------------------------------------------------------------------
    */

    'appearance' => [
        'rounded' => true,
        'shadows' => true,
        'animations' => true,
        'density' => 'comfortable',
        'content_width' => 'full',
        'body_class' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Brand assets (paths relative to storage disk, or absolute URLs)
    |--------------------------------------------------------------------------
    */

    'assets' => [
        'logo' => null,
        'logo_dark' => null,
        'favicon' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom code (disabled by default — enable only for trusted admins)
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'css' => '',
        'javascript' => '',
        'head' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings panel
    |--------------------------------------------------------------------------
    */

    'settings' => [
        'enabled' => env('BOA_THEME_SETTINGS_ENABLED', true),

        'driver' => env('BOA_THEME_SETTINGS_DRIVER', 'database'),

        'cache' => env('BOA_THEME_SETTINGS_CACHE', true),
        'cache_key' => 'boa-theme.settings',
        'cache_ttl' => 3600,

        'route' => [
            'prefix' => 'admin/theme',
            'name' => 'boa-theme.settings.',
            'middleware' => ['web', 'auth', 'boa-theme.authorize'],
        ],

        'authorization' => [
            // Gate ability checked first when defined.
            'gate' => 'manage-boa-theme-settings',
            // Spatie-style permission name (optional).
            'permission' => 'manage theme settings',
            // Optional callable resolved from the container: fn ($user): bool
            'callback' => null,
            // Separate ability for custom CSS/JS/head.
            'custom_code_gate' => 'manage-boa-theme-custom-code',
        ],

        'storage' => [
            'disk' => env('BOA_THEME_ASSET_DISK', 'public'),
            'directory' => 'theme-assets',
        ],

        'features' => [
            'live_preview' => true,
            'custom_css' => false,
            'custom_javascript' => false,
            'custom_head' => false,
            'import_export' => true,
            'uploads' => true,
            // Apply fonts/background/link colours to the document + emit utility classes.
            'apply_document_styles' => true,
            // Right-side drawer panel (recommended). Set false for full-page only.
            'drawer' => true,
        ],

        /*
        | Fonts offered in the settings panel (controlled list).
        */
        'allowed_fonts' => [
            'Source Sans 3',
            'Cinzel',
            'Inter',
            'Roboto',
            'Open Sans',
            'Lato',
            'Merriweather',
            'Playfair Display',
            'IBM Plex Sans',
            'DM Sans',
            'Georgia',
            'system-ui',
        ],
    ],

];
