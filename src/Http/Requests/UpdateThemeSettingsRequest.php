<?php

declare(strict_types=1);

namespace Boa\Theme\Http\Requests;

use Boa\Theme\Services\ThemeAuthorizer;
use Boa\Theme\Services\ThemeManager;
use Boa\Theme\Support\Presets;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

final class UpdateThemeSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return app(ThemeAuthorizer::class)->canManage($this->user());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $fonts = config('boa-theme.settings.allowed_fonts', []);
        $presets = array_merge(array_keys(Presets::all()), ['']);

        return [
            'general.display_label' => ['nullable', 'string', 'max:120'],
            'general.color_mode' => ['nullable', Rule::in(['light', 'dark', 'system'])],
            'general.preset' => ['nullable', Rule::in($presets)],
            'general.rounded' => ['sometimes', 'boolean'],
            'general.shadows' => ['sometimes', 'boolean'],
            'general.animations' => ['sometimes', 'boolean'],
            'general.density' => ['nullable', Rule::in(['comfortable', 'compact'])],
            'general.content_width' => ['nullable', Rule::in(['full', 'boxed'])],
            'general.body_class' => ['nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_\-\s]*$/'],

            'brand.name' => ['nullable', 'string', 'max:120'],
            'brand.tagline' => ['nullable', 'string', 'max:255'],
            'brand.colors.brand' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'brand.colors.accent' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'brand.colors.canvas' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'brand.colors.danger' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'brand.colors.success' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'brand.colors.warning' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'brand.colors.info' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'brand.colors.link' => ['nullable', 'string', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],

            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
            'logo_dark' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,jpg,jpeg,gif,webp,svg', 'max:512'],
            'remove_logo' => ['sometimes', 'boolean'],
            'remove_logo_dark' => ['sometimes', 'boolean'],
            'remove_favicon' => ['sometimes', 'boolean'],

            'typography.sans' => ['nullable', Rule::in($fonts)],
            'typography.display' => ['nullable', Rule::in($fonts)],
            'typography.base_size' => ['nullable', 'string', 'regex:/^([8-9]|1[0-9]|2[0-4])px$/'],
            'typography.heading_weight' => ['nullable', Rule::in(['400', '500', '600', '700', '800'])],
            'typography.body_weight' => ['nullable', Rule::in(['300', '400', '500', '600'])],
            'typography.line_height' => ['nullable', 'numeric', 'min:1', 'max:2.5'],
            'typography.letter_spacing' => ['nullable', 'string', 'regex:/^-?(0(\.\d+)?|\d+(\.\d+)?)(em|px)?$/'],

            'components.button_radius' => ['nullable', Rule::in(['none', 'sm', 'md', 'lg', 'xl', 'full'])],
            'components.card_radius' => ['nullable', Rule::in(['none', 'sm', 'md', 'lg', 'xl'])],
            'components.form_style' => ['nullable', Rule::in(['outline', 'filled', 'underline'])],

            'custom.css' => ['nullable', 'string', 'max:50000'],
            'custom.javascript' => ['nullable', 'string', 'max:50000'],
            'custom.head' => ['nullable', 'string', 'max:20000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $manager = app(ThemeManager::class);
            $authorizer = app(ThemeAuthorizer::class);

            foreach (['custom.css' => 'custom_css', 'custom.javascript' => 'custom_javascript', 'custom.head' => 'custom_head'] as $key => $feature) {
                if (! $this->filled($key)) {
                    continue;
                }

                if (! $manager->featureEnabled($feature) || ! $authorizer->canManageCustomCode($this->user())) {
                    $validator->errors()->add($key, 'Custom code is disabled or you are not authorised to edit it.');
                }
            }

            if (! $manager->featureEnabled('uploads')) {
                foreach (['logo', 'logo_dark', 'favicon'] as $file) {
                    if ($this->hasFile($file)) {
                        $validator->errors()->add($file, 'Theme asset uploads are disabled.');
                    }
                }
            }
        });
    }

    /**
     * Flatten nested validated input into dotted setting keys (excluding uploads).
     *
     * @return array<string, mixed>
     */
    public function settingsPayload(): array
    {
        $skip = ['logo', 'logo_dark', 'favicon', 'remove_logo', 'remove_logo_dark', 'remove_favicon'];

        return $this->flatten($this->safe()->except($skip));
    }

    /**
     * @param  array<string, mixed>  $items
     * @return array<string, mixed>
     */
    private function flatten(array $items, string $prefix = ''): array
    {
        $payload = [];

        foreach ($items as $key => $value) {
            $dot = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $payload = array_merge($payload, $this->flatten($value, $dot));

                continue;
            }

            $payload[$dot] = $value;
        }

        return $payload;
    }

    protected function prepareForValidation(): void
    {
        $general = $this->input('general', []);

        if (is_array($general)) {
            foreach (['rounded', 'shadows', 'animations'] as $key) {
                if (array_key_exists($key, $general)) {
                    $general[$key] = filter_var($general[$key], FILTER_VALIDATE_BOOLEAN);
                }
            }
            $this->merge(['general' => $general]);
        }

        foreach (['remove_logo', 'remove_logo_dark', 'remove_favicon'] as $key) {
            if ($this->has($key)) {
                $this->merge([
                    $key => filter_var($this->input($key), FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        }
    }
}
