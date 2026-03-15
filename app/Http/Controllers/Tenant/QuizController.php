<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\ClassGroup;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAnswer;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\LmsQuizQuestionChoice;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    /**
     * Store a new quiz.
     */
    public function store(Request $request, ClassGroup $classGroup, Subject $subject)
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:0'],
            'due_date' => ['nullable', 'date'],
            'lesson_id' => ['nullable', 'integer'],
        ]);

        $quiz = LmsQuiz::create([
            'school_id' => $schoolId,
            'class_group_id' => $classGroup->id,
            'subject_id' => $subject->id,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'created_by_user_id' => $userId,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'due_date' => $validated['due_date'],
            'is_published' => false,
        ]);

        return redirect()->back()->with('status', 'Quiz created successfully. Now add some questions.');
    }

    /**
     * Add a question to a quiz.
     */
    public function addQuestion(Request $request, LmsQuiz $quiz)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:multiple_choice,essay'],
            'question_text' => ['required', 'string'],
            'points' => ['required', 'integer', 'min:1'],
            'choices' => ['required_if:type,multiple_choice', 'array'],
            'choices.*.text' => ['required_if:type,multiple_choice', 'string'],
            'choices.*.is_correct' => ['nullable'],
            'correct_choice_index' => ['required_if:type,multiple_choice', 'integer'],
        ]);

        DB::transaction(function () use ($quiz, $validated) {
            $question = $quiz->questions()->create([
                'type' => $validated['type'],
                'question_text' => $validated['question_text'],
                'points' => $validated['points'],
                'position' => $quiz->questions()->count(),
            ]);

            if ($validated['type'] === 'multiple_choice' && !empty($validated['choices'])) {
                foreach ($validated['choices'] as $index => $choiceData) {
                    $question->choices()->create([
                        'choice_text' => $choiceData['text'],
                        'is_correct' => (isset($validated['correct_choice_index']) && (int)$validated['correct_choice_index'] === $index),
                    ]);
                }
            }
        });

        return redirect()->back()->with('status', 'Question added to quiz.');
    }

    /**
     * Publish a quiz.
     */
    public function publish(LmsQuiz $quiz)
    {
        if ($quiz->questions()->count() === 0) {
            return redirect()->back()->withErrors(['quiz' => 'Cannot publish a quiz with no questions.']);
        }

        $quiz->update(['is_published' => true]);
        return redirect()->back()->with('status', 'Quiz published and visible to students.');
    }

    /**
     * View quiz results (for teachers).
     */
    public function results(LmsQuiz $quiz)
    {
        $attempts = $quiz->attempts()->with('student')->orderByDesc('submitted_at')->get();
        return view('tenant.lms.quiz-results', compact('quiz', 'attempts'));
    }

    /**
     * View gradebook for a class and subject.
     */
    public function gradebook(Request $request, ClassGroup $classGroup, Subject $subject)
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        
        // Fetch all quizzes for this course
        $quizzes = LmsQuiz::where('school_id', $schoolId)
            ->where('class_group_id', $classGroup->id)
            ->where('subject_id', $subject->id)
            ->where('is_published', true)
            ->orderBy('id')
            ->get();

        // Fetch all enrolled students
        // We look for enrollments where the section identifier matches this ClassGroup
        $enrollments = \App\Models\Enrollment::with('student')
            ->where('school_id', $schoolId)
            ->whereHas('offering', function ($q) use ($subject) {
                $q->where('subject_id', $subject->id);
            })
            ->whereHas('section', function ($q) use ($classGroup) {
                $q->where('identifier', 'CG-' . $classGroup->id);
            })
            ->get();

        $students = $enrollments->map(fn($e) => $e->student)->filter()->unique('id')->sortBy('full_name');

        // Fetch all attempts for these quizzes and students
        $attempts = LmsQuizAttempt::whereIn('quiz_id', $quizzes->pluck('id'))
            ->whereIn('student_user_id', $students->pluck('id'))
            ->get()
            ->groupBy(fn($a) => $a->student_user_id . '_' . $a->quiz_id);

        return view('tenant.lms.gradebook', compact('classGroup', 'subject', 'quizzes', 'students', 'attempts'));
    }

    /**
     * Grade an essay (for teachers).
     */
    public function gradeEssay(Request $request, LmsQuizAttempt $attempt)
    {
        $userId = (int) $request->attributes->get('actor_user_id');
        
        $validated = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.answer_id' => ['required', 'integer'],
            'grades.*.points' => ['required', 'numeric', 'min:0'],
            'feedback' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($attempt, $validated, $userId) {
            foreach ($validated['grades'] as $grade) {
                $answer = $attempt->answers()->where('id', $grade['answer_id'])->first();
                if ($answer) {
                    $answer->update(['points_awarded' => $grade['points']]);
                }
            }

            // Recalculate total score
            $totalScore = $attempt->answers()->sum('points_awarded');
            
            $attempt->update([
                'score' => $totalScore,
                'status' => 'graded',
                'graded_by_user_id' => $userId,
                'graded_at' => now(),
                'teacher_feedback' => $validated['feedback'],
            ]);
        });

        return redirect()->back()->with('status', 'Quiz attempt graded.');
    }

    /**
     * Show quiz for student to attempt.
     */
    public function show(Request $request, LmsQuiz $quiz)
    {
        if (!$quiz->is_published) {
            abort(404);
        }

        $userId = (int) $request->attributes->get('actor_user_id');
        $attempt = $quiz->attempts()->where('student_user_id', $userId)->first();

        if ($attempt && $attempt->status !== 'in_progress') {
            return view('tenant.lms.quiz-result-summary', compact('quiz', 'attempt'));
        }

        $questions = $quiz->questions()->with('choices')->get();
        return view('tenant.lms.quiz-attempt', compact('quiz', 'questions', 'attempt'));
    }

    /**
     * Start a quiz attempt.
     */
    public function start(Request $request, LmsQuiz $quiz)
    {
        $userId = (int) $request->attributes->get('actor_user_id');
        
        // Check if already has an attempt
        $existing = $quiz->attempts()->where('student_user_id', $userId)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'You have already started or submitted this quiz.');
        }

        $totalPoints = $quiz->questions()->sum('points');
        
        LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_user_id' => $userId,
            'max_score' => $totalPoints,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return redirect()->back();
    }

    /**
     * Submit quiz attempt.
     */
    public function submit(Request $request, LmsQuiz $quiz)
    {
        $userId = (int) $request->attributes->get('actor_user_id');
        
        $validated = $request->validate([
            'answers' => ['nullable', 'array'],
        ]);

        return DB::transaction(function () use ($quiz, $validated, $userId) {
            $attempt = $quiz->attempts()
                ->where('student_user_id', $userId)
                ->where('status', 'in_progress')
                ->first();

            if (!$attempt) {
                return redirect()->back()->with('error', 'No active quiz attempt found.');
            }

            $score = 0;
            $hasEssays = false;

            foreach ($quiz->questions as $question) {
                $studentAnswer = $validated['answers'][$question->id] ?? null;
                
                $answerData = [
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                ];

                if ($question->type === 'multiple_choice') {
                    $choiceId = is_numeric($studentAnswer) ? (int) $studentAnswer : null;
                    $choice = null;
                    if (!is_null($choiceId) && $choiceId > 0) {
                        $choice = LmsQuizQuestionChoice::where('id', $choiceId)
                            ->where('question_id', $question->id)
                            ->first();
                    }
                    if ($choice) {
                        $answerData['choice_id'] = $choiceId;
                        if ($choice->is_correct) {
                            $score += $question->points;
                            $answerData['points_awarded'] = $question->points;
                        } else {
                            $answerData['points_awarded'] = 0;
                        }
                    } else {
                        $answerData['choice_id'] = null;
                        $answerData['points_awarded'] = 0;
                    }
                } else {
                    $answerData['essay_answer'] = $studentAnswer;
                    $hasEssays = true;
                }

                LmsQuizAnswer::create($answerData);
            }

            $attempt->update([
                'score' => $score,
                'status' => $hasEssays ? 'submitted' : 'graded',
                'submitted_at' => now(),
            ]);

            return redirect()->back()->with('status', 'Quiz submitted successfully.');
        });
    }
}
