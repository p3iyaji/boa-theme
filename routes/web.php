<?php

declare(strict_types=1);

use Boa\Theme\Http\Controllers\ThemeSettingsController;
use Illuminate\Support\Facades\Route;

if (! config('boa-theme.settings.enabled', true)) {
    return;
}

$prefix = config('boa-theme.settings.route.prefix', 'admin/theme');
$name = config('boa-theme.settings.route.name', 'boa-theme.settings.');
$middleware = config('boa-theme.settings.route.middleware', ['web', 'auth', 'boa-theme.authorize']);

Route::middleware($middleware)
    ->prefix($prefix)
    ->name($name)
    ->group(function (): void {
        Route::get('/', [ThemeSettingsController::class, 'index'])->name('index');
        Route::put('/', [ThemeSettingsController::class, 'update'])->name('update');
        Route::post('/reset', [ThemeSettingsController::class, 'reset'])->name('reset');
        Route::get('/preview.css', [ThemeSettingsController::class, 'previewCss'])->name('preview');

        if (config('boa-theme.settings.features.import_export', true)) {
            Route::get('/export', [ThemeSettingsController::class, 'export'])->name('export');
            Route::post('/import', [ThemeSettingsController::class, 'import'])->name('import');
        }
    });
