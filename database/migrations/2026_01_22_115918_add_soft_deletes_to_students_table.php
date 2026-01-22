<?php

/**
 * Migration: Add Soft Deletes to Students Table
 *
 * This migration adds the 'deleted_at' column to enable soft deletes
 * for the students table. Soft deletes allow "deleting" records without
 * actually removing them from the database.
 *
 * Why Soft Deletes?
 * - Preserve borrowing history even after student graduates/leaves
 * - Allow recovery of accidentally deleted records
 * - Maintain data integrity for reporting and auditing
 *
 * How it works:
 * - When a student is "deleted", deleted_at is set to current timestamp
 * - Normal queries automatically exclude soft-deleted records
 * - Use withTrashed() to include deleted records in queries
 * - Use onlyTrashed() to get only deleted records
 * - Use restore() to recover a soft-deleted record
 *
 * @see App\Models\Student (uses SoftDeletes trait)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds the deleted_at column for soft deletes.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Add deleted_at column for soft deletes
            // This column stores the timestamp when the record was "deleted"
            // NULL means the record is active (not deleted)
            $table->softDeletes()->after('guardian_contact');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Removes the deleted_at column.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
