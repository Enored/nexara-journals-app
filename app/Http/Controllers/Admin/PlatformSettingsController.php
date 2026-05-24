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

    public function update(Request $request, PlatformSettingsService $platformSettings): RedirectResponse
    {
        $data = $request->validate([
            'platform_name' => ['required', 'string', 'max:255'],
            'maintenance_mode' => ['sometimes', 'boolean'],
        ]);

        $platformSettings->update($data, $request->user());

        return redirect()
            ->route('admin.settings.edit')
            ->with('status', 'System settings saved.');
    }
}
