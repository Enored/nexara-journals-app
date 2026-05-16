<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('subdomain', 100)->unique();
            $table->string('issn', 20)->nullable();
            $table->text('description')->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->string('primary_color', 7)->nullable();
            $table->text('submission_guidelines')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
