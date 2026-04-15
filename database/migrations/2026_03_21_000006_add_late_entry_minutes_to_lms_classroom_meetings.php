<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lms_classroom_meetings', function (Blueprint $table): void {
            $table->unsignedSmallInteger('late_entry_minutes')->nullable()->after('scheduled_end');
        });
    }

    public function down(): void
    {
        Schema::table('lms_classroom_meetings', function (Blueprint $table): void {
            $table->dropColumn('late_entry_minutes');
        });
    }
};
