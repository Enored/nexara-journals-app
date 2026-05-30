<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->text('excerpt')->nullable()->after('description');
            $table->string('abbreviation', 50)->nullable()->after('name');
            $table->string('e_issn', 9)->nullable()->after('issn');
            $table->string('p_issn', 9)->nullable()->after('issn');
            $table->string('review_model')->default('single_blind')->after('is_active');
            $table->string('frequency', 50)->nullable()->after('review_model');
            $table->string('license_type', 50)->nullable()->after('frequency');
            $table->string('contact_email', 255)->nullable()->after('license_type');
        });

        DB::table('journals')->whereNotNull('issn')->update(['p_issn' => DB::raw('issn')]);

        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn('issn');
            $table->renameColumn('logo_path', 'cover_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->string('issn', 20)->nullable()->after('subdomain');
            $table->renameColumn('cover_image_url', 'logo_path');
        });

        DB::table('journals')->whereNotNull('p_issn')->update(['issn' => DB::raw('p_issn')]);

        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn([
                'excerpt',
                'abbreviation',
                'e_issn',
                'p_issn',
                'review_model',
                'frequency',
                'license_type',
                'contact_email',
            ]);
        });
    }
};
