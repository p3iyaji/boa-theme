<?php

declare(strict_types=1);

namespace Boa\Theme\View\Components;

use Boa\Theme\Services\ThemeAuthorizer;
use Boa\Theme\Services\ThemeManager;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SettingsLink extends Component
{
    public function __construct(
        public string $label = 'Theme Settings',
    ) {}

    public function shouldRender(): bool
    {
        $manager = app(ThemeManager::class);

        if (! $manager->panelEnabled()) {
            return false;
        }

        if (! app(ThemeAuthorizer::class)->canManage(auth()->user())) {
            return false;
        }

        $routeName = config('boa-theme.settings.route.name', 'boa-theme.settings.').'index';

        return app('router')->has($routeName);
    }

    public function render(): View|Closure|string
    {
        $drawer = app(ThemeManager::class)->featureEnabled('drawer');

        return view('boa-theme::components.settings-link', [
            'href' => route(config('boa-theme.settings.route.name', 'boa-theme.settings.').'index'),
            'label' => $this->label,
            'drawer' => $drawer,
        ]);
    }
}
