<?php

declare(strict_types=1);

namespace Boa\Theme\Contracts;

interface ThemeSettingsRepository
{
    /**
     * @return array<string, mixed>
     */
    public function all(): array;

    public function get(string $key, mixed $default = null): mixed;

    public function set(string $key, mixed $value): void;

    /**
     * @param  array<string, mixed>  $settings
     */
    public function setMany(array $settings): void;

    public function forget(string $key): void;

    public function reset(): void;
}
