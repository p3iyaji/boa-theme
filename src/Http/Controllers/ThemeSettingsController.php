<?php

declare(strict_types=1);

namespace Boa\Theme\Http\Controllers;

use Boa\Theme\Http\Requests\ImportThemeSettingsRequest;
use Boa\Theme\Http\Requests\UpdateThemeSettingsRequest;
use Boa\Theme\Services\ThemeAuthorizer;
use Boa\Theme\Services\ThemeManager;
use Boa\Theme\Support\Presets;
use Boa\Theme\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ThemeSettingsController extends Controller
{
    public function __construct(
        private readonly ThemeManager $manager,
        private readonly ThemeAuthorizer $authorizer,
    ) {}

    public function index(Request $request): View
    {
        $theme = $this->manager->makeTheme();
        $settings = $this->manager->all();

        return view('boa-theme::settings.index', [
            'theme' => $theme,
            'settings' => $settings,
            'presets' => array_keys(Presets::all()),
            'fonts' => config('boa-theme.settings.allowed_fonts', []),
            'features' => config('boa-theme.settings.features', []),
            'canCustomCode' => $this->authorizer->canManageCustomCode($request->user()),
            'logoUrl' => $this->manager->assetUrl($settings['brand.logo'] ?? null),
            'logoDarkUrl' => $this->manager->assetUrl($settings['brand.logo_dark'] ?? null),
            'faviconUrl' => $this->manager->assetUrl($settings['brand.favicon'] ?? null),
            'routePrefix' => config('boa-theme.settings.route.name', 'boa-theme.settings.'),
        ]);
    }

    public function update(UpdateThemeSettingsRequest $request): RedirectResponse
    {
        $payload = $request->settingsPayload();

        // Coerce line_height to string for storage consistency.
        if (isset($payload['typography.line_height'])) {
            $payload['typography.line_height'] = (string) $payload['typography.line_height'];
        }

        $this->manager->update($payload);

        if ($this->manager->featureEnabled('uploads')) {
            foreach (['logo' => 'logo', 'logo_dark' => 'logo_dark', 'favicon' => 'favicon'] as $input => $slot) {
                if ($request->boolean('remove_'.$input)) {
                    $this->manager->removeAsset($slot);
                }

                if ($request->hasFile($input)) {
                    $this->manager->storeUpload($request->file($input), $slot);
                }
            }
        }

        return redirect()
            ->route(config('boa-theme.settings.route.name').'index')
            ->with('boa-theme.status', 'Theme settings saved.');
    }

    public function reset(Request $request): RedirectResponse
    {
        $group = $request->string('group')->toString();

        if ($group !== '' && ! in_array($group, ['general', 'brand', 'typography', 'components', 'custom'], true)) {
            return back()->withErrors(['group' => 'Unknown settings group.']);
        }

        $this->manager->reset($group !== '' ? $group : null);

        $message = $group !== ''
            ? 'The '.ucfirst($group).' section was reset to defaults.'
            : 'All theme settings were reset to package defaults.';

        return redirect()
            ->route(config('boa-theme.settings.route.name').'index')
            ->with('boa-theme.status', $message);
    }

    public function export(): StreamedResponse|Response
    {
        if (! $this->manager->featureEnabled('import_export')) {
            abort(404);
        }

        $payload = json_encode($this->manager->export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return response()->streamDownload(
            static function () use ($payload): void {
                echo $payload;
            },
            'boa-theme-settings.json',
            ['Content-Type' => 'application/json'],
        );
    }

    public function import(ImportThemeSettingsRequest $request): RedirectResponse
    {
        $this->manager->import($request->payload());

        return redirect()
            ->route(config('boa-theme.settings.route.name').'index')
            ->with('boa-theme.status', 'Theme settings imported.');
    }

    public function previewCss(): Response
    {
        /** @var Theme $theme */
        $theme = app(Theme::class);

        return response($theme->cssVariables(), 200, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }
}
