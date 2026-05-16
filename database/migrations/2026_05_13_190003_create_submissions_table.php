<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->foreignUuid('author_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 500);
            $table->text('abstract');
            $table->json('keywords');
            $table->string('article_type', 100);
            $table->string('status', 32);
            $table->unsignedSmallInteger('version')->default(1);
            $table->foreignUuid('edition_id')->nullable()->constrained('editions')->nullOnDelete();
            $table->text('decision_letter')->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
