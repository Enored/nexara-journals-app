<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Serves the public directory query: WHERE status = 'published' ORDER BY submitted_at.
            $table->index(['status', 'submitted_at'], 'submissions_status_submitted_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex('submissions_status_submitted_at_index');
        });
    }
};
