<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->unsignedSmallInteger('read_time')->nullable()->after('content');
            $table->index(['is_published', 'category', 'published_at'], 'blogs_published_category_date_idx');
        });

        // Backfill read_time for existing rows from their content word count.
        DB::table('blogs')->select('id', 'content')->orderBy('id')->chunkById(100, function ($blogs) {
            foreach ($blogs as $blog) {
                $words = str_word_count(strip_tags((string) $blog->content));
                DB::table('blogs')
                    ->where('id', $blog->id)
                    ->update(['read_time' => max(1, (int) ceil($words / 200))]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropIndex('blogs_published_category_date_idx');
            $table->dropColumn('read_time');
        });
    }
};
