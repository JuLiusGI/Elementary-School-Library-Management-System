<?php

/**
 * Student Model
 *
 * This model represents students who can borrow books from the library.
 * Students are the primary users of the library system (borrowers).
 *
 * Student Information:
 * - Personal details (name, grade, section)
 * - Guardian/contact information
 * - Borrowing status and history
 *
 * Business Rules:
 * - Students can borrow up to 3 books at a time (configurable in settings)
 * - Only active students can borrow books
 * - Students with unpaid fines may be restricted from borrowing
 *
 * Grade Levels: 1-6 (Elementary school grades)
 * Status: active (can borrow), inactive (cannot borrow), graduated (left school)
 *
 * @see database/migrations/2026_01_22_004807_create_students_table.php
 * @see App\Services\BorrowingService
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (students table)
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    /**
     * Use HasFactory trait to enable model factories for testing.
     *
     * Factories allow you to create fake student data for testing:
     * Student::factory()->create(); // Creates a student in database
     * Student::factory()->make();   // Creates student instance without saving
     */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * These fields can be filled using Student::create([...]) or $student->fill([...])
     * All student fields except 'id' and timestamps are fillable.
     *
     * Security Note: Only include fields that should be user-editable.
     * The 'id', 'created_at', and 'updated_at' are managed by Laravel.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',       // School-assigned student ID (e.g., "2024-0001")
        'first_name',       // Student's first name
        'last_name',        // Student's last name
        'middle_name',      // Student's middle name (optional)
        'grade_level',      // Grade 1-6
        'section',          // Class section (e.g., "Section A", "Sampaguita")
        'status',           // active, inactive, or graduated
        'contact_number',   // Contact phone number (optional)
        'guardian_name',    // Parent/guardian name (optional)
        'guardian_contact', // Guardian's phone number (optional)
    ];

    /**
     * The attributes that should be cast.
     *
     * Casting ensures consistent data types when accessing attributes.
     * Even though grade_level is stored as enum in database,
     * we cast to string for easier comparison and display.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'grade_level' => 'string',  // Cast enum to string for comparisons
            'status' => 'string',       // Cast enum to string for comparisons
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get all borrowing transactions for this student.
     *
     * A student can have many transactions over time - each time they
     * borrow a book, a new transaction is created.
     *
     * Usage:
     * $student->transactions;                    // All transactions
     * $student->transactions()->latest()->get(); // Newest first
     * $student->transactions()->where('status', 'borrowed')->get(); // Currently borrowed
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Get the student's full name.
     *
     * This is an "accessor" - a virtual attribute that combines multiple fields.
     * Laravel automatically calls this when you access $student->full_name
     *
     * Format: "Last Name, First Name Middle Name"
     * Example: "Dela Cruz, Juan Miguel" or "Santos, Maria" (if no middle name)
     *
     * Usage:
     * $student->full_name;  // Returns "Dela Cruz, Juan Miguel"
     *
     * In Blade templates:
     * {{ $student->full_name }}
     *
     * @return string The student's formatted full name
     */
    public function getFullNameAttribute(): string
    {
        // Start with "Last Name, First Name"
        $fullName = "{$this->last_name}, {$this->first_name}";

        // Add middle name if it exists
        if ($this->middle_name) {
            $fullName .= " {$this->middle_name}";
        }

        return $fullName;
    }

    /**
     * Get the student's display name (First Last format).
     *
     * Alternative name format for casual display.
     * Format: "First Name Last Name"
     * Example: "Juan Dela Cruz"
     *
     * Usage:
     * $student->display_name;  // Returns "Juan Dela Cruz"
     *
     * @return string The student's name in First Last format
     */
    public function getDisplayNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the student's grade level with "Grade" prefix.
     *
     * Returns formatted grade level for display.
     * Example: "Grade 3" instead of just "3"
     *
     * Usage:
     * $student->grade_display;  // Returns "Grade 3"
     *
     * @return string Formatted grade level
     */
    public function getGradeDisplayAttribute(): string
    {
        return "Grade {$this->grade_level}";
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope to filter only active students.
     *
     * Scopes are reusable query constraints. They make your code cleaner
     * by encapsulating common WHERE conditions.
     *
     * Usage:
     * Student::active()->get();                    // All active students
     * Student::active()->where('grade_level', 3)->get(); // Active Grade 3 students
     *
     * In controllers:
     * $students = Student::active()->orderBy('last_name')->paginate(10);
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by grade level.
     *
     * Usage:
     * Student::inGrade(3)->get();         // All Grade 3 students
     * Student::active()->inGrade(5)->get(); // Active Grade 5 students
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance
     * @param int|string $gradeLevel The grade level to filter by (1-6)
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeInGrade(Builder $query, int|string $gradeLevel): Builder
    {
        return $query->where('grade_level', $gradeLevel);
    }

    /**
     * Scope to search students by name or student ID.
     *
     * Searches across first_name, last_name, middle_name, and student_id.
     * Case-insensitive search using LIKE.
     *
     * Usage:
     * Student::search('Juan')->get();           // Find students named Juan
     * Student::active()->search('2024')->get(); // Active students with 2024 in ID
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance
     * @param string $searchTerm The term to search for
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('first_name', 'like', "%{$searchTerm}%")
              ->orWhere('last_name', 'like', "%{$searchTerm}%")
              ->orWhere('middle_name', 'like', "%{$searchTerm}%")
              ->orWhere('student_id', 'like', "%{$searchTerm}%");
        });
    }

    // =========================================================================
    // BORROWING METHODS
    // =========================================================================

    /**
     * Check if the student can borrow more books.
     *
     * A student can borrow if:
     * 1. They are an active student
     * 2. They haven't reached the maximum borrowing limit
     *
     * The maximum books per student is configured in settings (default: 3)
     *
     * Usage:
     * if ($student->canBorrow()) {
     *     // Allow borrowing
     * } else {
     *     // Show error message
     * }
     *
     * @return bool True if student can borrow, false otherwise
     */
    public function canBorrow(): bool
    {
        // Only active students can borrow
        if ($this->status !== 'active') {
            return false;
        }

        // Get maximum books allowed from settings (default: 3)
        $maxBooks = (int) Setting::get('max_books_per_student', 3);

        // Check if student has reached the limit
        return $this->currentBorrowedBooks()->count() < $maxBooks;
    }

    /**
     * Get the books currently borrowed by this student.
     *
     * Returns transactions where status is 'borrowed' or 'overdue'
     * (meaning the book hasn't been returned yet).
     *
     * Usage:
     * $borrowedBooks = $student->currentBorrowedBooks();
     * $count = $student->currentBorrowedBooks()->count();
     *
     * To get the actual book models:
     * $books = $student->currentBorrowedBooks()->with('book')->get();
     * foreach ($books as $transaction) {
     *     echo $transaction->book->title;
     * }
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany Query for current borrowed books
     */
    public function currentBorrowedBooks(): HasMany
    {
        return $this->transactions()
            ->whereIn('status', ['borrowed', 'overdue']);
    }

    /**
     * Get the number of books the student can still borrow.
     *
     * Calculates: max_books_per_student - currently_borrowed_count
     *
     * Usage:
     * $remaining = $student->remainingBorrowingCapacity();
     * echo "You can borrow {$remaining} more books.";
     *
     * @return int Number of additional books the student can borrow
     */
    public function remainingBorrowingCapacity(): int
    {
        $maxBooks = (int) Setting::get('max_books_per_student', 3);
        $currentlyBorrowed = $this->currentBorrowedBooks()->count();

        // Return 0 if somehow over limit, never negative
        return max(0, $maxBooks - $currentlyBorrowed);
    }

    /**
     * Calculate the total unpaid fines for this student.
     *
     * Sums up all fine_amount where fine_paid is false.
     *
     * Usage:
     * $totalFines = $student->totalFines();
     * if ($totalFines > 0) {
     *     echo "You have ₱{$totalFines} in unpaid fines.";
     * }
     *
     * @return float Total unpaid fine amount in Philippine Pesos
     */
    public function totalFines(): float
    {
        return (float) $this->transactions()
            ->where('fine_paid', false)
            ->where('fine_amount', '>', 0)
            ->sum('fine_amount');
    }

    /**
     * Check if the student has any unpaid fines.
     *
     * Usage:
     * if ($student->hasUnpaidFines()) {
     *     // Show warning or restrict borrowing
     * }
     *
     * @return bool True if student has unpaid fines, false otherwise
     */
    public function hasUnpaidFines(): bool
    {
        return $this->totalFines() > 0;
    }

    /**
     * Check if the student has any overdue books.
     *
     * Usage:
     * if ($student->hasOverdueBooks()) {
     *     // Show warning
     * }
     *
     * @return bool True if student has overdue books, false otherwise
     */
    public function hasOverdueBooks(): bool
    {
        return $this->transactions()
            ->where('status', 'overdue')
            ->exists();
    }
}
