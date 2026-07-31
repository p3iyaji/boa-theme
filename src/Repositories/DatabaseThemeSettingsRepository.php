<?php

declare(strict_types=1);

namespace Boa\Theme\Repositories;

use Boa\Theme\Contracts\ThemeSettingsRepository;
use Boa\Theme\Models\ThemeSetting;
use Boa\Theme\Support\SettingDefinition;
use Illuminate\Support\Facades\Schema;
use Throwable;

final class DatabaseThemeSettingsRepository implements ThemeSettingsRepository
{
    private bool $tableChecked = false;

    private bool $tableExists = false;

    public function all(): array
    {
        if (! $this->ready()) {
            return [];
        }

        try {
            $rows = ThemeSetting::query()->get(['key', 'value', 'type']);
        } catch (Throwable) {
            return [];
        }

        $settings = [];

        foreach ($rows as $row) {
            $settings[$row->key] = $this->castFromStorage($row->value, $row->type);
        }

        return $settings;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if (! $this->ready()) {
            return $default;
        }

        try {
            $row = ThemeSetting::query()->where('key', $key)->first();
        } catch (Throwable) {
            return $default;
        }

        if ($row === null) {
            return $default;
        }

        return $this->castFromStorage($row->value, $row->type);
    }

    public function set(string $key, mixed $value): void
    {
        if (! SettingDefinition::isKnown($key) || ! $this->ready()) {
            return;
        }

        $definition = SettingDefinition::definitions()[$key];
        $type = $definition['type'];
        $group = $definition['group'];
        $isPublic = (bool) ($definition['public'] ?? true);

        ThemeSetting::query()->updateOrCreate(
            ['key' => $key],
            [
                'value' => $this->castToStorage($value, $type),
                'type' => $type,
                'group' => $group,
                'is_public' => $isPublic,
            ],
        );
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
        if (! $this->ready()) {
            return;
        }

        ThemeSetting::query()->where('key', $key)->delete();
    }

    public function reset(): void
    {
        if (! $this->ready()) {
            return;
        }

        ThemeSetting::query()->delete();
    }

    private function ready(): bool
    {
        if ($this->tableChecked) {
            return $this->tableExists;
        }

        $this->tableChecked = true;

        try {
            $this->tableExists = Schema::hasTable('boa_theme_settings');
        } catch (Throwable) {
            $this->tableExists = false;
        }

        return $this->tableExists;
    }

    private function castFromStorage(?string $value, string $type): mixed
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            SettingDefinition::TYPE_BOOLEAN => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            SettingDefinition::TYPE_INTEGER => (int) $value,
            SettingDefinition::TYPE_JSON => json_decode($value, true),
            default => $value,
        };
    }

    private function castToStorage(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            SettingDefinition::TYPE_BOOLEAN => $value ? '1' : '0',
            SettingDefinition::TYPE_INTEGER => (string) (int) $value,
            SettingDefinition::TYPE_JSON => json_encode($value, JSON_THROW_ON_ERROR),
            default => (string) $value,
        };
    }
}
