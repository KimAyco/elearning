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
        Schema::table('schools', function (Blueprint $table) {
            $table->string('logo_path', 255)->nullable()->after('short_description');
            $table->string('cover_image_path', 255)->nullable()->after('logo_path');
            $table->string('theme', 40)->nullable()->after('cover_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'cover_image_path', 'theme']);
        });
    }
};
