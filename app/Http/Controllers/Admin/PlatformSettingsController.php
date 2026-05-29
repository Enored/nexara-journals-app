<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Support\PlatformSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', [
            'settings' => PlatformSetting::current(),
        ]);
    }

    public function updateBranding(Request $request, PlatformSettingsService $platformSettings): RedirectResponse
    {
        $data = $request->validate([
            'platform_name' => ['required', 'string', 'max:255'],
            'logo_text' => ['nullable', 'string', 'max:100'],
            'show_logo_text_with_image' => ['sometimes', 'boolean'],
        ]);

        $platformSettings->updateBranding($data, $request->user());

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'Branding settings saved.');
    }

    public function updateGeneral(Request $request, PlatformSettingsService $platformSettings): RedirectResponse
    {
        $data = $request->validate([
            'maintenance_mode' => ['sometimes', 'boolean'],
        ]);

        $platformSettings->updateGeneral($data, $request->user());

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'General settings saved.');
    }
}
