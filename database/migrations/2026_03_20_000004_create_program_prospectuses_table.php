<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('program_prospectuses', function (Blueprint $table): void {
            $table->id();

            $table->unsignedBigInteger('school_id');
            $table->unsignedBigInteger('program_id');

            $table->string('name', 180);
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');

            $table->unsignedBigInteger('created_by_user_id')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'program_id']);
            $table->foreign('program_id')->references('id')->on('programs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_prospectuses');
    }
};

