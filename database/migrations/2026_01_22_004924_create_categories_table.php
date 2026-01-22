<?php

/**
 * Migration: Create Categories Table
 *
 * This migration creates the 'categories' table for organizing books
 * into different categories/genres.
 *
 * Common categories for an elementary school library:
 * - Fiction (storybooks, novels)
 * - Non-Fiction (informational books)
 * - Reference (dictionaries, encyclopedias)
 * - Filipino (Tagalog literature)
 * - Science (science books)
 * - Math (mathematics books)
 * - History (historical books)
 * - Arts (art and music books)
 *
 * Each book belongs to exactly one category.
 *
 * @see App\Models\Category
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (categories table)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the categories table with name and description fields.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            // Primary key - auto-incrementing unique identifier
            $table->id();

            // Category name (e.g., "Fiction", "Science", "Filipino")
            // Must be unique - no duplicate category names allowed
            $table->string('name', 100)
                  ->unique()
                  ->comment('Category name - must be unique');

            // Optional description explaining what books belong in this category
            // Example: "Storybooks, novels, and other fictional works"
            $table->text('description')
                  ->nullable()
                  ->comment('Description of what books belong in this category');

            // created_at: When the category was created
            // updated_at: When the category was last modified
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the categories table.
     * WARNING: This will fail if books table exists with foreign key constraint!
     * Make sure to drop books table first or remove the constraint.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
