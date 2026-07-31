<?php

declare(strict_types=1);

use Boa\Theme\Contracts\ThemeSettingsRepository;
use Boa\Theme\Repositories\ArrayThemeSettingsRepository;
use Boa\Theme\Repositories\CachedThemeSettingsRepository;
use Boa\Theme\Services\ThemeManager;
use Boa\Theme\Support\ThemeCssVariables;
use Boa\Theme\Theme;
use Illuminate\Support\Facades\Cache;

it('returns defaults when no settings are saved', function (): void {
    $manager = app(ThemeManager::class);

    expect($manager->get('brand.name'))->toBe(config('boa-theme.name'))
        ->and($manager->get('general.color_mode'))->toBe('system');
});

it('lets saved values override defaults', function (): void {
    $manager = app(ThemeManager::class);
    $manager->update([
        'brand.name' => 'Acme Library',
        'brand.colors.brand' => '#123456',
    ]);

    expect($manager->get('brand.name'))->toBe('Acme Library');

    $theme = $manager->makeTheme();
    expect($theme->name())->toBe('Acme Library')
        ->and($theme->color('brand', 600))->toStartWith('#');
});

it('rejects unknown keys when updating', function (): void {
    $repo = app(ThemeSettingsRepository::class);
    $repo->set('not.a.real.key', 'nope');

    expect($repo->get('not.a.real.key'))->toBeNull();
});

it('casts booleans correctly in the array repository', function (): void {
    $repo = new ArrayThemeSettingsRepository;
    $repo->set('general.rounded', true);

    expect($repo->get('general.rounded'))->toBeTrue();
});

it('clears cache after updates', function (): void {
    config(['boa-theme.settings.cache' => true, 'boa-theme.settings.cache_key' => 'test-theme-settings']);

    $inner = new ArrayThemeSettingsRepository;
    $cached = new CachedThemeSettingsRepository($inner, Cache::store(), 'test-theme-settings', 60);
    app()->instance(ThemeSettingsRepository::class, $cached);

    $cached->set('brand.name', 'Cached Brand');
    expect($cached->get('brand.name'))->toBe('Cached Brand');

    $cached->set('brand.name', 'Updated Brand');
    expect($cached->get('brand.name'))->toBe('Updated Brand');
});

it('generates safe css variables', function (): void {
    $theme = new Theme([
        'preset' => 'solar-stele',
        'name' => 'BOA',
        'fonts' => ['sans' => 'Source Sans 3', 'display' => 'Cinzel', 'google' => true],
    ]);

    $css = $theme->cssVariables();

    expect($css)->toContain(':root')
        ->and($css)->toContain('--boa-brand-600:')
        ->and($css)->not->toContain('<script');
});

it('sanitizes unsafe css values', function (): void {
    expect(ThemeCssVariables::sanitizeValue('expression(alert(1))'))->toBeNull()
        ->and(ThemeCssVariables::sanitizeValue('#ff0000'))->toBe('#ff0000')
        ->and(ThemeCssVariables::sanitizeClassList('foo bar<script>'))->toBe('foo');
});

it('resets settings to defaults', function (): void {
    $manager = app(ThemeManager::class);
    $manager->update(['brand.name' => 'Temporary']);
    $manager->reset();

    expect(app(ThemeSettingsRepository::class)->all())->toBe([]);
});

it('exports valid json-ready payload and validates import keys', function (): void {
    $manager = app(ThemeManager::class);
    $manager->update(['brand.name' => 'Export Me', 'general.density' => 'compact']);

    $export = $manager->export();
    expect($export)->toHaveKeys(['version', 'package', 'settings'])
        ->and($export['settings']['brand.name'])->toBe('Export Me');

    $manager->reset();
    $manager->import([
        'settings' => [
            'brand.name' => 'Imported',
            'evil.key' => 'nope',
        ],
    ]);

    expect($manager->get('brand.name'))->toBe('Imported')
        ->and(app(ThemeSettingsRepository::class)->get('evil.key'))->toBeNull();
});

it('keeps existing theme api working without database settings', function (): void {
    $theme = app(Theme::class);

    expect($theme->color('brand', 600))->toBeString()
        ->and($theme->palette('accent'))->toBeArray()
        ->and($theme->cssVariables())->toContain('--boa-font-sans:');
});
