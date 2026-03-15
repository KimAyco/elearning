<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_modules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->unsignedBigInteger('class_group_id')->index();
            $table->unsignedBigInteger('subject_id')->index();
            $table->unsignedBigInteger('uploaded_by_user_id')->index();

            $table->string('title', 140);
            $table->text('description')->nullable();

            $table->string('file_path', 255);
            $table->string('original_name', 255)->nullable();
            $table->string('mime_type', 120)->nullable();
            $table->unsignedBigInteger('file_size')->default(0);

            $table->timestamps();

            $table->index(['school_id', 'class_group_id', 'subject_id'], 'lms_modules_scope_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_modules');
    }
};

