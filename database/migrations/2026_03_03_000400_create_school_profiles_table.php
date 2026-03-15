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
        Schema::create('school_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade')->unique();
            $table->text('intro')->nullable();
            $table->string('tag_primary', 120)->nullable();
            $table->string('tag_neutral', 120)->nullable();
            $table->string('tag_accent', 120)->nullable();
            $table->string('fact1_label', 80)->nullable();
            $table->string('fact1_value', 80)->nullable();
            $table->string('fact1_caption', 160)->nullable();
            $table->string('fact2_label', 80)->nullable();
            $table->string('fact2_value', 80)->nullable();
            $table->string('fact2_caption', 160)->nullable();
            $table->string('fact3_label', 80)->nullable();
            $table->string('fact3_value', 80)->nullable();
            $table->string('fact3_caption', 160)->nullable();
            $table->string('campus_title', 160)->nullable();
            $table->string('campus_bullet1', 255)->nullable();
            $table->string('campus_bullet2', 255)->nullable();
            $table->string('campus_bullet3', 255)->nullable();
            $table->string('campus_bullet4', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_profiles');
    }
};

