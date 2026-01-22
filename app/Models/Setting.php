<?php

/**
 * Setting Model
 *
 * This model manages system configuration stored as key-value pairs.
 * Settings allow admins to customize the library system without changing code.
 *
 * Default Settings (created by seeder):
 * - max_books_per_student: 3 (Maximum books a student can borrow)
 * - borrowing_period: 7 (Days until book is due)
 * - fine_per_day: 5.00 (Fine amount per overdue day in PHP)
 * - grace_period: 1 (Days before fines start)
 * - school_name: "Bobon B Elementary School"
 * - school_address: "Bobon, Southern Leyte"
 * - library_hours: "7:00 AM - 5:00 PM"
 *
 * Usage Examples:
 * - Get a setting: Setting::get('max_books_per_student', 3)
 * - Set a setting: Setting::set('fine_per_day', 10.00)
 * - Check exists: Setting::has('school_name')
 *
 * Why Key-Value Storage?
 * - Easy to add new settings without database migrations
 * - Simple admin interface to update values
 * - Settings can be cached for performance
 *
 * @see database/migrations/2026_01_22_005511_create_settings_table.php
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (settings table)
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * Use HasFactory trait to enable model factories for testing.
     *
     * Usage:
     * Setting::factory()->create(['key' => 'test_key', 'value' => 'test_value']);
     */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',          // Setting key in snake_case (e.g., "max_books_per_student")
        'value',        // Setting value (stored as text, cast in application)
        'description',  // Human-readable description of what this setting does
    ];

    /**
     * Cache key prefix for settings.
     *
     * Used to cache settings for better performance.
     * Cache is invalidated when settings are updated.
     *
     * @var string
     */
    protected static string $cachePrefix = 'setting_';

    /**
     * Cache duration in seconds (1 hour).
     *
     * @var int
     */
    protected static int $cacheDuration = 3600;

    // =========================================================================
    // STATIC HELPER METHODS
    // =========================================================================

    /**
     * Get a setting value by its key.
     *
     * This is the primary way to retrieve settings throughout the application.
     * Uses caching for better performance.
     *
     * Usage:
     * $maxBooks = Setting::get('max_books_per_student', 3);
     * $finePerDay = Setting::get('fine_per_day', 5.00);
     * $schoolName = Setting::get('school_name', 'My School');
     *
     * @param string $key The setting key to retrieve
     * @param mixed $default Default value if setting doesn't exist
     * @return mixed The setting value, or default if not found
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // Try to get from cache first
        $cacheKey = static::$cachePrefix . $key;

        return Cache::remember($cacheKey, static::$cacheDuration, function () use ($key, $default) {
            // Query database for the setting
            $setting = static::where('key', $key)->first();

            // Return value if found, otherwise default
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set a setting value (create or update).
     *
     * Creates the setting if it doesn't exist, updates if it does.
     * Clears the cache for this setting.
     *
     * Usage:
     * Setting::set('max_books_per_student', 5);
     * Setting::set('school_name', 'New School Name', 'Name of the school');
     *
     * @param string $key The setting key
     * @param mixed $value The value to set
     * @param string|null $description Optional description (only used on create)
     * @return \App\Models\Setting The setting model instance
     */
    public static function set(string $key, mixed $value, ?string $description = null): Setting
    {
        // Clear cache for this setting
        Cache::forget(static::$cachePrefix . $key);

        // Update or create the setting
        $setting = static::updateOrCreate(
            ['key' => $key],  // Find by key
            [
                'value' => $value,
                // Only set description if provided and setting is new
                'description' => $description,
            ]
        );

        return $setting;
    }

    /**
     * Check if a setting exists.
     *
     * Usage:
     * if (Setting::has('school_logo')) {
     *     // Use the logo
     * }
     *
     * @param string $key The setting key to check
     * @return bool True if setting exists, false otherwise
     */
    public static function has(string $key): bool
    {
        return static::where('key', $key)->exists();
    }

    /**
     * Delete a setting by its key.
     *
     * Usage:
     * Setting::remove('deprecated_setting');
     *
     * @param string $key The setting key to delete
     * @return bool True if deleted, false if not found
     */
    public static function remove(string $key): bool
    {
        // Clear cache
        Cache::forget(static::$cachePrefix . $key);

        // Delete from database
        return (bool) static::where('key', $key)->delete();
    }

    /**
     * Get all settings as a key-value array.
     *
     * Useful for admin settings page or debugging.
     *
     * Usage:
     * $allSettings = Setting::getAll();
     * // Returns: ['max_books_per_student' => '3', 'fine_per_day' => '5.00', ...]
     *
     * @return array<string, mixed> Array of all settings
     */
    public static function getAll(): array
    {
        return static::pluck('value', 'key')->toArray();
    }

    /**
     * Get multiple settings at once.
     *
     * More efficient than calling get() multiple times.
     *
     * Usage:
     * $settings = Setting::getMany(['max_books_per_student', 'borrowing_period', 'fine_per_day']);
     * // Returns: ['max_books_per_student' => '3', 'borrowing_period' => '7', 'fine_per_day' => '5.00']
     *
     * @param array $keys Array of setting keys to retrieve
     * @param array $defaults Default values keyed by setting key
     * @return array<string, mixed> Array of settings
     */
    public static function getMany(array $keys, array $defaults = []): array
    {
        $settings = static::whereIn('key', $keys)
            ->pluck('value', 'key')
            ->toArray();

        // Merge with defaults for any missing keys
        foreach ($keys as $key) {
            if (!isset($settings[$key])) {
                $settings[$key] = $defaults[$key] ?? null;
            }
        }

        return $settings;
    }

    /**
     * Clear all settings cache.
     *
     * Use when doing bulk updates or when cache might be stale.
     *
     * Usage:
     * Setting::clearCache();
     *
     * @return void
     */
    public static function clearCache(): void
    {
        // Get all setting keys and clear their cache
        $keys = static::pluck('key');

        foreach ($keys as $key) {
            Cache::forget(static::$cachePrefix . $key);
        }
    }

    // =========================================================================
    // TYPE-SPECIFIC GETTERS
    // =========================================================================

    /**
     * Get a setting as an integer.
     *
     * Usage:
     * $maxBooks = Setting::getInt('max_books_per_student', 3);
     *
     * @param string $key The setting key
     * @param int $default Default value if not found
     * @return int The setting value as integer
     */
    public static function getInt(string $key, int $default = 0): int
    {
        return (int) static::get($key, $default);
    }

    /**
     * Get a setting as a float.
     *
     * Usage:
     * $finePerDay = Setting::getFloat('fine_per_day', 5.00);
     *
     * @param string $key The setting key
     * @param float $default Default value if not found
     * @return float The setting value as float
     */
    public static function getFloat(string $key, float $default = 0.0): float
    {
        return (float) static::get($key, $default);
    }

    /**
     * Get a setting as a boolean.
     *
     * Considers '1', 'true', 'yes', 'on' as true.
     *
     * Usage:
     * $allowReservations = Setting::getBool('allow_reservations', false);
     *
     * @param string $key The setting key
     * @param bool $default Default value if not found
     * @return bool The setting value as boolean
     */
    public static function getBool(string $key, bool $default = false): bool
    {
        $value = static::get($key, $default);

        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }

    // =========================================================================
    // LIBRARY-SPECIFIC CONVENIENCE METHODS
    // =========================================================================

    /**
     * Get the maximum books a student can borrow.
     *
     * Convenience method for commonly used setting.
     *
     * Usage:
     * $maxBooks = Setting::maxBooksPerStudent(); // Returns 3 by default
     *
     * @return int Maximum books per student
     */
    public static function maxBooksPerStudent(): int
    {
        return static::getInt('max_books_per_student', 3);
    }

    /**
     * Get the borrowing period in days.
     *
     * Usage:
     * $days = Setting::borrowingPeriod(); // Returns 7 by default
     *
     * @return int Number of days for borrowing period
     */
    public static function borrowingPeriod(): int
    {
        return static::getInt('borrowing_period', 7);
    }

    /**
     * Get the fine amount per day.
     *
     * Usage:
     * $fine = Setting::finePerDay(); // Returns 5.00 by default
     *
     * @return float Fine amount in PHP per day
     */
    public static function finePerDay(): float
    {
        return static::getFloat('fine_per_day', 5.00);
    }

    /**
     * Get the grace period before fines start.
     *
     * Usage:
     * $grace = Setting::gracePeriod(); // Returns 1 by default
     *
     * @return int Number of grace days
     */
    public static function gracePeriod(): int
    {
        return static::getInt('grace_period', 1);
    }

    /**
     * Get the school name.
     *
     * Usage:
     * $name = Setting::schoolName(); // Returns school name
     *
     * @return string School name
     */
    public static function schoolName(): string
    {
        return (string) static::get('school_name', 'Bobon B Elementary School');
    }
}
