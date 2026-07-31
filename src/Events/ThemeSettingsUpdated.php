<?php

declare(strict_types=1);

namespace Boa\Theme\Events;

final class ThemeSettingsUpdated
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public function __construct(
        public readonly array $settings,
    ) {}
}
