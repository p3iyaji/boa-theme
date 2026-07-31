<?php

declare(strict_types=1);

namespace Boa\Theme\Support;

/**
 * Runtime CSS that applies theme tokens without requiring a Tailwind rebuild.
 * Works with Vite-built CSS and Tailwind CDN hosts.
 */
final class ThemeCssBridge
{
    private const STOPS = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 950];

    private const ROLES = ['brand', 'accent', 'canvas', 'danger', 'success', 'warning', 'info'];

    /**
     * Base document styles + utility classes mapped to CSS variables.
     */
    public static function stylesheet(bool $applyDocument = true): string
    {
        $chunks = [];

        if ($applyDocument) {
            $chunks[] = <<<'CSS'
html {
    color-scheme: light dark;
}
html.boa-theme-dark {
    color-scheme: dark;
}
body {
    font-family: var(--boa-font-sans);
    font-size: var(--boa-font-size, 16px);
    font-weight: var(--boa-font-body-weight, 400);
    line-height: var(--boa-line-height, 1.5);
    letter-spacing: var(--boa-letter-spacing, 0);
    background-color: var(--boa-canvas-50);
    color: var(--boa-canvas-950);
    transition: background-color var(--boa-motion, 200ms), color var(--boa-motion, 200ms);
}
h1, h2, h3, h4, h5, h6, .font-display {
    font-family: var(--boa-font-display);
    font-weight: var(--boa-font-heading-weight, 700);
}
a:not([class]) {
    color: var(--boa-link);
}
.font-sans { font-family: var(--boa-font-sans) !important; }
.font-display { font-family: var(--boa-font-display) !important; }
.rounded-boa-sm { border-radius: var(--boa-radius-sm) !important; }
.rounded-boa-md { border-radius: var(--boa-radius-md) !important; }
.rounded-boa-lg { border-radius: var(--boa-radius-lg) !important; }
.rounded-boa-xl { border-radius: var(--boa-radius-xl) !important; }
CSS;
        }

        $utils = [];

        foreach (self::ROLES as $role) {
            foreach (self::STOPS as $stop) {
                $var = "var(--boa-{$role}-{$stop})";
                $utils[] = ".bg-{$role}-{$stop}{background-color:{$var}}";
                $utils[] = ".text-{$role}-{$stop}{color:{$var}}";
                $utils[] = ".border-{$role}-{$stop}{border-color:{$var}}";
                $utils[] = ".ring-{$role}-{$stop}{--tw-ring-color:{$var}}";
            }

            $utils[] = ".bg-{$role}{background-color:var(--boa-{$role})}";
            $utils[] = ".text-{$role}{color:var(--boa-{$role})}";
            $utils[] = ".border-{$role}{border-color:var(--boa-{$role})}";
            $utils[] = ".text-on-{$role}{color:var(--boa-on-{$role})}";
            $utils[] = ".bg-on-{$role}{background-color:var(--boa-on-{$role})}";
        }

        $utils[] = '.text-link{color:var(--boa-link)}';
        $utils[] = '.bg-link{background-color:var(--boa-link)}';

        $chunks[] = implode('', $utils);

        return implode("\n", $chunks);
    }
}
