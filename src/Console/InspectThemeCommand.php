<?php

declare(strict_types=1);

namespace Boa\Theme\Console;

use Boa\Theme\Support\Presets;
use Boa\Theme\Theme;
use Illuminate\Console\Command;

final class InspectThemeCommand extends Command
{
    protected $signature = 'boa-theme:inspect {--json : Output as JSON}';

    protected $description = 'Inspect the active BOA theme tokens and WCAG contrast report';

    public function handle(Theme $theme): int
    {
        if ($this->option('json')) {
            $this->line(json_encode($theme->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        }

        $this->info('BOA Theme: '.$theme->name());
        $this->line('Preset: '.($theme->preset() ?? 'custom'));
        $this->line('Tagline: '.$theme->tagline());
        $this->newLine();

        $this->table(
            ['Role', '50', '600', '950'],
            collect(['brand', 'accent', 'canvas', 'danger', 'success'])->map(
                fn (string $role) => [
                    $role,
                    $theme->color($role, 50),
                    $theme->color($role, 600),
                    $theme->color($role, 950),
                ]
            )->all()
        );

        $this->newLine();
        $this->info('Accessibility (text contrast)');
        $this->table(
            ['Pair', 'Ratio', 'AA', 'AAA'],
            collect($theme->accessibilityReport())->map(fn (array $row) => [
                $row['pair'],
                (string) $row['ratio'],
                $row['aa'] ? 'yes' : 'no',
                $row['aaa'] ? 'yes' : 'no',
            ])->all()
        );

        $this->newLine();
        $this->line('Available presets: '.implode(', ', array_keys(Presets::all())));

        return self::SUCCESS;
    }
}
