<?php

declare(strict_types=1);

namespace Boa\Theme\Facades;

use Boa\Theme\Theme;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string name()
 * @method static string tagline()
 * @method static string|null preset()
 * @method static bool darkMode()
 * @method static array fonts()
 * @method static array radius()
 * @method static array palette(string $role)
 * @method static string color(string $role, int $stop = 600)
 * @method static string onColor(string $role, int $stop = 600)
 * @method static float contrast(string $foregroundRole, int $foregroundStop, string $backgroundRole, int $backgroundStop)
 * @method static array accessibilityReport()
 * @method static string cssVariables()
 * @method static string|null googleFontsUrl()
 * @method static array toArray()
 *
 * @see Theme
 */
final class BoaTheme extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Theme::class;
    }
}
