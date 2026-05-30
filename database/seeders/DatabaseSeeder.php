<?php

namespace Database\Seeders;

use App\Enums\JournalRole;
use App\Enums\ReviewAssignmentStatus;
use App\Enums\SubmissionStatus;
use App\Models\Journal;
use App\Models\JournalUserRole;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->create([
            'first_name' => 'Platform',
            'last_name' => 'Admin',
            'name' => 'Platform Admin',
            'email' => 'admin@example.com',
            'password' => 'password',
            'is_platform_admin' => true,
        ]);

        $editor = User::query()->create([
            'first_name' => 'Demo',
            'last_name' => 'Editor',
            'name' => 'Demo Editor',
            'email' => 'editor@example.com',
            'password' => 'password',
        ]);

        $reviewer = User::query()->create([
            'first_name' => 'Demo',
            'last_name' => 'Reviewer',
            'name' => 'Demo Reviewer',
            'email' => 'reviewer@example.com',
            'password' => 'password',
        ]);

        $author = User::query()->create([
            'first_name' => 'Demo',
            'last_name' => 'Author',
            'name' => 'Demo Author',
            'email' => 'author@example.com',
            'password' => 'password',
        ]);

        $journal = Journal::query()->create([
            'name' => 'Journal of Demo Research',
            'subdomain' => 'demo',
            'p_issn' => '1234-5678',
            'description' => 'A demonstration journal for local development.',
            'primary_color' => '#0f766e',
            'submission_guidelines' => "Prepare your manuscript as PDF or DOCX.\nMaximum abstract length 300 words.",
            'is_active' => true,
        ]);

        foreach ([
            [$editor->id, JournalRole::Editor],
            [$reviewer->id, JournalRole::Reviewer],
        ] as [$userId, $role]) {
            JournalUserRole::query()->create([
                'user_id' => $userId,
                'journal_id' => $journal->id,
                'role' => $role,
                'assigned_by' => $admin->id,
            ]);
        }

        $submission = Submission::query()->create([
            'journal_id' => $journal->id,
            'author_id' => $author->id,
            'title' => 'Sample manuscript: effects of demonstration data on dashboard UX',
            'abstract' => 'We present a seeded submission to exercise the editor, reviewer, and author dashboards in local development. No claims are made about statistical validity.',
            'keywords' => ['demo', 'workflow', 'journals'],
            'article_type' => 'research_article',
            'status' => SubmissionStatus::UnderReview,
            'version' => 1,
            'submitted_at' => now()->subDays(3),
        ]);

        ReviewAssignment::query()->create([
            'submission_id' => $submission->id,
            'reviewer_id' => $reviewer->id,
            'editor_id' => $editor->id,
            'status' => ReviewAssignmentStatus::Assigned,
            'deadline' => now()->addWeeks(2)->toDateString(),
            'invited_at' => now()->subDay(),
            'responded_at' => now()->subDay(),
        ]);

        $this->call(BlogSeeder::class);
    }
}
