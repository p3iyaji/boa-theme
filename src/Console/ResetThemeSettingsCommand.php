<?php

declare(strict_types=1);

namespace Boa\Theme\Console;

use Boa\Theme\Services\ThemeManager;
use Illuminate\Console\Command;

final class ResetThemeSettingsCommand extends Command
{
    protected $signature = 'boa-theme:settings:reset {--force : Skip confirmation}';

    protected $description = 'Reset all saved BOA theme settings to package defaults';

    public function handle(ThemeManager $manager): int
    {
        if (! $this->option('force') && ! $this->confirm('Reset all saved theme settings?')) {
            $this->warn('Cancelled.');

            return self::SUCCESS;
        }

        $manager->reset();
        $this->info('Theme settings reset.');

        return self::SUCCESS;
    }
}
