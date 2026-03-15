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
        Schema::table('class_groups', function (Blueprint $table): void {
            $table->unsignedInteger('student_capacity')->default(10)->after('day_profile_id');
            $table->boolean('is_enrollment_open')->default(false)->after('student_capacity');
            $table->index(['school_id', 'semester_id', 'is_enrollment_open'], 'class_groups_enrollment_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_groups', function (Blueprint $table): void {
            $table->dropIndex('class_groups_enrollment_idx');
            $table->dropColumn(['student_capacity', 'is_enrollment_open']);
        });
    }
};
