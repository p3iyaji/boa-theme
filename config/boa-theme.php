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
    | - danger / success: feedback
    |
    */

    'colors' => [
        'brand' => env('BOA_THEME_BRAND'),
        'accent' => env('BOA_THEME_ACCENT'),
        'canvas' => env('BOA_THEME_CANVAS'),
        'danger' => env('BOA_THEME_DANGER'),
        'success' => env('BOA_THEME_SUCCESS'),
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
    | When true, :root.dark / .dark overrides flip brand surfaces for dark UI.
    | Host apps should toggle the `dark` class on <html> or <body>.
    |
    */

    'dark_mode' => env('BOA_THEME_DARK_MODE', false),

];
