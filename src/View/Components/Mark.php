<?php

declare(strict_types=1);

namespace Boa\Theme\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Mark extends Component
{
    public string $gradientId;

    public function __construct(
        public string $size = 'md',
    ) {
        $this->gradientId = 'boa-mark-'.Str::lower(Str::random(8));
    }

    public function sizeClass(): string
    {
        return match ($this->size) {
            'xs' => 'h-7 w-7',
            'sm' => 'h-9 w-9',
            'lg' => 'h-16 w-16',
            'xl' => 'h-20 w-20',
            default => 'h-12 w-12',
        };
    }

    public function render(): View|Closure|string
    {
        return view('boa-theme::components.mark');
    }
}
