<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 100)->default('')->after('name');
            $table->string('last_name', 100)->default('')->after('first_name');
            $table->string('mobile', 30)->nullable()->after('last_name');
            $table->text('bio')->nullable()->after('mobile');
            $table->string('city', 100)->nullable()->after('bio');
            $table->string('country', 100)->nullable()->after('city');
        });

        DB::table('users')->orderBy('id')->lazyById()->each(function (object $user) {
            $parts = preg_split('/\s+/', trim((string) $user->name), 2) ?: [];
            $firstName = $parts[0] ?? (string) $user->name;
            $lastName = $parts[1] ?? '';

            DB::table('users')->where('id', $user->id)->update([
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'mobile', 'bio', 'city', 'country']);
        });
    }
};
