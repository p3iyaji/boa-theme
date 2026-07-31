<?php

declare(strict_types=1);

namespace Boa\Theme\Repositories;

use Boa\Theme\Contracts\ThemeSettingsRepository;
use Boa\Theme\Support\SettingDefinition;

/**
 * In-memory / config-only driver — useful for tests and hosts that skip the database.
 */
final class ArrayThemeSettingsRepository implements ThemeSettingsRepository
{
    /**
     * @param  array<string, mixed>  $settings
     */
    public function __construct(
        private array $settings = [],
    ) {}

    public function all(): array
    {
        return $this->settings;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $this->settings)
            ? $this->settings[$key]
            : $default;
    }

    public function set(string $key, mixed $value): void
    {
        if (! SettingDefinition::isKnown($key)) {
            return;
        }

        $this->settings[$key] = $value;
    }

    public function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            if (is_string($key)) {
                $this->set($key, $value);
            }
        }
    }

    public function forget(string $key): void
    {
        unset($this->settings[$key]);
    }

    public function reset(): void
    {
        $this->settings = [];
    }
}
