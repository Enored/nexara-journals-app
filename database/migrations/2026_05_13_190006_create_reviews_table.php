<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('assignment_id')->constrained('review_assignments')->cascadeOnDelete();
            $table->foreignUuid('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignUuid('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('originality_score');
            $table->unsignedTinyInteger('methodology_score');
            $table->unsignedTinyInteger('clarity_score');
            $table->text('comments_for_author');
            $table->text('comments_for_editor')->nullable();
            $table->string('recommendation', 32);
            $table->foreignUuid('attachment_file_id')->nullable()->constrained('submission_files')->nullOnDelete();
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
