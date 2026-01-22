{{--
    Book Create Page

    This page displays the form for adding a new book with:
    - Required fields: title, author, category, copies
    - Optional fields: ISBN, publisher, year, edition, pages, location, description
    - Image upload for book cover (max 2MB)
    - Auto-generate accession number option

    @see App\Http\Controllers\BookController::create()
    @see App\Http\Controllers\BookController::store()
--}}

@extends('layouts.app')

@section('title', 'Add Book')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="{{ route('books.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                            Books
                        </a>
                    </li>
                    <li>
                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>
                    </li>
                    <li>
                        <span class="text-sm font-medium text-gray-900">Add Book</span>
                    </li>
                </ol>
            </nav>
            <h1 class="mt-2 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Add New Book
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Add a new book to the library catalog
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="px-4 py-5 sm:p-6 space-y-6">
                    {{-- Required Fields Section --}}
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Basic Information
                        </h3>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            {{-- Accession Number --}}
                            <div class="sm:col-span-1">
                                <label for="accession_number" class="block text-sm font-medium text-gray-700">
                                    Accession Number
                                </label>
                                <input
                                    type="text"
                                    name="accession_number"
                                    id="accession_number"
                                    value="{{ old('accession_number') }}"
                                    placeholder="Leave empty to auto-generate"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('accession_number') border-danger-300 @enderror"
                                >
                                <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate (e.g., 2026-0001)</p>
                                @error('accession_number')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div class="sm:col-span-1">
                                <label for="category_id" class="block text-sm font-medium text-gray-700">
                                    Category <span class="text-danger-500">*</span>
                                </label>
                                <select
                                    name="category_id"
                                    id="category_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('category_id') border-danger-300 @enderror"
                                    required
                                >
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Title --}}
                            <div class="sm:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700">
                                    Title <span class="text-danger-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="title"
                                    id="title"
                                    value="{{ old('title') }}"
                                    placeholder="Enter book title"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('title') border-danger-300 @enderror"
                                    required
                                >
                                @error('title')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Author --}}
                            <div class="sm:col-span-2">
                                <label for="author" class="block text-sm font-medium text-gray-700">
                                    Author <span class="text-danger-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="author"
                                    id="author"
                                    value="{{ old('author') }}"
                                    placeholder="Enter author name(s)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('author') border-danger-300 @enderror"
                                    required
                                >
                                @error('author')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Copies Total --}}
                            <div class="sm:col-span-1">
                                <label for="copies_total" class="block text-sm font-medium text-gray-700">
                                    Total Copies <span class="text-danger-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    name="copies_total"
                                    id="copies_total"
                                    value="{{ old('copies_total', 1) }}"
                                    min="1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('copies_total') border-danger-300 @enderror"
                                    required
                                >
                                @error('copies_total')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Copies Available --}}
                            <div class="sm:col-span-1">
                                <label for="copies_available" class="block text-sm font-medium text-gray-700">
                                    Available Copies <span class="text-danger-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    name="copies_available"
                                    id="copies_available"
                                    value="{{ old('copies_available', 1) }}"
                                    min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('copies_available') border-danger-300 @enderror"
                                    required
                                >
                                <p class="mt-1 text-xs text-gray-500">Cannot exceed total copies</p>
                                @error('copies_available')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-200"></div>

                    {{-- Optional Fields Section --}}
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Additional Details
                        </h3>

                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            {{-- ISBN --}}
                            <div class="sm:col-span-1">
                                <label for="isbn" class="block text-sm font-medium text-gray-700">
                                    ISBN
                                </label>
                                <input
                                    type="text"
                                    name="isbn"
                                    id="isbn"
                                    value="{{ old('isbn') }}"
                                    placeholder="978-0-123456-78-9"
                                    maxlength="13"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('isbn') border-danger-300 @enderror"
                                >
                                @error('isbn')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Publisher --}}
                            <div class="sm:col-span-1">
                                <label for="publisher" class="block text-sm font-medium text-gray-700">
                                    Publisher
                                </label>
                                <input
                                    type="text"
                                    name="publisher"
                                    id="publisher"
                                    value="{{ old('publisher') }}"
                                    placeholder="Publishing company"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('publisher') border-danger-300 @enderror"
                                >
                                @error('publisher')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Publication Year --}}
                            <div class="sm:col-span-1">
                                <label for="publication_year" class="block text-sm font-medium text-gray-700">
                                    Publication Year
                                </label>
                                <input
                                    type="number"
                                    name="publication_year"
                                    id="publication_year"
                                    value="{{ old('publication_year') }}"
                                    placeholder="{{ date('Y') }}"
                                    min="1800"
                                    max="{{ date('Y') + 1 }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('publication_year') border-danger-300 @enderror"
                                >
                                @error('publication_year')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Edition --}}
                            <div class="sm:col-span-1">
                                <label for="edition" class="block text-sm font-medium text-gray-700">
                                    Edition
                                </label>
                                <input
                                    type="text"
                                    name="edition"
                                    id="edition"
                                    value="{{ old('edition') }}"
                                    placeholder="e.g., 1st Edition"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('edition') border-danger-300 @enderror"
                                >
                                @error('edition')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Pages --}}
                            <div class="sm:col-span-1">
                                <label for="pages" class="block text-sm font-medium text-gray-700">
                                    Number of Pages
                                </label>
                                <input
                                    type="number"
                                    name="pages"
                                    id="pages"
                                    value="{{ old('pages') }}"
                                    placeholder="e.g., 350"
                                    min="1"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('pages') border-danger-300 @enderror"
                                >
                                @error('pages')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Location --}}
                            <div class="sm:col-span-1">
                                <label for="location" class="block text-sm font-medium text-gray-700">
                                    Shelf Location
                                </label>
                                <input
                                    type="text"
                                    name="location"
                                    id="location"
                                    value="{{ old('location') }}"
                                    placeholder="e.g., Shelf A-3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('location') border-danger-300 @enderror"
                                >
                                @error('location')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Condition --}}
                            <div class="sm:col-span-1">
                                <label for="condition" class="block text-sm font-medium text-gray-700">
                                    Condition
                                </label>
                                <select
                                    name="condition"
                                    id="condition"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('condition') border-danger-300 @enderror"
                                >
                                    @foreach ($conditions as $value => $label)
                                        <option value="{{ $value }}" {{ old('condition', 'good') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('condition')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="sm:col-span-1">
                                <label for="status" class="block text-sm font-medium text-gray-700">
                                    Status
                                </label>
                                <select
                                    name="status"
                                    id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('status') border-danger-300 @enderror"
                                >
                                    @foreach ($statuses as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', 'available') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea
                                    name="description"
                                    id="description"
                                    rows="4"
                                    placeholder="Brief summary or description of the book..."
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('description') border-danger-300 @enderror"
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-gray-200"></div>

                    {{-- Cover Image Section --}}
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Cover Image
                        </h3>

                        <div class="sm:col-span-2">
                            <label for="cover_image" class="block text-sm font-medium text-gray-700">
                                Upload Cover
                            </label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="cover_image" class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                            <span>Upload a file</span>
                                            <input id="cover_image" name="cover_image" type="file" class="sr-only" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        PNG, JPG, GIF, WebP up to 2MB
                                    </p>
                                </div>
                            </div>
                            @error('cover_image')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-3 rounded-b-lg">
                    <a href="{{ route('books.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Add Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
