<?php

declare(strict_types=1);

namespace Boa\Theme\View\Components;

use Boa\Theme\Services\ThemeManager;
use Boa\Theme\Theme;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Styles extends Component
{
    public function __construct(
        public bool $fonts = true,
        public bool $custom = true,
        public bool $bridge = true,
    ) {}

    public function render(): View|Closure|string
    {
        /** @var Theme $theme */
        $theme = app(Theme::class);
        $manager = app(ThemeManager::class);
        $custom = $theme->custom();
        $assets = $theme->assets();
        $applyDocument = $this->bridge && $manager->featureEnabled('apply_document_styles');

        return view('boa-theme::components.styles', [
            'css' => $theme->cssVariables(),
            'bridgeCss' => $this->bridge ? $theme->cssBridge($applyDocument) : '',
            'fontsUrl' => $this->fonts ? $theme->googleFontsUrl() : null,
            'faviconUrl' => $manager->assetUrl($assets['favicon']),
            'bodyClass' => $theme->appearance()['body_class'],
            'colorMode' => $theme->colorMode(),
            'customCss' => $this->custom && $manager->featureEnabled('custom_css') ? $custom['css'] : '',
            'customHead' => $this->custom && $manager->featureEnabled('custom_head') ? $custom['head'] : '',
            'customJavascript' => $this->custom && $manager->featureEnabled('custom_javascript') ? $custom['javascript'] : '',
        ]);
    }
}
