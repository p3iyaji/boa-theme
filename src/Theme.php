<?php

declare(strict_types=1);

namespace Boa\Theme;

use Boa\Theme\Support\Color;
use Boa\Theme\Support\PaletteGenerator;
use Boa\Theme\Support\Presets;
use Boa\Theme\Support\ThemeCssBridge;
use Boa\Theme\Support\ThemeCssVariables;
use Illuminate\Support\Arr;

final class Theme
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $palettes = [];

    private ?string $linkColor = null;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        private array $config,
        private readonly PaletteGenerator $generator = new PaletteGenerator,
    ) {
        $this->resolvePalettes();
    }

    public function name(): string
    {
        return (string) ($this->config['name'] ?? 'BOA');
    }

    public function tagline(): string
    {
        return (string) ($this->config['tagline'] ?? '');
    }

    public function preset(): ?string
    {
        $preset = $this->config['preset'] ?? null;

        return is_string($preset) && $preset !== '' ? $preset : null;
    }

    public function darkMode(): bool
    {
        return $this->colorMode() === 'dark' || (bool) ($this->config['dark_mode'] ?? false);
    }

    public function colorMode(): string
    {
        $mode = (string) ($this->config['color_mode'] ?? 'system');

        return in_array($mode, ['light', 'dark', 'system'], true) ? $mode : 'system';
    }

    /**
     * @return array{sans: string, display: string, google: bool, base_size: string, heading_weight: string, body_weight: string, line_height: string, letter_spacing: string}
     */
    public function fonts(): array
    {
        $presetFonts = $this->preset() ? (Presets::get($this->preset())['fonts'] ?? []) : [];

        return [
            'sans' => (string) ($this->config['fonts']['sans'] ?? $presetFonts['sans'] ?? 'Source Sans 3'),
            'display' => (string) ($this->config['fonts']['display'] ?? $presetFonts['display'] ?? 'Cinzel'),
            'google' => (bool) ($this->config['fonts']['google'] ?? true),
            'base_size' => (string) ($this->config['fonts']['base_size'] ?? '16px'),
            'heading_weight' => (string) ($this->config['fonts']['heading_weight'] ?? '700'),
            'body_weight' => (string) ($this->config['fonts']['body_weight'] ?? '400'),
            'line_height' => (string) ($this->config['fonts']['line_height'] ?? '1.5'),
            'letter_spacing' => (string) ($this->config['fonts']['letter_spacing'] ?? '0'),
        ];
    }

    /**
     * @return array{sm: string, md: string, lg: string, xl: string}
     */
    public function radius(): array
    {
        $rounded = (bool) ($this->config['appearance']['rounded'] ?? true);

        if (! $rounded) {
            return [
                'sm' => '0',
                'md' => '0',
                'lg' => '0',
                'xl' => '0',
            ];
        }

        return [
            'sm' => (string) ($this->config['radius']['sm'] ?? '0.5rem'),
            'md' => (string) ($this->config['radius']['md'] ?? '0.75rem'),
            'lg' => (string) ($this->config['radius']['lg'] ?? '1rem'),
            'xl' => (string) ($this->config['radius']['xl'] ?? '1.5rem'),
        ];
    }

    /**
     * @return array{rounded: bool, shadows: bool, animations: bool, density: string, content_width: string, body_class: string}
     */
    public function appearance(): array
    {
        return [
            'rounded' => (bool) ($this->config['appearance']['rounded'] ?? true),
            'shadows' => (bool) ($this->config['appearance']['shadows'] ?? true),
            'animations' => (bool) ($this->config['appearance']['animations'] ?? true),
            'density' => (string) ($this->config['appearance']['density'] ?? 'comfortable'),
            'content_width' => (string) ($this->config['appearance']['content_width'] ?? 'full'),
            'body_class' => ThemeCssVariables::sanitizeClassList((string) ($this->config['appearance']['body_class'] ?? '')),
        ];
    }

    /**
     * @return array{logo: ?string, logo_dark: ?string, favicon: ?string}
     */
    public function assets(): array
    {
        return [
            'logo' => $this->nullableString($this->config['assets']['logo'] ?? null),
            'logo_dark' => $this->nullableString($this->config['assets']['logo_dark'] ?? null),
            'favicon' => $this->nullableString($this->config['assets']['favicon'] ?? null),
        ];
    }

    /**
     * @return array{button_radius: string, card_radius: string, form_style: string}
     */
    public function components(): array
    {
        return [
            'button_radius' => (string) ($this->config['components']['button_radius'] ?? 'md'),
            'card_radius' => (string) ($this->config['components']['card_radius'] ?? 'lg'),
            'form_style' => (string) ($this->config['components']['form_style'] ?? 'outline'),
        ];
    }

    /**
     * @return array{css: string, javascript: string, head: string}
     */
    public function custom(): array
    {
        return [
            'css' => (string) ($this->config['custom']['css'] ?? ''),
            'javascript' => (string) ($this->config['custom']['javascript'] ?? ''),
            'head' => (string) ($this->config['custom']['head'] ?? ''),
        ];
    }

    /**
     * @return array<int, string>
     */
    public function palette(string $role): array
    {
        return $this->palettes[$role] ?? [];
    }

    public function color(string $role, int $stop = 600): string
    {
        if ($role === 'link' && $this->linkColor !== null) {
            return $this->linkColor;
        }

        return $this->palettes[$role][$stop] ?? '#000000';
    }

    /**
     * Semantic pairs: readable ink on a surface stop.
     */
    public function onColor(string $role, int $stop = 600): string
    {
        return Color::fromHex($this->color($role, $stop))->contrastingInk()->toHex();
    }

    /**
     * WCAG contrast ratio between two resolved theme colors.
     */
    public function contrast(string $foregroundRole, int $foregroundStop, string $backgroundRole, int $backgroundStop): float
    {
        return Color::fromHex($this->color($foregroundRole, $foregroundStop))
            ->contrastRatio(Color::fromHex($this->color($backgroundRole, $backgroundStop)));
    }

    /**
     * @return list<array{pair: string, ratio: float, aa: bool, aaa: bool}>
     */
    public function accessibilityReport(): array
    {
        $pairs = [
            ['brand', 50, 'brand', 950],
            ['accent', 950, 'accent', 300],
            ['brand', 950, 'canvas', 50],
            ['accent', 800, 'canvas', 50],
        ];

        $report = [];

        foreach ($pairs as [$fgRole, $fgStop, $bgRole, $bgStop]) {
            $ratio = $this->contrast($fgRole, $fgStop, $bgRole, $bgStop);
            $report[] = [
                'pair' => "{$fgRole}-{$fgStop} on {$bgRole}-{$bgStop}",
                'ratio' => round($ratio, 2),
                'aa' => $ratio >= 4.5,
                'aaa' => $ratio >= 7.0,
            ];
        }

        return $report;
    }

    /**
     * CSS custom properties for :root (and optional .dark).
     */
    public function cssVariables(): string
    {
        $variables = [];
        $fonts = $this->fonts();
        $radius = $this->radius();
        $appearance = $this->appearance();
        $components = $this->components();

        $variables['boa-font-sans'] = self::fontStack($fonts['sans']);
        $variables['boa-font-display'] = self::fontStack($fonts['display'], serif: true);
        $variables['boa-font-size'] = $fonts['base_size'];
        $variables['boa-font-heading-weight'] = $fonts['heading_weight'];
        $variables['boa-font-body-weight'] = $fonts['body_weight'];
        $variables['boa-line-height'] = $fonts['line_height'];
        $variables['boa-letter-spacing'] = $fonts['letter_spacing'];

        foreach ($radius as $key => $value) {
            $variables["boa-radius-{$key}"] = $value;
        }

        $variables['boa-shadow'] = $appearance['shadows']
            ? '0 10px 30px -12px rgb(0 0 0 / 0.25)'
            : 'none';
        $variables['boa-motion'] = $appearance['animations'] ? '200ms' : '0ms';
        $variables['boa-density'] = $appearance['density'] === 'compact' ? '0.75rem' : '1rem';
        $variables['boa-content-width'] = $appearance['content_width'] === 'boxed' ? '72rem' : '100%';

        $variables['boa-button-radius'] = $this->resolveComponentRadius($components['button_radius']);
        $variables['boa-card-radius'] = $this->resolveComponentRadius($components['card_radius']);

        foreach ($this->palettes as $role => $stops) {
            foreach ($stops as $stop => $hex) {
                $variables["boa-{$role}-{$stop}"] = $hex;
            }
            $variables["boa-{$role}"] = $stops[600];
            $variables["boa-on-{$role}"] = $this->onColor($role, 600);
        }

        if ($this->linkColor !== null) {
            $variables['boa-link'] = $this->linkColor;
        } else {
            $variables['boa-link'] = $this->color('accent', 700);
        }

        // Logo gradient anchors (themeable mark)
        $variables['boa-mark-stele-start'] = $this->color('brand', 800);
        $variables['boa-mark-stele-mid'] = $this->color('brand', 600);
        $variables['boa-mark-stele-end'] = $this->color('brand', 950);
        $variables['boa-mark-sun-start'] = $this->color('accent', 100);
        $variables['boa-mark-sun-mid'] = $this->color('accent', 400);
        $variables['boa-mark-sun-end'] = $this->color('accent', 600);
        $variables['boa-mark-linen-start'] = $this->color('brand', 50);
        $variables['boa-mark-linen-end'] = $this->color('brand', 300);
        $variables['boa-mark-ray'] = $this->color('accent', 200);
        $variables['boa-mark-stroke'] = $this->color('accent', 400);

        $block = ThemeCssVariables::block(':root', $variables);

        if ($this->darkMode() || $this->colorMode() === 'system') {
            $dark = [
                'boa-canvas-50' => $this->color('canvas', 950),
                'boa-canvas-100' => $this->color('canvas', 900),
                'boa-brand-50' => $this->color('brand', 950),
            ];

            if ($this->colorMode() === 'system') {
                $darkBlock = ThemeCssVariables::block(':root, .dark', $dark);
                $block .= "\n\n@media (prefers-color-scheme: dark) {\n".$darkBlock."\n}";
            } elseif ($this->colorMode() === 'dark') {
                $block .= "\n\n".ThemeCssVariables::block(':root, .dark', $dark);
            } else {
                $block .= "\n\n".ThemeCssVariables::block('.dark', $dark);
            }
        }

        return $block;
    }

    /**
     * Runtime CSS bridge so token utilities work without a Tailwind rebuild.
     */
    public function cssBridge(bool $applyDocument = true): string
    {
        return ThemeCssBridge::stylesheet($applyDocument);
    }

    /**
     * Full CSS payload (variables + bridge) for live refresh after saving.
     */
    public function cssPayload(bool $applyDocument = true): string
    {
        return $this->cssVariables()."\n\n".$this->cssBridge($applyDocument);
    }

    public function googleFontsUrl(): ?string
    {
        $fonts = $this->fonts();

        if (! $fonts['google']) {
            return null;
        }

        $parts = [];
        $system = ['system-ui', 'Georgia', 'ui-sans-serif', 'ui-serif'];

        if ($fonts['display'] !== '' && ! in_array($fonts['display'], $system, true)) {
            $parts[] = 'family='.str_replace(' ', '+', $fonts['display']).':wght@600;700';
        }

        if ($fonts['sans'] !== '' && ! in_array($fonts['sans'], $system, true)) {
            $parts[] = 'family='.str_replace(' ', '+', $fonts['sans']).':ital,wght@0,400;0,500;0,600;0,700;1,400';
        }

        if ($parts === []) {
            return null;
        }

        return 'https://fonts.googleapis.com/css2?'.implode('&', $parts).'&display=swap';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name(),
            'tagline' => $this->tagline(),
            'preset' => $this->preset(),
            'color_mode' => $this->colorMode(),
            'fonts' => $this->fonts(),
            'radius' => $this->radius(),
            'appearance' => $this->appearance(),
            'assets' => $this->assets(),
            'components' => $this->components(),
            'palettes' => $this->palettes,
            'accessibility' => $this->accessibilityReport(),
        ];
    }

    private function resolvePalettes(): void
    {
        $preset = $this->preset() ? Presets::get($this->preset()) : null;
        $presetColors = $preset['colors'] ?? [
            'brand' => '#0f766e',
            'accent' => '#d97706',
            'canvas' => '#78716c',
            'danger' => '#dc2626',
            'success' => '#059669',
        ];

        $fallbackExtras = [
            'warning' => '#d97706',
            'info' => '#0284c7',
        ];

        $overrides = array_filter(
            Arr::wrap($this->config['colors'] ?? []),
            static fn ($value) => is_string($value) && $value !== '',
        );

        $link = $overrides['link'] ?? null;
        unset($overrides['link']);

        if (is_string($link) && $link !== '') {
            $this->linkColor = Color::fromHex($link)->toHex();
        }

        $seeds = array_merge($presetColors, $fallbackExtras, $overrides);

        foreach ($seeds as $role => $hex) {
            if (! is_string($hex) || $hex === '') {
                continue;
            }

            try {
                $this->palettes[$role] = $this->generator->generate($hex);
            } catch (\InvalidArgumentException) {
                // Skip invalid seeds; keep package resilient.
            }
        }
    }

    private function resolveComponentRadius(string $token): string
    {
        return match ($token) {
            'none' => '0',
            'sm' => $this->radius()['sm'],
            'lg' => $this->radius()['lg'],
            'xl' => $this->radius()['xl'],
            'full' => '9999px',
            default => $this->radius()['md'],
        };
    }

    private function nullableString(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }

    private static function fontStack(string $family, bool $serif = false): string
    {
        $fallback = $serif ? 'Georgia, ui-serif, serif' : 'ui-sans-serif, system-ui, sans-serif';

        if ($family === '' || $family === 'system-ui') {
            return $fallback;
        }

        if ($family === 'Georgia') {
            return 'Georgia, ui-serif, serif';
        }

        $safe = preg_replace("/['\"]/", '', $family) ?? $family;

        return "'{$safe}', {$fallback}";
    }
}
