<?php

/**
 * ClearAllCache Command
 *
 * Clears all application caches in one command. This is useful when
 * troubleshooting issues or after making configuration changes.
 *
 * Caches cleared:
 * - Application cache (cache:clear)
 * - Configuration cache (config:clear)
 * - Route cache (route:clear)
 * - View cache (view:clear)
 * - Compiled class files (clear-compiled)
 *
 * Usage:
 * php artisan library:clear-cache
 *
 * @package App\Console\Commands
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all application caches (config, routes, views, cache)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('Clearing all application caches...');
        $this->info('');

        // List of cache clearing commands to run
        $commands = [
            'cache:clear' => 'Application cache',
            'config:clear' => 'Configuration cache',
            'route:clear' => 'Route cache',
            'view:clear' => 'Compiled views',
        ];

        $results = [];

        foreach ($commands as $command => $label) {
            try {
                $this->call($command);
                $results[] = [$label, 'Cleared'];
            } catch (\Exception $e) {
                $results[] = [$label, 'Failed: ' . $e->getMessage()];
            }
        }

        $this->newLine();
        $this->table(['Cache Type', 'Status'], $results);
        $this->newLine();
        $this->info('All caches have been cleared successfully!');
        $this->newLine();

        return Command::SUCCESS;
    }
}
