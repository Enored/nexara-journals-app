<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\JournalRole;
use App\Http\Controllers\Controller;
use Illuminate\View\View;

class EditorJournalController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $journals = $user->journalUserRoles()
            ->where('role', JournalRole::Editor)
            ->with('journal')
            ->get()
            ->pluck('journal')
            ->filter()
            ->sortBy('name')
            ->values();

        abort_if($journals->isEmpty(), 403);

        return view('dashboard.editor.journals', [
            'journals' => $journals,
        ]);
    }
}
