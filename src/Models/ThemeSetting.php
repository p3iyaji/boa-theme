<?php

declare(strict_types=1);

namespace Boa\Theme\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $type
 * @property string $group
 * @property bool $is_public
 */
class ThemeSetting extends Model
{
    protected $table = 'boa_theme_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'is_public',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }
}
