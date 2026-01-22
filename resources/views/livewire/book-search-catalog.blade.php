{{--
    Book Search Catalog Livewire Component View

    This view displays a searchable, filterable book catalog with:
    - Search bar for title, author, ISBN, accession number
    - Filter dropdowns for category, status, condition
    - Grid and list view toggle
    - Color-coded availability indicators
    - Pagination

    @see App\Livewire\BookSearchCatalog
--}}

<div>
    {{-- Search and Filter Bar --}}
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Search Input --}}
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                        Search Books
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input
                            wire:model.live.debounce.300ms="search"
                            type="text"
                            id="search"
                            placeholder="Search by title, author, ISBN, or accession number..."
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                        >
                    </div>
                </div>

                {{-- Category Filter --}}
                <div>
                    <label for="categoryId" class="block text-sm font-medium text-gray-700 mb-1">
                        Category
                    </label>
                    <select
                        wire:model.live="categoryId"
                        id="categoryId"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                    >
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>
                    <select
                        wire:model.live="status"
                        id="status"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                    >
                        <option value="">All Statuses</option>
                        <option value="available">Available</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </div>
            </div>

            {{-- Second Row: Condition Filter, View Toggle, Clear Filters --}}
            <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    {{-- Condition Filter --}}
                    <div>
                        <select
                            wire:model.live="condition"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                        >
                            <option value="">All Conditions</option>
                            <option value="excellent">Excellent</option>
                            <option value="good">Good</option>
                            <option value="fair">Fair</option>
                            <option value="poor">Poor</option>
                        </select>
                    </div>

                    {{-- Clear Filters Button --}}
                    @if ($search || $categoryId || $status || $condition)
                        <button
                            wire:click="clearFilters"
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                        >
                            <svg class="-ml-0.5 mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                            Clear Filters
                        </button>
                    @endif
                </div>

                {{-- View Toggle --}}
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">View:</span>
                    <div class="inline-flex rounded-md shadow-sm">
                        <button
                            wire:click="setViewMode('grid')"
                            type="button"
                            class="relative inline-flex items-center px-3 py-2 rounded-l-md border text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 {{ $viewMode === 'grid' ? 'bg-primary-50 border-primary-500 text-primary-700' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                            title="Grid View"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 002 4.25v2.5A2.25 2.25 0 004.25 9h2.5A2.25 2.25 0 009 6.75v-2.5A2.25 2.25 0 006.75 2h-2.5zm0 9A2.25 2.25 0 002 13.25v2.5A2.25 2.25 0 004.25 18h2.5A2.25 2.25 0 009 15.75v-2.5A2.25 2.25 0 006.75 11h-2.5zm9-9A2.25 2.25 0 0011 4.25v2.5A2.25 2.25 0 0013.25 9h2.5A2.25 2.25 0 0018 6.75v-2.5A2.25 2.25 0 0015.75 2h-2.5zm0 9A2.25 2.25 0 0011 13.25v2.5A2.25 2.25 0 0013.25 18h2.5A2.25 2.25 0 0018 15.75v-2.5A2.25 2.25 0 0015.75 11h-2.5z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button
                            wire:click="setViewMode('list')"
                            type="button"
                            class="relative -ml-px inline-flex items-center px-3 py-2 rounded-r-md border text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 {{ $viewMode === 'list' ? 'bg-primary-50 border-primary-500 text-primary-700' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}"
                            title="List View"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10zm0 5.25a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75a.75.75 0 01-.75-.75z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Results Count --}}
    <div class="mb-4 flex items-center justify-between">
        <p class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ $books->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $books->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $books->total() }}</span> books
        </p>

        {{-- Sort Options --}}
        <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-500">Sort by:</span>
            <select
                wire:model.live="sortField"
                class="block pl-3 pr-10 py-1.5 text-sm border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-md"
            >
                <option value="title">Title</option>
                <option value="author">Author</option>
                <option value="accession_number">Accession #</option>
                <option value="copies_available">Availability</option>
                <option value="created_at">Date Added</option>
            </select>
            <button
                wire:click="sortBy('{{ $sortField }}')"
                type="button"
                class="p-1.5 text-gray-400 hover:text-gray-600"
                title="Toggle sort direction"
            >
                @if ($sortDirection === 'asc')
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04l-3.96-4.158V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                    </svg>
                @endif
            </button>
        </div>
    </div>

    {{-- Books Display --}}
    @if ($books->count() > 0)
        {{-- Grid View --}}
        @if ($viewMode === 'grid')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($books as $book)
                    <div class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow duration-200">
                        {{-- Book Cover --}}
                        <div class="aspect-[3/4] bg-gray-100 relative">
                            @if ($book->cover_image)
                                <img
                                    src="{{ Storage::url($book->cover_image) }}"
                                    alt="{{ $book->title }}"
                                    class="w-full h-full object-cover"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-primary-200">
                                    <svg class="h-16 w-16 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Availability Badge --}}
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $this->getAvailabilityClass($book) }}">
                                    {{ $this->getAvailabilityText($book) }}
                                </span>
                            </div>
                        </div>

                        {{-- Book Info --}}
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 mb-1">
                                <a href="{{ route('books.show', $book) }}" class="hover:text-primary-600">
                                    {{ $book->title }}
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600 line-clamp-1 mb-2">
                                {{ $book->author }}
                            </p>

                            {{-- Category Badge --}}
                            @if ($book->category)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $book->category->name }}
                                </span>
                            @endif

                            {{-- Accession Number --}}
                            <p class="mt-2 text-xs text-gray-500">
                                Acc#: {{ $book->accession_number }}
                            </p>

                            {{-- Actions --}}
                            <div class="mt-3 flex items-center justify-between">
                                <a href="{{ route('books.show', $book) }}" class="text-sm text-primary-600 hover:text-primary-900">
                                    View Details
                                </a>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('books.edit', $book) }}" class="text-gray-400 hover:text-primary-600" title="Edit">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M5.433 13.917l1.262-3.155A4 4 0 017.58 9.42l6.92-6.918a2.121 2.121 0 013 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 01-.65-.65z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- List View --}}
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Book
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Accession #
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Availability
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Condition
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($books as $book)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-9">
                                            @if ($book->cover_image)
                                                <img class="h-12 w-9 object-cover rounded" src="{{ Storage::url($book->cover_image) }}" alt="">
                                            @else
                                                <div class="h-12 w-9 bg-gradient-to-br from-primary-100 to-primary-200 rounded flex items-center justify-center">
                                                    <svg class="h-5 w-5 text-primary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('books.show', $book) }}" class="hover:text-primary-600">
                                                    {{ $book->title }}
                                                </a>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $book->author }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($book->category)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $book->category->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $book->accession_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getAvailabilityClass($book) }}">
                                        {{ $this->getAvailabilityText($book) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                    {{ $book->condition ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-3">
                                        <a href="{{ route('books.show', $book) }}" class="text-primary-600 hover:text-primary-900">View</a>
                                        <a href="{{ route('books.edit', $book) }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $books->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No books found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if ($search || $categoryId || $status || $condition)
                        Try adjusting your search or filter criteria.
                    @else
                        Get started by adding a new book to the catalog.
                    @endif
                </p>
                @if (!$search && !$categoryId && !$status && !$condition)
                    <div class="mt-6">
                        <a href="{{ route('books.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            Add Book
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
