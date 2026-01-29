<?php

/**
 * UserSeeder
 *
 * Seeds the default admin and librarian users for the library management system.
 *
 * Default Credentials:
 * - Admin: admin@bobon.edu.ph / password
 * - Librarian 1: librarian1@bobon.edu.ph / password
 * - Librarian 2: librarian2@bobon.edu.ph / password
 *
 * IMPORTANT: These credentials should be changed immediately
 * after the first login in a production environment!
 *
 * @package Database\Seeders
 * @see App\Models\User
 */

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Default users to seed.
     *
     * @var array<int, array<string, string>>
     */
    protected array $users = [
        [
            'name' => 'System Administrator',
            'email' => 'admin@bobon.edu.ph',
            'password' => 'password',
            'role' => 'admin',
        ],
        [
            'name' => 'Maria Santos',
            'email' => 'librarian1@bobon.edu.ph',
            'password' => 'password',
            'role' => 'librarian',
        ],
        [
            'name' => 'Juan Dela Cruz',
            'email' => 'librarian2@bobon.edu.ph',
            'password' => 'password',
            'role' => 'librarian',
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
        foreach ($this->users as $userData) {
            User::updateOrCreate(
                // Search criteria: match by email
                ['email' => $userData['email']],
                // Values to set/update
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role'],
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('Default users seeded successfully!');
        $this->command->newLine();
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@bobon.edu.ph', 'password'],
                ['Librarian', 'librarian1@bobon.edu.ph', 'password'],
                ['Librarian', 'librarian2@bobon.edu.ph', 'password'],
            ]
        );
        $this->command->warn('Please change these passwords after first login!');
    }
}
