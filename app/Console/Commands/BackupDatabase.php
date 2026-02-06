<?php

/**
 * BackupDatabase Command
 *
 * Creates a MySQL database backup (SQL dump) and saves it to the
 * storage/app/backups directory with a timestamp filename.
 *
 * Requirements:
 * - mysqldump must be available in the system PATH
 * - For XAMPP on Windows: C:\xampp\mysql\bin\mysqldump.exe
 *
 * Usage:
 * php artisan library:backup-db                    # Create backup
 * php artisan library:backup-db --path=C:\backups  # Custom output path
 *
 * @package App\Console\Commands
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:backup-db
                            {--path= : Custom backup directory path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a MySQL database backup (SQL dump)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('Starting database backup...');
        $this->info('');

        // Get database config from .env
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database', 'library_management');
        $username = config('database.connections.mysql.username', 'root');
        $password = config('database.connections.mysql.password', '');

        // Determine backup directory
        $backupDir = $this->option('path') ?: storage_path('app/backups');

        // Create backup directory if it doesn't exist
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
            $this->info("Created backup directory: {$backupDir}");
        }

        // Generate filename with timestamp
        $timestamp = now()->format('Y-m-d_His');
        $filename = "backup_{$database}_{$timestamp}.sql";
        $filepath = $backupDir . DIRECTORY_SEPARATOR . $filename;

        // Build mysqldump command
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s %s %s > "%s"',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database),
            $filepath
        );

        $this->info("Database: {$database}");
        $this->info("Output: {$filepath}");
        $this->info('');

        // Execute the backup
        $this->info('Running mysqldump...');
        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);

        if ($returnCode !== 0) {
            $errorMessage = implode("\n", $output);
            $this->error("Backup failed with exit code {$returnCode}");
            $this->error($errorMessage);
            $this->newLine();
            $this->warn('Troubleshooting tips:');
            $this->line('  1. Make sure mysqldump is in your system PATH');
            $this->line('  2. For XAMPP, add C:\\xampp\\mysql\\bin to your PATH');
            $this->line('  3. Verify database credentials in .env file');
            $this->line('  4. Ensure the database exists and is accessible');

            Log::error('Database backup failed', [
                'exit_code' => $returnCode,
                'output' => $errorMessage,
            ]);

            return Command::FAILURE;
        }

        // Check if file was created and has content
        if (!file_exists($filepath) || filesize($filepath) === 0) {
            $this->error('Backup file was not created or is empty.');
            return Command::FAILURE;
        }

        $fileSize = $this->formatBytes(filesize($filepath));

        $this->info("Backup completed successfully!");
        $this->info("File: {$filename}");
        $this->info("Size: {$fileSize}");
        $this->info("Path: {$filepath}");

        Log::info('Database backup created', [
            'file' => $filename,
            'size' => $fileSize,
            'path' => $filepath,
        ]);

        // Clean up old backups (keep last 10)
        $this->cleanOldBackups($backupDir, 10);

        return Command::SUCCESS;
    }

    /**
     * Format bytes to human-readable size.
     *
     * @param int $bytes
     * @return string
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }

    /**
     * Remove old backup files, keeping the most recent ones.
     *
     * @param string $directory
     * @param int $keep Number of backups to keep
     * @return void
     */
    protected function cleanOldBackups(string $directory, int $keep): void
    {
        $files = glob($directory . DIRECTORY_SEPARATOR . 'backup_*.sql');

        if (count($files) <= $keep) {
            return;
        }

        // Sort by modification time (oldest first)
        usort($files, function ($a, $b) {
            return filemtime($a) - filemtime($b);
        });

        // Remove oldest files
        $toRemove = count($files) - $keep;
        for ($i = 0; $i < $toRemove; $i++) {
            unlink($files[$i]);
            $this->line("Removed old backup: " . basename($files[$i]));
        }

        $this->info("Cleaned up {$toRemove} old backup(s). Keeping last {$keep}.");
    }
}
