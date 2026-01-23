{{--
    Return Book Form Livewire Component View

    This component handles book returns with:
    - Search for borrowed books by student or book
    - List of borrowed books with due dates
    - Fine calculation for overdue books
    - Book condition update
    - Fine payment option

    @see App\Livewire\ReturnBookForm
--}}

<div>
    {{-- Success Message --}}
    @if ($successMessage)
        <div class="mb-6 rounded-md bg-success-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-success-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-success-800">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif

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

    {{-- Overdue Alert --}}
    @if ($this->overdueCount > 0)
        <div class="mb-6 rounded-md bg-warning-50 border border-warning-200 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-warning-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-warning-800">
                        {{ $this->overdueCount }} {{ Str::plural('book', $this->overdueCount) }} currently overdue!
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column: Search and List --}}
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                        Return a Book
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">
                        Search for a borrowed book by student name, student ID, book title, or accession number.
                    </p>

                    {{-- Search Input --}}
                    <div class="mb-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input
                                wire:model.live.debounce.300ms="search"
                                type="text"
                                placeholder="Search by student or book..."
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 text-base"
                            >
                        </div>
                    </div>

                    {{-- Borrowed Books List --}}
                    @if ($this->transactions->count() > 0)
                        <div class="space-y-3">
                            @foreach ($this->transactions as $transaction)
                                <div
                                    wire:click="selectTransaction({{ $transaction->id }})"
                                    class="px-4 py-3 rounded-lg border cursor-pointer transition-colors duration-150 {{ $selectedTransactionId === $transaction->id ? 'bg-primary-50 border-primary-300' : 'bg-gray-50 border-gray-200 hover:bg-gray-100' }}"
                                >
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2">
                                                <p class="font-medium text-gray-900 truncate">{{ $transaction->book->title }}</p>
                                                @if ($transaction->status === 'overdue' || $transaction->due_date->isPast())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-danger-100 text-danger-800">
                                                        Overdue
                                                    </span>
                                                @elseif ($transaction->due_date->isToday())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-800">
                                                        Due Today
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-600">{{ $transaction->book->author }}</p>
                                            <p class="text-sm text-gray-500">
                                                <span class="font-medium">{{ $transaction->student->full_name }}</span>
                                                ({{ $transaction->student->student_id }})
                                            </p>
                                            <p class="text-xs text-gray-400">
                                                Borrowed: {{ $transaction->borrowed_date->format('M d, Y') }}
                                                • Due: {{ $transaction->due_date->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">
                                @if (strlen($search) >= 2)
                                    No borrowed books found matching "{{ $search }}"
                                @else
                                    No books currently borrowed
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right Column: Return Form --}}
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg sticky top-6">
                <div class="px-4 py-5 sm:p-6">
                    @if ($this->selectedTransaction)
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">Return Details</h4>

                        {{-- Book Info --}}
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="font-medium text-gray-900">{{ $this->selectedTransaction->book->title }}</p>
                            <p class="text-sm text-gray-500">{{ $this->selectedTransaction->book->author }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $this->selectedTransaction->book->accession_number }}</p>
                        </div>

                        {{-- Student Info --}}
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <p class="text-sm text-gray-500">Borrowed by</p>
                            <p class="font-medium text-gray-900">{{ $this->selectedTransaction->student->full_name }}</p>
                            <p class="text-sm text-gray-500">{{ $this->selectedTransaction->student->student_id }}</p>
                        </div>

                        {{-- Dates --}}
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Borrowed</dt>
                                    <dd class="text-gray-900">{{ $this->selectedTransaction->borrowed_date->format('M d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Due Date</dt>
                                    <dd class="{{ $this->selectedTransaction->due_date->isPast() ? 'text-danger-600 font-medium' : 'text-gray-900' }}">
                                        {{ $this->selectedTransaction->due_date->format('M d, Y') }}
                                    </dd>
                                </div>
                                @if ($this->daysOverdue > 0)
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Days Overdue</dt>
                                        <dd class="text-danger-600 font-medium">{{ $this->daysOverdue }} days</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        {{-- Fine Calculation with Detailed Breakdown --}}
                        @if ($this->calculatedFine > 0 && !empty($this->fineBreakdown))
                            <div class="mb-4 rounded-lg border border-danger-200 overflow-hidden">
                                {{-- Header --}}
                                <div class="bg-danger-50 px-3 py-2 border-b border-danger-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold text-danger-800">Fine Due</span>
                                        <span class="text-lg font-bold text-danger-600">₱{{ number_format($this->calculatedFine, 2) }}</span>
                                    </div>
                                </div>

                                {{-- Breakdown Details --}}
                                <div class="bg-white px-3 py-3 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Days Overdue:</span>
                                        <span class="font-medium text-danger-600">{{ $this->fineBreakdown['days_overdue'] }} days</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Grace Period:</span>
                                        <span class="font-medium text-success-600">- {{ $this->fineBreakdown['grace_period'] }} day(s)</span>
                                    </div>
                                    <hr class="border-gray-200">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Chargeable Days:</span>
                                        <span class="font-medium text-gray-900">{{ $this->fineBreakdown['chargeable_days'] }} days</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Rate per Day:</span>
                                        <span class="font-medium text-gray-900">₱{{ number_format($this->fineBreakdown['fine_per_day'], 2) }}</span>
                                    </div>
                                </div>

                                {{-- Formula --}}
                                <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                                    <p class="text-xs text-gray-600 font-mono">
                                        {{ $this->fineBreakdown['formula'] }}
                                    </p>
                                </div>
                            </div>

                            {{-- Pay Fine Now Option --}}
                            <div class="mb-4">
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        wire:model="payFineNow"
                                        type="checkbox"
                                        class="rounded border-gray-300 text-success-600 shadow-sm focus:ring-success-500"
                                    >
                                    <span class="ml-2 text-sm text-gray-600">Mark fine as paid now</span>
                                </label>
                                @if($payFineNow)
                                    <p class="mt-1 ml-6 text-xs text-success-600">Fine will be recorded as paid upon return.</p>
                                @endif
                            </div>
                        @elseif ($this->daysOverdue > 0 && $this->calculatedFine == 0)
                            {{-- Within Grace Period --}}
                            <div class="mb-4 p-3 bg-success-50 rounded-lg border border-success-200">
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-success-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                    </svg>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-success-800">Within Grace Period</p>
                                        <p class="text-xs text-success-700 mt-1">
                                            Book is {{ $this->daysOverdue }} day(s) overdue but within the {{ $this->finePolicy['grace_period'] }} day grace period. No fine will be charged.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Book Condition --}}
                        <div class="mb-4">
                            <label for="condition" class="block text-sm font-medium text-gray-700 mb-1">
                                Book Condition
                            </label>
                            <select
                                wire:model="condition"
                                id="condition"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            >
                                <option value="excellent">Excellent - Like new</option>
                                <option value="good">Good - Normal wear</option>
                                <option value="fair">Fair - Noticeable wear</option>
                                <option value="poor">Poor - Damaged</option>
                            </select>
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                Notes (Optional)
                            </label>
                            <textarea
                                wire:model="notes"
                                id="notes"
                                rows="2"
                                placeholder="Any notes about the return..."
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            ></textarea>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="space-y-2">
                            <button
                                wire:click="processReturn"
                                wire:loading.attr="disabled"
                                type="button"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="processReturn">
                                    Process Return
                                </span>
                                <span wire:loading wire:target="processReturn">
                                    Processing...
                                </span>
                            </button>
                            <button
                                wire:click="cancelSelection"
                                type="button"
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Select a book from the list to process return</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Fine Policy Info --}}
            <div class="mt-4 bg-gray-50 rounded-lg p-4">
                <h5 class="text-sm font-medium text-gray-700 mb-2">Fine Policy</h5>
                <ul class="text-xs text-gray-500 space-y-1">
                    <li>• Fine: ₱{{ number_format($this->finePolicy['fine_per_day'], 2) }} per day</li>
                    <li>• Grace Period: {{ $this->finePolicy['grace_period'] }} day(s)</li>
                    <li>• Fines start after the grace period ends</li>
                </ul>
            </div>
        </div>
    </div>
</div>
