<?php

/**
 * SettingRequest
 *
 * Form request for validating system settings updates.
 * Ensures all setting values are in the correct format.
 *
 * @package App\Http\Requests
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Only administrators can update settings.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Circulation Rules
            'max_books_per_student' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
            'borrowing_period' => [
                'required',
                'integer',
                'min:1',
                'max:30',
            ],
            'allow_renewals' => [
                'nullable',
                'boolean',
            ],
            'max_renewals' => [
                'required',
                'integer',
                'min:0',
                'max:5',
            ],

            // Fine Configuration
            'fine_per_day' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],
            'grace_period' => [
                'required',
                'integer',
                'min:0',
                'max:7',
            ],
            'max_fine_amount' => [
                'required',
                'numeric',
                'min:0',
                'max:1000',
            ],

            // School Information
            'school_name' => [
                'required',
                'string',
                'max:255',
            ],
            'school_address' => [
                'nullable',
                'string',
                'max:500',
            ],
            'library_name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'library_email' => [
                'nullable',
                'email',
                'max:255',
            ],
            'library_phone' => [
                'nullable',
                'string',
                'max:20',
            ],
            'library_hours' => [
                'nullable',
                'string',
                'max:100',
            ],

            // System Preferences
            'date_format' => [
                'required',
                'string',
                'in:M d, Y,d/m/Y,Y-m-d,F j, Y',
            ],
            'items_per_page' => [
                'required',
                'integer',
                'min:5',
                'max:100',
            ],
            'enable_email_notifications' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'max_books_per_student' => 'maximum books per student',
            'borrowing_period' => 'borrowing period',
            'allow_renewals' => 'allow renewals',
            'max_renewals' => 'maximum renewals',
            'fine_per_day' => 'fine per day',
            'grace_period' => 'grace period',
            'max_fine_amount' => 'maximum fine amount',
            'school_name' => 'school name',
            'school_address' => 'school address',
            'library_name' => 'library name',
            'library_email' => 'library email',
            'library_phone' => 'library phone',
            'library_hours' => 'library hours',
            'date_format' => 'date format',
            'items_per_page' => 'items per page',
            'enable_email_notifications' => 'email notifications',
        ];
    }

    /**
     * Get custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'max_books_per_student.min' => 'Students must be able to borrow at least 1 book.',
            'max_books_per_student.max' => 'Maximum books per student cannot exceed 10.',
            'borrowing_period.min' => 'Borrowing period must be at least 1 day.',
            'borrowing_period.max' => 'Borrowing period cannot exceed 30 days.',
            'fine_per_day.min' => 'Fine per day cannot be negative.',
            'fine_per_day.max' => 'Fine per day cannot exceed P100.00.',
            'grace_period.max' => 'Grace period cannot exceed 7 days.',
            'library_email.email' => 'Please enter a valid email address.',
            'items_per_page.min' => 'Minimum items per page is 5.',
            'items_per_page.max' => 'Maximum items per page is 100.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * Convert checkbox values to proper booleans.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox values (checkboxes not submitted = false)
        $this->merge([
            'allow_renewals' => $this->has('allow_renewals') ? 1 : 0,
            'enable_email_notifications' => $this->has('enable_email_notifications') ? 1 : 0,
        ]);
    }
}
