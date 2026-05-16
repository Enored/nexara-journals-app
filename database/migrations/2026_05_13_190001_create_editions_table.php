<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->unsignedSmallInteger('volume');
            $table->unsignedSmallInteger('issue');
            $table->string('title')->nullable();
            $table->date('published_at')->nullable();
            $table->timestamps();

            $table->unique(['journal_id', 'volume', 'issue']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};
