{{--
    Book Show Page

    This page displays detailed information about a book including:
    - Book cover and basic info
    - Full details (ISBN, publisher, year, etc.)
    - Current borrowers
    - Borrowing history with pagination
    - Action buttons (edit, delete)

    @see App\Http\Controllers\BookController::show()
--}}

@extends('layouts.app')

@section('title', $book->title)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        <span class="text-sm font-medium text-gray-900">{{ Str::limit($book->title, 50) }}</span>
                    </li>
                </ol>
            </nav>
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Book Cover and Quick Info --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Book Cover Card --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    {{-- Cover Image --}}
                    <div class="aspect-[3/4] bg-gray-100">
                        @if ($book->cover_image)
                            <img
                                src="{{ Storage::url($book->cover_image) }}"
                                alt="{{ $book->title }}"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                <svg class="h-24 w-24 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Quick Actions --}}
                    <div class="px-4 py-4 space-y-3">
                        <a href="{{ route('books.edit', $book) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                            </svg>
                            Edit Book
                        </a>
                        <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this book? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-danger-300 shadow-sm text-sm font-medium rounded-md text-danger-700 bg-white hover:bg-danger-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-danger-500">
                                <svg class="-ml-1 mr-2 h-5 w-5 text-danger-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 006 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 10.23 1.482l.149-.022.841 10.518A2.75 2.75 0 007.596 19h4.807a2.75 2.75 0 002.742-2.53l.841-10.519.149.023a.75.75 0 00.23-1.482A41.03 41.03 0 0014 4.193V3.75A2.75 2.75 0 0011.25 1h-2.5zM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4zM8.58 7.72a.75.75 0 00-1.5.06l.3 7.5a.75.75 0 101.5-.06l-.3-7.5zm4.34.06a.75.75 0 10-1.5-.06l-.3 7.5a.75.75 0 101.5.06l.3-7.5z" clip-rule="evenodd" />
                                </svg>
                                Delete Book
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Availability Card --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Availability
                        </h3>

                        <div class="space-y-4">
                            {{-- Status Badge --}}
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Status</span>
                                @if ($book->status === 'unavailable')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Unavailable
                                    </span>
                                @elseif ($book->copies_available === 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-800">
                                        All Borrowed
                                    </span>
                                @elseif ($book->copies_available <= 2)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800">
                                        Limited
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                                        Available
                                    </span>
                                @endif
                            </div>

                            {{-- Copies --}}
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Available Copies</span>
                                <span class="text-sm font-medium text-gray-900">{{ $book->copies_available }} of {{ $book->copies_total }}</span>
                            </div>

                            {{-- Visual Progress Bar --}}
                            @php
                                $availabilityPercentage = $book->copies_total > 0
                                    ? ($book->copies_available / $book->copies_total) * 100
                                    : 0;
                            @endphp
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div
                                    class="h-2.5 rounded-full {{ $availabilityPercentage > 50 ? 'bg-success-600' : ($availabilityPercentage > 20 ? 'bg-warning-500' : 'bg-danger-600') }}"
                                    style="width: {{ $availabilityPercentage }}%"
                                ></div>
                            </div>

                            {{-- Condition --}}
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Condition</span>
                                <span class="text-sm font-medium text-gray-900 capitalize">{{ $book->condition ?? 'Not specified' }}</span>
                            </div>

                            {{-- Location --}}
                            @if ($book->location)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">Location</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $book->location }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Book Details and History --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Book Details Card --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <div class="flex items-start justify-between">
                            <div>
                                <h1 class="text-xl font-bold text-gray-900">
                                    {{ $book->title }}
                                </h1>
                                <p class="mt-1 text-sm text-gray-500">
                                    by {{ $book->author }}
                                </p>
                            </div>
                            @if ($book->category)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                                    {{ $book->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            {{-- Accession Number --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Accession Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $book->accession_number }}</dd>
                            </div>

                            {{-- ISBN --}}
                            @if ($book->isbn)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ISBN</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $book->isbn }}</dd>
                                </div>
                            @endif

                            {{-- Publisher --}}
                            @if ($book->publisher)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Publisher</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $book->publisher }}</dd>
                                </div>
                            @endif

                            {{-- Publication Year --}}
                            @if ($book->publication_year)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Publication Year</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $book->publication_year }}</dd>
                                </div>
                            @endif

                            {{-- Edition --}}
                            @if ($book->edition)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Edition</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $book->edition }}</dd>
                                </div>
                            @endif

                            {{-- Pages --}}
                            @if ($book->pages)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pages</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $book->pages }}</dd>
                                </div>
                            @endif

                            {{-- Date Added --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date Added</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $book->created_at->format('M d, Y') }}</dd>
                            </div>

                            {{-- Last Updated --}}
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $book->updated_at->format('M d, Y') }}</dd>
                            </div>
                        </dl>

                        {{-- Description --}}
                        @if ($book->description)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <dt class="text-sm font-medium text-gray-500 mb-2">Description</dt>
                                <dd class="text-sm text-gray-900">{{ $book->description }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Current Borrowers Card --}}
                @if ($currentBorrowers->count() > 0)
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">
                                Current Borrowers
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Students who currently have this book
                            </p>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            @foreach ($currentBorrowers as $transaction)
                                <li class="px-4 py-4 sm:px-6">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-primary-600">
                                                    {{ substr($transaction->student->first_name, 0, 1) }}{{ substr($transaction->student->last_name, 0, 1) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $transaction->student->full_name }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $transaction->student->student_id }} - Grade {{ $transaction->student->grade_level }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-900">
                                                Borrowed: {{ $transaction->borrowed_date->format('M d, Y') }}
                                            </p>
                                            <p class="text-sm {{ $transaction->status === 'overdue' ? 'text-danger-600 font-medium' : 'text-gray-500' }}">
                                                Due: {{ $transaction->due_date->format('M d, Y') }}
                                                @if ($transaction->status === 'overdue')
                                                    (Overdue)
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Borrowing History Card --}}
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            Borrowing History
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Complete borrowing history for this book
                        </p>
                    </div>

                    @if ($borrowingHistory->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Student
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Borrowed
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Due
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Returned
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($borrowingHistory as $transaction)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                                        <span class="text-xs font-medium text-gray-600">
                                                            {{ substr($transaction->student->first_name, 0, 1) }}{{ substr($transaction->student->last_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $transaction->student->full_name }}
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            {{ $transaction->student->student_id }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->borrowed_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->due_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->returned_date ? $transaction->returned_date->format('M d, Y') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @switch($transaction->status)
                                                    @case('borrowed')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                                            Borrowed
                                                        </span>
                                                        @break
                                                    @case('returned')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                                                            Returned
                                                        </span>
                                                        @break
                                                    @case('overdue')
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-800">
                                                            Overdue
                                                        </span>
                                                        @break
                                                    @default
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ ucfirst($transaction->status) }}
                                                        </span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="px-4 py-3 border-t border-gray-200">
                            {{ $borrowingHistory->links() }}
                        </div>
                    @else
                        <div class="px-4 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No borrowing history</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                This book has not been borrowed yet.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
