<?php

/**
 * SettingService.php
 *
 * This service provides a centralized interface for managing library settings.
 * It wraps the Setting model and provides additional functionality like
 * resetting to defaults and grouped settings retrieval.
 *
 * @package App\Services
 * @author  Library Management System
 * @version 1.0
 */

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Default settings for the library system.
     *
     * These values are used when resetting to defaults or
     * when a setting doesn't exist in the database.
     *
     * @var array
     */
    protected array $defaults = [
        // Circulation Rules
        'max_books_per_student' => [
            'value' => '3',
            'description' => 'Maximum number of books a student can borrow at once',
            'group' => 'circulation',
            'type' => 'integer',
        ],
        'borrowing_period' => [
            'value' => '7',
            'description' => 'Number of days a student can keep a borrowed book',
            'group' => 'circulation',
            'type' => 'integer',
        ],
        'allow_renewals' => [
            'value' => '1',
            'description' => 'Allow students to renew borrowed books',
            'group' => 'circulation',
            'type' => 'boolean',
        ],
        'max_renewals' => [
            'value' => '1',
            'description' => 'Maximum number of times a book can be renewed',
            'group' => 'circulation',
            'type' => 'integer',
        ],

        // Fine Configuration
        'fine_per_day' => [
            'value' => '5.00',
            'description' => 'Fine amount per day for overdue books (in PHP)',
            'group' => 'fines',
            'type' => 'decimal',
        ],
        'grace_period' => [
            'value' => '1',
            'description' => 'Number of days before fines start accumulating',
            'group' => 'fines',
            'type' => 'integer',
        ],
        'max_fine_amount' => [
            'value' => '100.00',
            'description' => 'Maximum fine amount per transaction (in PHP)',
            'group' => 'fines',
            'type' => 'decimal',
        ],

        // School Information
        'school_name' => [
            'value' => 'Bobon B Elementary School',
            'description' => 'Name of the school',
            'group' => 'school',
            'type' => 'text',
        ],
        'school_address' => [
            'value' => 'Southern Leyte, Philippines',
            'description' => 'Full address of the school',
            'group' => 'school',
            'type' => 'text',
        ],
        'library_name' => [
            'value' => 'School Library',
            'description' => 'Name of the library',
            'group' => 'school',
            'type' => 'text',
        ],
        'library_email' => [
            'value' => '',
            'description' => 'Library contact email address',
            'group' => 'school',
            'type' => 'email',
        ],
        'library_phone' => [
            'value' => '',
            'description' => 'Library contact phone number',
            'group' => 'school',
            'type' => 'text',
        ],
        'library_hours' => [
            'value' => '7:00 AM - 5:00 PM',
            'description' => 'Library operating hours',
            'group' => 'school',
            'type' => 'text',
        ],

        // System Preferences
        'date_format' => [
            'value' => 'M d, Y',
            'description' => 'Date display format (PHP date format)',
            'group' => 'system',
            'type' => 'select',
            'options' => ['M d, Y', 'd/m/Y', 'Y-m-d', 'F j, Y'],
        ],
        'items_per_page' => [
            'value' => '15',
            'description' => 'Number of items to show per page in lists',
            'group' => 'system',
            'type' => 'integer',
        ],
        'enable_email_notifications' => [
            'value' => '0',
            'description' => 'Send email notifications for overdue books',
            'group' => 'system',
            'type' => 'boolean',
        ],
    ];

    /**
     * Setting group labels for the UI.
     *
     * @var array
     */
    protected array $groupLabels = [
        'school' => 'School Information',
        'circulation' => 'Circulation Rules',
        'fines' => 'Fine Configuration',
        'system' => 'System Preferences',
    ];

    /**
     * Get a setting value by key.
     *
     * @param string $key The setting key
     * @param mixed $default Default value if setting doesn't exist
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        // If no default provided, use our defaults array
        if ($default === null && isset($this->defaults[$key])) {
            $default = $this->defaults[$key]['value'];
        }

        return Setting::get($key, $default);
    }

    /**
     * Set a setting value.
     *
     * @param string $key The setting key
     * @param mixed $value The value to set
     * @return Setting
     */
    public function set(string $key, mixed $value): Setting
    {
        $description = $this->defaults[$key]['description'] ?? null;
        return Setting::set($key, $value, $description);
    }

    /**
     * Update multiple settings at once.
     *
     * @param array $settings Key-value pairs of settings to update
     * @return void
     */
    public function updateMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            // Only update known settings
            if (isset($this->defaults[$key])) {
                $this->set($key, $value);
            }
        }

        // Clear all settings cache
        Setting::clearCache();
    }

    /**
     * Get all settings as a key-value array.
     *
     * @return array
     */
    public function getAll(): array
    {
        $dbSettings = Setting::getAll();

        // Merge with defaults to ensure all settings are present
        $result = [];
        foreach ($this->defaults as $key => $config) {
            $result[$key] = $dbSettings[$key] ?? $config['value'];
        }

        return $result;
    }

    /**
     * Get all settings with their metadata (for admin UI).
     *
     * @return array
     */
    public function getAllWithMetadata(): array
    {
        $dbSettings = Setting::getAll();
        $result = [];

        foreach ($this->defaults as $key => $config) {
            $result[$key] = [
                'key' => $key,
                'value' => $dbSettings[$key] ?? $config['value'],
                'description' => $config['description'],
                'group' => $config['group'],
                'type' => $config['type'],
                'options' => $config['options'] ?? null,
            ];
        }

        return $result;
    }

    /**
     * Get settings grouped by category.
     *
     * @return array
     */
    public function getGroupedSettings(): array
    {
        $settings = $this->getAllWithMetadata();
        $grouped = [];

        foreach ($this->groupLabels as $groupKey => $groupLabel) {
            $grouped[$groupKey] = [
                'label' => $groupLabel,
                'settings' => [],
            ];
        }

        foreach ($settings as $key => $setting) {
            $group = $setting['group'];
            if (isset($grouped[$group])) {
                $grouped[$group]['settings'][$key] = $setting;
            }
        }

        return $grouped;
    }

    /**
     * Reset all settings to their default values.
     *
     * @return void
     */
    public function resetDefaults(): void
    {
        foreach ($this->defaults as $key => $config) {
            Setting::set($key, $config['value'], $config['description']);
        }

        // Clear all cache
        Setting::clearCache();
    }

    /**
     * Get the default value for a setting.
     *
     * @param string $key The setting key
     * @return mixed
     */
    public function getDefault(string $key): mixed
    {
        return $this->defaults[$key]['value'] ?? null;
    }

    /**
     * Get all default settings.
     *
     * @return array
     */
    public function getDefaults(): array
    {
        return $this->defaults;
    }

    /**
     * Get group labels.
     *
     * @return array
     */
    public function getGroupLabels(): array
    {
        return $this->groupLabels;
    }

    /**
     * Check if a setting key is valid/known.
     *
     * @param string $key
     * @return bool
     */
    public function isValidKey(string $key): bool
    {
        return isset($this->defaults[$key]);
    }

    /**
     * Get setting type for validation.
     *
     * @param string $key
     * @return string|null
     */
    public function getType(string $key): ?string
    {
        return $this->defaults[$key]['type'] ?? null;
    }
}
