<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JournalRole;
use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\User;
use App\Support\AdminUserIndexFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class UserManageController extends Controller
{
    public function index(Request $request): View
    {
        $filters = AdminUserIndexFilters::fromRequest($request);
        $journals = Journal::query()->orderBy('name')->get();
        $users = AdminUserIndexFilters::paginate($filters);

        return view('admin.users.index', [
            'users' => $users,
            'filters' => $filters,
            'journals' => $journals,
            'roles' => JournalRole::assignable(),
            'activeFilterPills' => AdminUserIndexFilters::activeFilterPills($filters, $journals),
            'hasActiveFilters' => AdminUserIndexFilters::hasActiveFilters($filters),
        ]);
    }

    public function editRoles(Request $request, User $user): View|RedirectResponse
    {
        if (! $request->boolean('modal')) {
            return redirect()->route('admin.users.index', AdminUserIndexFilters::queryParamsFromRequest($request));
        }

        return view('admin.users.partials.edit-roles-form', array_merge(
            $this->rolesFormData($user),
            ['returnQuery' => AdminUserIndexFilters::queryParamsFromRequest($request)],
        ));
    }

    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        JournalUserRole::query()->where('user_id', $user->id)->delete();

        $rolesInput = $request->input('roles', []);

        foreach (Journal::query()->cursor() as $journal) {
            $flags = $rolesInput[$journal->id] ?? [];
            foreach (JournalRole::assignable() as $role) {
                if (! empty($flags[$role->value])) {
                    JournalUserRole::query()->create([
                        'user_id' => $user->id,
                        'journal_id' => $journal->id,
                        'role' => $role,
                        'assigned_by' => $request->user()->id,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.users.index', AdminUserIndexFilters::returnQueryFromRequest($request))
            ->with('status', 'Roles updated for '.$user->name.'.');
    }

    /**
     * @return array{user: User, journals: Collection, existing: Collection}
     */
    private function rolesFormData(User $user): array
    {
        return [
            'user' => $user,
            'journals' => Journal::query()->orderBy('name')->get(),
            'existing' => $user->staffJournalRoles()->get()->groupBy('journal_id'),
            'roles' => JournalRole::assignable(),
        ];
    }
}
