<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('class_day_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->string('name', 120);
            $table->time('class_start_time');
            $table->time('class_end_time');
            $table->unsignedSmallInteger('slot_minutes')->default(60);
            $table->json('days_mask')->nullable();
            $table->timestamps();

            $table->unique(['school_id', 'name']);
            $table->index(['school_id', 'slot_minutes']);
        });

        Schema::create('class_break_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('class_day_profile_id')->constrained('class_day_profiles')->cascadeOnDelete();
            $table->string('label', 80);
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['school_id', 'class_day_profile_id', 'sort_order'], 'class_break_blocks_profile_sort_idx');
        });

        Schema::create('class_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('program_id')->constrained('programs')->onDelete('restrict');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('restrict');
            $table->unsignedTinyInteger('year_level');
            $table->string('name', 80);
            $table->foreignId('day_profile_id')->constrained('class_day_profiles')->onDelete('restrict');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();

            $table->unique(['school_id', 'semester_id', 'program_id', 'year_level', 'name'], 'class_groups_unique');
            $table->index(['school_id', 'semester_id', 'status']);
        });

        Schema::create('class_generation_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('class_group_id')->constrained('class_groups')->cascadeOnDelete();
            $table->foreignId('initiated_by_user_id')->constrained('users')->onDelete('restrict');
            $table->json('summary_json')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'class_group_id']);
        });

        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('class_group_id')->constrained('class_groups')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('restrict');
            $table->enum('session_type', ['lecture', 'lab']);
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('teacher_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('generation_run_id')->nullable()->constrained('class_generation_runs')->nullOnDelete();
            $table->enum('status', ['draft', 'locked', 'cancelled'])->default('draft');
            $table->timestamps();

            $table->index(['school_id', 'class_group_id', 'day_of_week', 'start_time', 'end_time'], 'class_sessions_group_time_idx');
            $table->index(['school_id', 'teacher_user_id', 'day_of_week', 'start_time', 'end_time'], 'class_sessions_teacher_time_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
        Schema::dropIfExists('class_generation_runs');
        Schema::dropIfExists('class_groups');
        Schema::dropIfExists('class_break_blocks');
        Schema::dropIfExists('class_day_profiles');
    }
};
