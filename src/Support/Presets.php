<?php

declare(strict_types=1);

namespace Boa\Theme\Support;

final class Presets
{
    /**
     * @return array<string, array{
     *     colors: array{brand: string, accent: string, canvas: string, danger: string, success: string},
     *     fonts: array{sans: string, display: string}
     * }>
     */
    public static function all(): array
    {
        return [
            'solar-stele' => [
                'colors' => [
                    'brand' => '#0f766e',
                    'accent' => '#d97706',
                    'canvas' => '#78716c',
                    'danger' => '#dc2626',
                    'success' => '#059669',
                ],
                'fonts' => [
                    'sans' => 'Source Sans 3',
                    'display' => 'Cinzel',
                ],
            ],
            'midnight' => [
                'colors' => [
                    'brand' => '#1e3a5f',
                    'accent' => '#38bdf8',
                    'canvas' => '#64748b',
                    'danger' => '#f43f5e',
                    'success' => '#10b981',
                ],
                'fonts' => [
                    'sans' => 'Source Sans 3',
                    'display' => 'Cinzel',
                ],
            ],
            'coastal' => [
                'colors' => [
                    'brand' => '#0369a1',
                    'accent' => '#ea580c',
                    'canvas' => '#78716c',
                    'danger' => '#e11d48',
                    'success' => '#0d9488',
                ],
                'fonts' => [
                    'sans' => 'Source Sans 3',
                    'display' => 'Cinzel',
                ],
            ],
            'ember' => [
                'colors' => [
                    'brand' => '#7c2d12',
                    'accent' => '#f59e0b',
                    'canvas' => '#57534e',
                    'danger' => '#b91c1c',
                    'success' => '#15803d',
                ],
                'fonts' => [
                    'sans' => 'Source Sans 3',
                    'display' => 'Cinzel',
                ],
            ],
        ];
    }

    /**
     * @return array{
     *     colors: array{brand: string, accent: string, canvas: string, danger: string, success: string},
     *     fonts: array{sans: string, display: string}
     * }|null
     */
    public static function get(string $name): ?array
    {
        return self::all()[$name] ?? null;
    }
}
