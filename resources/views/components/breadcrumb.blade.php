{{--
    Breadcrumb Component
    ====================

    Displays navigation breadcrumbs showing the user's current location.

    Usage:
    <x-breadcrumb :items="[
        ['label' => 'Dashboard', 'route' => 'dashboard'],
        ['label' => 'Books', 'route' => 'books.index'],
        ['label' => 'Add New Book'],  // No route = current page
    ]" />

    @props array $items - Array of breadcrumb items with 'label' and optional 'route'
--}}

@props(['items' => []])

<nav class="flex mb-4" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        {{-- Home/Dashboard is always first --}}
        <li class="inline-flex items-center">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                </svg>
                Dashboard
            </a>
        </li>

        {{-- Loop through provided items --}}
        @foreach($items as $item)
            <li>
                <div class="flex items-center">
                    {{-- Separator --}}
                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>

                    @if(isset($item['route']))
                        {{-- Link to another page --}}
                        <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                           class="ml-1 text-sm font-medium text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors md:ml-2">
                            {{ $item['label'] }}
                        </a>
                    @else
                        {{-- Current page (no link) --}}
                        <span class="ml-1 text-sm font-medium text-gray-700 dark:text-gray-300 md:ml-2">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </div>
            </li>
        @endforeach
    </ol>
</nav>
