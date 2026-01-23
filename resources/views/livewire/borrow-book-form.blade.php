{{--
    Borrow Book Form Livewire Component View

    This is a multi-step form for borrowing books:
    Step 1: Search and select a student
    Step 2: Review student info and select a book
    Step 3: Confirm borrowing details
    Step 4: Success/Receipt

    @see App\Livewire\BorrowBookForm
--}}

<div>
    {{-- Error Message --}}
    @if ($errorMessage)
        <div class="mb-6 rounded-md bg-danger-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-danger-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-danger-800">{{ $errorMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Progress Steps --}}
    <nav aria-label="Progress" class="mb-8">
        <ol class="flex items-center justify-center">
            @foreach ([1 => 'Select Student', 2 => 'Select Book', 3 => 'Confirm', 4 => 'Complete'] as $stepNum => $stepName)
                <li class="relative {{ $stepNum < 4 ? 'pr-8 sm:pr-20' : '' }}">
                    @if ($stepNum < 4)
                        {{-- Connector line --}}
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full {{ $step > $stepNum ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                        </div>
                    @endif

                    <div class="relative flex items-center justify-center">
                        @if ($step > $stepNum)
                            {{-- Completed step --}}
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-600">
                                <svg class="h-6 w-6 text-white" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @elseif ($step === $stepNum)
                            {{-- Current step --}}
                            <span class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-primary-600 bg-white">
                                <span class="text-primary-600 font-semibold">{{ $stepNum }}</span>
                            </span>
                        @else
                            {{-- Upcoming step --}}
                            <span class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-gray-300 bg-white">
                                <span class="text-gray-500">{{ $stepNum }}</span>
                            </span>
                        @endif
                    </div>
                    <span class="absolute -bottom-6 left-1/2 -translate-x-1/2 text-xs font-medium {{ $step >= $stepNum ? 'text-primary-600' : 'text-gray-500' }}">
                        {{ $stepName }}
                    </span>
                </li>
            @endforeach
        </ol>
    </nav>

    <div class="mt-12">
        {{-- Step 1: Select Student --}}
        @if ($step === 1)
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                        Step 1: Select a Student
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Search for the student who wants to borrow a book.
                    </p>

                    {{-- Student Search --}}
                    <div class="max-w-xl">
                        <label for="studentSearch" class="block text-sm font-medium text-gray-700 mb-1">
                            Search Student
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input
                                wire:model.live.debounce.300ms="studentSearch"
                                type="text"
                                id="studentSearch"
                                placeholder="Enter student name or ID..."
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-base"
                                autofocus
                            >
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Type at least 2 characters to search</p>
                    </div>

                    {{-- Search Results --}}
                    @if (strlen($studentSearch) >= 2)
                        <div class="mt-6">
                            @if ($this->students->count() > 0)
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Search Results</h4>
                                <div class="space-y-2">
                                    @foreach ($this->students as $student)
                                        <button
                                            wire:click="selectStudent({{ $student->id }})"
                                            type="button"
                                            class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-primary-50 rounded-lg border border-gray-200 hover:border-primary-300 transition-colors duration-150"
                                        >
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $student->full_name }}</p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $student->student_id }} - Grade {{ $student->grade_level }}, {{ $student->section }}
                                                    </p>
                                                </div>
                                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-6">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No students found matching "{{ $studentSearch }}"</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Step 2: Select Book --}}
        @if ($step === 2)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Student Info Card --}}
                <div class="lg:col-span-1">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">Selected Student</h4>

                            @if ($this->selectedStudent)
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center">
                                        <span class="text-lg font-medium text-primary-600">
                                            {{ substr($this->selectedStudent->first_name, 0, 1) }}{{ substr($this->selectedStudent->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $this->selectedStudent->full_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $this->selectedStudent->student_id }}</p>
                                    </div>
                                </div>

                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Grade & Section</dt>
                                        <dd class="text-gray-900">Grade {{ $this->selectedStudent->grade_level }}, {{ $this->selectedStudent->section }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Books Borrowed</dt>
                                        <dd class="text-gray-900">{{ $this->studentEligibility['current_books']->count() }} / {{ $this->studentEligibility['max_books'] }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Can Borrow</dt>
                                        <dd class="text-gray-900">{{ $this->studentEligibility['remaining_capacity'] }} more</dd>
                                    </div>
                                    @if ($this->studentEligibility['unpaid_fines'] > 0)
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">Unpaid Fines</dt>
                                            <dd class="text-danger-600 font-medium">₱{{ number_format($this->studentEligibility['unpaid_fines'], 2) }}</dd>
                                        </div>
                                    @endif
                                </dl>

                                {{-- Current Borrowed Books --}}
                                @if ($this->studentEligibility['current_books']->count() > 0)
                                    <div class="mt-4 pt-4 border-t border-gray-200">
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">Currently Borrowed</h5>
                                        <ul class="space-y-2">
                                            @foreach ($this->studentEligibility['current_books'] as $transaction)
                                                <li class="text-sm">
                                                    <p class="text-gray-900 truncate">{{ $transaction->book->title }}</p>
                                                    <p class="text-xs {{ $transaction->status === 'overdue' || $transaction->due_date->isPast() ? 'text-danger-600' : 'text-gray-500' }}">
                                                        Due: {{ $transaction->due_date->format('M d, Y') }}
                                                        @if ($transaction->status === 'overdue' || $transaction->due_date->isPast())
                                                            (Overdue)
                                                        @endif
                                                    </p>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <button
                                    wire:click="previousStep"
                                    type="button"
                                    class="mt-4 w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Change Student
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Book Search --}}
                <div class="lg:col-span-2">
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                                Step 2: Select a Book
                            </h3>
                            <p class="text-sm text-gray-500 mb-6">
                                Search for the book to borrow.
                            </p>

                            {{-- Book Search Input --}}
                            <div>
                                <label for="bookSearch" class="block text-sm font-medium text-gray-700 mb-1">
                                    Search Book
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input
                                        wire:model.live.debounce.300ms="bookSearch"
                                        type="text"
                                        id="bookSearch"
                                        placeholder="Enter book title, author, or accession number..."
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-base"
                                    >
                                </div>
                            </div>

                            {{-- Book Search Results --}}
                            <div class="mt-6">
                                @if (strlen($bookSearch) >= 2)
                                    @if ($this->books->count() > 0)
                                        <h4 class="text-sm font-medium text-gray-700 mb-3">Available Books</h4>
                                        <div class="space-y-2">
                                            @foreach ($this->books as $book)
                                                <button
                                                    wire:click="selectBook({{ $book->id }})"
                                                    type="button"
                                                    class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-primary-50 rounded-lg border border-gray-200 hover:border-primary-300 transition-colors duration-150"
                                                >
                                                    <div class="flex items-center justify-between">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="font-medium text-gray-900 truncate">{{ $book->title }}</p>
                                                            <p class="text-sm text-gray-500">{{ $book->author }}</p>
                                                            <p class="text-xs text-gray-400">
                                                                {{ $book->accession_number }}
                                                                @if ($book->category)
                                                                    • {{ $book->category->name }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="ml-4 flex-shrink-0 text-right">
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                                                                {{ $book->copies_available }} available
                                                            </span>
                                                        </div>
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-6">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                            </svg>
                                            <p class="mt-2 text-sm text-gray-500">No available books found matching "{{ $bookSearch }}"</p>
                                        </div>
                                    @endif
                                @else
                                    <div class="text-center py-6 text-gray-500">
                                        <p class="text-sm">Type at least 2 characters to search for books</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Step 3: Confirm Details --}}
        @if ($step === 3)
            <div class="max-w-2xl mx-auto">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-6">
                            Step 3: Confirm Borrowing Details
                        </h3>

                        {{-- Summary --}}
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <dl class="space-y-4">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Student</dt>
                                    <dd class="text-sm text-gray-900">{{ $this->selectedStudent?->full_name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Student ID</dt>
                                    <dd class="text-sm text-gray-900">{{ $this->selectedStudent?->student_id }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Book Title</dt>
                                    <dd class="text-sm text-gray-900">{{ $this->selectedBook?->title }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Author</dt>
                                    <dd class="text-sm text-gray-900">{{ $this->selectedBook?->author }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Accession #</dt>
                                    <dd class="text-sm text-gray-900">{{ $this->selectedBook?->accession_number }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Due Date --}}
                        <div class="mb-6">
                            <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-1">
                                Due Date
                            </label>
                            <input
                                wire:model="dueDate"
                                type="date"
                                id="dueDate"
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            >
                            <p class="mt-1 text-sm text-gray-500">
                                Default borrowing period: {{ $this->settings['borrowing_period'] }} days.
                                Fine: ₱{{ number_format($this->settings['fine_per_day'], 2) }}/day after {{ $this->settings['grace_period'] }} day grace period.
                            </p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-between">
                            <button
                                wire:click="previousStep"
                                type="button"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                </svg>
                                Back
                            </button>
                            <button
                                wire:click="processBorrow"
                                wire:loading.attr="disabled"
                                type="button"
                                class="inline-flex items-center px-6 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="processBorrow">
                                    Confirm Borrow
                                </span>
                                <span wire:loading wire:target="processBorrow">
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Step 4: Success --}}
        @if ($step === 4 && $transaction)
            <div class="max-w-2xl mx-auto">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6 text-center">
                        {{-- Success Icon --}}
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-success-100 mb-4">
                            <svg class="h-8 w-8 text-success-600" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>

                        <h3 class="text-lg font-medium text-gray-900 mb-2">Book Borrowed Successfully!</h3>
                        <p class="text-sm text-gray-500 mb-6">The transaction has been recorded.</p>

                        {{-- Receipt Summary --}}
                        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                            <h4 class="text-sm font-medium text-gray-700 mb-3 text-center">Borrowing Receipt</h4>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Transaction ID</dt>
                                    <dd class="text-gray-900 font-mono">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Student</dt>
                                    <dd class="text-gray-900">{{ $transaction->student->full_name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Book</dt>
                                    <dd class="text-gray-900">{{ $transaction->book->title }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Borrowed Date</dt>
                                    <dd class="text-gray-900">{{ $transaction->borrowed_date->format('M d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Due Date</dt>
                                    <dd class="text-gray-900 font-medium text-primary-600">{{ $transaction->due_date->format('M d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Processed By</dt>
                                    <dd class="text-gray-900">{{ $transaction->librarian->name }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- Actions --}}
                        <div class="flex justify-center space-x-4">
                            <button
                                wire:click="startNew"
                                type="button"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700"
                            >
                                <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                </svg>
                                Borrow Another Book
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
