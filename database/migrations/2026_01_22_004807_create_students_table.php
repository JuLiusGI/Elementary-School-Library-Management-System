<?php

/**
 * Migration: Create Students Table
 *
 * This migration creates the 'students' table which stores all student
 * information for the library system. Each student can borrow books
 * and may accumulate fines for overdue returns.
 *
 * Table Structure:
 * - Basic info: student_id, name fields
 * - Academic info: grade_level (1-6), section
 * - Status tracking: active, inactive, or graduated
 * - Contact info: student and guardian contact details
 *
 * Indexes are added on frequently queried columns for performance:
 * - student_id: For quick lookup by school ID
 * - grade_level: For filtering by grade
 * - status: For filtering active/inactive students
 *
 * @see App\Models\Student
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (students table)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the students table with all required fields and indexes.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            // Primary key - auto-incrementing unique identifier
            $table->id();

            // ===== STUDENT IDENTIFICATION =====

            // School-assigned student ID (e.g., "2024-001", "2024-002")
            // Must be unique across all students
            // Used for quick lookup when borrowing books
            $table->string('student_id', 50)
                  ->unique()
                  ->comment('School-assigned student ID number');

            // ===== NAME FIELDS =====

            // Student's first name (required)
            $table->string('first_name', 100)
                  ->comment('Student first name');

            // Student's last name (required)
            $table->string('last_name', 100)
                  ->comment('Student last name');

            // Student's middle name (optional - some students may not have one)
            $table->string('middle_name', 100)
                  ->nullable()
                  ->comment('Student middle name (optional)');

            // ===== ACADEMIC INFORMATION =====

            // Grade level: 1 through 6 for elementary school
            // Using enum to restrict valid values
            $table->enum('grade_level', ['1', '2', '3', '4', '5', '6'])
                  ->comment('Student grade level (1-6)');

            // Class section (e.g., "A", "B", "Mabini", "Rizal")
            // Each grade may have multiple sections
            $table->string('section', 50)
                  ->comment('Class section name');

            // ===== STATUS TRACKING =====

            // Student status for tracking enrollment state:
            // - 'active': Currently enrolled, can borrow books
            // - 'inactive': Temporarily not enrolled, cannot borrow
            // - 'graduated': Completed elementary, cannot borrow
            $table->enum('status', ['active', 'inactive', 'graduated'])
                  ->default('active')
                  ->comment('Enrollment status: active can borrow, others cannot');

            // ===== CONTACT INFORMATION =====

            // Student's contact number (optional)
            // May be parent's phone if student doesn't have one
            $table->string('contact_number', 20)
                  ->nullable()
                  ->comment('Student or parent contact number');

            // Guardian/Parent name for emergency contact
            $table->string('guardian_name', 255)
                  ->nullable()
                  ->comment('Parent or guardian full name');

            // Guardian contact number for notifications about overdue books
            $table->string('guardian_contact', 20)
                  ->nullable()
                  ->comment('Guardian phone number for overdue notifications');

            // ===== TIMESTAMPS =====

            // created_at: When the student record was created
            // updated_at: When the student record was last modified
            $table->timestamps();

            // ===== INDEXES FOR PERFORMANCE =====

            // Composite index for common query patterns:
            // - Filtering by grade and status (e.g., "all active Grade 3 students")
            // - Quick lookup by student_id
            $table->index(['student_id', 'grade_level', 'status'], 'students_lookup_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the students table.
     * WARNING: This will delete all student data!
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
