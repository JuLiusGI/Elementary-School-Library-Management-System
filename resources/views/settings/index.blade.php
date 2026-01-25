{{--
    System Settings Page

    Allows administrators to configure library settings.
    Settings are grouped by category for easy navigation.

    @author Library Management System
--}}

@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">System Settings</h1>
        <p class="text-gray-600 mt-1">Configure library rules and preferences</p>
    </div>

    {{-- Settings Form --}}
    <form action="{{ route('settings.update') }}" method="POST" id="settingsForm">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            {{-- School Information Section --}}
            @if(isset($groupedSettings['school']))
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $groupedSettings['school']['label'] }}</h2>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- School Name --}}
                    <div>
                        <label for="school_name" class="block text-sm font-medium text-gray-700 mb-1">
                            School Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="school_name"
                               id="school_name"
                               value="{{ old('school_name', $groupedSettings['school']['settings']['school_name']['value'] ?? '') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('school_name') border-red-500 @enderror"
                               required>
                        @error('school_name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ $groupedSettings['school']['settings']['school_name']['description'] ?? '' }}</p>
                    </div>

                    {{-- School Address --}}
                    <div>
                        <label for="school_address" class="block text-sm font-medium text-gray-700 mb-1">
                            School Address
                        </label>
                        <input type="text"
                               name="school_address"
                               id="school_address"
                               value="{{ old('school_address', $groupedSettings['school']['settings']['school_address']['value'] ?? '') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('school_address') border-red-500 @enderror">
                        @error('school_address')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Library Name --}}
                    <div>
                        <label for="library_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Library Name
                        </label>
                        <input type="text"
                               name="library_name"
                               id="library_name"
                               value="{{ old('library_name', $groupedSettings['school']['settings']['library_name']['value'] ?? '') }}"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('library_name') border-red-500 @enderror">
                        @error('library_name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Library Hours --}}
                    <div>
                        <label for="library_hours" class="block text-sm font-medium text-gray-700 mb-1">
                            Library Hours
                        </label>
                        <input type="text"
                               name="library_hours"
                               id="library_hours"
                               value="{{ old('library_hours', $groupedSettings['school']['settings']['library_hours']['value'] ?? '') }}"
                               placeholder="e.g., 7:00 AM - 5:00 PM"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('library_hours') border-red-500 @enderror">
                        @error('library_hours')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Library Email --}}
                    <div>
                        <label for="library_email" class="block text-sm font-medium text-gray-700 mb-1">
                            Library Email
                        </label>
                        <input type="email"
                               name="library_email"
                               id="library_email"
                               value="{{ old('library_email', $groupedSettings['school']['settings']['library_email']['value'] ?? '') }}"
                               placeholder="library@school.edu"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('library_email') border-red-500 @enderror">
                        @error('library_email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Library Phone --}}
                    <div>
                        <label for="library_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Library Phone
                        </label>
                        <input type="text"
                               name="library_phone"
                               id="library_phone"
                               value="{{ old('library_phone', $groupedSettings['school']['settings']['library_phone']['value'] ?? '') }}"
                               placeholder="e.g., (053) 123-4567"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('library_phone') border-red-500 @enderror">
                        @error('library_phone')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            @endif

            {{-- Circulation Rules Section --}}
            @if(isset($groupedSettings['circulation']))
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $groupedSettings['circulation']['label'] }}</h2>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Max Books Per Student --}}
                    <div>
                        <label for="max_books_per_student" class="block text-sm font-medium text-gray-700 mb-1">
                            Maximum Books Per Student <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="max_books_per_student"
                               id="max_books_per_student"
                               value="{{ old('max_books_per_student', $groupedSettings['circulation']['settings']['max_books_per_student']['value'] ?? 3) }}"
                               min="1"
                               max="10"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('max_books_per_student') border-red-500 @enderror"
                               required>
                        @error('max_books_per_student')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ $groupedSettings['circulation']['settings']['max_books_per_student']['description'] ?? '' }}</p>
                    </div>

                    {{-- Borrowing Period --}}
                    <div>
                        <label for="borrowing_period" class="block text-sm font-medium text-gray-700 mb-1">
                            Borrowing Period (Days) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="borrowing_period"
                               id="borrowing_period"
                               value="{{ old('borrowing_period', $groupedSettings['circulation']['settings']['borrowing_period']['value'] ?? 7) }}"
                               min="1"
                               max="30"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('borrowing_period') border-red-500 @enderror"
                               required>
                        @error('borrowing_period')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ $groupedSettings['circulation']['settings']['borrowing_period']['description'] ?? '' }}</p>
                    </div>

                    {{-- Allow Renewals --}}
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="allow_renewals"
                                   id="allow_renewals"
                                   value="1"
                                   {{ old('allow_renewals', $groupedSettings['circulation']['settings']['allow_renewals']['value'] ?? 0) == 1 ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Allow Book Renewals</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500 ml-6">{{ $groupedSettings['circulation']['settings']['allow_renewals']['description'] ?? '' }}</p>
                    </div>

                    {{-- Max Renewals --}}
                    <div>
                        <label for="max_renewals" class="block text-sm font-medium text-gray-700 mb-1">
                            Maximum Renewals <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="max_renewals"
                               id="max_renewals"
                               value="{{ old('max_renewals', $groupedSettings['circulation']['settings']['max_renewals']['value'] ?? 1) }}"
                               min="0"
                               max="5"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('max_renewals') border-red-500 @enderror"
                               required>
                        @error('max_renewals')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ $groupedSettings['circulation']['settings']['max_renewals']['description'] ?? '' }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Fine Configuration Section --}}
            @if(isset($groupedSettings['fines']))
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $groupedSettings['fines']['label'] }}</h2>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Fine Per Day --}}
                    <div>
                        <label for="fine_per_day" class="block text-sm font-medium text-gray-700 mb-1">
                            Fine Per Day (PHP) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">P</span>
                            <input type="number"
                                   name="fine_per_day"
                                   id="fine_per_day"
                                   value="{{ old('fine_per_day', $groupedSettings['fines']['settings']['fine_per_day']['value'] ?? 5.00) }}"
                                   min="0"
                                   max="100"
                                   step="0.50"
                                   class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('fine_per_day') border-red-500 @enderror"
                                   required>
                        </div>
                        @error('fine_per_day')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ $groupedSettings['fines']['settings']['fine_per_day']['description'] ?? '' }}</p>
                    </div>

                    {{-- Grace Period --}}
                    <div>
                        <label for="grace_period" class="block text-sm font-medium text-gray-700 mb-1">
                            Grace Period (Days) <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="grace_period"
                               id="grace_period"
                               value="{{ old('grace_period', $groupedSettings['fines']['settings']['grace_period']['value'] ?? 1) }}"
                               min="0"
                               max="7"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('grace_period') border-red-500 @enderror"
                               required>
                        @error('grace_period')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ $groupedSettings['fines']['settings']['grace_period']['description'] ?? '' }}</p>
                    </div>

                    {{-- Max Fine Amount --}}
                    <div>
                        <label for="max_fine_amount" class="block text-sm font-medium text-gray-700 mb-1">
                            Maximum Fine (PHP) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">P</span>
                            <input type="number"
                                   name="max_fine_amount"
                                   id="max_fine_amount"
                                   value="{{ old('max_fine_amount', $groupedSettings['fines']['settings']['max_fine_amount']['value'] ?? 100.00) }}"
                                   min="0"
                                   max="1000"
                                   step="10"
                                   class="w-full pl-8 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('max_fine_amount') border-red-500 @enderror"
                                   required>
                        </div>
                        @error('max_fine_amount')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ $groupedSettings['fines']['settings']['max_fine_amount']['description'] ?? '' }}</p>
                    </div>
                </div>

                {{-- Fine Calculation Example --}}
                <div class="px-6 pb-6">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-yellow-800 mb-2">Fine Calculation Example</h4>
                        <p class="text-sm text-yellow-700">
                            If a book is <strong>5 days overdue</strong> with a <strong>1-day grace period</strong> and <strong>P5.00 fine per day</strong>:
                        </p>
                        <p class="text-sm text-yellow-700 mt-1 font-mono">
                            Fine = (5 days - 1 grace day) x P5.00 = <strong>P20.00</strong>
                        </p>
                    </div>
                </div>
            </div>
            @endif

            {{-- System Preferences Section --}}
            @if(isset($groupedSettings['system']))
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $groupedSettings['system']['label'] }}</h2>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Date Format --}}
                    <div>
                        <label for="date_format" class="block text-sm font-medium text-gray-700 mb-1">
                            Date Format <span class="text-red-500">*</span>
                        </label>
                        <select name="date_format"
                                id="date_format"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('date_format') border-red-500 @enderror"
                                required>
                            @foreach($dateFormatOptions as $format => $example)
                                <option value="{{ $format }}"
                                    {{ old('date_format', $groupedSettings['system']['settings']['date_format']['value'] ?? 'M d, Y') === $format ? 'selected' : '' }}>
                                    {{ $example }} ({{ $format }})
                                </option>
                            @endforeach
                        </select>
                        @error('date_format')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ $groupedSettings['system']['settings']['date_format']['description'] ?? '' }}</p>
                    </div>

                    {{-- Items Per Page --}}
                    <div>
                        <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-1">
                            Items Per Page <span class="text-red-500">*</span>
                        </label>
                        <select name="items_per_page"
                                id="items_per_page"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('items_per_page') border-red-500 @enderror"
                                required>
                            @foreach([10, 15, 25, 50, 100] as $count)
                                <option value="{{ $count }}"
                                    {{ old('items_per_page', $groupedSettings['system']['settings']['items_per_page']['value'] ?? 15) == $count ? 'selected' : '' }}>
                                    {{ $count }} items
                                </option>
                            @endforeach
                        </select>
                        @error('items_per_page')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">{{ $groupedSettings['system']['settings']['items_per_page']['description'] ?? '' }}</p>
                    </div>

                    {{-- Enable Email Notifications --}}
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox"
                                   name="enable_email_notifications"
                                   id="enable_email_notifications"
                                   value="1"
                                   {{ old('enable_email_notifications', $groupedSettings['system']['settings']['enable_email_notifications']['value'] ?? 0) == 1 ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Enable Email Notifications</span>
                        </label>
                        <p class="mt-1 text-xs text-gray-500 ml-6">{{ $groupedSettings['system']['settings']['enable_email_notifications']['description'] ?? '' }}</p>
                        <p class="mt-1 text-xs text-yellow-600 ml-6">Note: Requires email server configuration</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white rounded-lg shadow p-6">
                {{-- Reset to Defaults Button --}}
                <button type="button"
                        onclick="document.getElementById('resetModal').classList.remove('hidden')"
                        class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md font-semibold text-xs text-red-700 uppercase tracking-widest hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset to Defaults
                </button>

                {{-- Save Button --}}
                <button type="submit"
                        class="inline-flex items-center px-6 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Settings
                </button>
            </div>
        </div>
    </form>

    {{-- Reset Confirmation Modal --}}
    <div id="resetModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Reset All Settings</h3>
                <div class="mt-2 px-4 py-3">
                    <p class="text-sm text-gray-500">
                        Are you sure you want to reset all settings to their default values? This action cannot be undone.
                    </p>
                </div>
                <div class="flex justify-center gap-4 mt-4">
                    <button type="button"
                            onclick="document.getElementById('resetModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <form action="{{ route('settings.reset') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="confirm_reset" value="1">
                        <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Reset Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
