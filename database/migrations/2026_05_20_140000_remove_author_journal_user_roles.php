<?php

use App\Enums\JournalRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('journal_user_roles')
            ->where('role', JournalRole::Author->value)
            ->delete();
    }

    public function down(): void
    {
        // Author rows were legacy assignments; author access is implicit and not restored.
    }
};
