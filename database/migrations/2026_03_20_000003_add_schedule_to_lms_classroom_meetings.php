<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lms_classroom_meetings', function (Blueprint $table): void {
            $table->timestamp('scheduled_start')->nullable()->after('meet_link');
            $table->timestamp('scheduled_end')->nullable()->after('scheduled_start');
            $table->string('google_event_id', 255)->nullable()->after('scheduled_end');
        });
    }

    public function down(): void
    {
        Schema::table('lms_classroom_meetings', function (Blueprint $table): void {
            $table->dropColumn(['scheduled_start', 'scheduled_end', 'google_event_id']);
        });
    }
};
