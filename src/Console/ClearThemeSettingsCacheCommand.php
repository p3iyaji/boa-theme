<?php

declare(strict_types=1);

namespace Boa\Theme\Console;

use Boa\Theme\Services\ThemeManager;
use Illuminate\Console\Command;

final class ClearThemeSettingsCacheCommand extends Command
{
    protected $signature = 'boa-theme:settings:clear-cache';

    protected $description = 'Clear the cached BOA theme settings';

    public function handle(ThemeManager $manager): int
    {
        $manager->clearCache();
        $this->info('Theme settings cache cleared.');

        return self::SUCCESS;
    }
}
