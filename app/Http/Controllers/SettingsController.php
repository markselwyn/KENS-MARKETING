<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Support\SystemAudit;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        return view('settings', [
            'preferences' => $request->user()->appPreferences(),
            'landingPages' => $this->landingPages($request->user()),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'theme' => ['required', Rule::in(['light', 'dark', 'system'])],
            'landing_page' => ['required', Rule::in(array_keys($this->landingPages($request->user())))],
            'sidebar_state' => ['required', Rule::in(['expanded', 'collapsed'])],
            'reduced_motion' => ['required', 'boolean'],
        ]);

        $before = $request->user()->appPreferences();
        $validated['reduced_motion'] = $request->boolean('reduced_motion');
        $request->user()->preferences = $validated;
        $request->user()->save();

        $changedSettings = array_keys(array_filter(
            $validated,
            fn ($value, $key) => ($before[$key] ?? null) !== $value,
            ARRAY_FILTER_USE_BOTH
        ));

        SystemAudit::record(
            'Settings',
            'preferences_updated',
            "Updated application settings for {$request->user()->name}: " . ($changedSettings ? implode(', ', $changedSettings) : 'no preferences changed') . '.',
            $request->user(),
            ['changed_settings' => $changedSettings]
        );

        return back()->with('success', 'Application settings saved.');
    }

    public function updateSidebar(Request $request)
    {
        $validated = $request->validate([
            'sidebar_state' => ['required', Rule::in(['expanded', 'collapsed'])],
        ]);

        $preferences = $request->user()->appPreferences();
        $preferences['sidebar_state'] = $validated['sidebar_state'];
        $request->user()->preferences = $preferences;
        $request->user()->save();

        return response()->json(['sidebar_state' => $validated['sidebar_state']]);
    }

    /**
     * Landing pages available to the current role, keyed by route name.
     */
    private function landingPages($user): array
    {
        $pages = [
            'dashboard' => 'Dashboard',
            'inventory.index' => 'Inventory',
            'sales.index' => 'Sales Module',
            'reports' => 'Report Center',
            'dss-insights' => 'DSS Insights',
        ];

        if (strtolower(trim($user->role)) === 'admin') {
            $pages['admin.security'] = 'Security Hub';
        }

        return $pages;
    }
}
