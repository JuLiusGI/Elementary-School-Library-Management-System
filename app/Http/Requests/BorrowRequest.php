<?php

/**
 * BorrowRequest Form Request
 *
 * This class handles validation for book borrowing operations.
 * It ensures that:
 * - A valid student is selected
 * - A valid and available book is selected
 * - The due date (if provided) is a future date
 *
 * The actual eligibility checks (borrowing limits, fines, overdue books)
 * are handled by the BorrowingService, not here. This request only validates
 * the basic data format.
 *
 * @see App\Http\Controllers\TransactionController
 * @see App\Services\BorrowingService
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Book;

class BorrowRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Only authenticated users (librarians/admins) can borrow books.
     *
     * @return bool True if the user is logged in
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Rules:
     * - student_id: Must exist in students table
     * - book_id: Must exist in books table and have available copies
     * - due_date: Optional, but if provided must be a future date
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Student ID: Must be a valid student record
            'student_id' => [
                'required',
                'integer',
                'exists:students,id',
            ],

            // Book ID: Must be a valid book record
            // Additional availability check is done in the after() callback
            'book_id' => [
                'required',
                'integer',
                'exists:books,id',
            ],

            // Due Date: Optional custom due date
            // If not provided, the system will use the default from settings
            'due_date' => [
                'nullable',
                'date',
                'after:today', // Must be at least tomorrow
            ],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Adds a custom validation check to ensure the book is available.
     * This is done in after() because we need to check the database.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function after(): array
    {
        return [
            function ($validator) {
                // Only check if book_id passed initial validation
                if (!$validator->errors()->has('book_id') && $this->book_id) {
                    $book = Book::find($this->book_id);

                    // Check if book has available copies
                    if ($book && $book->copies_available <= 0) {
                        $validator->errors()->add(
                            'book_id',
                            'This book is not available. All copies are currently borrowed.'
                        );
                    }

                    // Check if book status is available
                    if ($book && $book->status === 'unavailable') {
                        $validator->errors()->add(
                            'book_id',
                            'This book is marked as unavailable (lost/withdrawn).'
                        );
                    }
                }
            }
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * Provides user-friendly error messages for each validation failure.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Student validation messages
            'student_id.required' => 'Please select a student.',
            'student_id.integer' => 'Invalid student selection.',
            'student_id.exists' => 'The selected student was not found in the system.',

            // Book validation messages
            'book_id.required' => 'Please select a book to borrow.',
            'book_id.integer' => 'Invalid book selection.',
            'book_id.exists' => 'The selected book was not found in the catalog.',

            // Due date validation messages
            'due_date.date' => 'Please enter a valid date.',
            'due_date.after' => 'The due date must be at least tomorrow.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     *
     * Makes error messages more readable by using friendly names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'student_id' => 'student',
            'book_id' => 'book',
            'due_date' => 'due date',
        ];
    }
}
