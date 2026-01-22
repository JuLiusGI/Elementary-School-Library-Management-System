{{--
    Categories Index Page

    This page displays all book categories with:
    - List of categories with book counts
    - Add new category form (inline)
    - Edit and delete actions for each category

    @see App\Http\Controllers\CategoryController::index()
--}}

@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Book Categories
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Organize your book collection into categories
            </p>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-4 rounded-md bg-success-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-success-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-success-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-md bg-danger-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-danger-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-danger-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Add Category Form --}}
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Add New Category
                        </h3>

                        <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
                            @csrf

                            {{-- Category Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Category Name <span class="text-danger-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    value="{{ old('name') }}"
                                    placeholder="e.g., Fiction, Science"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('name') border-danger-300 @enderror"
                                    required
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    name="description"
                                    id="description"
                                    rows="3"
                                    placeholder="Optional description..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('description') border-danger-300 @enderror"
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                </svg>
                                Add Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Categories List --}}
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            All Categories
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $categories->count() }} categories total
                        </p>
                    </div>

                    @if ($categories->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach ($categories as $category)
                                <li class="px-4 py-4 sm:px-6 hover:bg-gray-50" x-data="{ editing: false }">
                                    {{-- View Mode --}}
                                    <div x-show="!editing" class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-primary-100">
                                                        <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                        </svg>
                                                    </span>
                                                </div>
                                                <div class="ml-4">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $category->name }}
                                                    </p>
                                                    @if ($category->description)
                                                        <p class="text-sm text-gray-500 truncate max-w-md">
                                                            {{ $category->description }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $category->books_count }} {{ Str::plural('book', $category->books_count) }}
                                            </span>
                                            <div class="flex items-center space-x-2">
                                                <button @click="editing = true" class="text-primary-600 hover:text-primary-900" title="Edit">
                                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                                    </svg>
                                                </button>
                                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-danger-600 hover:text-danger-900" title="Delete">
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.519.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Edit Mode --}}
                                    <div x-show="editing" x-cloak>
                                        <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-3">
                                            @csrf
                                            @method('PUT')

                                            <div>
                                                <input
                                                    type="text"
                                                    name="name"
                                                    value="{{ $category->name }}"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                    required
                                                >
                                            </div>

                                            <div>
                                                <textarea
                                                    name="description"
                                                    rows="2"
                                                    placeholder="Description (optional)"
                                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                                >{{ $category->description }}</textarea>
                                            </div>

                                            <div class="flex justify-end space-x-2">
                                                <button type="button" @click="editing = false" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                                                    Save
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="px-4 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No categories</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get started by adding a new category using the form.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
