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
        Schema::table('lms_modules', function (Blueprint $table) {
            $table->string('type')->default('file')->after('uploaded_by_user_id'); // file, link, doc
            $table->longText('content')->nullable()->after('description'); // URL for links, HTML for docs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_modules', function (Blueprint $table) {
            $table->dropColumn(['type', 'content']);
        });
    }
};
