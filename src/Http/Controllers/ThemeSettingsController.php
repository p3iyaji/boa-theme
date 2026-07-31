<?php

declare(strict_types=1);

namespace Boa\Theme\Http\Controllers;

use Boa\Theme\Http\Requests\ImportThemeSettingsRequest;
use Boa\Theme\Http\Requests\UpdateThemeSettingsRequest;
use Boa\Theme\Services\ThemeAuthorizer;
use Boa\Theme\Services\ThemeManager;
use Boa\Theme\Support\Presets;
use Boa\Theme\Theme;
use Illuminate\Http\JsonResponse;
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
            'standalone' => true,
        ]);
    }

    public function update(UpdateThemeSettingsRequest $request): JsonResponse|RedirectResponse
    {
        $payload = $request->settingsPayload();

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

        return $this->respond($request, 'Theme settings saved.');
    }

    public function reset(Request $request): JsonResponse|RedirectResponse
    {
        $group = $request->string('group')->toString();

        if ($group !== '' && ! in_array($group, ['general', 'brand', 'typography', 'components', 'custom'], true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unknown settings group.', 'errors' => ['group' => ['Unknown settings group.']]], 422);
            }

            return back()->withErrors(['group' => 'Unknown settings group.']);
        }

        $this->manager->reset($group !== '' ? $group : null);

        $message = $group !== ''
            ? 'The '.ucfirst($group).' section was reset to package defaults.'
            : 'All theme settings were reset to package defaults.';

        return $this->respond($request, $message);
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

    public function import(ImportThemeSettingsRequest $request): JsonResponse|RedirectResponse
    {
        $this->manager->import($request->payload());

        return $this->respond($request, 'Theme settings imported.');
    }

    public function previewCss(): Response
    {
        $theme = $this->manager->makeTheme();
        $apply = $this->manager->featureEnabled('apply_document_styles');

        return response($theme->cssPayload($apply), 200, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    private function respond(Request $request, string $message): JsonResponse|RedirectResponse
    {
        $theme = $this->manager->makeTheme();
        app()->instance(Theme::class, $theme);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'name' => $theme->name(),
                'color_mode' => $theme->colorMode(),
                'css_variables' => $theme->cssVariables(),
                'css_bridge' => $theme->cssBridge($this->manager->featureEnabled('apply_document_styles')),
            ]);
        }

        return redirect()
            ->route(config('boa-theme.settings.route.name').'index')
            ->with('boa-theme.status', $message);
    }
}
