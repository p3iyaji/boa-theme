<?php

declare(strict_types=1);

namespace Boa\Theme;

use Boa\Theme\Console\InspectThemeCommand;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BoaThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/boa-theme.php', 'boa-theme');

        $this->app->singleton(Theme::class, function ($app): Theme {
            return new Theme($app['config']->get('boa-theme', []));
        });
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'boa-theme');

        $this->publishes([
            __DIR__.'/../config/boa-theme.php' => config_path('boa-theme.php'),
        ], 'boa-theme-config');

        $this->publishes([
            __DIR__.'/../resources/css/theme.css' => resource_path('css/boa-theme.css'),
        ], 'boa-theme-css');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/boa-theme'),
        ], 'boa-theme-views');

        Blade::componentNamespace('Boa\\Theme\\View\\Components', 'boa-theme');

        Blade::directive('boaThemeStyles', function (): string {
            return '<?php echo app(\\Boa\\Theme\\Theme::class)->cssVariables(); ?>';
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                InspectThemeCommand::class,
            ]);
        }
    }
}
