<?php

/**
 * Migration: Add Role to Users Table
 *
 * This migration adds a 'role' column to the users table to support
 * role-based access control in the library management system.
 *
 * Roles:
 * - 'admin': Full system access, can manage users, settings, and all features
 * - 'librarian': Can manage students, books, transactions, and view reports
 *
 * The role column is placed after the 'password' column for logical ordering.
 * Default value is 'librarian' so new users are restricted by default.
 *
 * @see App\Models\User
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (users table)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds the 'role' column to the users table.
     * This enables role-based access control throughout the application.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add role column after password
            // Values: 'admin' or 'librarian'
            // Default: 'librarian' (more restricted role)
            $table->enum('role', ['admin', 'librarian'])
                  ->default('librarian')
                  ->after('password')
                  ->comment('User role for access control: admin has full access, librarian has limited access');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Removes the 'role' column from the users table.
     * Use with caution - this will delete all role data.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove the role column
            $table->dropColumn('role');
        });
    }
};
