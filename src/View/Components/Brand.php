<?php

declare(strict_types=1);

namespace Boa\Theme\View\Components;

use Boa\Theme\Services\ThemeManager;
use Boa\Theme\Theme;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Brand extends Component
{
    public function __construct(
        public string $size = 'md',
        public bool $showTagline = false,
        public ?string $name = null,
        public ?string $tagline = null,
        public string $orientation = 'horizontal',
    ) {}

    public function render(): View|Closure|string
    {
        /** @var Theme $theme */
        $theme = app(Theme::class);
        $manager = app(ThemeManager::class);
        $assets = $theme->assets();

        return view('boa-theme::components.brand', [
            'brandName' => $this->name ?? $theme->name(),
            'brandTagline' => $this->tagline ?? $theme->tagline(),
            'logoUrl' => $manager->assetUrl($assets['logo']),
            'logoDarkUrl' => $manager->assetUrl($assets['logo_dark']),
        ]);
    }
}
