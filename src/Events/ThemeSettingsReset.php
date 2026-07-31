<?php

declare(strict_types=1);

namespace Boa\Theme\Events;

final class ThemeSettingsReset
{
    public function __construct(
        public readonly ?string $group,
    ) {}
}
