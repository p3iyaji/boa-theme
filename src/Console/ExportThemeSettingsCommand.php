<?php

declare(strict_types=1);

namespace Boa\Theme\Console;

use Boa\Theme\Services\ThemeManager;
use Illuminate\Console\Command;

final class ExportThemeSettingsCommand extends Command
{
    protected $signature = 'boa-theme:settings:export {path? : Destination file path}';

    protected $description = 'Export saved BOA theme settings as JSON';

    public function handle(ThemeManager $manager): int
    {
        $json = json_encode($manager->export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            $this->error('Failed to encode settings.');

            return self::FAILURE;
        }

        $path = $this->argument('path');

        if (is_string($path) && $path !== '') {
            file_put_contents($path, $json.PHP_EOL);
            $this->info("Exported to {$path}");

            return self::SUCCESS;
        }

        $this->line($json);

        return self::SUCCESS;
    }
}
