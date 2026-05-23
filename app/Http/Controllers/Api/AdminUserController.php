<?php

namespace App\Http\Controllers\Api;

use App\Enums\JournalRole;
use App\Http\Controllers\Controller;
use App\Models\JournalUserRole;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->with(['staffJournalRoles.journal'])
            ->orderBy('name')
            ->paginate(30);

        return response()->json($users);
    }

    public function updateRoles(Request $request, User $user): JsonResponse
    {
        $assignable = array_map(fn (JournalRole $role) => $role->value, JournalRole::assignable());

        $data = $request->validate([
            'assignments' => ['present', 'array'],
            'assignments.*.journal_id' => ['required', 'uuid', 'exists:journals,id'],
            'assignments.*.roles' => ['array'],
            'assignments.*.roles.*' => ['string', Rule::in($assignable)],
        ]);

        JournalUserRole::query()->where('user_id', $user->id)->delete();

        foreach ($data['assignments'] as $row) {
            foreach ($row['roles'] ?? [] as $roleValue) {
                JournalUserRole::query()->create([
                    'user_id' => $user->id,
                    'journal_id' => $row['journal_id'],
                    'role' => JournalRole::from($roleValue),
                    'assigned_by' => $request->user()->id,
                ]);
            }
        }

        return response()->json($user->fresh()->load('staffJournalRoles.journal'));
    }
}
