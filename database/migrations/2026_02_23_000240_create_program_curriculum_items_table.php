<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('program_curriculum_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('program_id')->constrained('programs')->onDelete('restrict');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('restrict');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('restrict');
            $table->unsignedTinyInteger('year_level');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->foreignId('created_by_user_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->unique(
                ['school_id', 'program_id', 'semester_id', 'subject_id', 'year_level'],
                'program_curriculum_unique'
            );
            $table->index(['school_id', 'program_id', 'year_level', 'semester_id'], 'program_curriculum_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_curriculum_items');
    }
};
