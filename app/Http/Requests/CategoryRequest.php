<?php

/**
 * CategoryRequest Form Request
 *
 * This class handles validation for category create and update operations.
 * Categories are simple entities with just a name and optional description.
 *
 * @see App\Http\Controllers\CategoryController
 * @see App\Models\Category
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        // Get category ID for unique rule exception during updates
        $categoryId = $this->route('category')?->id;

        return [
            // Category name: Required, unique, max 100 characters
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],

            // Description: Optional, text
            'description' => [
                'nullable',
                'string',
                'max:500',
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
            'name.required' => 'The category name is required.',
            'name.unique' => 'A category with this name already exists.',
            'name.max' => 'The category name cannot exceed 100 characters.',
            'description.max' => 'The description cannot exceed 500 characters.',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim($this->name ?? ''),
            'description' => trim($this->description ?? '') ?: null,
        ]);
    }
}
