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

    public function create(): View
    {
        return view('admin.announcements.create', [
            'journals' => AnnouncementRules::journalOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Announcement::query()->create($data);

        return redirect()
            ->route('admin.announcements.index')
            ->with('status', 'Announcement created.');
    }

    public function edit(Announcement $announcement): View
    {
        return view('admin.announcements.edit', [
            'announcement' => $announcement,
            'journals' => AnnouncementRules::journalOptions(),
        ]);
    }

    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $announcement->update($this->validated($request));

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
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $validator = validator($request->all(), AnnouncementRules::rules());
        AnnouncementRules::configureValidator($validator);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return AnnouncementRules::normalize($validator->validated());
    }
}
