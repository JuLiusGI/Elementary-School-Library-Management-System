{{--
    Student Edit Page

    This page displays a form for editing an existing student's information.
    The form is pre-filled with the student's current data.

    Form Fields:
    - Required: Student ID, First Name, Last Name, Grade Level, Section
    - Optional: Middle Name, Status, Contact Number, Guardian Info

    Validation is handled by StudentRequest form request.

    @see App\Http\Controllers\StudentController::edit()
    @see App\Http\Controllers\StudentController::update()
    @see App\Http\Requests\StudentRequest
--}}

@extends('layouts.app')

@section('title', 'Edit Student - ' . $student->full_name)

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
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
                            <a href="{{ route('students.show', $student) }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">{{ $student->full_name }}</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-500">Edit</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="mt-4 text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Edit Student
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Update the student's information below.
            </p>
        </div>

        {{-- Student Form --}}
        <form action="{{ route('students.update', $student) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Personal Information Section --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                        Personal Information
                    </h3>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        {{-- Student ID --}}
                        <div class="sm:col-span-2">
                            <label for="student_id" class="block text-sm font-medium text-gray-700">
                                Student ID <span class="text-danger-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="student_id"
                                id="student_id"
                                value="{{ old('student_id', $student->student_id) }}"
                                placeholder="e.g., 2024-0001"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('student_id') border-danger-300 @enderror"
                                required
                            >
                            @error('student_id')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- First Name --}}
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                First Name <span class="text-danger-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="first_name"
                                id="first_name"
                                value="{{ old('first_name', $student->first_name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('first_name') border-danger-300 @enderror"
                                required
                            >
                            @error('first_name')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Last Name --}}
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                Last Name <span class="text-danger-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="last_name"
                                id="last_name"
                                value="{{ old('last_name', $student->last_name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('last_name') border-danger-300 @enderror"
                                required
                            >
                            @error('last_name')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Middle Name --}}
                        <div>
                            <label for="middle_name" class="block text-sm font-medium text-gray-700">
                                Middle Name
                            </label>
                            <input
                                type="text"
                                name="middle_name"
                                id="middle_name"
                                value="{{ old('middle_name', $student->middle_name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('middle_name') border-danger-300 @enderror"
                            >
                            @error('middle_name')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Grade Level --}}
                        <div>
                            <label for="grade_level" class="block text-sm font-medium text-gray-700">
                                Grade Level <span class="text-danger-500">*</span>
                            </label>
                            <select
                                name="grade_level"
                                id="grade_level"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('grade_level') border-danger-300 @enderror"
                                required
                            >
                                <option value="">Select Grade Level</option>
                                @foreach ($gradeLevels as $value => $label)
                                    <option value="{{ $value }}" {{ old('grade_level', $student->grade_level) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('grade_level')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Section --}}
                        <div>
                            <label for="section" class="block text-sm font-medium text-gray-700">
                                Section <span class="text-danger-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="section"
                                id="section"
                                value="{{ old('section', $student->section) }}"
                                placeholder="e.g., Section A, Sampaguita"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('section') border-danger-300 @enderror"
                                required
                            >
                            @error('section')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">
                                Status
                            </label>
                            <select
                                name="status"
                                id="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('status') border-danger-300 @enderror"
                            >
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" {{ old('status', $student->status) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Information Section --}}
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">
                        Contact Information
                    </h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Optional information for contacting the student or their guardian.
                    </p>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        {{-- Contact Number --}}
                        <div>
                            <label for="contact_number" class="block text-sm font-medium text-gray-700">
                                Student Contact Number
                            </label>
                            <input
                                type="text"
                                name="contact_number"
                                id="contact_number"
                                value="{{ old('contact_number', $student->contact_number) }}"
                                placeholder="e.g., 09123456789"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('contact_number') border-danger-300 @enderror"
                            >
                            @error('contact_number')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Guardian Name --}}
                        <div>
                            <label for="guardian_name" class="block text-sm font-medium text-gray-700">
                                Guardian Name
                            </label>
                            <input
                                type="text"
                                name="guardian_name"
                                id="guardian_name"
                                value="{{ old('guardian_name', $student->guardian_name) }}"
                                placeholder="Parent or Guardian's name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('guardian_name') border-danger-300 @enderror"
                            >
                            @error('guardian_name')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Guardian Contact --}}
                        <div>
                            <label for="guardian_contact" class="block text-sm font-medium text-gray-700">
                                Guardian Contact Number
                            </label>
                            <input
                                type="text"
                                name="guardian_contact"
                                id="guardian_contact"
                                value="{{ old('guardian_contact', $student->guardian_contact) }}"
                                placeholder="e.g., 09123456789"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('guardian_contact') border-danger-300 @enderror"
                            >
                            @error('guardian_contact')
                                <p class="mt-1 text-sm text-danger-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('students.show', $student) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                    </svg>
                    Update Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
