<?php

declare(strict_types=1);

namespace Boa\Theme\Support;

use InvalidArgumentException;

final class Color
{
    public function __construct(
        public readonly int $r,
        public readonly int $g,
        public readonly int $b,
    ) {}

    public static function fromHex(string $hex): self
    {
        $hex = ltrim(trim($hex), '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            throw new InvalidArgumentException("Invalid hex color [{$hex}].");
        }

        return new self(
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        );
    }

    public function toHex(): string
    {
        return sprintf('#%02x%02x%02x', $this->r, $this->g, $this->b);
    }

    /**
     * @return array{0: float, 1: float, 2: float} H in [0,360), S and L in [0,1]
     */
    public function toHsl(): array
    {
        $r = $this->r / 255;
        $g = $this->g / 255;
        $b = $this->b / 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;
        $d = $max - $min;

        if ($d < 0.00001) {
            return [0.0, 0.0, $l];
        }

        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

        $h = match ($max) {
            $r => (($g - $b) / $d) + ($g < $b ? 6 : 0),
            $g => (($b - $r) / $d) + 2,
            default => (($r - $g) / $d) + 4,
        };

        return [$h * 60, $s, $l];
    }

    public static function fromHsl(float $h, float $s, float $l): self
    {
        $h = fmod(($h % 360) + 360, 360) / 360;
        $s = max(0.0, min(1.0, $s));
        $l = max(0.0, min(1.0, $l));

        if ($s < 0.00001) {
            $v = (int) round($l * 255);

            return new self($v, $v, $v);
        }

        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        return new self(
            (int) round(self::hueToRgb($p, $q, $h + 1 / 3) * 255),
            (int) round(self::hueToRgb($p, $q, $h) * 255),
            (int) round(self::hueToRgb($p, $q, $h - 1 / 3) * 255),
        );
    }

    private static function hueToRgb(float $p, float $q, float $t): float
    {
        if ($t < 0) {
            $t += 1;
        }
        if ($t > 1) {
            $t -= 1;
        }
        if ($t < 1 / 6) {
            return $p + ($q - $p) * 6 * $t;
        }
        if ($t < 1 / 2) {
            return $q;
        }
        if ($t < 2 / 3) {
            return $p + ($q - $p) * (2 / 3 - $t) * 6;
        }

        return $p;
    }

    /**
     * Relative luminance (WCAG) in [0, 1].
     */
    public function luminance(): float
    {
        $channel = static function (int $c): float {
            $v = $c / 255;

            return $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4;
        };

        return 0.2126 * $channel($this->r) + 0.7152 * $channel($this->g) + 0.0722 * $channel($this->b);
    }

    /**
     * WCAG contrast ratio against another color.
     */
    public function contrastRatio(self $other): float
    {
        $l1 = $this->luminance();
        $l2 = $other->luminance();
        $lighter = max($l1, $l2);
        $darker = min($l1, $l2);

        return ($lighter + 0.05) / ($darker + 0.05);
    }

    /**
     * Pick black or white ink that meets AA contrast when possible.
     */
    public function contrastingInk(): self
    {
        $white = self::fromHex('#ffffff');
        $black = self::fromHex('#0c0a09');

        return $this->contrastRatio($white) >= $this->contrastRatio($black) ? $white : $black;
    }
}
