<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Quizzes Table
        Schema::create('lms_quizzes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();
            $table->unsignedBigInteger('class_group_id')->index();
            $table->unsignedBigInteger('subject_id')->index();
            $table->unsignedBigInteger('lesson_id')->nullable()->index();
            $table->unsignedBigInteger('created_by_user_id')->index();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->integer('time_limit_minutes')->nullable(); // 0 or null for no limit
            $table->dateTime('due_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // 2. Quiz Questions
        Schema::create('lms_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('lms_quizzes')->onDelete('cascade');
            $table->string('type')->default('multiple_choice'); // multiple_choice, essay
            $table->text('question_text');
            $table->integer('points')->default(1);
            $table->integer('position')->default(0);
            $table->timestamps();
        });

        // 3. Question Choices (for Multiple Choice)
        Schema::create('lms_quiz_question_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('lms_quiz_questions')->onDelete('cascade');
            $table->text('choice_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        // 4. Student Quiz Attempts
        Schema::create('lms_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('lms_quizzes')->onDelete('cascade');
            $table->unsignedBigInteger('student_user_id')->index();
            $table->decimal('score', 8, 2)->default(0);
            $table->decimal('max_score', 8, 2)->default(0);
            $table->string('status')->default('in_progress'); // in_progress, submitted, graded
            $table->dateTime('started_at');
            $table->dateTime('submitted_at')->nullable();
            $table->unsignedBigInteger('graded_by_user_id')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->text('teacher_feedback')->nullable();
            $table->timestamps();
        });

        // 5. Student Answers
        Schema::create('lms_quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('lms_quiz_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('lms_quiz_questions')->onDelete('cascade');
            $table->foreignId('choice_id')->nullable()->constrained('lms_quiz_question_choices')->onDelete('cascade'); // For multiple choice
            $table->text('essay_answer')->nullable(); // For essays
            $table->decimal('points_awarded', 8, 2)->nullable(); // Manually graded for essays
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_quiz_answers');
        Schema::dropIfExists('lms_quiz_attempts');
        Schema::dropIfExists('lms_quiz_question_choices');
        Schema::dropIfExists('lms_quiz_questions');
        Schema::dropIfExists('lms_quizzes');
    }
};
