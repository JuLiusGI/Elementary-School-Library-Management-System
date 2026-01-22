<?php

/**
 * Migration: Create Settings Table
 *
 * This migration creates the 'settings' table which stores configurable
 * system settings as key-value pairs.
 *
 * Key Settings Used by the Library System:
 * - max_books_per_student: Maximum books a student can borrow (default: 3)
 * - borrowing_period: Days until book is due (default: 7)
 * - fine_per_day: Fine amount per overdue day in PHP (default: 5.00)
 * - grace_period: Days before fines start (default: 1)
 * - school_name: Name of the school
 * - school_address: School address
 * - library_hours: Operating hours
 *
 * This key-value approach allows admins to change settings without
 * modifying code. Settings are accessed via Setting::getValue('key').
 *
 * @see App\Models\Setting
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (settings table)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the settings table with key-value structure.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            // Primary key - auto-incrementing unique identifier
            $table->id();

            // Setting key (e.g., "max_books_per_student", "fine_per_day")
            // Must be unique - each setting can only have one value
            // Use snake_case for consistency
            $table->string('key', 100)
                  ->unique()
                  ->comment('Setting key in snake_case format');

            // Setting value stored as text
            // Can store strings, numbers, JSON, etc.
            // The application is responsible for type conversion
            $table->text('value')
                  ->comment('Setting value (stored as text, cast in application)');

            // Optional description explaining what this setting does
            // Useful for admin interface to show help text
            $table->string('description', 255)
                  ->nullable()
                  ->comment('Human-readable description of this setting');

            // created_at: When the setting was created
            // updated_at: When the setting was last modified
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the settings table.
     * WARNING: This will delete all system configuration!
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
