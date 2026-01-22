<?php

/**
 * Web Routes
 *
 * This file defines all web routes for the Library Management System.
 * Routes are loaded by RouteServiceProvider within a group which
 * contains the "web" middleware group.
 *
 * Route Organization:
 * 1. Public routes (home, welcome)
 * 2. Authentication required routes (dashboard, profile)
 * 3. Resource routes (students, books, categories, etc.)
 *
 * @see App\Providers\RouteServiceProvider
 */

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// =========================================================================
// PUBLIC ROUTES
// =========================================================================

/**
 * Home page - redirects to login or dashboard
 */
Route::get('/', function () {
    return view('welcome');
});

// =========================================================================
// AUTHENTICATED ROUTES
// =========================================================================

/**
 * Dashboard - Main landing page after login
 * Requires authentication and email verification
 */
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/**
 * Authenticated routes group
 * All routes in this group require the user to be logged in
 */
Route::middleware('auth')->group(function () {

    // ===== PROFILE ROUTES =====
    // User profile management (from Laravel Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ===== STUDENT ROUTES =====
    // Student management (CRUD operations)
    // Route::resource creates all standard CRUD routes:
    // - GET    /students           -> index   (list all)
    // - GET    /students/create    -> create  (show form)
    // - POST   /students           -> store   (save new)
    // - GET    /students/{id}      -> show    (view one)
    // - GET    /students/{id}/edit -> edit    (show edit form)
    // - PUT    /students/{id}      -> update  (save changes)
    // - DELETE /students/{id}      -> destroy (delete)
    Route::resource('students', StudentController::class);

    // ===== PLACEHOLDER ROUTES =====
    // These routes will be implemented in future phases
    // They are defined here to prevent errors in the sidebar navigation

    // Books routes (Phase 2.2)
    Route::get('/books', function () {
        return redirect()->route('dashboard')->with('warning', 'Books management coming soon!');
    })->name('books.index');

    // Categories routes (Phase 2.2)
    Route::get('/categories', function () {
        return redirect()->route('dashboard')->with('warning', 'Categories management coming soon!');
    })->name('categories.index');

    // Transaction routes (Phase 3)
    Route::get('/transactions/borrow', function () {
        return redirect()->route('dashboard')->with('warning', 'Borrow functionality coming soon!');
    })->name('transactions.borrow');

    Route::get('/transactions/return', function () {
        return redirect()->route('dashboard')->with('warning', 'Return functionality coming soon!');
    })->name('transactions.return');

    Route::get('/transactions', function () {
        return redirect()->route('dashboard')->with('warning', 'Transaction history coming soon!');
    })->name('transactions.index');

    // Reports routes (Phase 4)
    Route::get('/reports', function () {
        return redirect()->route('dashboard')->with('warning', 'Reports coming soon!');
    })->name('reports.index');

    // Admin routes (Phase 5)
    Route::get('/settings', function () {
        return redirect()->route('dashboard')->with('warning', 'Settings coming soon!');
    })->name('settings.index');

    Route::get('/users', function () {
        return redirect()->route('dashboard')->with('warning', 'User management coming soon!');
    })->name('users.index');
});

// =========================================================================
// AUTHENTICATION ROUTES
// =========================================================================

// Load authentication routes from auth.php (login, register, password reset, etc.)
require __DIR__.'/auth.php';
