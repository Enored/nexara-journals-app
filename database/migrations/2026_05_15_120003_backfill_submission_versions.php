<?php

use App\Models\Submission;
use App\Support\SubmissionVersionRecorder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Submission::query()->each(function (Submission $submission) {
            SubmissionVersionRecorder::backfill($submission);
        });
    }

    public function down(): void
    {
        // Snapshots are derived data; leave rows on rollback of later migrations.
    }
};
