<?php

/**
 * SettingSeeder
 *
 * Seeds the default settings for the library management system.
 * These settings can be modified through the admin settings page.
 *
 * @package Database\Seeders
 */

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Default settings configuration.
     *
     * @var array
     */
    protected array $settings = [
        // Circulation Rules
        [
            'key' => 'max_books_per_student',
            'value' => '3',
            'description' => 'Maximum number of books a student can borrow at once',
        ],
        [
            'key' => 'borrowing_period',
            'value' => '7',
            'description' => 'Number of days a student can keep a borrowed book',
        ],
        [
            'key' => 'allow_renewals',
            'value' => '1',
            'description' => 'Allow students to renew borrowed books',
        ],
        [
            'key' => 'max_renewals',
            'value' => '1',
            'description' => 'Maximum number of times a book can be renewed',
        ],

        // Fine Configuration
        [
            'key' => 'fine_per_day',
            'value' => '5.00',
            'description' => 'Fine amount per day for overdue books (in PHP)',
        ],
        [
            'key' => 'grace_period',
            'value' => '1',
            'description' => 'Number of days before fines start accumulating',
        ],
        [
            'key' => 'max_fine_amount',
            'value' => '100.00',
            'description' => 'Maximum fine amount per transaction (in PHP)',
        ],

        // School Information
        [
            'key' => 'school_name',
            'value' => 'Bobon B Elementary School',
            'description' => 'Name of the school',
        ],
        [
            'key' => 'school_address',
            'value' => 'Southern Leyte, Philippines',
            'description' => 'Full address of the school',
        ],
        [
            'key' => 'library_name',
            'value' => 'School Library',
            'description' => 'Name of the library',
        ],
        [
            'key' => 'library_email',
            'value' => '',
            'description' => 'Library contact email address',
        ],
        [
            'key' => 'library_phone',
            'value' => '',
            'description' => 'Library contact phone number',
        ],
        [
            'key' => 'library_hours',
            'value' => '7:00 AM - 5:00 PM',
            'description' => 'Library operating hours',
        ],

        // System Preferences
        [
            'key' => 'date_format',
            'value' => 'M d, Y',
            'description' => 'Date display format (PHP date format)',
        ],
        [
            'key' => 'items_per_page',
            'value' => '15',
            'description' => 'Number of items to show per page in lists',
        ],
        [
            'key' => 'enable_email_notifications',
            'value' => '0',
            'description' => 'Send email notifications for overdue books',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * Uses updateOrCreate to avoid duplicates if seeder is run multiple times.
     *
     * @return void
     */
    public function run(): void
    {
        foreach ($this->settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'description' => $setting['description'],
                ]
            );
        }

        $this->command->info('Default settings seeded successfully!');
    }
}
