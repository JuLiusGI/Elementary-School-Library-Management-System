{{--
    Tooltip Component
    =================

    A reusable tooltip component that shows on hover.

    Usage:
    <x-tooltip text="This is helpful information">
        <button>Hover me</button>
    </x-tooltip>

    <x-tooltip text="Click to delete" position="left">
        <button>Delete</button>
    </x-tooltip>

    @props string $text - Tooltip text
    @props string $position - Position: top, bottom, left, right (default: top)
--}}

@props([
    'text' => '',
    'position' => 'top'
])

@php
    $positions = [
        'top' => [
            'tooltip' => 'bottom-full left-1/2 -translate-x-1/2 mb-2',
            'arrow' => 'top-full left-1/2 -translate-x-1/2 border-t-gray-900 dark:border-t-gray-700 border-l-transparent border-r-transparent border-b-transparent',
        ],
        'bottom' => [
            'tooltip' => 'top-full left-1/2 -translate-x-1/2 mt-2',
            'arrow' => 'bottom-full left-1/2 -translate-x-1/2 border-b-gray-900 dark:border-b-gray-700 border-l-transparent border-r-transparent border-t-transparent',
        ],
        'left' => [
            'tooltip' => 'right-full top-1/2 -translate-y-1/2 mr-2',
            'arrow' => 'left-full top-1/2 -translate-y-1/2 border-l-gray-900 dark:border-l-gray-700 border-t-transparent border-b-transparent border-r-transparent',
        ],
        'right' => [
            'tooltip' => 'left-full top-1/2 -translate-y-1/2 ml-2',
            'arrow' => 'right-full top-1/2 -translate-y-1/2 border-r-gray-900 dark:border-r-gray-700 border-t-transparent border-b-transparent border-l-transparent',
        ],
    ];
    $pos = $positions[$position] ?? $positions['top'];
@endphp

<div x-data="{ show: false }"
     class="relative inline-block"
     @mouseenter="show = true"
     @mouseleave="show = false"
     @focus="show = true"
     @blur="show = false">

    {{-- Trigger Element --}}
    {{ $slot }}

    {{-- Tooltip --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 {{ $pos['tooltip'] }} pointer-events-none"
         x-cloak>
        <div class="bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-md py-1.5 px-3 whitespace-nowrap shadow-lg">
            {{ $text }}
        </div>
        {{-- Arrow --}}
        <div class="absolute {{ $pos['arrow'] }} border-4"></div>
    </div>
</div>
