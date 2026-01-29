{{--
    Confirm Modal Component
    =======================

    A reusable confirmation modal for dangerous actions like delete, reset, etc.

    Usage:
    <x-confirm-modal
        id="delete-student"
        title="Delete Student"
        message="Are you sure you want to delete this student? This action cannot be undone."
        confirmText="Delete"
        confirmColor="danger"
    />

    Then trigger with Alpine.js:
    <button @click="$dispatch('open-confirm-modal', 'delete-student')">Delete</button>

    @props string $id - Unique identifier for the modal
    @props string $title - Modal title
    @props string $message - Confirmation message
    @props string $confirmText - Text for confirm button (default: "Confirm")
    @props string $cancelText - Text for cancel button (default: "Cancel")
    @props string $confirmColor - Color for confirm button: primary, danger, warning, success
    @props string $icon - Icon type: warning, danger, info, success
--}}

@props([
    'id' => 'confirm-modal',
    'title' => 'Confirm Action',
    'message' => 'Are you sure you want to proceed?',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmColor' => 'danger',
    'icon' => 'warning'
])

@php
    // Map color to Tailwind classes
    $confirmColors = [
        'primary' => 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500',
        'danger' => 'bg-danger-600 hover:bg-danger-700 focus:ring-danger-500',
        'warning' => 'bg-warning-600 hover:bg-warning-700 focus:ring-warning-500',
        'success' => 'bg-success-600 hover:bg-success-700 focus:ring-success-500',
    ];
    $buttonClasses = $confirmColors[$confirmColor] ?? $confirmColors['danger'];

    // Icon colors
    $iconColors = [
        'warning' => 'bg-warning-100 text-warning-600 dark:bg-warning-900 dark:text-warning-400',
        'danger' => 'bg-danger-100 text-danger-600 dark:bg-danger-900 dark:text-danger-400',
        'info' => 'bg-primary-100 text-primary-600 dark:bg-primary-900 dark:text-primary-400',
        'success' => 'bg-success-100 text-success-600 dark:bg-success-900 dark:text-success-400',
    ];
    $iconColorClasses = $iconColors[$icon] ?? $iconColors['warning'];
@endphp

<div x-data="{ open: false, loading: false }"
     x-on:open-confirm-modal.window="if ($event.detail === '{{ $id }}') open = true"
     x-on:close-confirm-modal.window="if ($event.detail === '{{ $id }}') { open = false; loading = false; }"
     x-on:keydown.escape.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     aria-labelledby="modal-title-{{ $id }}"
     role="dialog"
     aria-modal="true">

    {{-- Backdrop --}}
    <div x-show="open"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75 transition-opacity"
         @click="open = false">
    </div>

    {{-- Modal Content --}}
    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="open"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

            <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    {{-- Icon --}}
                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full {{ $iconColorClasses }} sm:mx-0 sm:h-10 sm:w-10">
                        @if($icon === 'warning' || $icon === 'danger')
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                        @elseif($icon === 'info')
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                            </svg>
                        @elseif($icon === 'success')
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100" id="modal-title-{{ $id }}">
                            {{ $title }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $message }}
                            </p>
                            {{-- Slot for additional content --}}
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                <button type="button"
                        @click="loading = true; $dispatch('confirm-{{ $id }}')"
                        :disabled="loading"
                        class="inline-flex w-full justify-center rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm {{ $buttonClasses }} focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Processing...' : '{{ $confirmText }}'"></span>
                </button>
                <button type="button"
                        @click="open = false"
                        :disabled="loading"
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-600 px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-500 hover:bg-gray-50 dark:hover:bg-gray-500 sm:mt-0 sm:w-auto disabled:opacity-50">
                    {{ $cancelText }}
                </button>
            </div>
        </div>
    </div>
</div>
