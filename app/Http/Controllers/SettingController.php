<?php

/**
 * SettingController.php
 *
 * This controller handles system settings management.
 * Only administrators can access these functions.
 *
 * Features:
 * - View all settings grouped by category
 * - Update settings with validation
 * - Reset settings to default values
 *
 * @package App\Http\Controllers
 * @author  Library Management System
 * @version 1.0
 */

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * The setting service instance.
     *
     * @var SettingService
     */
    protected SettingService $settingService;

    /**
     * Create a new controller instance.
     *
     * @param SettingService $settingService
     */
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    /**
     * Display the settings page.
     *
     * Shows all settings grouped by category with their current values.
     *
     * @return View
     */
    public function index(): View
    {
        // Get all settings grouped by category
        $groupedSettings = $this->settingService->getGroupedSettings();

        // Get date format options for the dropdown
        $dateFormatOptions = [
            'M d, Y' => 'Jan 25, 2026',
            'd/m/Y' => '25/01/2026',
            'Y-m-d' => '2026-01-25',
            'F j, Y' => 'January 25, 2026',
        ];

        return view('settings.index', [
            'groupedSettings' => $groupedSettings,
            'dateFormatOptions' => $dateFormatOptions,
        ]);
    }

    /**
     * Update the settings.
     *
     * Validates and saves all settings from the form.
     *
     * @param SettingRequest $request
     * @return RedirectResponse
     */
    public function update(SettingRequest $request): RedirectResponse
    {
        // Get validated data
        $validated = $request->validated();

        // Update all settings
        $this->settingService->updateMany($validated);

        return redirect()
            ->route('settings.index')
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Reset all settings to their default values.
     *
     * Requires confirmation via request parameter.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function reset(Request $request): RedirectResponse
    {
        // Validate confirmation
        $request->validate([
            'confirm_reset' => 'required|accepted',
        ], [
            'confirm_reset.accepted' => 'Please confirm that you want to reset all settings.',
        ]);

        // Reset all settings to defaults
        $this->settingService->resetDefaults();

        return redirect()
            ->route('settings.index')
            ->with('success', 'All settings have been reset to their default values.');
    }

    /**
     * Get a single setting value (API endpoint).
     *
     * @param Request $request
     * @param string $key
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSetting(Request $request, string $key)
    {
        if (!$this->settingService->isValidKey($key)) {
            return response()->json(['error' => 'Invalid setting key'], 404);
        }

        return response()->json([
            'key' => $key,
            'value' => $this->settingService->get($key),
        ]);
    }
}
