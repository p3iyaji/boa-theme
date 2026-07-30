<?php

declare(strict_types=1);

namespace Boa\Theme\View\Components;

use Boa\Theme\Theme;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Styles extends Component
{
    public function __construct(
        public bool $fonts = true,
    ) {}

    public function render(): View|Closure|string
    {
        /** @var Theme $theme */
        $theme = app(Theme::class);

        return view('boa-theme::components.styles', [
            'css' => $theme->cssVariables(),
            'fontsUrl' => $this->fonts ? $theme->googleFontsUrl() : null,
        ]);
    }
}
