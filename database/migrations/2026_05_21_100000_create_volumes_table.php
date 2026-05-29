<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('volumes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->unsignedSmallInteger('number');
            $table->string('title')->nullable();
            $table->timestamps();

            $table->unique(['journal_id', 'number']);
        });

        Schema::table('editions', function (Blueprint $table) {
            $table->foreignUuid('volume_id')->nullable()->after('journal_id')->constrained('volumes')->restrictOnDelete();
        });

        $editionRows = DB::table('editions')->orderBy('journal_id')->orderBy('volume')->get();

        foreach ($editionRows as $row) {
            $volumeId = DB::table('volumes')
                ->where('journal_id', $row->journal_id)
                ->where('number', $row->volume)
                ->value('id');

            if (! $volumeId) {
                $volumeId = (string) Str::uuid();
                DB::table('volumes')->insert([
                    'id' => $volumeId,
                    'journal_id' => $row->journal_id,
                    'number' => $row->volume,
                    'title' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('editions')->where('id', $row->id)->update(['volume_id' => $volumeId]);
        }

        Schema::table('editions', function (Blueprint $table) {
            $table->uuid('volume_id')->nullable(false)->change();
            $table->dropUnique(['journal_id', 'volume', 'issue']);
            $table->dropColumn('volume');
            $table->unique(['journal_id', 'volume_id', 'issue']);
        });
    }

    public function down(): void
    {
        Schema::table('editions', function (Blueprint $table) {
            $table->unsignedSmallInteger('volume')->nullable()->after('journal_id');
        });

        $editionRows = DB::table('editions')
            ->join('volumes', 'editions.volume_id', '=', 'volumes.id')
            ->select('editions.id', 'volumes.number')
            ->get();

        foreach ($editionRows as $row) {
            DB::table('editions')->where('id', $row->id)->update(['volume' => $row->number]);
        }

        Schema::table('editions', function (Blueprint $table) {
            $table->dropUnique(['journal_id', 'volume_id', 'issue']);
            $table->dropConstrainedForeignId('volume_id');
            $table->unsignedSmallInteger('volume')->nullable(false)->change();
            $table->unique(['journal_id', 'volume', 'issue']);
        });

        Schema::dropIfExists('volumes');
    }
};
