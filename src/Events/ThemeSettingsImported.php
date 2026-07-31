<?php

declare(strict_types=1);

namespace Boa\Theme\Events;

final class ThemeSettingsImported
{
    /**
     * @param  list<string>  $keys
     */
    public function __construct(
        public readonly array $keys,
    ) {}
}
