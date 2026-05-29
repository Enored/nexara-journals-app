<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AdminAuditLogger;
use App\Support\Impersonation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImpersonationController extends Controller
{
    public function stop(Request $request): RedirectResponse
    {
        if (! Impersonation::isActive()) {
            return redirect()->route('dashboard');
        }

        $target = $request->user();
        $admin = Impersonation::stop();

        AdminAuditLogger::log(
            AdminAuditLogger::USER_IMPERSONATION_STOPPED,
            $admin,
            $target,
            ['impersonator_id' => $admin->id],
        );

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Impersonation ended. You are signed in as '.$admin->name.' again.');
    }
}
