{{--
    Books Index Page

    This page displays the book catalog with:
    - Statistics cards (total books, copies, available, borrowed)
    - Books by category chart
    - Add book button
    - Livewire-powered searchable/filterable book catalog

    @see App\Http\Controllers\BookController::index()
    @see App\Livewire\BookSearchCatalog
--}}

@extends('layouts.app')

@section('title', 'Book Catalog')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                    Book Catalog
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Manage and browse the library's book collection
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('books.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    Add Book
                </a>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="mb-6 rounded-md bg-success-50 p-4">
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
            <div class="mb-6 rounded-md bg-danger-50 p-4">
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

        {{-- Statistics Cards --}}
        <div class="mb-6 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Total Book Titles --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-primary-100 rounded-lg">
                                <svg class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Book Titles
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ number_format($statistics['total_books']) }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Copies --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-gray-100 rounded-lg">
                                <svg class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Total Copies
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ number_format($statistics['total_copies']) }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Available Copies --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-success-100 rounded-lg">
                                <svg class="h-6 w-6 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Available
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-success-600">
                                        {{ number_format($statistics['available_copies']) }}
                                    </div>
                                    <span class="ml-2 text-sm text-gray-500">copies</span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Currently Borrowed --}}
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 bg-warning-100 rounded-lg">
                                <svg class="h-6 w-6 text-warning-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Borrowed
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-warning-600">
                                        {{ number_format($statistics['borrowed_copies']) }}
                                    </div>
                                    <span class="ml-2 text-sm text-gray-500">copies</span>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Books by Category --}}
        @if ($statistics['by_category']->count() > 0)
            <div class="mb-6 bg-white shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">
                        Books by Category
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Top 5 categories in your collection
                    </p>
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <div class="space-y-4">
                        @foreach ($statistics['by_category'] as $category)
                            @php
                                $percentage = $statistics['total_books'] > 0
                                    ? ($category['count'] / $statistics['total_books']) * 100
                                    : 0;
                            @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700">{{ $category['name'] }}</span>
                                    <span class="text-sm text-gray-500">{{ $category['count'] }} {{ Str::plural('book', $category['count']) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-primary-600 h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                    <a href="{{ route('categories.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-500">
                        Manage Categories &rarr;
                    </a>
                </div>
            </div>
        @endif

        {{-- Book Catalog (Livewire Component) --}}
        <livewire:book-search-catalog />
    </div>
</div>
@endsection
