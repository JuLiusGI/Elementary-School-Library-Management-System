<?php

/**
 * BookRequest Form Request
 *
 * This class handles validation for book create and update operations.
 * Books are the core entity of the library system.
 *
 * Validation includes:
 * - Required fields: accession_number, title, author, category_id, copies
 * - Optional fields: ISBN, publisher, year, edition, pages, location, etc.
 * - Image upload: cover_image (max 2MB, image types only)
 * - Copy validation: copies_available must be <= copies_total
 *
 * @see App\Http\Controllers\BookController
 * @see App\Models\Book
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if authorized
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get book ID for unique rule exception during updates
        $bookId = $this->route('book')?->id;

        // Check if this is an update request
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            // ===== REQUIRED FIELDS =====

            // Accession Number: Library's unique identifier
            // Required on create, may be auto-generated if empty
            'accession_number' => [
                $isUpdate ? 'required' : 'nullable', // Auto-generate on create if empty
                'string',
                'max:50',
                Rule::unique('books', 'accession_number')->ignore($bookId),
            ],

            // Title: Book title (required)
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            // Author: Book author(s) (required)
            'author' => [
                'required',
                'string',
                'max:255',
            ],

            // Category: Must exist in categories table (required)
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            // Total Copies: Number of copies owned (required)
            'copies_total' => [
                'required',
                'integer',
                'min:1',
                'max:9999',
            ],

            // Available Copies: Must not exceed total copies
            'copies_available' => [
                'required',
                'integer',
                'min:0',
                'lte:copies_total', // Less than or equal to copies_total
            ],

            // ===== OPTIONAL FIELDS =====

            // ISBN: International Standard Book Number
            'isbn' => [
                'nullable',
                'string',
                'max:13',
            ],

            // Publisher: Publishing company
            'publisher' => [
                'nullable',
                'string',
                'max:255',
            ],

            // Publication Year: 4-digit year
            'publication_year' => [
                'nullable',
                'integer',
                'min:1800',
                'max:' . (date('Y') + 1), // Can't be in future
            ],

            // Edition: e.g., "1st Edition"
            'edition' => [
                'nullable',
                'string',
                'max:50',
            ],

            // Pages: Number of pages
            'pages' => [
                'nullable',
                'integer',
                'min:1',
                'max:99999',
            ],

            // Location: Physical location in library
            'location' => [
                'nullable',
                'string',
                'max:100',
            ],

            // Condition: Physical condition of the book
            'condition' => [
                'nullable',
                'in:excellent,good,fair,poor',
            ],

            // Description: Book summary/description
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            // Status: Overall availability status
            'status' => [
                'nullable',
                'in:available,unavailable',
            ],

            // Cover Image: Image file upload
            // Max 2MB, must be an image type
            'cover_image' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:2048', // 2MB in kilobytes
            ],
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // Required fields
            'accession_number.required' => 'The accession number is required.',
            'accession_number.unique' => 'This accession number is already in use.',
            'accession_number.max' => 'The accession number cannot exceed 50 characters.',

            'title.required' => 'The book title is required.',
            'title.max' => 'The book title cannot exceed 255 characters.',

            'author.required' => 'The author name is required.',
            'author.max' => 'The author name cannot exceed 255 characters.',

            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'The selected category is invalid.',

            'copies_total.required' => 'The total copies is required.',
            'copies_total.integer' => 'The total copies must be a number.',
            'copies_total.min' => 'The library must have at least 1 copy.',

            'copies_available.required' => 'The available copies is required.',
            'copies_available.integer' => 'The available copies must be a number.',
            'copies_available.min' => 'Available copies cannot be negative.',
            'copies_available.lte' => 'Available copies cannot exceed total copies.',

            // Optional fields
            'isbn.max' => 'The ISBN cannot exceed 13 characters.',
            'publisher.max' => 'The publisher name cannot exceed 255 characters.',
            'publication_year.integer' => 'The publication year must be a valid year.',
            'publication_year.min' => 'The publication year seems too old.',
            'publication_year.max' => 'The publication year cannot be in the future.',
            'edition.max' => 'The edition cannot exceed 50 characters.',
            'pages.integer' => 'The page count must be a number.',
            'pages.min' => 'The page count must be at least 1.',
            'location.max' => 'The location cannot exceed 100 characters.',
            'condition.in' => 'Please select a valid condition.',
            'description.max' => 'The description cannot exceed 2000 characters.',
            'status.in' => 'Please select a valid status.',

            // Image
            'cover_image.image' => 'The cover must be an image file.',
            'cover_image.mimes' => 'The cover image must be a JPEG, PNG, GIF, or WebP file.',
            'cover_image.max' => 'The cover image cannot be larger than 2MB.',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'accession_number' => 'accession number',
            'category_id' => 'category',
            'copies_total' => 'total copies',
            'copies_available' => 'available copies',
            'publication_year' => 'publication year',
            'cover_image' => 'cover image',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Trim string fields
        $this->merge([
            'accession_number' => trim($this->accession_number ?? '') ?: null,
            'title' => trim($this->title ?? ''),
            'author' => trim($this->author ?? ''),
            'isbn' => trim($this->isbn ?? '') ?: null,
            'publisher' => trim($this->publisher ?? '') ?: null,
            'edition' => trim($this->edition ?? '') ?: null,
            'location' => trim($this->location ?? '') ?: null,
            'description' => trim($this->description ?? '') ?: null,
        ]);

        // Set default status if not provided
        if (!$this->has('status') || empty($this->status)) {
            $this->merge(['status' => 'available']);
        }

        // Set default condition if not provided
        if (!$this->has('condition') || empty($this->condition)) {
            $this->merge(['condition' => 'good']);
        }
    }
}
