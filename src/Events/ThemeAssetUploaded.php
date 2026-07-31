<?php

declare(strict_types=1);

namespace Boa\Theme\Events;

final class ThemeAssetUploaded
{
    public function __construct(
        public readonly string $slot,
        public readonly string $path,
    ) {}
}
