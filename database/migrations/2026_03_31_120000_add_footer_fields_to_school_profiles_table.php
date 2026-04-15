<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table): void {
            $table->string('footer_title', 255)->nullable()->after('campus_bullet4');
            $table->text('footer_description')->nullable();
            $table->text('footer_address')->nullable();
            $table->string('footer_email', 255)->nullable();
            $table->string('footer_phone', 80)->nullable();
            $table->string('footer_copyright', 500)->nullable();
            $table->json('footer_quick_links')->nullable();
            $table->string('footer_social_facebook', 500)->nullable();
            $table->string('footer_social_instagram', 500)->nullable();
            $table->string('footer_social_x', 500)->nullable();
            $table->string('footer_social_youtube', 500)->nullable();
            $table->string('footer_social_website', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('school_profiles', function (Blueprint $table): void {
            $table->dropColumn([
                'footer_title',
                'footer_description',
                'footer_address',
                'footer_email',
                'footer_phone',
                'footer_copyright',
                'footer_quick_links',
                'footer_social_facebook',
                'footer_social_instagram',
                'footer_social_x',
                'footer_social_youtube',
                'footer_social_website',
            ]);
        });
    }
};
