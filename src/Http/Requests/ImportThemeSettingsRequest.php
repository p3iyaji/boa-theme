<?php

declare(strict_types=1);

namespace Boa\Theme\Http\Requests;

use Boa\Theme\Services\ThemeAuthorizer;
use Boa\Theme\Services\ThemeManager;
use Boa\Theme\Support\SettingDefinition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

final class ImportThemeSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $manager = app(ThemeManager::class);

        return $manager->featureEnabled('import_export')
            && app(ThemeAuthorizer::class)->canManage($this->user());
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:json,txt', 'max:512'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (! $this->hasFile('file')) {
                return;
            }

            try {
                $this->payload();
            } catch (\InvalidArgumentException $e) {
                $validator->errors()->add('file', $e->getMessage());
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        $raw = file_get_contents($this->file('file')->getRealPath() ?: '');

        if ($raw === false || trim($raw) === '') {
            throw new \InvalidArgumentException('The import file is empty.');
        }

        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            throw new \InvalidArgumentException('The import file must contain valid JSON.');
        }

        $settings = $decoded['settings'] ?? $decoded;

        if (! is_array($settings)) {
            throw new \InvalidArgumentException('The import payload must include a settings object.');
        }

        $known = 0;

        foreach ($settings as $key => $value) {
            if (is_string($key) && SettingDefinition::isKnown($key)) {
                $known++;
            }
        }

        if ($known === 0) {
            throw new \InvalidArgumentException('No supported theme settings were found in the import file.');
        }

        return $decoded;
    }
}
