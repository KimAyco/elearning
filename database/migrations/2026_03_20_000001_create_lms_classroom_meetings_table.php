<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lms_classroom_meetings', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('class_group_id');
            $table->unsignedBigInteger('subject_id');
            $table->string('title', 140);
            $table->text('description')->nullable();
            $table->string('meet_link', 2083);
            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'class_group_id', 'subject_id']);

            $table->foreign('class_group_id')->references('id')->on('class_groups')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_classroom_meetings');
    }
};

