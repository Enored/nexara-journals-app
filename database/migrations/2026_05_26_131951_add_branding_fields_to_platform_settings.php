<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platform_settings', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('favicon_path');
            $table->string('logo_text')->nullable()->after('logo_path');
            $table->boolean('show_logo_text_with_image')->default(false)->after('logo_text');
        });
    }

    public function down(): void
    {
        Schema::table('platform_settings', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'logo_text', 'show_logo_text_with_image']);
        });
    }
};
