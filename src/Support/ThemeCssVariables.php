<?php

declare(strict_types=1);

namespace Boa\Theme\Support;

/**
 * Escapes and formats CSS custom property declarations safely.
 */
final class ThemeCssVariables
{
    /**
     * @param  array<string, string>  $variables  name without leading -- => value
     */
    public static function block(string $selector, array $variables): string
    {
        $lines = [];

        foreach ($variables as $name => $value) {
            $safeName = self::sanitizeName($name);
            $safeValue = self::sanitizeValue($value);

            if ($safeName === null || $safeValue === null) {
                continue;
            }

            $lines[] = "--{$safeName}: {$safeValue};";
        }

        if ($lines === []) {
            return '';
        }

        return $selector." {\n    ".implode("\n    ", $lines)."\n}";
    }

    public static function sanitizeName(string $name): ?string
    {
        $name = ltrim(trim($name), '-');

        if ($name === '' || ! preg_match('/^[a-zA-Z0-9_-]+$/', $name)) {
            return null;
        }

        return $name;
    }

    /**
     * Allow hex colors, lengths, font stacks, keywords, and safe CSS values.
     * Rejects braces, angle brackets, and expression()-style payloads.
     */
    public static function sanitizeValue(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/[{}<>]|expression\s*\(|url\s*\(\s*["\']?\s*javascript:/i', $value)) {
            return null;
        }

        // Strip control characters.
        $value = preg_replace('/[\x00-\x1F\x7F]/', '', $value) ?? '';

        if ($value === '') {
            return null;
        }

        return $value;
    }

    public static function sanitizeClassList(string $classes): string
    {
        $classes = trim($classes);

        if ($classes === '') {
            return '';
        }

        $parts = preg_split('/\s+/', $classes) ?: [];
        $safe = [];

        foreach ($parts as $part) {
            if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_-]*$/', $part)) {
                $safe[] = $part;
            }
        }

        return implode(' ', $safe);
    }
}
