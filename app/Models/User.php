<?php

/**
 * User Model
 *
 * This model represents librarians and administrators who can log into
 * the Library Management System. Users are responsible for processing
 * book borrowing and returning transactions.
 *
 * User Roles:
 * - 'admin': Full system access, can manage users and settings
 * - 'librarian': Can manage books, students, and transactions
 *
 * This model extends Laravel's default Authenticatable class which
 * provides authentication features like login, logout, and password reset.
 *
 * @see database/migrations/2026_01_22_004621_add_role_to_users_table.php
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (users table)
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /**
     * Use Laravel's HasFactory trait for model factories (testing)
     * Use Notifiable trait for sending notifications
     *
     * @use HasFactory<\Database\Factories\UserFactory>
     */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * Mass assignment allows you to create or update multiple fields at once:
     * User::create(['name' => 'John', 'email' => 'john@example.com', ...])
     *
     * Only fields listed here can be mass assigned for security.
     * This prevents users from maliciously setting fields like 'role' directly.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',  // Added: User role (admin or librarian)
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * When converting user to JSON (e.g., for API responses),
     * these fields will be excluded for security reasons.
     * We don't want passwords or tokens exposed in responses.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * Casting automatically converts attribute values to specific types
     * when you access them. This ensures consistent data types.
     *
     * - 'email_verified_at' => 'datetime': Converts to Carbon date object
     * - 'password' => 'hashed': Automatically hashes password when set
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get all transactions processed by this user (librarian/admin).
     *
     * A user (librarian) can process many transactions over time.
     * Each transaction records who processed the borrowing/returning.
     *
     * Usage:
     * $user->transactions; // Get all transactions processed by this user
     * $user->transactions()->where('status', 'borrowed')->get(); // Filter transactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        // 'librarian_id' is the foreign key in transactions table
        // that references this user's id
        return $this->hasMany(Transaction::class, 'librarian_id');
    }

    // =========================================================================
    // ROLE HELPER METHODS
    // =========================================================================

    /**
     * Check if the user has admin role.
     *
     * Admins have full system access including:
     * - Managing other users
     * - Changing system settings
     * - All librarian permissions
     *
     * Usage:
     * if ($user->isAdmin()) {
     *     // Show admin-only features
     * }
     *
     * In Blade templates:
     * @if(auth()->user()->isAdmin())
     *     <a href="/admin/settings">Settings</a>
     * @endif
     *
     * @return bool True if user is an admin, false otherwise
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user has librarian role.
     *
     * Librarians can:
     * - Manage books (add, edit, view)
     * - Manage students (add, edit, view)
     * - Process borrowing and returning
     * - View reports
     *
     * Note: Admins are NOT librarians by this check.
     * If you want to check if user can do librarian tasks,
     * you might want: $user->isAdmin() || $user->isLibrarian()
     *
     * Usage:
     * if ($user->isLibrarian()) {
     *     // Show librarian features
     * }
     *
     * @return bool True if user is a librarian, false otherwise
     */
    public function isLibrarian(): bool
    {
        return $this->role === 'librarian';
    }

    /**
     * Check if the user can perform librarian duties.
     *
     * Both admins and librarians can perform librarian duties.
     * This is a convenience method for permission checking.
     *
     * Usage:
     * if ($user->canManageLibrary()) {
     *     // Allow access to library management features
     * }
     *
     * @return bool True if user can manage library operations
     */
    public function canManageLibrary(): bool
    {
        return $this->isAdmin() || $this->isLibrarian();
    }
}
