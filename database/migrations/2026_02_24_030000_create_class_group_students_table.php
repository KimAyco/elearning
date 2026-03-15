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
        Schema::create('class_group_students', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('restrict');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('restrict');
            $table->foreignId('class_group_id')->constrained('class_groups')->cascadeOnDelete();
            $table->foreignId('student_user_id')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['school_id', 'semester_id', 'student_user_id'],
                'class_group_students_unique_student_semester'
            );
            $table->unique(
                ['school_id', 'class_group_id', 'student_user_id'],
                'class_group_students_unique_group_student'
            );
            $table->index(['school_id', 'class_group_id', 'status'], 'class_group_students_group_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_group_students');
    }
};

