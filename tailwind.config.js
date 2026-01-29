/**
 * Tailwind CSS Configuration
 *
 * This file configures Tailwind CSS for the Library Management System.
 * Custom colors are defined to match the school's branding and provide
 * consistent styling throughout the application.
 *
 * Color Scheme:
 * - Primary (Blue): Used for main actions, headers, navigation
 * - Success (Green): Used for successful actions, available status
 * - Warning (Yellow): Used for warnings, due soon alerts
 * - Danger (Red): Used for errors, overdue status, destructive actions
 */

import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    /**
     * Dark mode configuration
     * Using 'class' strategy so we can toggle dark mode via Alpine.js
     * The dark mode is toggled by adding/removing 'dark' class on <html> element
     */
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            /**
             * Custom font family
             * Figtree is the default Laravel font, provides clean readability
             */
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            /**
             * Custom color palette for the Library Management System
             * These colors are used consistently throughout the application
             * to provide visual feedback and maintain brand consistency
             */
            colors: {
                // Primary color - Blue (#3B82F6)
                // Used for: main buttons, links, primary actions, navigation highlights
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3B82F6',  // Main primary color
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },

                // Success color - Green (#10B981)
                // Used for: successful actions, returned books, available status
                success: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10B981',  // Main success color
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                    950: '#022c22',
                },

                // Warning color - Yellow (#F59E0B)
                // Used for: warnings, books due soon, caution alerts
                warning: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#F59E0B',  // Main warning color
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                    950: '#451a03',
                },

                // Danger color - Red (#EF4444)
                // Used for: errors, overdue books, unavailable status, delete actions
                danger: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#EF4444',  // Main danger color
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                    950: '#450a0a',
                },
            },
        },
    },

    plugins: [forms],
};
