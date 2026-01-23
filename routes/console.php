<?php

/**
 * Console Routes
 *
 * This file defines Artisan commands and scheduled tasks for the Library Management System.
 * Commands registered here can be run via the command line: php artisan <command>
 *
 * Scheduled tasks are defined using the Schedule facade and run via:
 * php artisan schedule:run (should be set up as a cron job on the server)
 *
 * @see https://laravel.com/docs/scheduling
 */

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// =========================================================================
// ARTISAN COMMANDS
// =========================================================================

/**
 * Display an inspiring quote (Laravel default)
 */
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// =========================================================================
// SCHEDULED TASKS
// =========================================================================

/**
 * Update overdue transactions daily at midnight
 *
 * This task checks all borrowed transactions and marks any that are
 * past their due date as 'overdue'. This ensures:
 * - Students are notified of overdue status
 * - Borrowing restrictions are applied (can't borrow with overdue books)
 * - Fine calculations are accurate
 *
 * To set up on server, add this cron entry:
 * * * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
 */
Schedule::command('transactions:update-overdue')
    ->daily()
    ->at('00:00')
    ->description('Update borrowed transactions to overdue if past due date')
    ->emailOutputOnFailure(env('ADMIN_EMAIL', null));
