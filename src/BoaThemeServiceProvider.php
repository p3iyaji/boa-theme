<?php

declare(strict_types=1);

namespace Boa\Theme;

use Boa\Theme\Console\ClearThemeSettingsCacheCommand;
use Boa\Theme\Console\ExportThemeSettingsCommand;
use Boa\Theme\Console\InspectThemeCommand;
use Boa\Theme\Console\InstallThemeSettingsCommand;
use Boa\Theme\Console\ResetThemeSettingsCommand;
use Boa\Theme\Contracts\ThemeSettingsRepository;
use Boa\Theme\Http\Middleware\AuthorizeThemeSettings;
use Boa\Theme\Repositories\ArrayThemeSettingsRepository;
use Boa\Theme\Repositories\CachedThemeSettingsRepository;
use Boa\Theme\Repositories\DatabaseThemeSettingsRepository;
use Boa\Theme\Services\ThemeAuthorizer;
use Boa\Theme\Services\ThemeManager;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BoaThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/boa-theme.php', 'boa-theme');

        $this->app->singleton(ThemeAuthorizer::class);

        $this->app->singleton(ThemeSettingsRepository::class, function ($app): ThemeSettingsRepository {
            $driver = (string) $app['config']->get('boa-theme.settings.driver', 'database');

            $repository = match ($driver) {
                'array', 'config' => new ArrayThemeSettingsRepository,
                default => new DatabaseThemeSettingsRepository,
            };

            if ($app['config']->get('boa-theme.settings.cache', true)) {
                return new CachedThemeSettingsRepository(
                    $repository,
                    $app['cache']->store(),
                    (string) $app['config']->get('boa-theme.settings.cache_key', 'boa-theme.settings'),
                    (int) $app['config']->get('boa-theme.settings.cache_ttl', 3600),
                );
            }

            return $repository;
        });

        $this->app->singleton(ThemeManager::class, function ($app): ThemeManager {
            return new ThemeManager(
                $app->make(ThemeSettingsRepository::class),
                $app['config'],
                $app['events'],
            );
        });

        $this->app->singleton(Theme::class, function ($app): Theme {
            return $app->make(ThemeManager::class)->makeTheme();
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'boa-theme');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'boa-theme');

        $this->publishes([
            __DIR__.'/../config/boa-theme.php' => config_path('boa-theme.php'),
        ], 'boa-theme-config');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'boa-theme-migrations');

        $this->publishes([
            __DIR__.'/../resources/css/theme.css' => resource_path('css/boa-theme.css'),
        ], 'boa-theme-css');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/boa-theme'),
        ], 'boa-theme-views');

        $this->publishes([
            __DIR__.'/../resources/lang' => lang_path('vendor/boa-theme'),
        ], 'boa-theme-translations');

        $this->publishes([
            __DIR__.'/../config/boa-theme.php' => config_path('boa-theme.php'),
            __DIR__.'/../database/migrations' => database_path('migrations'),
            __DIR__.'/../resources/css/theme.css' => resource_path('css/boa-theme.css'),
            __DIR__.'/../resources/views' => resource_path('views/vendor/boa-theme'),
            __DIR__.'/../resources/lang' => lang_path('vendor/boa-theme'),
        ], 'boa-theme');

        Blade::componentNamespace('Boa\\Theme\\View\\Components', 'boa-theme');

        Blade::directive('boaThemeStyles', function (): string {
            return '<?php echo app(\\Boa\\Theme\\Theme::class)->cssVariables(); ?>';
        });

        Blade::directive('themeSetting', function (string $expression): string {
            return "<?php echo e(app(\\Boa\\Theme\\Services\\ThemeManager::class)->get({$expression})); ?>";
        });

        /** @var Router $router */
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('boa-theme.authorize', AuthorizeThemeSettings::class);

        if ($this->app->runningInConsole()) {
            $this->commands([
                InspectThemeCommand::class,
                InstallThemeSettingsCommand::class,
                ResetThemeSettingsCommand::class,
                ExportThemeSettingsCommand::class,
                ClearThemeSettingsCacheCommand::class,
            ]);
        }
    }
}
