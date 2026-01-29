<?php

/**
 * DatabaseSeeder
 *
 * Main seeder that orchestrates all other seeders for the library management system.
 *
 * Seeder Execution Order (Dependencies):
 * 1. SettingSeeder    - System settings (no dependencies)
 * 2. UserSeeder       - Admin and librarian accounts (no dependencies)
 * 3. CategorySeeder   - Book categories (no dependencies)
 * 4. StudentSeeder    - 50 sample students (no dependencies)
 * 5. BookSeeder       - 100 sample books (depends on CategorySeeder)
 * 6. TransactionSeeder - 85 transactions (depends on UserSeeder, StudentSeeder, BookSeeder)
 *
 * Usage:
 * php artisan db:seed                          - Run all seeders
 * php artisan db:seed --class=UserSeeder       - Run specific seeder
 * php artisan db:seed --class=DatabaseSeeder   - Run main seeder
 * php artisan migrate:fresh --seed             - Fresh database with seeds
 *
 * @package Database\Seeders
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Runs all seeders in the correct order to ensure
     * dependencies are satisfied.
     *
     * @return void
     */
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║     Bobon B Elementary School Library Management System      ║');
        $this->command->info('║                      Database Seeder                         ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
        $this->command->info('');

        // =========================================================================
        // STEP 1: Core System Data (No Dependencies)
        // =========================================================================
        $this->command->info('Step 1: Seeding core system data...');
        $this->command->info('────────────────────────────────────────');

        // System settings (circulation rules, school info, etc.)
        $this->call(SettingSeeder::class);

        // Admin and librarian accounts (1 admin + 2 librarians)
        $this->call(UserSeeder::class);

        // Book categories (11 categories)
        $this->call(CategorySeeder::class);

        $this->command->info('');

        // =========================================================================
        // STEP 2: Sample Data (Some Dependencies)
        // =========================================================================
        $this->command->info('Step 2: Seeding sample data...');
        $this->command->info('────────────────────────────────────────');

        // 50 sample students using factory
        $this->call(StudentSeeder::class);

        // 100 sample books using factory (depends on categories)
        $this->call(BookSeeder::class);

        $this->command->info('');

        // =========================================================================
        // STEP 3: Transaction Data (Depends on Users, Students, Books)
        // =========================================================================
        $this->command->info('Step 3: Seeding transaction data...');
        $this->command->info('────────────────────────────────────────');

        // 85 transactions: 30 active + 50 returned + 5 overdue
        $this->call(TransactionSeeder::class);

        // =========================================================================
        // COMPLETION MESSAGE
        // =========================================================================
        $this->command->info('');
        $this->command->info('╔══════════════════════════════════════════════════════════════╗');
        $this->command->info('║               Database seeding completed!                    ║');
        $this->command->info('╚══════════════════════════════════════════════════════════════╝');
        $this->command->info('');

        // Summary
        $this->command->info('Summary:');
        $this->command->table(
            ['Data Type', 'Count'],
            [
                ['Users (Admin + Librarians)', '3'],
                ['Categories', '11'],
                ['Students', '50'],
                ['Books', '100'],
                ['Transactions (30 active + 50 returned + 5 overdue)', '85'],
            ]
        );

        $this->command->info('');
        $this->command->info('Default Login Credentials:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@bobon.edu.ph', 'password'],
                ['Librarian', 'librarian1@bobon.edu.ph', 'password'],
                ['Librarian', 'librarian2@bobon.edu.ph', 'password'],
            ]
        );
        $this->command->warn('⚠️  Remember to change these passwords in production!');
        $this->command->info('');
    }
}
