{{--
    Loading Spinner Component
    =========================

    A reusable loading spinner for indicating loading states.

    Usage:
    <x-loading-spinner />
    <x-loading-spinner size="lg" color="primary" />
    <x-loading-spinner size="sm" color="white" text="Loading..." />

    @props string $size - Size: sm, md, lg, xl (default: md)
    @props string $color - Color: primary, white, gray, success, danger
    @props string $text - Optional loading text
--}}

@props([
    'size' => 'md',
    'color' => 'primary',
    'text' => ''
])

@php
    $sizes = [
        'sm' => 'h-4 w-4',
        'md' => 'h-6 w-6',
        'lg' => 'h-8 w-8',
        'xl' => 'h-12 w-12',
    ];

    $colors = [
        'primary' => 'text-primary-600',
        'white' => 'text-white',
        'gray' => 'text-gray-600',
        'success' => 'text-success-600',
        'danger' => 'text-danger-600',
        'warning' => 'text-warning-600',
    ];

    $sizeClasses = $sizes[$size] ?? $sizes['md'];
    $colorClasses = $colors[$color] ?? $colors['primary'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center justify-center']) }}>
    <svg class="animate-spin {{ $sizeClasses }} {{ $colorClasses }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    @if($text)
        <span class="ml-2 {{ $colorClasses }}">{{ $text }}</span>
    @endif
</div>
