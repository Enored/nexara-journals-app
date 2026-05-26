<?php

namespace App\Http\Controllers\Admin;

use App\Enums\JournalRole;
use App\Http\Controllers\Concerns\ReturnsDashListPartial;
use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\User;
use App\Support\AdminAuditLogger;
use App\Support\AdminUserAccountService;
use App\Support\AdminUserCsvExporter;
use App\Support\AdminUserCreator;
use App\Support\AdminUserIndexFilters;
use App\Support\AdminUserRoleSynchronizer;
use App\Support\Impersonation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserManageController extends Controller
{
    use ReturnsDashListPartial;

    public function index(Request $request): View
    {
        $filters = AdminUserIndexFilters::fromRequest($request);
        $journals = Journal::query()->orderBy('name')->get();
        $users = AdminUserIndexFilters::paginate($filters);

        $data = [
            'users' => $users,
            'filters' => $filters,
            'journals' => $journals,
            'roles' => JournalRole::assignable(),
            'activeFilterPills' => AdminUserIndexFilters::activeFilterPills($filters, $journals),
            'hasActiveFilters' => AdminUserIndexFilters::hasActiveFilters($filters),
        ];

        return $this->dashListResponse(
            $request,
            'admin.users.partials.list',
            'admin.users.index',
            $data,
        );
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = AdminUserIndexFilters::fromRequest($request);
        $users = AdminUserIndexFilters::results($filters);
        $filename = 'users-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(
            fn () => AdminUserCsvExporter::stream($users),
            $filename,
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'is_platform_admin' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $data['is_platform_admin'] = $request->boolean('is_platform_admin');
        $data['is_active'] = $request->boolean('is_active', true);

        $result = AdminUserCreator::create($data, $request->user());
        $user = $result['user'];

        return redirect()
            ->route('admin.users.index', AdminUserIndexFilters::returnQueryFromRequest($request))
            ->with('created_user_name', $user->name)
            ->with('created_user_email', $user->email)
            ->with('created_user_password', $result['plainPassword']);
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        AdminUserAccountService::suspend($request->user(), $user);

        return redirect()
            ->route('admin.users.index', AdminUserIndexFilters::returnQueryFromRequest($request))
            ->with('status', $user->fresh()->name.' has been suspended.');
    }

    public function unsuspend(Request $request, User $user): RedirectResponse
    {
        AdminUserAccountService::unsuspend($request->user(), $user);

        return redirect()
            ->route('admin.users.index', AdminUserIndexFilters::returnQueryFromRequest($request))
            ->with('status', $user->fresh()->name.' has been reactivated.');
    }

    public function impersonate(Request $request, User $user): RedirectResponse
    {
        $admin = $request->user();
        Impersonation::start($admin, $user);

        AdminAuditLogger::log(
            AdminAuditLogger::USER_IMPERSONATION_STARTED,
            $admin,
            $user,
        );

        return redirect()
            ->route('dashboard')
            ->with('status', 'You are now viewing the platform as '.$user->name.'.');
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
        AdminUserRoleSynchronizer::sync($user, $request->input('roles', []), $request->user()->id);

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
