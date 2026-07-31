<?php

declare(strict_types=1);

namespace Boa\Theme\Console;

use Illuminate\Console\Command;

final class InstallThemeSettingsCommand extends Command
{
    protected $signature = 'boa-theme:settings:install
                            {--migrate : Run migrations after publishing}
                            {--force : Overwrite existing published files}';

    protected $description = 'Publish BOA theme settings config, migrations, and explain authorisation setup';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        $this->info('Publishing BOA Theme configuration…');
        $this->call('vendor:publish', [
            '--tag' => 'boa-theme-config',
            '--force' => $force,
        ]);

        $this->info('Publishing BOA Theme migrations…');
        $this->call('vendor:publish', [
            '--tag' => 'boa-theme-migrations',
            '--force' => $force,
        ]);

        if ($this->option('migrate')) {
            if ($this->confirm('Run migrations now?', true)) {
                $this->call('migrate');
            }
        } else {
            $this->line('Run migrations when ready: <comment>php artisan migrate</comment>');
        }

        $prefix = config('boa-theme.settings.route.prefix', 'admin/theme');
        $gate = config('boa-theme.settings.authorization.gate', 'manage-boa-theme-settings');

        $this->newLine();
        $this->info('Next steps');
        $this->line('1. Ensure the public disk is linked: <comment>php artisan storage:link</comment>');
        $this->line("2. Define the authorisation gate <comment>{$gate}</comment> (access is denied by default).");
        $this->line('3. Open the settings panel at: <comment>/'.$prefix.'</comment>');
        $this->newLine();
        $this->line('Example gate in AppServiceProvider:');
        $this->line(<<<'PHP'
Gate::define('manage-boa-theme-settings', function ($user) {
    return $user->is_admin === true;
});
PHP);

        return self::SUCCESS;
    }
}
