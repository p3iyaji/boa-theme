<?php

declare(strict_types=1);

namespace Boa\Theme\Support;

/**
 * Canonical setting keys, types, groups, and defaults for the settings panel.
 */
final class SettingDefinition
{
    public const TYPE_STRING = 'string';

    public const TYPE_BOOLEAN = 'boolean';

    public const TYPE_INTEGER = 'integer';

    public const TYPE_COLOR = 'color';

    public const TYPE_ENUM = 'enum';

    public const TYPE_JSON = 'json';

    /**
     * @return array<string, array{type: string, group: string, default: mixed, public?: bool, options?: list<string|int>}>
     */
    public static function definitions(): array
    {
        return [
            'general.display_label' => [
                'type' => self::TYPE_STRING,
                'group' => 'general',
                'default' => null,
                'public' => true,
            ],
            'general.color_mode' => [
                'type' => self::TYPE_ENUM,
                'group' => 'general',
                'default' => 'system',
                'options' => ['light', 'dark', 'system'],
                'public' => true,
            ],
            'general.preset' => [
                'type' => self::TYPE_STRING,
                'group' => 'general',
                'default' => 'solar-stele',
                'public' => true,
            ],
            'general.rounded' => [
                'type' => self::TYPE_BOOLEAN,
                'group' => 'general',
                'default' => true,
                'public' => true,
            ],
            'general.shadows' => [
                'type' => self::TYPE_BOOLEAN,
                'group' => 'general',
                'default' => true,
                'public' => true,
            ],
            'general.animations' => [
                'type' => self::TYPE_BOOLEAN,
                'group' => 'general',
                'default' => true,
                'public' => true,
            ],
            'general.density' => [
                'type' => self::TYPE_ENUM,
                'group' => 'general',
                'default' => 'comfortable',
                'options' => ['comfortable', 'compact'],
                'public' => true,
            ],
            'general.content_width' => [
                'type' => self::TYPE_ENUM,
                'group' => 'general',
                'default' => 'full',
                'options' => ['full', 'boxed'],
                'public' => true,
            ],
            'general.body_class' => [
                'type' => self::TYPE_STRING,
                'group' => 'general',
                'default' => '',
                'public' => true,
            ],

            'brand.name' => [
                'type' => self::TYPE_STRING,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.tagline' => [
                'type' => self::TYPE_STRING,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.logo' => [
                'type' => self::TYPE_STRING,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.logo_dark' => [
                'type' => self::TYPE_STRING,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.favicon' => [
                'type' => self::TYPE_STRING,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.colors.brand' => [
                'type' => self::TYPE_COLOR,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.colors.accent' => [
                'type' => self::TYPE_COLOR,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.colors.canvas' => [
                'type' => self::TYPE_COLOR,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.colors.danger' => [
                'type' => self::TYPE_COLOR,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.colors.success' => [
                'type' => self::TYPE_COLOR,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.colors.warning' => [
                'type' => self::TYPE_COLOR,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.colors.info' => [
                'type' => self::TYPE_COLOR,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],
            'brand.colors.link' => [
                'type' => self::TYPE_COLOR,
                'group' => 'brand',
                'default' => null,
                'public' => true,
            ],

            'typography.sans' => [
                'type' => self::TYPE_STRING,
                'group' => 'typography',
                'default' => null,
                'public' => true,
            ],
            'typography.display' => [
                'type' => self::TYPE_STRING,
                'group' => 'typography',
                'default' => null,
                'public' => true,
            ],
            'typography.base_size' => [
                'type' => self::TYPE_STRING,
                'group' => 'typography',
                'default' => '16px',
                'public' => true,
            ],
            'typography.heading_weight' => [
                'type' => self::TYPE_ENUM,
                'group' => 'typography',
                'default' => '700',
                'options' => ['400', '500', '600', '700', '800'],
                'public' => true,
            ],
            'typography.body_weight' => [
                'type' => self::TYPE_ENUM,
                'group' => 'typography',
                'default' => '400',
                'options' => ['300', '400', '500', '600'],
                'public' => true,
            ],
            'typography.line_height' => [
                'type' => self::TYPE_STRING,
                'group' => 'typography',
                'default' => '1.5',
                'public' => true,
            ],
            'typography.letter_spacing' => [
                'type' => self::TYPE_STRING,
                'group' => 'typography',
                'default' => '0',
                'public' => true,
            ],

            'components.button_radius' => [
                'type' => self::TYPE_ENUM,
                'group' => 'components',
                'default' => 'md',
                'options' => ['none', 'sm', 'md', 'lg', 'xl', 'full'],
                'public' => true,
            ],
            'components.card_radius' => [
                'type' => self::TYPE_ENUM,
                'group' => 'components',
                'default' => 'lg',
                'options' => ['none', 'sm', 'md', 'lg', 'xl'],
                'public' => true,
            ],
            'components.form_style' => [
                'type' => self::TYPE_ENUM,
                'group' => 'components',
                'default' => 'outline',
                'options' => ['outline', 'filled', 'underline'],
                'public' => true,
            ],

            'custom.css' => [
                'type' => self::TYPE_STRING,
                'group' => 'custom',
                'default' => '',
                'public' => false,
            ],
            'custom.javascript' => [
                'type' => self::TYPE_STRING,
                'group' => 'custom',
                'default' => '',
                'public' => false,
            ],
            'custom.head' => [
                'type' => self::TYPE_STRING,
                'group' => 'custom',
                'default' => '',
                'public' => false,
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function keys(): array
    {
        return array_keys(self::definitions());
    }

    /**
     * @return list<string>
     */
    public static function keysForGroup(string $group): array
    {
        return array_values(array_filter(
            self::keys(),
            static fn (string $key): bool => str_starts_with($key, $group.'.'),
        ));
    }

    public static function isKnown(string $key): bool
    {
        return isset(self::definitions()[$key]);
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        $defaults = [];

        foreach (self::definitions() as $key => $definition) {
            $defaults[$key] = $definition['default'];
        }

        return $defaults;
    }
}
