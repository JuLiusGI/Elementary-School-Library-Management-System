{{--
    Student Details Page

    This page displays comprehensive information about a student:
    - Personal information
    - Contact/guardian information
    - Current borrowed books
    - Borrowing history with pagination
    - Fine information

    @see App\Http\Controllers\StudentController::show()
--}}

@extends('layouts.app')

@section('title', $student->full_name . ' - Student Details')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <a href="{{ route('students.index') }}" class="text-gray-400 hover:text-gray-500">
                            <svg class="flex-shrink-0 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <a href="{{ route('students.index') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">Students</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500">{{ $student->full_name }}</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="mt-4 md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                        {{ $student->full_name }}
                    </h1>
                    <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zm0 13a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 15zm0-8a3 3 0 100 6 3 3 0 000-6zm5.657-1.596a.75.75 0 010 1.06l-1.061 1.061a.75.75 0 01-1.06-1.06l1.06-1.061a.75.75 0 011.061 0zm-9.193 9.193a.75.75 0 010 1.06l-1.06 1.061a.75.75 0 01-1.061-1.06l1.06-1.061a.75.75 0 011.061 0z" />
                            </svg>
                            {{ $student->student_id }}
                        </div>
                        <div class="mt-2 flex items-center text-sm text-gray-500">
                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.664 1.319a.75.75 0 01.672 0 41.059 41.059 0 018.198 5.424.75.75 0 01-.254 1.285 31.372 31.372 0 00-7.86 3.83.75.75 0 01-.84 0 31.508 31.508 0 00-7.86-3.83.75.75 0 01-.254-1.285 41.059 41.059 0 018.198-5.424zM6.5 9.5a.75.75 0 01.75.75v6.5a.75.75 0 01-1.5 0v-6.5a.75.75 0 01.75-.75zm3.5 0a.75.75 0 01.75.75v6.5a.75.75 0 01-1.5 0v-6.5A.75.75 0 0110 9.5zm3.5 0a.75.75 0 01.75.75v6.5a.75.75 0 01-1.5 0v-6.5a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                            </svg>
                            {{ $student->grade_display }} - {{ $student->section }}
                        </div>
                        <div class="mt-2 flex items-center text-sm">
                            @php
                                $statusClasses = [
                                    'active' => 'text-success-600',
                                    'inactive' => 'text-gray-600',
                                    'graduated' => 'text-primary-600',
                                ];
                                $statusClass = $statusClasses[$student->status] ?? 'text-gray-600';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }} bg-opacity-10 {{ str_replace('text-', 'bg-', $statusClass) }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('students.edit', $student) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                        </svg>
                        Edit
                    </a>
                </div>
            </div>
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

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Left Column: Student Info --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Personal Information Card --}}
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Personal Information
                        </h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->full_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Student ID</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->student_id }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Grade Level</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->grade_display }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Section</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->section }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($student->status) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Contact Information Card --}}
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Contact Information
                        </h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->contact_number ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Guardian Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->guardian_name ?? 'Not provided' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Guardian Contact</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $student->guardian_contact ?? 'Not provided' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Borrowing Summary Card --}}
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Borrowing Summary
                        </h3>
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Currently Borrowed</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $currentBorrowedBooks->count() }} book(s)</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Remaining Slots</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $student->remainingBorrowingCapacity() }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500">Total Transactions</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $borrowingHistory->total() }}</dd>
                            </div>
                            @if ($totalFines > 0)
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <dt class="text-sm font-medium text-danger-600">Unpaid Fines</dt>
                                    <dd class="text-sm font-semibold text-danger-600">₱{{ number_format($totalFines, 2) }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Right Column: Borrowed Books & History --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Currently Borrowed Books --}}
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Currently Borrowed Books
                        </h3>
                        @if ($currentBorrowedBooks->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed Date</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($currentBorrowedBooks as $transaction)
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->book->title }}</div>
                                                    <div class="text-sm text-gray-500">{{ $transaction->book->author }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $transaction->borrowed_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $transaction->due_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    @if ($transaction->status === 'overdue')
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-danger-100 text-danger-800">
                                                            Overdue ({{ $transaction->daysOverdue() }} days)
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-primary-100 text-primary-800">
                                                            Borrowed
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No books currently borrowed</h3>
                                <p class="mt-1 text-sm text-gray-500">This student has no books checked out at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Borrowing History --}}
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                            Borrowing History
                        </h3>
                        @if ($borrowingHistory->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Borrowed</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Returned</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fine</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($borrowingHistory as $transaction)
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->book->title }}</div>
                                                    <div class="text-sm text-gray-500">{{ $transaction->book->accession_number }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $transaction->borrowed_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $transaction->returned_date ? $transaction->returned_date->format('M d, Y') : '-' }}
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->status_color }}">
                                                        {{ $transaction->status_label }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                    @if ($transaction->fine_amount > 0)
                                                        <span class="{{ $transaction->fine_paid ? 'text-gray-500' : 'text-danger-600 font-medium' }}">
                                                            {{ $transaction->formatted_fine }}
                                                            @if ($transaction->fine_paid)
                                                                <span class="text-success-600">(Paid)</span>
                                                            @endif
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Pagination --}}
                            @if ($borrowingHistory->hasPages())
                                <div class="mt-4 border-t border-gray-200 pt-4">
                                    {{ $borrowingHistory->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-6">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No borrowing history</h3>
                                <p class="mt-1 text-sm text-gray-500">This student hasn't borrowed any books yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
