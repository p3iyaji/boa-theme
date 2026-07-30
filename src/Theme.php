<?php

declare(strict_types=1);

namespace Boa\Theme;

use Boa\Theme\Support\Color;
use Boa\Theme\Support\PaletteGenerator;
use Boa\Theme\Support\Presets;
use Illuminate\Support\Arr;

final class Theme
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $palettes = [];

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
        return (bool) ($this->config['dark_mode'] ?? false);
    }

    /**
     * @return array{sans: string, display: string, google: bool}
     */
    public function fonts(): array
    {
        $presetFonts = $this->preset() ? (Presets::get($this->preset())['fonts'] ?? []) : [];

        return [
            'sans' => (string) ($this->config['fonts']['sans'] ?? $presetFonts['sans'] ?? 'Source Sans 3'),
            'display' => (string) ($this->config['fonts']['display'] ?? $presetFonts['display'] ?? 'Cinzel'),
            'google' => (bool) ($this->config['fonts']['google'] ?? true),
        ];
    }

    /**
     * @return array{sm: string, md: string, lg: string, xl: string}
     */
    public function radius(): array
    {
        return [
            'sm' => (string) ($this->config['radius']['sm'] ?? '0.5rem'),
            'md' => (string) ($this->config['radius']['md'] ?? '0.75rem'),
            'lg' => (string) ($this->config['radius']['lg'] ?? '1rem'),
            'xl' => (string) ($this->config['radius']['xl'] ?? '1.5rem'),
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
        $lines = [];
        $fonts = $this->fonts();
        $radius = $this->radius();

        $lines[] = '--boa-font-sans: '.self::fontStack($fonts['sans']).';';
        $lines[] = '--boa-font-display: '.self::fontStack($fonts['display'], serif: true).';';

        foreach ($radius as $key => $value) {
            $lines[] = "--boa-radius-{$key}: {$value};";
        }

        foreach ($this->palettes as $role => $stops) {
            foreach ($stops as $stop => $hex) {
                $lines[] = "--boa-{$role}-{$stop}: {$hex};";
            }
            $lines[] = "--boa-{$role}: {$stops[600]};";
            $lines[] = "--boa-on-{$role}: {$this->onColor($role, 600)};";
        }

        // Logo gradient anchors (themeable mark)
        $lines[] = '--boa-mark-stele-start: '.$this->color('brand', 800).';';
        $lines[] = '--boa-mark-stele-mid: '.$this->color('brand', 600).';';
        $lines[] = '--boa-mark-stele-end: '.$this->color('brand', 950).';';
        $lines[] = '--boa-mark-sun-start: '.$this->color('accent', 100).';';
        $lines[] = '--boa-mark-sun-mid: '.$this->color('accent', 400).';';
        $lines[] = '--boa-mark-sun-end: '.$this->color('accent', 600).';';
        $lines[] = '--boa-mark-linen-start: '.$this->color('brand', 50).';';
        $lines[] = '--boa-mark-linen-end: '.$this->color('brand', 300).';';
        $lines[] = '--boa-mark-ray: '.$this->color('accent', 200).';';
        $lines[] = '--boa-mark-stroke: '.$this->color('accent', 400).';';

        $block = ":root {\n    ".implode("\n    ", $lines)."\n}";

        if ($this->darkMode()) {
            $dark = [
                '--boa-canvas-50: '.$this->color('canvas', 950).';',
                '--boa-canvas-100: '.$this->color('canvas', 900).';',
                '--boa-brand-50: '.$this->color('brand', 950).';',
            ];
            $block .= "\n\n.dark {\n    ".implode("\n    ", $dark)."\n}";
        }

        return $block;
    }

    public function googleFontsUrl(): ?string
    {
        $fonts = $this->fonts();

        if (! $fonts['google']) {
            return null;
        }

        $parts = [];

        if ($fonts['display'] !== '') {
            $parts[] = 'family='.str_replace(' ', '+', $fonts['display']).':wght@600;700';
        }

        if ($fonts['sans'] !== '') {
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
            'fonts' => $this->fonts(),
            'radius' => $this->radius(),
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

        $overrides = array_filter(
            Arr::wrap($this->config['colors'] ?? []),
            static fn ($value) => is_string($value) && $value !== '',
        );

        $seeds = array_merge($presetColors, $overrides);

        foreach ($seeds as $role => $hex) {
            $this->palettes[$role] = $this->generator->generate($hex);
        }
    }

    private static function fontStack(string $family, bool $serif = false): string
    {
        $fallback = $serif ? 'Georgia, ui-serif, serif' : 'ui-sans-serif, system-ui, sans-serif';

        if ($family === '') {
            return $fallback;
        }

        return "'{$family}', {$fallback}";
    }
}
