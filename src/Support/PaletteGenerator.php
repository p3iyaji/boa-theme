<?php

declare(strict_types=1);

namespace Boa\Theme\Support;

/**
 * Expands a seed hex into a Tailwind-like 50–950 scale using HSL lightness stops.
 * Stops are tuned for UI surfaces (light tints → deep brand).
 */
final class PaletteGenerator
{
    /**
     * Lightness targets for each stop (0–1). Seed color is blended toward stop 500–600.
     *
     * @var array<int, float>
     */
    private const LIGHTNESS = [
        50 => 0.97,
        100 => 0.94,
        200 => 0.88,
        300 => 0.78,
        400 => 0.66,
        500 => 0.54,
        600 => 0.44,
        700 => 0.35,
        800 => 0.26,
        900 => 0.18,
        950 => 0.11,
    ];

    /**
     * @return array<int, string> stop => #hex
     */
    public function generate(string $seedHex): array
    {
        $seed = Color::fromHex($seedHex);
        [$h, $s] = $seed->toHsl();

        // Keep chroma slightly elevated on mid tones for brand punch.
        $saturation = max(0.08, min(0.92, $s));

        $palette = [];

        foreach (self::LIGHTNESS as $stop => $lightness) {
            $stopSaturation = $stop <= 100
                ? $saturation * 0.55
                : ($stop >= 900 ? $saturation * 0.85 : $saturation);

            $palette[$stop] = Color::fromHsl($h, $stopSaturation, $lightness)->toHex();
        }

        // Anchor stop 600 near the original seed hue/lightness when seed is mid-dark.
        [, , $seedL] = $seed->toHsl();
        if ($seedL > 0.2 && $seedL < 0.55) {
            $palette[600] = $seed->toHex();
        } elseif ($seedL <= 0.2) {
            $palette[900] = $seed->toHex();
        } elseif ($seedL >= 0.7) {
            $palette[200] = $seed->toHex();
        }

        return $palette;
    }
}
