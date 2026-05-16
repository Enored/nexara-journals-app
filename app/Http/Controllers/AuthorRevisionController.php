<?php

namespace App\Http\Controllers;

use App\Enums\JournalRole;
use App\Enums\SubmissionFileType;
use App\Enums\SubmissionStatus;
use App\Models\JournalUserRole;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\WorkflowNotification;
use App\Support\SubmissionVersionRecorder;
use App\Support\SubmissionWorkspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorRevisionController extends Controller
{
    public function store(Request $request, Submission $submission): RedirectResponse
    {
        $this->authorize('submitRevision', $submission);

        $data = $request->validate([
            'manuscript' => ['required', 'file', 'max:20480'],
            'title' => ['nullable', 'string', 'max:500'],
            'abstract' => ['nullable', 'string', 'max:5000'],
            'keywords' => ['nullable', 'string', 'max:2000'],
        ]);

        $file = $request->file('manuscript');
        $newVersion = $submission->version + 1;

        DB::transaction(function () use ($submission, $data, $file, $newVersion, $request) {
            $path = $file->store('submissions/'.$submission->id, 'local');

            SubmissionFile::query()->create([
                'submission_id' => $submission->id,
                'file_type' => SubmissionFileType::Revision,
                'original_name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getClientMimeType() ?? 'application/octet-stream',
                'file_size' => $file->getSize(),
                'version' => $newVersion,
                'uploaded_by' => $request->user()->id,
            ]);

            $updates = [
                'version' => $newVersion,
                'status' => SubmissionStatus::Submitted,
                'submitted_at' => now(),
            ];

            if (! empty($data['title'])) {
                $updates['title'] = $data['title'];
            }
            if (! empty($data['abstract'])) {
                $updates['abstract'] = $data['abstract'];
            }
            if (isset($data['keywords']) && $data['keywords'] !== '') {
                $keywords = array_values(array_filter(array_map('trim', preg_split('/[,;]+/', $data['keywords']))));
                if ($keywords !== []) {
                    $updates['keywords'] = $keywords;
                }
            }

            $submission->update($updates);
            $submission->refresh();

            SubmissionVersionRecorder::record($submission);

            $editorIds = JournalUserRole::query()
                ->where('journal_id', $submission->journal_id)
                ->whereIn('role', [JournalRole::Editor, JournalRole::Admin])
                ->pluck('user_id')
                ->unique();

            foreach ($editorIds as $userId) {
                WorkflowNotification::query()->create([
                    'user_id' => $userId,
                    'type' => 'author_revision_submitted',
                    'data' => [
                        'submission_id' => $submission->id,
                        'version' => $newVersion,
                    ],
                ]);
            }
        });

        return redirect()
            ->away(SubmissionWorkspace::authorRoute($submission))
            ->with('status', 'Your revision was uploaded. The manuscript is back with the editor as a new submission round.');
    }
}
