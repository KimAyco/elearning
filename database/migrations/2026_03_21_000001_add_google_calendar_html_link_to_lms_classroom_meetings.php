<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lms_classroom_meetings', function (Blueprint $table): void {
            $table->string('google_calendar_html_link', 2083)->nullable()->after('meet_link');
        });
    }

    public function down(): void
    {
        Schema::table('lms_classroom_meetings', function (Blueprint $table): void {
            $table->dropColumn('google_calendar_html_link');
        });
    }
};

