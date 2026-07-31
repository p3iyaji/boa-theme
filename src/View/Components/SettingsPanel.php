<?php

declare(strict_types=1);

namespace Boa\Theme\View\Components;

use Boa\Theme\Services\ThemeAuthorizer;
use Boa\Theme\Services\ThemeManager;
use Boa\Theme\Support\Presets;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SettingsPanel extends Component
{
    public function shouldRender(): bool
    {
        $manager = app(ThemeManager::class);

        if (! $manager->panelEnabled() || ! $manager->featureEnabled('drawer')) {
            return false;
        }

        return app(ThemeAuthorizer::class)->canManage(auth()->user());
    }

    public function render(): View|Closure|string
    {
        $manager = app(ThemeManager::class);
        $theme = $manager->makeTheme();
        $settings = $manager->all();

        return view('boa-theme::settings.drawer', [
            'theme' => $theme,
            'settings' => $settings,
            'presets' => array_keys(Presets::all()),
            'fonts' => config('boa-theme.settings.allowed_fonts', []),
            'features' => config('boa-theme.settings.features', []),
            'canCustomCode' => app(ThemeAuthorizer::class)->canManageCustomCode(auth()->user()),
            'logoUrl' => $manager->assetUrl($settings['brand.logo'] ?? null),
            'logoDarkUrl' => $manager->assetUrl($settings['brand.logo_dark'] ?? null),
            'faviconUrl' => $manager->assetUrl($settings['brand.favicon'] ?? null),
            'routePrefix' => config('boa-theme.settings.route.name', 'boa-theme.settings.'),
            'standalone' => false,
        ]);
    }
}
