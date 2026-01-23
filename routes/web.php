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
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
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

    // ===== CATEGORY ROUTES =====
    // Category management for organizing books
    // Route::resource creates all standard CRUD routes:
    // - GET    /categories           -> index   (list all)
    // - GET    /categories/create    -> create  (show form)
    // - POST   /categories           -> store   (save new)
    // - GET    /categories/{id}      -> show    (view one)
    // - GET    /categories/{id}/edit -> edit    (show edit form)
    // - PUT    /categories/{id}      -> update  (save changes)
    // - DELETE /categories/{id}      -> destroy (delete)
    Route::resource('categories', CategoryController::class);

    // ===== BOOK ROUTES =====
    // Book catalog management (CRUD operations)
    Route::resource('books', BookController::class);

    // Additional book route for removing cover image
    Route::delete('/books/{book}/cover', [BookController::class, 'removeCover'])->name('books.remove-cover');

    // ===== TRANSACTION ROUTES (Phase 3) =====
    // Book borrowing, returns, and transaction history

    // Borrow book routes
    Route::get('/transactions/borrow', [TransactionController::class, 'borrowIndex'])->name('transactions.borrow');
    Route::post('/transactions/borrow', [TransactionController::class, 'borrowStore'])->name('transactions.borrow.store');

    // Return book routes
    Route::get('/transactions/return', [TransactionController::class, 'returnIndex'])->name('transactions.return');
    Route::post('/transactions/return', [TransactionController::class, 'returnStore'])->name('transactions.return.store');

    // Transaction history
    Route::get('/transactions', [TransactionController::class, 'history'])->name('transactions.index');
    Route::get('/transactions/history', [TransactionController::class, 'history'])->name('transactions.history');

    // Fine management routes
    Route::post('/transactions/{transaction}/pay-fine', [TransactionController::class, 'payFine'])->name('transactions.pay-fine');
    Route::post('/transactions/{transaction}/waive-fine', [TransactionController::class, 'waiveFine'])->name('transactions.waive-fine');

    // API-style routes for Livewire components
    Route::get('/transactions/check-student/{student}', [TransactionController::class, 'checkStudentEligibility'])->name('transactions.check-student');
    Route::get('/transactions/check-book/{book}', [TransactionController::class, 'checkBookAvailability'])->name('transactions.check-book');

    // ===== PLACEHOLDER ROUTES =====
    // These routes will be implemented in future phases

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
