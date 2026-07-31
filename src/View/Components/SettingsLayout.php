<?php

declare(strict_types=1);

namespace Boa\Theme\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SettingsLayout extends Component
{
    public function __construct(
        public string $title = 'Theme Settings',
    ) {}

    public function render(): View|Closure|string
    {
        return view('boa-theme::settings.layout');
    }
}
