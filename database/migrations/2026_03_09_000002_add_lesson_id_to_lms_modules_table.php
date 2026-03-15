<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lms_modules', function (Blueprint $table): void {
            $table->unsignedBigInteger('lesson_id')->nullable()->after('subject_id');
            $table->foreign('lesson_id')->references('id')->on('lms_lessons')->onDelete('set null');
            $table->index(['lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::table('lms_modules', function (Blueprint $table): void {
            $table->dropForeign(['lesson_id']);
            $table->dropIndex(['lesson_id']);
            $table->dropColumn('lesson_id');
        });
    }
};

