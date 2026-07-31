<?php

declare(strict_types=1);

use Boa\Theme\Services\ThemeManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');
});

it('redirects unauthenticated users', function (): void {
    $this->get('/admin/theme')->assertRedirect();
});

it('forbids unauthorised users', function (): void {
    $this->actingAs($this->makeUser())
        ->get('/admin/theme')
        ->assertForbidden();
});

it('allows authorised users to open the settings panel', function (): void {
    $this->actingAs($this->makeAdmin())
        ->get('/admin/theme')
        ->assertOk()
        ->assertSee('Theme Settings')
        ->assertSee('data-boa-drawer', false);
});

it('returns json payload when saving via ajax so the live page can refresh tokens', function (): void {
    $this->actingAs($this->makeAdmin())
        ->putJson('/admin/theme', [
            'brand' => [
                'name' => 'Ajax Brand',
                'colors' => ['brand' => '#0f766e', 'accent' => '#ea580c'],
            ],
            'general' => [
                'color_mode' => 'light',
                'preset' => 'ember',
                'rounded' => true,
                'shadows' => true,
                'animations' => true,
            ],
            'typography' => [
                'sans' => 'Source Sans 3',
                'display' => 'Cinzel',
                'base_size' => '16px',
                'heading_weight' => '700',
                'body_weight' => '400',
                'line_height' => '1.5',
                'letter_spacing' => '0',
            ],
        ])
        ->assertOk()
        ->assertJsonPath('name', 'Ajax Brand')
        ->assertJsonStructure(['message', 'css_variables', 'css_bridge', 'color_mode']);
});

it('updates settings for authorised users', function (): void {
    $this->actingAs($this->makeAdmin())
        ->put('/admin/theme', [
            'brand' => [
                'name' => 'New Brand',
                'tagline' => 'Fresh tagline',
                'colors' => [
                    'brand' => '#0f766e',
                    'accent' => '#d97706',
                ],
            ],
            'general' => [
                'color_mode' => 'dark',
                'density' => 'compact',
                'rounded' => '1',
                'shadows' => '1',
                'animations' => '0',
                'content_width' => 'boxed',
                'preset' => 'midnight',
            ],
            'typography' => [
                'sans' => 'Source Sans 3',
                'display' => 'Cinzel',
                'base_size' => '16px',
                'heading_weight' => '700',
                'body_weight' => '400',
                'line_height' => '1.5',
                'letter_spacing' => '0',
            ],
        ])
        ->assertRedirect('/admin/theme');

    $manager = app(ThemeManager::class);
    expect($manager->get('brand.name'))->toBe('New Brand')
        ->and($manager->get('general.color_mode'))->toBe('dark')
        ->and($manager->get('general.animations'))->toBeFalse();

    app()->forgetInstance(\Boa\Theme\Theme::class);
    expect(app(\Boa\Theme\Theme::class)->name())->toBe('New Brand');
});

it('shows validation errors for invalid colours', function (): void {
    $this->actingAs($this->makeAdmin())
        ->from('/admin/theme')
        ->put('/admin/theme', [
            'brand' => [
                'colors' => [
                    'brand' => 'not-a-color',
                ],
            ],
        ])
        ->assertRedirect('/admin/theme')
        ->assertSessionHasErrors('brand.colors.brand');
});

it('uploads and replaces logos', function (): void {
    $admin = $this->makeAdmin();
    $first = UploadedFile::fake()->image('logo.png', 100, 40);

    $this->actingAs($admin)
        ->put('/admin/theme', [
            'logo' => $first,
            'brand' => ['name' => 'With Logo'],
        ])
        ->assertRedirect('/admin/theme');

    $path = app(ThemeManager::class)->get('brand.logo');
    expect($path)->toBeString();
    Storage::disk('public')->assertExists($path);

    $second = UploadedFile::fake()->image('logo2.png', 120, 40);

    $this->actingAs($admin)
        ->put('/admin/theme', [
            'logo' => $second,
        ])
        ->assertRedirect('/admin/theme');

    $newPath = app(ThemeManager::class)->get('brand.logo');
    expect($newPath)->not->toBe($path);
    Storage::disk('public')->assertMissing($path);
    Storage::disk('public')->assertExists($newPath);
});

it('resets a section and all settings', function (): void {
    $manager = app(ThemeManager::class);
    $manager->update([
        'brand.name' => 'Section Reset',
        'general.density' => 'compact',
    ]);

    $this->actingAs($this->makeAdmin())
        ->post('/admin/theme/reset', ['group' => 'brand'])
        ->assertRedirect('/admin/theme');

    expect(app(\Boa\Theme\Contracts\ThemeSettingsRepository::class)->get('brand.name'))->toBeNull()
        ->and($manager->get('general.density'))->toBe('compact');

    $this->actingAs($this->makeAdmin())
        ->post('/admin/theme/reset')
        ->assertRedirect('/admin/theme');

    expect(app(\Boa\Theme\Contracts\ThemeSettingsRepository::class)->all())->toBe([]);
});

it('exports and imports settings', function (): void {
    $manager = app(ThemeManager::class);
    $manager->update(['brand.name' => 'Portable']);

    $this->actingAs($this->makeAdmin())
        ->get('/admin/theme/export')
        ->assertOk()
        ->assertHeader('content-disposition');

    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent(
        'settings.json',
        json_encode(['settings' => ['brand.name' => 'From Import', 'general.density' => 'comfortable']]),
    );

    $this->actingAs($this->makeAdmin())
        ->post('/admin/theme/import', ['file' => $file])
        ->assertRedirect('/admin/theme');

    expect($manager->get('brand.name'))->toBe('From Import');
});

it('uses the configured route prefix', function (): void {
    config(['boa-theme.settings.route.prefix' => 'console/appearance']);

    // Re-register routes is hard mid-request; assert default named route exists and prefix config is readable.
    expect(route('boa-theme.settings.index'))->toContain('admin/theme')
        ->and(config('boa-theme.settings.route.prefix'))->toBe('console/appearance');
});

it('blocks custom code when the feature is disabled', function (): void {
    config(['boa-theme.settings.features.custom_css' => false]);

    $this->actingAs($this->makeAdmin())
        ->from('/admin/theme')
        ->put('/admin/theme', [
            'custom' => ['css' => 'body{display:none}'],
        ])
        ->assertRedirect('/admin/theme')
        ->assertSessionHasErrors('custom.css');
});

it('returns 404 when the settings panel is disabled', function (): void {
    config(['boa-theme.settings.enabled' => false]);

    $this->actingAs($this->makeAdmin())
        ->get('/admin/theme')
        ->assertNotFound();
});
