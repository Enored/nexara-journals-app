<?php

namespace App\Http\Controllers\Api;

use App\Enums\JournalRole;
use App\Http\Controllers\Controller;
use App\Models\JournalUserRole;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->with('journalUserRoles.journal')
            ->orderBy('name')
            ->paginate(30);

        return response()->json($users);
    }

    public function updateRoles(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'assignments' => ['required', 'array'],
            'assignments.*.journal_id' => ['required', 'uuid', 'exists:journals,id'],
            'assignments.*.role' => ['required', 'string', 'in:author,reviewer,editor,admin'],
        ]);

        foreach ($data['assignments'] as $row) {
            JournalUserRole::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'journal_id' => $row['journal_id'],
                    'role' => JournalRole::from($row['role']),
                ],
                [
                    'assigned_by' => $request->user()->id,
                ]
            );
        }

        return response()->json($user->fresh()->load('journalUserRoles.journal'));
    }
}
