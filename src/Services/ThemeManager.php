<?php

declare(strict_types=1);

namespace Boa\Theme\Services;

use Boa\Theme\Contracts\ThemeSettingsRepository;
use Boa\Theme\Events\ThemeAssetUploaded;
use Boa\Theme\Events\ThemeSettingsImported;
use Boa\Theme\Events\ThemeSettingsReset;
use Boa\Theme\Events\ThemeSettingsUpdated;
use Boa\Theme\Events\ThemeSettingsUpdating;
use Boa\Theme\Repositories\CachedThemeSettingsRepository;
use Boa\Theme\Support\SettingDefinition;
use Boa\Theme\Support\ThemeCssVariables;
use Boa\Theme\Theme;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

final class ThemeManager
{
    public function __construct(
        private readonly ThemeSettingsRepository $repository,
        private readonly ConfigRepository $config,
        private readonly Dispatcher $events,
    ) {}

    public function get(string $key, mixed $default = null): mixed
    {
        $resolved = $this->all();

        return Arr::get($resolved, $key, $default);
    }

    /**
     * Flat dotted settings merged over package defaults + config.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        $defaults = $this->configDefaults();
        $saved = $this->repository->all();

        $merged = $defaults;

        foreach ($saved as $key => $value) {
            if (! SettingDefinition::isKnown($key)) {
                continue;
            }

            if ($value === null || $value === '') {
                continue;
            }

            $merged[$key] = $value;
        }

        return $merged;
    }

    /**
     * Config shape consumed by Theme (backward compatible).
     *
     * @return array<string, mixed>
     */
    public function themeConfig(): array
    {
        $base = $this->config->get('boa-theme', []);
        $settings = $this->all();

        $colors = array_merge(
            Arr::wrap($base['colors'] ?? []),
            array_filter([
                'brand' => $settings['brand.colors.brand'] ?? null,
                'accent' => $settings['brand.colors.accent'] ?? null,
                'canvas' => $settings['brand.colors.canvas'] ?? null,
                'danger' => $settings['brand.colors.danger'] ?? null,
                'success' => $settings['brand.colors.success'] ?? null,
                'warning' => $settings['brand.colors.warning'] ?? null,
                'info' => $settings['brand.colors.info'] ?? null,
                'link' => $settings['brand.colors.link'] ?? null,
            ], static fn ($v) => is_string($v) && $v !== ''),
        );

        $fonts = array_merge(
            Arr::wrap($base['fonts'] ?? []),
            array_filter([
                'sans' => $settings['typography.sans'] ?? null,
                'display' => $settings['typography.display'] ?? null,
                'base_size' => $settings['typography.base_size'] ?? null,
                'heading_weight' => $settings['typography.heading_weight'] ?? null,
                'body_weight' => $settings['typography.body_weight'] ?? null,
                'line_height' => $settings['typography.line_height'] ?? null,
                'letter_spacing' => $settings['typography.letter_spacing'] ?? null,
            ], static fn ($v) => $v !== null && $v !== ''),
        );

        $colorMode = (string) ($settings['general.color_mode'] ?? $base['color_mode'] ?? 'system');

        return array_merge($base, [
            'preset' => $settings['general.preset'] ?? $base['preset'] ?? null,
            'name' => $settings['brand.name'] ?? $settings['general.display_label'] ?? $base['name'] ?? 'BOA',
            'tagline' => $settings['brand.tagline'] ?? $base['tagline'] ?? '',
            'colors' => $colors,
            'fonts' => $fonts,
            'color_mode' => $colorMode,
            'dark_mode' => $colorMode === 'dark' || (bool) ($base['dark_mode'] ?? false),
            'appearance' => [
                'rounded' => (bool) ($settings['general.rounded'] ?? true),
                'shadows' => (bool) ($settings['general.shadows'] ?? true),
                'animations' => (bool) ($settings['general.animations'] ?? true),
                'density' => (string) ($settings['general.density'] ?? 'comfortable'),
                'content_width' => (string) ($settings['general.content_width'] ?? 'full'),
                'body_class' => ThemeCssVariables::sanitizeClassList((string) ($settings['general.body_class'] ?? '')),
            ],
            'assets' => [
                'logo' => $settings['brand.logo'] ?? ($base['assets']['logo'] ?? null),
                'logo_dark' => $settings['brand.logo_dark'] ?? ($base['assets']['logo_dark'] ?? null),
                'favicon' => $settings['brand.favicon'] ?? ($base['assets']['favicon'] ?? null),
            ],
            'components' => [
                'button_radius' => $settings['components.button_radius'] ?? 'md',
                'card_radius' => $settings['components.card_radius'] ?? 'lg',
                'form_style' => $settings['components.form_style'] ?? 'outline',
            ],
            'custom' => [
                'css' => $settings['custom.css'] ?? '',
                'javascript' => $settings['custom.javascript'] ?? '',
                'head' => $settings['custom.head'] ?? '',
            ],
        ]);
    }

    public function makeTheme(): Theme
    {
        return new Theme($this->themeConfig());
    }

    /**
     * @param  array<string, mixed>  $settings  dotted keys
     */
    public function update(array $settings): void
    {
        $filtered = [];

        foreach ($settings as $key => $value) {
            if (! is_string($key) || ! SettingDefinition::isKnown($key)) {
                continue;
            }

            $filtered[$key] = $value;
        }

        $this->events->dispatch(new ThemeSettingsUpdating($filtered));
        $this->repository->setMany($filtered);
        $this->events->dispatch(new ThemeSettingsUpdated($filtered));
        $this->forgetThemeSingleton();
    }

    public function reset(?string $group = null): void
    {
        if ($group === null) {
            $paths = $this->managedAssetPaths();
            $this->repository->reset();
            $this->deleteManagedFiles($paths);
            $this->events->dispatch(new ThemeSettingsReset(null));
            $this->forgetThemeSingleton();

            return;
        }

        $keys = SettingDefinition::keysForGroup($group);
        $paths = [];

        foreach ($keys as $key) {
            if (in_array($key, ['brand.logo', 'brand.logo_dark', 'brand.favicon'], true)) {
                $current = $this->repository->get($key);
                if (is_string($current) && $current !== '') {
                    $paths[] = $current;
                }
            }
            $this->repository->forget($key);
        }

        $this->deleteManagedFiles($paths);
        $this->events->dispatch(new ThemeSettingsReset($group));
        $this->forgetThemeSingleton();
    }

    /**
     * @return array<string, mixed>
     */
    public function export(): array
    {
        $export = [
            'version' => 1,
            'package' => 'boa/theme',
            'settings' => [],
        ];

        foreach ($this->repository->all() as $key => $value) {
            if (! SettingDefinition::isKnown($key)) {
                continue;
            }

            // Do not export binary asset paths as portable values — keep keys only if remote URL.
            if (in_array($key, ['brand.logo', 'brand.logo_dark', 'brand.favicon'], true)
                && is_string($value)
                && ! Str::startsWith($value, ['http://', 'https://'])) {
                continue;
            }

            $export['settings'][$key] = $value;
        }

        return $export;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function import(array $payload): void
    {
        $settings = $payload['settings'] ?? $payload;

        if (! is_array($settings)) {
            throw new InvalidArgumentException('Import payload must contain a settings object.');
        }

        $filtered = [];

        foreach ($settings as $key => $value) {
            if (! is_string($key) || ! SettingDefinition::isKnown($key)) {
                continue;
            }

            // Block custom code via import unless features enabled.
            if (str_starts_with($key, 'custom.') && ! $this->customCodeEnabled()) {
                continue;
            }

            $filtered[$key] = $value;
        }

        $this->repository->setMany($filtered);
        $this->events->dispatch(new ThemeSettingsImported(array_keys($filtered)));
        $this->forgetThemeSingleton();
    }

    public function storeUpload(UploadedFile $file, string $slot): string
    {
        if (! in_array($slot, ['logo', 'logo_dark', 'favicon'], true)) {
            throw new InvalidArgumentException("Unknown asset slot [{$slot}].");
        }

        $disk = (string) $this->config->get('boa-theme.settings.storage.disk', 'public');
        $directory = trim((string) $this->config->get('boa-theme.settings.storage.directory', 'theme-assets'), '/');

        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'png');
        $filename = $slot.'-'.Str::lower(Str::random(16)).'.'.$extension;

        $path = $file->storeAs($directory, $filename, $disk);

        if ($path === false) {
            throw new RuntimeException('Failed to store theme asset.');
        }

        $key = 'brand.'.$slot;
        $previous = $this->repository->get($key);

        $this->repository->set($key, $path);

        if (is_string($previous) && $previous !== '' && $previous !== $path) {
            $this->deleteManagedFiles([$previous]);
        }

        $this->events->dispatch(new ThemeAssetUploaded($slot, $path));
        $this->forgetThemeSingleton();

        return $path;
    }

    public function removeAsset(string $slot): void
    {
        if (! in_array($slot, ['logo', 'logo_dark', 'favicon'], true)) {
            throw new InvalidArgumentException("Unknown asset slot [{$slot}].");
        }

        $key = 'brand.'.$slot;
        $previous = $this->repository->get($key);
        $this->repository->forget($key);

        if (is_string($previous) && $previous !== '') {
            $this->deleteManagedFiles([$previous]);
        }

        $this->forgetThemeSingleton();
    }

    public function assetUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
            return $path;
        }

        $disk = (string) $this->config->get('boa-theme.settings.storage.disk', 'public');

        return Storage::disk($disk)->url($path);
    }

    public function clearCache(): void
    {
        if ($this->repository instanceof CachedThemeSettingsRepository) {
            $this->repository->forgetCache();
        }

        $this->forgetThemeSingleton();
    }

    public function panelEnabled(): bool
    {
        return (bool) $this->config->get('boa-theme.settings.enabled', true);
    }

    public function featureEnabled(string $feature): bool
    {
        $defaultsOn = [
            'live_preview',
            'import_export',
            'uploads',
            'apply_document_styles',
            'drawer',
        ];

        $default = in_array($feature, $defaultsOn, true);

        return (bool) $this->config->get("boa-theme.settings.features.{$feature}", $default);
    }

    public function customCodeEnabled(): bool
    {
        return $this->featureEnabled('custom_css')
            || $this->featureEnabled('custom_javascript')
            || $this->featureEnabled('custom_head');
    }

    /**
     * @return array<string, mixed>
     */
    private function configDefaults(): array
    {
        $base = $this->config->get('boa-theme', []);
        $defaults = SettingDefinition::defaults();

        $defaults['general.display_label'] = $base['name'] ?? 'BOA';
        $defaults['general.color_mode'] = $base['color_mode'] ?? 'system';
        $defaults['general.preset'] = $base['preset'] ?? 'solar-stele';
        $defaults['general.rounded'] = $base['appearance']['rounded'] ?? true;
        $defaults['general.shadows'] = $base['appearance']['shadows'] ?? true;
        $defaults['general.animations'] = $base['appearance']['animations'] ?? true;
        $defaults['general.density'] = $base['appearance']['density'] ?? 'comfortable';
        $defaults['general.content_width'] = $base['appearance']['content_width'] ?? 'full';
        $defaults['general.body_class'] = $base['appearance']['body_class'] ?? '';

        $defaults['brand.name'] = $base['name'] ?? 'BOA';
        $defaults['brand.tagline'] = $base['tagline'] ?? '';
        $defaults['brand.logo'] = $base['assets']['logo'] ?? null;
        $defaults['brand.logo_dark'] = $base['assets']['logo_dark'] ?? null;
        $defaults['brand.favicon'] = $base['assets']['favicon'] ?? null;

        foreach (['brand', 'accent', 'canvas', 'danger', 'success', 'warning', 'info', 'link'] as $role) {
            $defaults["brand.colors.{$role}"] = $base['colors'][$role] ?? null;
        }

        $defaults['typography.sans'] = $base['fonts']['sans'] ?? 'Source Sans 3';
        $defaults['typography.display'] = $base['fonts']['display'] ?? 'Cinzel';
        $defaults['typography.base_size'] = $base['fonts']['base_size'] ?? '16px';
        $defaults['typography.heading_weight'] = $base['fonts']['heading_weight'] ?? '700';
        $defaults['typography.body_weight'] = $base['fonts']['body_weight'] ?? '400';
        $defaults['typography.line_height'] = $base['fonts']['line_height'] ?? '1.5';
        $defaults['typography.letter_spacing'] = $base['fonts']['letter_spacing'] ?? '0';

        $defaults['custom.css'] = $base['custom']['css'] ?? '';
        $defaults['custom.javascript'] = $base['custom']['javascript'] ?? '';
        $defaults['custom.head'] = $base['custom']['head'] ?? '';

        return $defaults;
    }

    /**
     * @return list<string>
     */
    private function managedAssetPaths(): array
    {
        $paths = [];

        foreach (['brand.logo', 'brand.logo_dark', 'brand.favicon'] as $key) {
            $value = $this->repository->get($key);
            if (is_string($value) && $value !== '') {
                $paths[] = $value;
            }
        }

        return $paths;
    }

    /**
     * @param  list<string>  $paths
     */
    private function deleteManagedFiles(array $paths): void
    {
        $disk = (string) $this->config->get('boa-theme.settings.storage.disk', 'public');
        $directory = trim((string) $this->config->get('boa-theme.settings.storage.directory', 'theme-assets'), '/');
        $storage = Storage::disk($disk);

        foreach ($paths as $path) {
            if (Str::startsWith($path, ['http://', 'https://', '//', 'data:'])) {
                continue;
            }

            // Only delete files inside the package-managed directory.
            if (! Str::startsWith($path, $directory.'/') && $path !== $directory) {
                continue;
            }

            if ($storage->exists($path)) {
                $storage->delete($path);
            }
        }
    }

    private function forgetThemeSingleton(): void
    {
        if (app()->bound(Theme::class)) {
            app()->forgetInstance(Theme::class);
        }
    }
}
