<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('program_prospectus_items')) {
            Schema::create('program_prospectus_items', function (Blueprint $table): void {
                $table->id();

                $table->unsignedBigInteger('program_prospectus_id');
                $table->unsignedBigInteger('semester_id');
                $table->unsignedBigInteger('subject_id');
                $table->unsignedTinyInteger('year_level');

                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->unsignedBigInteger('created_by_user_id')->nullable();
                $table->timestamps();

                // MySQL has a limit on index-name length. Use a short explicit name.
                $table->index(['program_prospectus_id', 'year_level', 'semester_id'], 'ppi_lookup');

                $table->unique(
                    ['program_prospectus_id', 'year_level', 'semester_id', 'subject_id'],
                    'program_prospectus_unique'
                );

                $table->foreign('program_prospectus_id')
                    ->references('id')
                    ->on('program_prospectuses')
                    ->onDelete('cascade');

                $table->foreign('semester_id')->references('id')->on('semesters')->onDelete('restrict');
                $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('restrict');
            });

            return;
        }

        // Table exists already from a previous failed migration.
        // Ensure the short lookup index exists.
        $hasIndex = DB::table('information_schema.statistics')
            ->where('table_schema', DB::raw('database()'))
            ->where('table_name', 'program_prospectus_items')
            ->where('index_name', 'ppi_lookup')
            ->exists();

        if (! $hasIndex) {
            DB::statement('CREATE INDEX ppi_lookup ON program_prospectus_items (program_prospectus_id, year_level, semester_id)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('program_prospectus_items');
    }
};

