<?php

/**
 * CategoryController
 *
 * This controller handles category management operations.
 * Categories are used to organize books into groups (e.g., Fiction, Science, Math).
 *
 * Features:
 * - Simple CRUD operations
 * - Check for associated books before deletion
 * - Flash messages for user feedback
 *
 * @see App\Models\Category
 * @see App\Http\Requests\CategoryRequest
 */

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     *
     * Shows all categories with their book counts.
     * Categories can be managed inline on this page.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Get all categories with book count, ordered by name
        $categories = Category::withCount('books')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Store a newly created category in the database.
     *
     * @param \App\Http\Requests\CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $category = Category::create($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', "Category '{$category->name}' has been created successfully.");
    }

    /**
     * Display the specified category.
     *
     * Shows category details and its books.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\View\View
     */
    public function show(Category $category): View
    {
        // Load books in this category with pagination
        $books = $category->books()
            ->orderBy('title')
            ->paginate(12);

        return view('categories.show', compact('category', 'books'));
    }

    /**
     * Show the form for editing the specified category.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\View\View
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in the database.
     *
     * @param \App\Http\Requests\CategoryRequest $request
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        $category->update($validated);

        return redirect()
            ->route('categories.index')
            ->with('success', "Category '{$category->name}' has been updated successfully.");
    }

    /**
     * Remove the specified category from the database.
     *
     * Checks if category has associated books before deletion.
     *
     * @param \App\Models\Category $category
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Category $category): RedirectResponse
    {
        // Check if category has books
        if ($category->books()->count() > 0) {
            return redirect()
                ->route('categories.index')
                ->with('error', "Cannot delete '{$category->name}'. This category has {$category->books()->count()} book(s) associated with it.");
        }

        $categoryName = $category->name;
        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', "Category '{$categoryName}' has been deleted successfully.");
    }
}
