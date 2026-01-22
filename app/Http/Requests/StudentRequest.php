<?php

/**
 * StudentRequest Form Request
 *
 * This class handles validation for student create and update operations.
 * Form Requests centralize validation logic and keep controllers clean.
 *
 * How Form Requests Work:
 * 1. Controller type-hints this class in method signature
 * 2. Laravel automatically validates before controller method runs
 * 3. If validation fails, user is redirected back with errors
 * 4. If validation passes, controller receives validated data
 *
 * Usage in Controller:
 * public function store(StudentRequest $request) {
 *     // Validation already passed if we reach here
 *     $validated = $request->validated();
 *     Student::create($validated);
 * }
 *
 * @see App\Http\Controllers\StudentController
 * @see App\Models\Student
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Returns true if the user is authenticated.
     * Additional role-based checks can be added here if needed.
     *
     * @return bool True if authorized, false otherwise
     */
    public function authorize(): bool
    {
        // Only authenticated users can manage students
        // Additional role checks can be added:
        // return auth()->check() && auth()->user()->canManageLibrary();
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * These rules are applied to both create and update operations.
     * The student_id unique rule ignores the current student on update.
     *
     * Validation Rules Explained:
     * - required: Field must be present and not empty
     * - max:N: Maximum N characters
     * - unique:table,column: Must be unique in the table
     * - in:a,b,c: Must be one of the listed values
     * - nullable: Field can be empty/null
     * - regex: Must match the pattern (Philippine phone format)
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the student ID for unique rule exception during updates
        // When updating, we need to ignore the current student's student_id
        $studentId = $this->route('student')?->id;

        return [
            // ===== REQUIRED FIELDS =====

            // Student ID: Required, unique, max 50 characters
            // Format: School's student ID system (e.g., "2024-0001")
            'student_id' => [
                'required',
                'string',
                'max:50',
                // Unique in students table, ignore current student on update
                Rule::unique('students', 'student_id')->ignore($studentId),
            ],

            // First Name: Required, max 100 characters
            'first_name' => [
                'required',
                'string',
                'max:100',
            ],

            // Last Name: Required, max 100 characters
            'last_name' => [
                'required',
                'string',
                'max:100',
            ],

            // Grade Level: Required, must be 1-6 (elementary grades)
            'grade_level' => [
                'required',
                'in:1,2,3,4,5,6',
            ],

            // Section: Required, max 50 characters
            // Example: "Section A", "Sampaguita", "Mahogany"
            'section' => [
                'required',
                'string',
                'max:50',
            ],

            // ===== OPTIONAL FIELDS =====

            // Middle Name: Optional, max 100 characters
            'middle_name' => [
                'nullable',
                'string',
                'max:100',
            ],

            // Status: Optional (defaults to 'active' in controller)
            // Must be one of: active, inactive, graduated
            'status' => [
                'nullable',
                'in:active,inactive,graduated',
            ],

            // Contact Number: Optional, Philippine phone format
            // Accepts: 09XXXXXXXXX or +639XXXXXXXXX
            'contact_number' => [
                'nullable',
                'string',
                'max:20',
                // Optional: Add phone format validation
                // 'regex:/^(09|\+639)\d{9}$/',
            ],

            // Guardian Name: Optional, max 255 characters
            'guardian_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            // Guardian Contact: Optional, Philippine phone format
            'guardian_contact' => [
                'nullable',
                'string',
                'max:20',
                // Optional: Add phone format validation
                // 'regex:/^(09|\+639)\d{9}$/',
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * These messages are shown to users when validation fails.
     * Makes error messages more user-friendly and specific.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Student ID messages
            'student_id.required' => 'The student ID is required.',
            'student_id.unique' => 'This student ID is already registered.',
            'student_id.max' => 'The student ID cannot exceed 50 characters.',

            // Name messages
            'first_name.required' => 'The first name is required.',
            'first_name.max' => 'The first name cannot exceed 100 characters.',
            'last_name.required' => 'The last name is required.',
            'last_name.max' => 'The last name cannot exceed 100 characters.',
            'middle_name.max' => 'The middle name cannot exceed 100 characters.',

            // Grade and Section messages
            'grade_level.required' => 'Please select a grade level.',
            'grade_level.in' => 'Please select a valid grade level (1-6).',
            'section.required' => 'The section is required.',
            'section.max' => 'The section name cannot exceed 50 characters.',

            // Status messages
            'status.in' => 'Please select a valid status.',

            // Contact messages
            'contact_number.max' => 'The contact number cannot exceed 20 characters.',
            'guardian_name.max' => 'The guardian name cannot exceed 255 characters.',
            'guardian_contact.max' => 'The guardian contact cannot exceed 20 characters.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     *
     * Makes error messages more readable by providing
     * human-friendly names for form fields.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'student_id' => 'student ID',
            'first_name' => 'first name',
            'last_name' => 'last name',
            'middle_name' => 'middle name',
            'grade_level' => 'grade level',
            'contact_number' => 'contact number',
            'guardian_name' => 'guardian name',
            'guardian_contact' => 'guardian contact',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * This method runs before validation and allows you to
     * modify or sanitize input data.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from string fields
        $this->merge([
            'student_id' => trim($this->student_id ?? ''),
            'first_name' => trim($this->first_name ?? ''),
            'last_name' => trim($this->last_name ?? ''),
            'middle_name' => trim($this->middle_name ?? '') ?: null,
            'section' => trim($this->section ?? ''),
            'contact_number' => trim($this->contact_number ?? '') ?: null,
            'guardian_name' => trim($this->guardian_name ?? '') ?: null,
            'guardian_contact' => trim($this->guardian_contact ?? '') ?: null,
        ]);
    }
}
