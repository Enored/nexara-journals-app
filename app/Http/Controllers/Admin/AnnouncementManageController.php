<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ReturnsDashListPartial;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Support\AdminAnnouncementIndexFilters;
use App\Support\AnnouncementRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AnnouncementManageController extends Controller
{
    use ReturnsDashListPartial;

    public function index(Request $request): View
    {
        $filters = AdminAnnouncementIndexFilters::fromRequest($request);
        $announcements = AdminAnnouncementIndexFilters::paginate($filters);

        return $this->dashListResponse(
            $request,
            'admin.announcements.partials.list',
            'admin.announcements.index',
            [
                'announcements' => $announcements,
                'filters' => $filters,
                'journals' => AnnouncementRules::journalOptions(),
                'activeFilterPills' => AdminAnnouncementIndexFilters::activeFilterPills($filters),
                'hasActiveFilters' => AdminAnnouncementIndexFilters::hasActiveFilters($filters),
            ],
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request, 'create');
        Announcement::query()->create($data);

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Announcement created.');
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $data = $this->validated($request, 'edit', $announcement);
        $announcement->update($data);

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Announcement updated.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $announcement->delete();

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Announcement removed.');
    }

    /**
     * Validate the request, redirecting back to the index (reopening the
     * relevant modal) when validation fails.
     *
     * @return array<string, mixed>
     */
    private function validated(Request $request, string $context, ?Announcement $announcement = null): array
    {
        $validator = validator($request->all(), AnnouncementRules::rules());
        AnnouncementRules::configureValidator($validator);

        if ($validator->fails()) {
            $redirect = redirect()
                ->route('admin.announcements.index')
                ->withErrors($validator)
                ->withInput()
                ->with('announcement_form', $context);

            if ($announcement) {
                $redirect->with('announcement_editing_id', $announcement->id);
            }

            throw new ValidationException($validator, $redirect);
        }

        return AnnouncementRules::normalize($validator->validated());
    }
}
