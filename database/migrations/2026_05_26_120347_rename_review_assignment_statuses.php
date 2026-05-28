<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('review_assignments')
            ->whereIn('status', ['invited', 'accepted'])
            ->update(['status' => 'assigned']);

        DB::table('review_assignments')
            ->where('status', 'declined')
            ->update(['status' => 'expired']);
    }

    public function down(): void
    {
        DB::table('review_assignments')
            ->where('status', 'assigned')
            ->update(['status' => 'accepted']);
    }
};
