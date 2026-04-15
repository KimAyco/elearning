<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lms_course_card_customizations', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('class_group_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('accent_color', 32)->nullable();
            $table->longText('image_data_url')->nullable();
            $table->unsignedBigInteger('created_by_user_id')->nullable();

            $table->timestamps();

            $table->unique(['school_id', 'class_group_id', 'subject_id'], 'lms_cc_customizations_unique');
            $table->index(['school_id']);

            $table->foreign('class_group_id')->references('id')->on('class_groups')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_course_card_customizations');
    }
};

