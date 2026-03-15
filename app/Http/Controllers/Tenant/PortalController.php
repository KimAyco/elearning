<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Billing;
use App\Models\BillingCategory;
use App\Models\BillingRule;
use App\Models\BillingGroup;
use App\Models\BillingGroupItem;
use App\Models\ClassDayProfile;
use App\Models\ClassGenerationRun;
use App\Models\ClassGroup;
use App\Models\College;
use App\Models\ClassSession;
use App\Models\Department;
use App\Models\Enrollment;
use App\Models\FinanceSetting;
use App\Models\FinanceFeeSetting;
use App\Models\Grade;
use App\Models\Payment;
use App\Models\Program;
use App\Models\ProgramCurriculumItem;
use App\Models\Role;
use App\Models\School;
use App\Models\SchoolProfile;
use App\Models\Section;
use App\Models\Semester;
use App\Models\LmsModule;
use App\Models\LmsQuiz;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\SubjectOffering;
use App\Models\User;
use App\Models\Discount;
use App\Models\Scholarship;
use App\Models\StudentWallet;
use App\Models\LmsLesson;
use App\Models\CashierPayment;
use App\Models\UserRole;
use App\Services\BillingWorkflowService;
use App\Services\ClassesSchedulingService;
use App\Services\PayMongoService;
use App\Services\EnrollmentWorkflowService;
use App\Services\GradeWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Illuminate\Validation\ValidationException;

class PortalController extends Controller
{
    public function __construct(
        private readonly EnrollmentWorkflowService $enrollmentWorkflowService,
        private readonly BillingWorkflowService $billingWorkflowService,
        private readonly GradeWorkflowService $gradeWorkflowService,
        private readonly ClassesSchedulingService $classesSchedulingService,
        private readonly PayMongoService $payMongoService,
    ) {
    }

    public function dashboard(Request $request): View
    {
        $roleCodes = (array) $request->session()->get('role_codes', []);
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');
        $teachableSubjects = collect();
        $teacherWeeklyScheduleRows = collect();
        $teacherWeeklyScheduleSummary = [
            'total_sessions' => 0,
            'days_covered' => 0,
            'subjects' => 0,
            'class_groups' => 0,
        ];

        $needsProgramEnrollment = false;
        $openEnrollmentSemesterName = null;
        $studentProfile = null;

        if (in_array('student', $roleCodes, true) && $schoolId > 0 && $userId > 0) {
            $semester = $this->resolveEnrollmentSemester($schoolId);
            $openEnrollmentSemesterName = $semester?->name;

            $studentProfile = StudentProfile::query()
                ->with(['program:id,code,name'])
                ->where('school_id', $schoolId)
                ->where('user_id', $userId)
                ->first();
            $hasStudentProfile = $studentProfile !== null;

            $hasAnyCurrentSemesterEnrollment = false;
            if ($semester !== null) {
                $hasAnyCurrentSemesterEnrollment = Enrollment::query()
                    ->where('school_id', $schoolId)
                    ->where('student_user_id', $userId)
                    ->where('semester_id', (int) $semester->id)
                    ->whereIn('status', ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'])
                    ->exists();
            }

            $needsProgramEnrollment = (! $hasStudentProfile) || (! $hasAnyCurrentSemesterEnrollment);
        }

        if (in_array('teacher', $roleCodes, true) && $schoolId > 0 && $userId > 0) {
            $teachableSubjects = $this->fetchTeachableSubjects($schoolId, $userId);

            $teacherWeeklyScheduleRows = ClassSession::query()
                ->with(['subject:id,code,title', 'classGroup:id,name'])
                ->where('school_id', $schoolId)
                ->where('teacher_user_id', $userId)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->map(function (ClassSession $session): array {
                    $dayLabel = match ((int) $session->day_of_week) {
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        7 => 'Sunday',
                        default => 'Day ' . (int) $session->day_of_week,
                    };

                    $startTime = substr((string) $session->start_time, 0, 5);
                    $endTime = substr((string) $session->end_time, 0, 5);
                    $startMin = ((int) substr((string) $session->start_time, 0, 2) * 60) + (int) substr((string) $session->start_time, 3, 2);
                    $endMin = ((int) substr((string) $session->end_time, 0, 2) * 60) + (int) substr((string) $session->end_time, 3, 2);

                    return [
                        'day_order' => (int) $session->day_of_week,
                        'day_label' => $dayLabel,
                        'time_label' => $startTime . ' - ' . $endTime,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'start_min' => $startMin,
                        'end_min' => $endMin,
                        'subject_code' => (string) ($session->subject?->code ?? 'N/A'),
                        'subject_title' => (string) ($session->subject?->title ?? 'Untitled Subject'),
                        'class_group' => (string) ($session->classGroup?->name ?? 'N/A'),
                        'session_type' => strtoupper((string) ($session->session_type ?? 'CLASS')),
                    ];
                })
                ->values();

            $teacherWeeklyScheduleSummary = [
                'total_sessions' => (int) $teacherWeeklyScheduleRows->count(),
                'days_covered' => (int) $teacherWeeklyScheduleRows->pluck('day_order')->unique()->count(),
                'subjects' => (int) $teacherWeeklyScheduleRows->pluck('subject_code')->unique()->count(),
                'class_groups' => (int) $teacherWeeklyScheduleRows->pluck('class_group')->filter(fn ($name): bool => trim((string) $name) !== '')->unique()->count(),
            ];
        }

        return view('tenant.dashboard', [
            'roleCodes' => $roleCodes,
            'needsProgramEnrollment' => $needsProgramEnrollment,
            'openEnrollmentSemesterName' => $openEnrollmentSemesterName,
            'studentProfile' => $studentProfile,
            'teachableSubjects' => $teachableSubjects,
            'teacherWeeklyScheduleRows' => $teacherWeeklyScheduleRows,
            'teacherWeeklyScheduleSummary' => $teacherWeeklyScheduleSummary,
        ]);
    }

    public function lmsPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');

        $teachableSubjects = $this->fetchTeachableSubjects($schoolId, $userId);

        $weeklySessions = ClassSession::query()
            ->with(['subject', 'classGroup'])
            ->where('school_id', $schoolId)
            ->where('teacher_user_id', $userId)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view('tenant.lms', compact('teachableSubjects', 'weeklySessions'));
    }

    public function studentLessonsPage(Request $request, ClassGroup $classGroup, Subject $subject): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');

        if ((int) $classGroup->school_id !== $schoolId || (int) $subject->school_id !== $schoolId) {
            abort(404);
        }

        $hasEnrollment = Enrollment::query()
            ->where('school_id', $schoolId)
            ->where('student_user_id', $userId)
            ->whereHas('offering', function ($query) use ($subject): void {
                $query->where('subject_id', (int) $subject->id);
            })
            ->whereHas('section', function ($query) use ($classGroup): void {
                $query->where('identifier', 'CG-' . (int) $classGroup->id);
            })
            ->where('status', 'enrolled')
            ->exists();

        if (! $hasEnrollment) {
            abort(403);
        }

        $modules = LmsModule::query()
            ->where('school_id', $schoolId)
            ->where('class_group_id', (int) $classGroup->id)
            ->where('subject_id', (int) $subject->id)
            ->whereNull('lesson_id')
            ->orderByDesc('id')
            ->get();

        $lessons = LmsLesson::query()
            ->with(['modules' => fn ($q) => $q->orderBy('id')])
            ->with(['quizzes' => fn ($q) => $q->where('is_published', true)->withCount('questions')])
            ->where('school_id', $schoolId)
            ->where('class_group_id', (int) $classGroup->id)
            ->where('subject_id', (int) $subject->id)
            ->orderByRaw('COALESCE(position, 999999) ASC')
            ->orderBy('id', 'asc')
            ->get();

        // Also fetch ungrouped published quizzes
        $ungroupedQuizzes = LmsQuiz::query()
            ->withCount('questions')
            ->where('school_id', $schoolId)
            ->where('class_group_id', (int) $classGroup->id)
            ->where('subject_id', (int) $subject->id)
            ->whereNull('lesson_id')
            ->where('is_published', true)
            ->get();

        return view('tenant.student-lessons', compact('classGroup', 'subject', 'modules', 'lessons', 'ungroupedQuizzes'));
    }

    public function lmsClassDashboard(Request $request, ClassGroup $classGroup, Subject $subject): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');
        $roleCodes = (array) ($request->attributes->get('role_codes') ?? []);
        $isSchoolAdmin = in_array('school_admin', $roleCodes, true);

        if ((int) $classGroup->school_id !== $schoolId || (int) $subject->school_id !== $schoolId) {
            abort(404);
        }

        if (! $isSchoolAdmin) {
            $hasTeachingAssignment = ClassSession::query()
                ->where('school_id', $schoolId)
                ->where('teacher_user_id', $userId)
                ->where('class_group_id', (int) $classGroup->id)
                ->where('subject_id', (int) $subject->id)
                ->exists();

            if (! $hasTeachingAssignment) {
                abort(403);
            }
        }

        $lessons = LmsLesson::query()
            ->with(['modules' => function ($q): void {
                $q->orderByDesc('id');
            }, 'modules.uploader', 'quizzes' => function ($q): void {
                $q->withCount('questions');
            }])
            ->where('school_id', $schoolId)
            ->where('class_group_id', (int) $classGroup->id)
            ->where('subject_id', (int) $subject->id)
            ->orderByRaw('COALESCE(position, 999999) ASC')
            ->orderBy('id')
            ->get();

        $ungroupedModules = LmsModule::query()
            ->with('uploader')
            ->where('school_id', $schoolId)
            ->where('class_group_id', (int) $classGroup->id)
            ->where('subject_id', (int) $subject->id)
            ->whereNull('lesson_id')
            ->orderByDesc('id')
            ->get();

        $ungroupedQuizzes = LmsQuiz::query()
            ->withCount('questions')
            ->where('school_id', $schoolId)
            ->where('class_group_id', (int) $classGroup->id)
            ->where('subject_id', (int) $subject->id)
            ->whereNull('lesson_id')
            ->get();

        // Get enrolled students for this class group and subject
        // Use the same logic as gradebook - fetch via Enrollments
        $enrolledStudents = \App\Models\Enrollment::query()
            ->with(['student.studentProfile'])
            ->where('school_id', $schoolId)
            ->whereHas('offering', function ($q) use ($subject) {
                $q->where('subject_id', (int) $subject->id);
            })
            ->whereHas('section', function ($q) use ($classGroup) {
                $q->where('identifier', 'CG-' . $classGroup->id);
            })
            ->get()
            ->map(function ($enrollment) {
                return $enrollment->student;
            })
            ->filter()
            ->unique('id')
            ->sortBy('full_name')
            ->values();

        return view('tenant.lms-class-dashboard', [
            'classGroup' => $classGroup,
            'subject' => $subject,
            'lessons' => $lessons,
            'modules' => $ungroupedModules,
            'ungroupedQuizzes' => $ungroupedQuizzes,
            'enrolledStudents' => $enrolledStudents,
        ]);
    }

    public function lmsStoreModule(Request $request, ClassGroup $classGroup, Subject $subject): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');
        $roleCodes = (array) ($request->attributes->get('role_codes') ?? []);
        $isSchoolAdmin = in_array('school_admin', $roleCodes, true);

        if ((int) $classGroup->school_id !== $schoolId || (int) $subject->school_id !== $schoolId) {
            abort(404);
        }

        if (! $isSchoolAdmin) {
            $hasTeachingAssignment = ClassSession::query()
                ->where('school_id', $schoolId)
                ->where('teacher_user_id', $userId)
                ->where('class_group_id', (int) $classGroup->id)
                ->where('subject_id', (int) $subject->id)
                ->exists();

            if (! $hasTeachingAssignment) {
                abort(403);
            }
        }

        $payload = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', 'string', 'in:file,link,doc'],
            'file' => ['nullable', 'file', 'max:102400'], // Increased to 100MB for videos
            'content' => ['nullable', 'string'],
            'lesson_id' => ['nullable', 'integer'],
        ]);

        $lessonId = null;
        if (!empty($payload['lesson_id']) && (int) $payload['lesson_id'] > 0) {
            $lesson = LmsLesson::query()
                ->where('id', (int) $payload['lesson_id'])
                ->where('school_id', $schoolId)
                ->where('class_group_id', (int) $classGroup->id)
                ->where('subject_id', (int) $subject->id)
                ->first();
            if (! $lesson) {
                return back()->withErrors(['lesson_id' => 'Invalid lesson selected.']);
            }
            $lessonId = (int) $lesson->id;
        }

        $type = $payload['type'];
        $storedPath = null;
        $originalName = null;
        $mimeType = null;
        $fileSize = 0;
        $content = $payload['content'] ?? null;

        if ($type === 'file') {
            $file = $request->file('file');
            if (! $file) {
                return back()->withErrors(['file' => 'File is required when type is file.']);
            }
            $dir = "lms/{$schoolId}/class-{$classGroup->id}/subject-{$subject->id}";
            $storedPath = Storage::disk('public')->putFile($dir, $file);
            $originalName = method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : null;
            $mimeType = method_exists($file, 'getClientMimeType') ? $file->getClientMimeType() : null;
            $fileSize = method_exists($file, 'getSize') ? (int) ($file->getSize() ?? 0) : 0;
        }

        LmsModule::query()->create([
            'school_id' => $schoolId,
            'class_group_id' => (int) $classGroup->id,
            'subject_id' => (int) $subject->id,
            'lesson_id' => $lessonId,
            'uploaded_by_user_id' => $userId,
            'type' => $type,
            'title' => (string) $payload['title'],
            'description' => $payload['description'] ?? null,
            'content' => $content,
            'file_path' => $storedPath,
            'original_name' => $originalName,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ]);

        return redirect("/tenant/lms/classes/{$classGroup->id}/{$subject->id}")
            ->with('status', 'Resource added.');
    }

    public function lmsStoreLesson(Request $request, ClassGroup $classGroup, Subject $subject): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');
        $roleCodes = (array) ($request->attributes->get('role_codes') ?? []);
        $isSchoolAdmin = in_array('school_admin', $roleCodes, true);

        if ((int) $classGroup->school_id !== $schoolId || (int) $subject->school_id !== $schoolId) {
            abort(404);
        }

        if (! $isSchoolAdmin) {
            $hasTeachingAssignment = ClassSession::query()
                ->where('school_id', $schoolId)
                ->where('teacher_user_id', $userId)
                ->where('class_group_id', (int) $classGroup->id)
                ->where('subject_id', (int) $subject->id)
                ->exists();

            if (! $hasTeachingAssignment) {
                abort(403);
            }
        }

        $payload = $request->validate([
            'title' => ['required', 'string', 'max:140'],
        ]);

        $maxPosition = (int) LmsLesson::query()
            ->where('school_id', $schoolId)
            ->where('class_group_id', (int) $classGroup->id)
            ->where('subject_id', (int) $subject->id)
            ->max('position');

        LmsLesson::query()->create([
            'school_id' => $schoolId,
            'class_group_id' => (int) $classGroup->id,
            'subject_id' => (int) $subject->id,
            'title' => $payload['title'],
            'position' => $maxPosition ? $maxPosition + 1 : 1,
            'created_by_user_id' => $userId,
        ]);

        return redirect("/tenant/lms/classes/{$classGroup->id}/{$subject->id}")
            ->with('status', 'Lesson created.');
    }

    public function lmsDownloadModule(Request $request, LmsModule $module)
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        if ((int) $module->school_id !== $schoolId) {
            abort(404);
        }

        $path = $module->file_path;
        if (! is_string($path) || $path === '' || ! Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $downloadName = $module->original_name ?: basename($path);
        return Storage::disk('public')->download($path, $downloadName);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Subject>
     */
    private function fetchTeachableSubjects(int $schoolId, int $teacherUserId)
    {
        $teachableSubjectIds = DB::table('teacher_subjects')
            ->where('school_id', $schoolId)
            ->where('teacher_user_id', $teacherUserId)
            ->distinct()
            ->pluck('subject_id')
            ->map(fn ($id): int => (int) $id)
            ->values();

        if ($teachableSubjectIds->isEmpty()) {
            return collect();
        }

        return Subject::query()
            ->where('school_id', $schoolId)
            ->whereIn('id', $teachableSubjectIds->all())
            ->orderBy('code')
            ->get();
    }

    public function classPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');

        $enrollments = Enrollment::query()
            ->with(['offering.subject', 'offering.semester', 'section'])
            ->where('school_id', $schoolId)
            ->where('student_user_id', $userId)
            ->orderByDesc('id')
            ->get();

        $classGroupNameById = $this->classGroupNamesForSectionIdentifiers(
            $schoolId,
            $enrollments->pluck('section.identifier')->all()
        );

        $enrollments->each(function (Enrollment $enrollment) use ($classGroupNameById): void {
            $enrollment->setAttribute(
                'class_group_label',
                $this->resolveClassGroupLabelFromSectionIdentifier(
                    $enrollment->section?->identifier,
                    $classGroupNameById
                )
            );
        });

        $activeStatuses = ['enrolled'];

        $classSubjectRows = $enrollments
            ->filter(fn (Enrollment $enrollment): bool => in_array((string) $enrollment->status, $activeStatuses, true))
            ->map(function (Enrollment $enrollment): ?array {
                $subject = $enrollment->offering?->subject;
                $identifier = (string) ($enrollment->section?->identifier ?? '');
                $classGroupId = $this->extractClassGroupIdFromSectionIdentifier($identifier);

                if (! $subject || $classGroupId <= 0) {
                    return null;
                }

                return [
                    'class_group_id' => $classGroupId,
                    'subject_id' => (int) ($subject->id ?? 0),
                    'class_label' => (string) ($enrollment->class_group_label ?? 'N/A'),
                    'subject_code' => (string) ($subject->code ?? ''),
                    'subject_title' => (string) ($subject->title ?? ''),
                    'term_code' => (string) ($enrollment->offering?->semester->term_code ?? ''),
                ];
            })
            ->filter(fn (?array $row): bool => $row !== null && $row['subject_code'] !== '' && ($row['subject_id'] ?? 0) > 0)
            ->unique(fn (array $row): string => ($row['class_group_id'] ?? 0) . '|' . ($row['subject_id'] ?? 0))
            ->sortBy(fn (array $row): string => strtoupper((string) $row['class_label']) . '|' . strtoupper((string) $row['subject_code']))
            ->values();

        return view('tenant.class', compact('classSubjectRows'));
    }

    public function enrollmentsPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');
        $roleCodes = (array) ($request->attributes->get('role_codes') ?? []);
        $canRegistrarViewAllRecords = in_array('registrar_staff', $roleCodes, true)
            || in_array('registrar', $roleCodes, true)
            || in_array('school_admin', $roleCodes, true);

        $semesters = Semester::query()->where('school_id', $schoolId)->orderByDesc('id')->get();
        $sections = Section::query()
            ->with(['offering.subject', 'offering.semester'])
            ->where('school_id', $schoolId)
            ->where('status', 'open')
            ->orderByDesc('id')
            ->get();

        $myEnrollments = Enrollment::query()
            ->with(['offering.subject', 'section'])
            ->where('school_id', $schoolId)
            ->where('student_user_id', $userId)
            ->orderByDesc('id')
            ->get();

        $classGroupNameById = $this->classGroupNamesForSectionIdentifiers(
            $schoolId,
            $myEnrollments->pluck('section.identifier')->all()
        );
        $myEnrollments->each(function (Enrollment $enrollment) use ($classGroupNameById): void {
            $enrollment->setAttribute(
                'class_group_label',
                $this->resolveClassGroupLabelFromSectionIdentifier(
                    $enrollment->section?->identifier,
                    $classGroupNameById
                )
            );
        });

        $activeEnrollmentStatuses = ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'];
        $activeStudentEnrollments = $myEnrollments
            ->filter(fn (Enrollment $enrollment): bool => in_array((string) $enrollment->status, $activeEnrollmentStatuses, true))
            ->values();

        $weeklyClassGroupIds = $activeStudentEnrollments
            ->pluck('section.identifier')
            ->map(fn ($identifier): int => $this->extractClassGroupIdFromSectionIdentifier((string) $identifier))
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $weeklySubjectIds = $activeStudentEnrollments
            ->pluck('offering.subject_id')
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $weeklyEnrollmentPairs = $activeStudentEnrollments
            ->map(function (Enrollment $enrollment): string {
                $classGroupId = $this->extractClassGroupIdFromSectionIdentifier((string) ($enrollment->section?->identifier ?? ''));
                $subjectId = (int) ($enrollment->offering?->subject_id ?? 0);
                if ($classGroupId <= 0 || $subjectId <= 0) {
                    return '';
                }
                return $classGroupId . '|' . $subjectId;
            })
            ->filter(fn (string $pair): bool => $pair !== '')
            ->unique()
            ->values()
            ->all();

        $studentWeeklyScheduleRows = collect();
        if ($weeklyClassGroupIds !== [] && $weeklySubjectIds !== []) {
            $studentWeeklyScheduleRows = ClassSession::query()
                ->with(['subject:id,code,title', 'teacher:id,full_name', 'classGroup:id,name'])
                ->where('school_id', $schoolId)
                ->whereIn('class_group_id', $weeklyClassGroupIds)
                ->whereIn('subject_id', $weeklySubjectIds)
                ->whereIn('status', ['draft', 'locked'])
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->filter(fn (ClassSession $session): bool => in_array(
                    (int) $session->class_group_id . '|' . (int) $session->subject_id,
                    $weeklyEnrollmentPairs,
                    true
                ))
                ->map(function (ClassSession $session): array {
                    $dayLabel = match ((int) $session->day_of_week) {
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        7 => 'Sunday',
                        default => 'Day ' . (int) $session->day_of_week,
                    };

                    $startTime = substr((string) $session->start_time, 0, 5);
                    $endTime = substr((string) $session->end_time, 0, 5);
                    $startMin = ((int) substr((string) $session->start_time, 0, 2) * 60) + (int) substr((string) $session->start_time, 3, 2);
                    $endMin = ((int) substr((string) $session->end_time, 0, 2) * 60) + (int) substr((string) $session->end_time, 3, 2);

                    return [
                        'day_order' => (int) $session->day_of_week,
                        'day_label' => $dayLabel,
                        'time_label' => $startTime . ' - ' . $endTime,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'start_min' => $startMin,
                        'end_min' => $endMin,
                        'subject_code' => (string) ($session->subject?->code ?? 'N/A'),
                        'subject_title' => (string) ($session->subject?->title ?? 'Untitled Subject'),
                        'class_group' => (string) ($session->classGroup?->name ?? 'N/A'),
                        'session_type' => strtoupper((string) ($session->session_type ?? 'CLASS')),
                        'teacher_name' => (string) ($session->teacher?->full_name ?? 'TBA'),
                    ];
                })
                ->values();
        }

        $studentWeeklyScheduleSummary = [
            'total_sessions' => (int) $studentWeeklyScheduleRows->count(),
            'days_covered' => (int) $studentWeeklyScheduleRows->pluck('day_order')->unique()->count(),
            'subjects' => (int) $studentWeeklyScheduleRows->pluck('subject_code')->unique()->count(),
            'class_groups' => (int) $studentWeeklyScheduleRows->pluck('class_group')->filter(fn ($name): bool => trim((string) $name) !== '')->unique()->count(),
        ];

        $pendingForRegistrar = Enrollment::query()
            ->with(['offering.subject', 'section'])
            ->where('school_id', $schoolId)
            ->whereIn('status', ['billing_pending', 'payment_verified'])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('billing as b')
                    ->whereColumn('b.school_id', 'enrollments.school_id')
                    ->whereColumn('b.student_user_id', 'enrollments.student_user_id')
                    ->whereColumn('b.semester_id', 'enrollments.semester_id')
                    ->where(function ($inner): void {
                        $inner->where('b.payment_status', '!=', 'verified')
                             ->orWhere('b.clearance_status', '!=', 'cleared');
                    });
            })
            ->orderByDesc('id')
            ->get();

        $semesterForStudentEnrollment = $this->resolveEnrollmentSemester($schoolId);
        $studentHasProgramSelection = false;
        $studentHasRegistrarVerifiedSubjects = false;
        $studentHasPaidTuition = false;
        $showProgramSelectionCard = false;
        $programsForStudent = collect();
        $yearLevelsByProgram = [];
        $selectedProgramId = 0;
        $selectedYearLevel = 0;
        $availableYearLevels = [];
        $subjectScheduleRows = [];
        $selectedSubjectChoice = [];
        $shouldLoadSchedules = false;

        $financeVerifiedForRegistrar = collect();
        $pendingClearancesForRegistrar = collect();
        $classGroupCapacityForRegistrar = collect();
        $inactiveStudentAccountsForRegistrar = collect();
        $allStudentsForRegistrar = collect();
        $schoolStaffForRegistrar = collect();

        if ($canRegistrarViewAllRecords) {
            // Pending clearances that need registrar approval
            $pendingClearancesForRegistrar = Billing::query()
                ->with(['student', 'rule', 'clearedByFinance'])
                ->where('school_id', $schoolId)
                ->where('clearance_status', 'pending_approval')
                ->where('payment_status', 'verified')
                ->orderByDesc('cleared_at')
                ->limit(200)
                ->get();

            $financeVerifiedForRegistrar = Enrollment::query()
                ->with(['offering.subject', 'section', 'student'])
                ->where('school_id', $schoolId)
                ->where('status', 'payment_verified')
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('billing as b')
                        ->whereColumn('b.school_id', 'enrollments.school_id')
                        ->whereColumn('b.student_user_id', 'enrollments.student_user_id')
                        ->whereColumn('b.semester_id', 'enrollments.semester_id')
                        ->where(function ($inner): void {
                            $inner->where('b.payment_status', '!=', 'verified')
                                 ->orWhere('b.clearance_status', '!=', 'cleared');
                        });
                })
                ->orderByDesc('id')
                ->limit(300)
                ->get();

            $classGroupCapacityForRegistrar = ClassGroup::query()
                ->with(['program', 'semester'])
                ->withCount(['studentAssignments as students_inside_count'])
                ->where('school_id', $schoolId)
                ->orderByDesc('id')
                ->limit(200)
                ->get();

            $studentUserIds = UserRole::query()
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.school_id', $schoolId)
                ->where('user_roles.is_active', true)
                ->where('roles.code', 'student')
                ->pluck('user_roles.user_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values();

            $verifiedBillingStudentIds = Billing::query()
                ->where('school_id', $schoolId)
                ->where('payment_status', 'verified')
                ->pluck('student_user_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values();

            $inactiveStudentAccountsForRegistrar = User::query()
                ->whereIn('id', $studentUserIds->all())
                ->whereIn('id', $verifiedBillingStudentIds->all())
                ->where('status', '!=', 'active')
                ->orderBy('full_name')
                ->limit(500)
                ->get();

            $allStudentsForRegistrar = User::query()
                ->whereIn('id', $studentUserIds->all())
                ->orderBy('full_name')
                ->limit(1000)
                ->get();

            $staffRows = UserRole::query()
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->join('users', 'users.id', '=', 'user_roles.user_id')
                ->where('user_roles.school_id', $schoolId)
                ->where('user_roles.is_active', true)
                ->where('roles.code', '!=', 'student')
                ->select([
                    'users.id',
                    'users.full_name',
                    'users.email',
                    'users.phone',
                    'users.status',
                    'roles.code as role_code',
                ])
                ->orderBy('users.full_name')
                ->get();

            $schoolStaffForRegistrar = $staffRows
                ->groupBy('id')
                ->map(function ($rows): array {
                    $first = $rows->first();
                    return [
                        'id' => (int) ($first->id ?? 0),
                        'full_name' => (string) ($first->full_name ?? ''),
                        'email' => (string) ($first->email ?? ''),
                        'phone' => (string) ($first->phone ?? ''),
                        'status' => (string) ($first->status ?? ''),
                        'roles' => collect($rows)->pluck('role_code')->filter()->unique()->values()->all(),
                    ];
                })
                ->values();
        } else {
            $studentHasActiveSubjectSelectionsQuery = Enrollment::query()
                ->where('school_id', $schoolId)
                ->where('student_user_id', $userId)
                ->whereIn('status', $activeEnrollmentStatuses);

            if ($semesterForStudentEnrollment !== null) {
                $studentHasActiveSubjectSelectionsQuery->where('semester_id', (int) $semesterForStudentEnrollment->id);
            }

            $studentHasActiveSubjectSelections = $studentHasActiveSubjectSelectionsQuery->exists();

            $studentHasProgramSelection = StudentProfile::query()
                ->where('school_id', $schoolId)
                ->where('user_id', $userId)
                ->whereNotNull('program_id')
                ->exists();

            // Some legacy rows may have enrollments/billing before student_profiles.program_id is set.
            if (! $studentHasProgramSelection && $studentHasActiveSubjectSelections) {
                $studentHasProgramSelection = true;
            }

            $studentHasRegistrarVerifiedSubjects = Enrollment::query()
                ->where('school_id', $schoolId)
                ->where('student_user_id', $userId)
                ->whereIn('status', ['registrar_confirmed', 'enrolled'])
                ->exists();

            $studentHasPaidTuition = Billing::query()
                ->where('school_id', $schoolId)
                ->where('student_user_id', $userId)
                ->where('charge_type', 'tuition')
                ->where(function ($query): void {
                    $query->whereIn('payment_status', ['verified', 'paid_unverified', 'waived'])
                        ->orWhere(function ($paidInFull): void {
                            $paidInFull->whereColumn('amount_paid', '>=', 'amount_due')
                                ->where('amount_due', '>', 0);
                        });
                })
                ->exists();

            // Hide selection UI when student already has active subjects, or already selected a program and paid tuition.
            $showProgramSelectionCard = ! $studentHasActiveSubjectSelections
                && ! ($studentHasProgramSelection && $studentHasPaidTuition);

            if ($showProgramSelectionCard && $semesterForStudentEnrollment !== null) {
                $programsForStudent = Program::query()
                    ->where('school_id', $schoolId)
                    ->where('status', 'active')
                    ->orderBy('name')
                    ->get();

                $openClassGroups = ClassGroup::query()
                    ->where('school_id', $schoolId)
                    ->where('semester_id', (int) $semesterForStudentEnrollment->id)
                    ->where('status', '!=', 'archived')
                    ->orderBy('program_id')
                    ->orderBy('year_level')
                    ->orderBy('name')
                    ->get();

                $yearLevelsByProgram = $openClassGroups
                    ->groupBy('program_id')
                    ->map(fn ($rows) => $rows->pluck('year_level')->map(fn ($v): int => (int) $v)->unique()->sort()->values()->all())
                    ->all();

                if ($yearLevelsByProgram === []) {
                    $yearLevelsByProgram = ClassGroup::query()
                        ->where('school_id', $schoolId)
                        ->where('status', '!=', 'archived')
                        ->get(['program_id', 'year_level'])
                        ->groupBy('program_id')
                        ->map(fn ($rows) => $rows->pluck('year_level')->map(fn ($v): int => (int) $v)->unique()->sort()->values()->all())
                        ->all();
                }

                $curriculumYearLevelsByProgram = ProgramCurriculumItem::query()
                    ->where('school_id', $schoolId)
                    ->where('status', 'active')
                    ->get(['program_id', 'year_level'])
                    ->groupBy('program_id')
                    ->map(fn ($rows) => $rows->pluck('year_level')->map(fn ($v): int => (int) $v)->unique()->sort()->values()->all())
                    ->all();

                foreach ($curriculumYearLevelsByProgram as $programId => $levels) {
                    $existing = collect((array) ($yearLevelsByProgram[$programId] ?? []))
                        ->map(fn ($v): int => (int) $v)
                        ->all();
                    $merged = collect(array_merge($existing, (array) $levels))
                        ->map(fn ($v): int => (int) $v)
                        ->filter(fn (int $v): bool => $v > 0)
                        ->unique()
                        ->sort()
                        ->values()
                        ->all();
                    if ($merged !== []) {
                        $yearLevelsByProgram[$programId] = $merged;
                    }
                }

                $selectedProgramId = max((int) $request->query('program_id', 0), 0);
                if (! $programsForStudent->contains(fn (Program $p): bool => (int) $p->id === $selectedProgramId)) {
                    $selectedProgramId = 0;
                }

                $availableYearLevels = $selectedProgramId > 0
                    ? collect($yearLevelsByProgram[$selectedProgramId] ?? [])->map(fn ($v): int => (int) $v)->values()->all()
                    : [];

                if ($availableYearLevels === []) {
                    $availableYearLevels = [1, 2, 3, 4];
                }

                $selectedYearLevel = max((int) $request->query('year_level', 0), 0);
                if (! in_array($selectedYearLevel, $availableYearLevels, true)) {
                    $selectedYearLevel = $availableYearLevels[0] ?? 0;
                }

                $shouldLoadSchedules = ((int) $request->query('load_schedules', 0) === 1);
                if ($shouldLoadSchedules && $selectedProgramId > 0 && $selectedYearLevel > 0) {
                    $subjectScheduleRows = $this->buildSubjectScheduleRows(
                        schoolId: $schoolId,
                        semesterId: (int) $semesterForStudentEnrollment->id,
                        programId: $selectedProgramId,
                        yearLevel: $selectedYearLevel,
                    );
                }

                $selectedSubjectChoice = (array) old('subject_choice', []);
            }
        }

        return view('tenant.enrollments', compact(
            'pendingClearancesForRegistrar',
            'semesters',
            'sections',
            'myEnrollments',
            'pendingForRegistrar',
            'canRegistrarViewAllRecords',
            'financeVerifiedForRegistrar',
            'classGroupCapacityForRegistrar',
            'inactiveStudentAccountsForRegistrar',
            'allStudentsForRegistrar',
            'schoolStaffForRegistrar',
            'semesterForStudentEnrollment',
            'studentHasProgramSelection',
            'studentHasRegistrarVerifiedSubjects',
            'studentHasPaidTuition',
            'showProgramSelectionCard',
            'programsForStudent',
            'yearLevelsByProgram',
            'selectedProgramId',
            'selectedYearLevel',
            'availableYearLevels',
            'subjectScheduleRows',
            'selectedSubjectChoice',
            'shouldLoadSchedules',
            'studentWeeklyScheduleRows',
            'studentWeeklyScheduleSummary'
        ));
    }

    public function enrollSelect(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'subject_offering_id' => ['required', 'integer', 'exists:subject_offerings,id'],
            'section_id' => ['required', 'integer', 'exists:sections,id'],
        ]);

        try {
            $this->enrollmentWorkflowService->selectOffering(
                schoolId: (int) $request->attributes->get('active_school_id'),
                studentUserId: (int) $request->attributes->get('actor_user_id'),
                semesterId: (int) $payload['semester_id'],
                subjectOfferingId: (int) $payload['subject_offering_id'],
                sectionId: (int) $payload['section_id'],
            );

            return back()->with('status', 'Enrollment request submitted.');
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['enrollment' => $exception->getMessage()])->withInput();
        }
    }

    public function submitStudentPlan(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $studentUserId = (int) $request->attributes->get('actor_user_id');

        $semester = $this->resolveEnrollmentSemester($schoolId);
        if ($semester === null) {
            return back()->withErrors([
                'student_plan' => 'No semester is currently available for enrollment.',
            ])->withInput();
        }

        $payload = $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'year_level' => ['required', 'integer', 'min:1', 'max:8'],
            'subject_choice' => ['required', 'array', 'min:1'],
            'subject_choice.*' => ['required', 'integer'],
        ]);

        $program = Program::query()
            ->where('school_id', $schoolId)
            ->where('id', (int) $payload['program_id'])
            ->where('status', 'active')
            ->first();
        if ($program === null) {
            return back()->withErrors(['program_id' => 'Selected program is invalid for this school.'])->withInput();
        }

        $yearLevel = (int) $payload['year_level'];
        $subjectScheduleRows = $this->buildSubjectScheduleRows(
            schoolId: $schoolId,
            semesterId: (int) $semester->id,
            programId: (int) $program->id,
            yearLevel: $yearLevel,
        );
        if ($subjectScheduleRows === []) {
            return back()->withErrors([
                'subject_choice' => 'No generated class schedules are available for the selected program/year level.',
            ])->withInput();
        }

        $choiceInput = collect((array) ($payload['subject_choice'] ?? []))
            ->mapWithKeys(fn ($classGroupId, $subjectId): array => [(int) $subjectId => (int) $classGroupId])
            ->filter(fn (int $classGroupId, int $subjectId): bool => $classGroupId > 0 && $subjectId > 0)
            ->all();

        $availableBySubject = [];
        foreach ($subjectScheduleRows as $row) {
            $subjectId = (int) ($row['subject_id'] ?? 0);
            $availableBySubject[$subjectId] = collect((array) ($row['options'] ?? []))
                ->keyBy(fn (array $option): int => (int) ($option['class_group_id'] ?? 0))
                ->all();
        }

        $selectedOptions = [];
        foreach ($availableBySubject as $subjectId => $optionsByGroupId) {
            $chosenClassGroupId = (int) ($choiceInput[$subjectId] ?? 0);
            if ($chosenClassGroupId <= 0 || ! isset($optionsByGroupId[$chosenClassGroupId])) {
                return back()->withErrors([
                    'subject_choice' => 'Please select one schedule option for each subject.',
                ])->withInput();
            }

            $selectedOption = (array) $optionsByGroupId[$chosenClassGroupId];
            if ((int) ($selectedOption['remaining'] ?? 0) <= 0) {
                return back()->withErrors([
                    'subject_choice' => (($selectedOption['subject_code'] ?? 'Subject') . ' in class group ' . ($selectedOption['class_group_name'] ?? '?') . ' is already full.'),
                ])->withInput();
            }

            $selectedOptions[] = $selectedOption;
        }

        $conflictMessage = $this->detectScheduleConflictMessage($selectedOptions);
        if ($conflictMessage !== null) {
            return back()->withErrors(['subject_choice' => $conflictMessage])->withInput();
        }

        try {
            DB::transaction(function () use ($schoolId, $studentUserId, $semester, $program, $yearLevel, $selectedOptions): void {
                $profile = \App\Models\StudentProfile::query()
                    ->where('school_id', $schoolId)
                    ->where('user_id', $studentUserId)
                    ->first();

                if ($profile === null) {
                    \App\Models\StudentProfile::query()->create([
                        'school_id' => $schoolId,
                        'user_id' => $studentUserId,
                        'student_no' => $this->generateStudentNo($schoolId),
                        'program_id' => (int) $program->id,
                        'department_id' => (int) $program->department_id,
                        'year_level' => $yearLevel,
                    ]);
                } else {
                    $profile->program_id = (int) $program->id;
                    $profile->department_id = (int) $program->department_id;
                    $profile->year_level = $yearLevel;
                    $profile->save();
                }

                $systemActorUserId = $this->resolveSystemActorUserId($schoolId, $studentUserId);
                $activeStatuses = ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'];
                $selectedOfferingIds = [];
                $selectedSubjectIds = [];

                foreach ($selectedOptions as $option) {
                    $subjectId = (int) ($option['subject_id'] ?? 0);
                    $classGroupId = (int) ($option['class_group_id'] ?? 0);
                    if ($subjectId <= 0 || $classGroupId <= 0) {
                        throw new \RuntimeException('Invalid schedule option payload.');
                    }

                    $classGroup = ClassGroup::query()
                        ->where('school_id', $schoolId)
                        ->where('semester_id', (int) $semester->id)
                        ->where('program_id', (int) $program->id)
                        ->where('year_level', $yearLevel)
                        ->where('id', $classGroupId)
                        ->where('status', '!=', 'archived')
                        ->lockForUpdate()
                        ->first();
                    if ($classGroup === null) {
                        throw new \RuntimeException('One or more selected class schedules is no longer available.');
                    }

                    $offering = SubjectOffering::query()
                        ->where('school_id', $schoolId)
                        ->where('semester_id', (int) $semester->id)
                        ->where('subject_id', $subjectId)
                        ->orderByRaw("CASE WHEN status = 'open' THEN 0 WHEN status = 'draft' THEN 1 ELSE 9 END")
                        ->lockForUpdate()
                        ->first();

                    if ($offering === null) {
                        $offering = SubjectOffering::query()->create([
                            'school_id' => $schoolId,
                            'subject_id' => $subjectId,
                            'semester_id' => (int) $semester->id,
                            'assigned_teacher_user_id' => null,
                            'schedule_summary' => 'Auto-created from student schedule preference.',
                            'status' => 'open',
                            'created_by_user_id' => $systemActorUserId,
                        ]);
                    }

                    $sectionIdentifier = $this->buildSectionIdentifierForClassGroup($classGroupId);
                    $section = Section::query()
                        ->where('school_id', $schoolId)
                        ->where('subject_offering_id', (int) $offering->id)
                        ->where('identifier', $sectionIdentifier)
                        ->lockForUpdate()
                        ->first();

                    if ($section === null) {
                        $section = Section::query()->create([
                            'school_id' => $schoolId,
                            'subject_offering_id' => (int) $offering->id,
                            'identifier' => $sectionIdentifier,
                            'max_capacity' => max((int) ($classGroup->student_capacity ?? 0), 1),
                            'status' => 'open',
                        ]);
                    } elseif ((int) $section->max_capacity !== (int) $classGroup->student_capacity) {
                        $section->max_capacity = max((int) ($classGroup->student_capacity ?? 0), 1);
                        $section->save();
                    }

                    $currentEnrolled = Enrollment::query()
                        ->where('school_id', $schoolId)
                        ->where('semester_id', (int) $semester->id)
                        ->where('subject_offering_id', (int) $offering->id)
                        ->where('section_id', (int) $section->id)
                        ->whereIn('status', $activeStatuses)
                        ->count();

                    if ($currentEnrolled >= (int) $section->max_capacity) {
                        throw new \RuntimeException('Schedule slot is full for subject ' . ($option['subject_code'] ?? ('#' . $subjectId)) . '. Please choose another time slot.');
                    }

                    Enrollment::query()->updateOrCreate(
                        [
                            'school_id' => $schoolId,
                            'semester_id' => (int) $semester->id,
                            'student_user_id' => $studentUserId,
                            'subject_offering_id' => (int) $offering->id,
                        ],
                        [
                            'section_id' => (int) $section->id,
                            'status' => 'billing_pending',
                            'validation_remarks' => null,
                        ]
                    );

                    $selectedOfferingIds[] = (int) $offering->id;
                    $selectedSubjectIds[] = $subjectId;
                }

                $selectedOfferingIds = array_values(array_unique($selectedOfferingIds));
                $selectedSubjectIds = array_values(array_unique($selectedSubjectIds));

                Enrollment::query()
                    ->where('school_id', $schoolId)
                    ->where('semester_id', (int) $semester->id)
                    ->where('student_user_id', $studentUserId)
                    ->whereIn('status', ['selected', 'validated', 'billing_pending'])
                    ->whereNotIn('subject_offering_id', $selectedOfferingIds)
                    ->delete();

                $tuitionFee = (float) Subject::query()
                    ->where('school_id', $schoolId)
                    ->whereIn('id', $selectedSubjectIds)
                    ->sum('price_per_subject');

                $financeUserId = (int) DB::table('user_roles')
                    ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                    ->where('user_roles.school_id', $schoolId)
                    ->where('user_roles.is_active', true)
                    ->where('roles.code', 'finance_staff')
                    ->orderBy('user_roles.id')
                    ->value('user_roles.user_id');
                if ($financeUserId <= 0) {
                    $financeUserId = $studentUserId;
                }

                Billing::query()->updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'semester_id' => (int) $semester->id,
                        'student_user_id' => $studentUserId,
                        'enrollment_id' => null,
                        'billing_rule_id' => null,
                        'charge_type' => 'tuition',
                        'description' => 'Tuition fee for selected subjects',
                    ],
                    [
                        'amount_due' => $tuitionFee,
                        'amount_paid' => 0,
                        'payment_status' => 'unpaid',
                        'clearance_status' => 'not_cleared',
                        'generated_by_finance_user_id' => $financeUserId,
                    ]
                );
            });
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['student_plan' => $exception->getMessage()])->withInput();
        }

        $tuitionBilling = Billing::query()
            ->where('school_id', $schoolId)
            ->where('semester_id', (int) $semester->id)
            ->where('student_user_id', $studentUserId)
            ->where('charge_type', 'tuition')
            ->orderByDesc('id')
            ->first();

        if ($tuitionBilling !== null) {
            $remaining = max((float) $tuitionBilling->amount_due - (float) $tuitionBilling->amount_paid, 0.0);
            if ($remaining > 0) {
                return redirect('/tenant/billing/' . (int) $tuitionBilling->id . '/review');
            }
        }

        return redirect('/tenant/payments')->with('status', 'Program and subject schedules saved. Proceed to payment.');
    }

    public function enrollConfirm(Request $request, Enrollment $enrollment): RedirectResponse
    {
        if ((int) $enrollment->school_id !== (int) $request->attributes->get('active_school_id')) {
            return back()->withErrors(['enrollment' => 'Enrollment does not belong to your school.']);
        }

        try {
            $this->enrollmentWorkflowService->confirmByRegistrar(
                enrollment: $enrollment,
                registrarUserId: (int) $request->attributes->get('actor_user_id'),
            );

            return back()->with('status', 'Enrollment confirmed.');
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['enrollment' => $exception->getMessage()]);
        }
    }

    public function registrarActivateStudentAccount(Request $request, User $user): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $isStudent = UserRole::query()
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.user_id', (int) $user->id)
            ->where('user_roles.is_active', true)
            ->where('roles.code', 'student')
            ->exists();
        if (! $isStudent) {
            return back()->withErrors(['student' => 'User is not an active student in this school.']);
        }

        $hasVerifiedBilling = Billing::query()
            ->where('school_id', $schoolId)
            ->where('student_user_id', (int) $user->id)
            ->where('payment_status', 'verified')
            ->exists();
        if (! $hasVerifiedBilling) {
            return back()->withErrors(['student' => 'Student has no finance-verified payment record yet.']);
        }

        $user->status = 'active';
        $user->save();

        return back()->with('status', 'Student account activated.');
    }

    public function billingPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');
        $roleCodes = (array) ($request->attributes->get('role_codes') ?? []);
        $isFinanceUser = in_array('finance_staff', $roleCodes, true)
            || in_array('school_admin', $roleCodes, true);

        $myBilling = Billing::query()
            ->with(['payments'])
            ->where('school_id', $schoolId)
            ->where('student_user_id', $userId)
            ->orderByDesc('id')
            ->get();

        $billings = $myBilling;
        $payments = collect();
        $pendingPayments = collect();
        $forClearance = collect();
        $billingRules = collect();
        $rules = collect();
        $billingSemesters = collect();
        $billingAcademicYears = collect();
        $billingPrograms = collect();
        $financeFeeSettings = collect();
        $generalEnrollmentFeeSetting = null;
        $semesterNameMap = collect();
        $academicYearNameMap = collect();
        $programNameMap = collect();
        $subjectsForPricing = collect();
        $billingCategories = collect();
        $billingGroups = collect();

        if ($isFinanceUser) {
            $payments = Payment::query()
                ->with(['billing'])
                ->whereHas('billing', function ($query) use ($schoolId): void {
                    $query->where('school_id', $schoolId);
                })
                ->whereIn('status', ['submitted'])
                ->orderByDesc('id')
                ->get();

            $pendingPayments = $payments;

            $billingRules = BillingRule::query()
                ->with(['category'])
                ->where('school_id', $schoolId)
                ->orderByDesc('id')
                ->get();
            $rules = $billingRules;

            $billingCategories = BillingCategory::query()
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->get();

            $billingGroups = BillingGroup::query()
                ->with(['program', 'items.billingRule.category'])
                ->where('school_id', $schoolId)
                ->orderByDesc('id')
                ->get();

            $billingSemesters = Semester::query()
                ->where('school_id', $schoolId)
                ->orderByDesc('id')
                ->get();
            $billingAcademicYears = AcademicYear::query()
                ->where('school_id', $schoolId)
                ->orderByDesc('name')
                ->get();
            $billingPrograms = Program::query()
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->get();
            $financeFeeSettings = FinanceFeeSetting::query()
                ->where('school_id', $schoolId)
                ->orderByDesc('id')
                ->get();
            $generalEnrollmentFeeSetting = FinanceFeeSetting::query()
                ->where('school_id', $schoolId)
                ->whereNull('semester_id')
                ->whereNull('academic_year_id')
                ->whereNull('program_id')
                ->where('status', 'active')
                ->orderByDesc('id')
                ->first();

            $semesterNameMap = Semester::query()
                ->where('school_id', $schoolId)
                ->pluck('name', 'id');
            $academicYearNameMap = AcademicYear::query()
                ->where('school_id', $schoolId)
                ->pluck('name', 'id');
            $programNameMap = Program::query()
                ->where('school_id', $schoolId)
                ->orderBy('name')
                ->get()
                ->mapWithKeys(fn (Program $program): array => [
                    (int) $program->id => trim(($program->code ?? '') . ' - ' . $program->name, ' -'),
                ]);
            $subjectsForPricing = Subject::query()
                ->where('school_id', $schoolId)
                ->orderBy('code')
                ->orderBy('title')
                ->limit(300)
                ->get();
        }

        return view('tenant.billing', compact(
            'myBilling', 'billings', 'payments', 'pendingPayments', 'billingRules', 'rules',
            'billingSemesters', 'billingAcademicYears', 'billingPrograms', 'financeFeeSettings',
            'semesterNameMap', 'academicYearNameMap', 'programNameMap', 'generalEnrollmentFeeSetting',
            'subjectsForPricing', 'isFinanceUser', 'billingCategories', 'billingGroups'
        ));
    }

    public function payBillingReview(Request $request, Billing $billing): View|\Illuminate\Http\RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $studentUserId = (int) $request->attributes->get('actor_user_id');
        if ((int) $billing->school_id !== $schoolId || (int) $billing->student_user_id !== $studentUserId) {
            return redirect('/tenant/payments')->withErrors(['billing' => 'Billing record not accessible.']);
        }

        $enrollments = Enrollment::query()
            ->with(['offering.subject'])
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->where('semester_id', (int) $billing->semester_id)
            ->whereIn('status', ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'])
            ->get();

        $breakdown = $enrollments->map(function (Enrollment $e) use ($schoolId) {
            $subject = $e->offering?->subject;
            $units = (float) ($subject?->units ?? 0);
            $fallback = (float) ($subject?->price_per_subject ?? 0);
            $amount = $this->resolveSubjectPriceForSchool($schoolId, $units, $fallback);
            return [
                'code' => (string) ($subject?->code ?? 'N/A'),
                'title' => (string) ($subject?->title ?? 'Untitled'),
                'units' => $units,
                'amount' => $amount,
            ];
        })->values();

        $totalDue = (float) $billing->amount_due;
        $amountPaid = (float) $billing->amount_paid;
        $remaining = max($totalDue - $amountPaid, 0.0);

        $school = School::query()->where('id', $schoolId)->first();

        return view('tenant.payment-review', [
            'billing' => $billing,
            'breakdown' => $breakdown,
            'totalDue' => $totalDue,
            'amountPaid' => $amountPaid,
            'remaining' => $remaining,
            'school' => $school,
        ]);
    }

    public function payBillingStripe(Request $request, Billing $billing): \Illuminate\Http\RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $studentUserId = (int) $request->attributes->get('actor_user_id');
        if ((int) $billing->school_id !== $schoolId || (int) $billing->student_user_id !== $studentUserId) {
            return redirect('/tenant/payments')->withErrors(['billing' => 'Billing record not accessible.']);
        }

        $remaining = max((float) $billing->amount_due - (float) $billing->amount_paid, 0.0);
        if ($remaining <= 0.0) {
            return redirect('/tenant/payments')->with('status', 'This bill is already settled.');
        }

        $school = School::query()->where('id', $schoolId)->first();

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $productData = [
            'name' => 'Tuition Fee',
            'description' => 'Tuition payment',
        ];
        if (!empty($school?->logo_url)) {
            $productData['images'] = [$school->logo_url];
        }

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'customer_email' => auth()->user()?->email,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'php',
                    'product_data' => $productData,
                    'unit_amount' => (int) round($remaining * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/tenant/payments/stripe/success?billing_id=' . (int) $billing->id . '&session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/tenant/payments'),
        ]);

        return redirect($session->url);
    }

    public function stripePaymentSuccess(Request $request): \Illuminate\Http\RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $studentUserId = (int) $request->attributes->get('actor_user_id');
        $billingId = (int) $request->query('billing_id');
        $sessionId = (string) $request->query('session_id', '');

        $billing = Billing::query()->where('id', $billingId)->where('school_id', $schoolId)->where('student_user_id', $studentUserId)->first();
        if ($billing === null || $sessionId === '') {
            return redirect('/tenant/payments')->withErrors(['payment' => 'Invalid payment session.']);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = StripeSession::retrieve($sessionId);
        if (($session->payment_status ?? '') !== 'paid') {
            return redirect('/tenant/payments')->withErrors(['payment' => 'Payment not completed.']);
        }

        $remaining = max((float) $billing->amount_due - (float) $billing->amount_paid, 0.0);
        if ($remaining > 0.0) {
            $payment = $this->billingWorkflowService->submitPayment(
                billing: $billing,
                studentUserId: (int) $billing->student_user_id,
                amount: $remaining,
                referenceNo: (string) ($session->payment_intent ?? ''),
            );

            // Do not auto-verify here; let cashier confirm the payment.
        }

        return redirect('/tenant/payments')->with('status', 'Payment submitted. Awaiting cashier verification.');
    }

    public function payBillingPayMongo(Request $request, Billing $billing): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $studentUserId = (int) $request->attributes->get('actor_user_id');
        if ((int) $billing->school_id !== $schoolId || (int) $billing->student_user_id !== $studentUserId) {
            return redirect('/tenant/payments')->withErrors(['billing' => 'Billing record not accessible.']);
        }

        $remaining = max((float) $billing->amount_due - (float) $billing->amount_paid, 0.0);
        if ($remaining <= 0.0) {
            return redirect('/tenant/payments')->with('status', 'This bill is already settled.');
        }

        $wallet = trim((string) $request->input('wallet', ''));
        if (! in_array($wallet, ['gcash', 'paymaya'], true)) {
            return redirect('/tenant/billing/' . (int) $billing->id . '/review')->withErrors(['payment' => 'Invalid payment method.']);
        }

        $user = $request->user();
        $name = $user?->full_name ?? $user?->name ?? 'Customer';
        $email = $user?->email ?? '';
        $rawPhone = $user?->phone ?? '09171234567';
        $phoneDigits = preg_replace('/\D/', '', $rawPhone);
        if (strlen($phoneDigits) === 10 && str_starts_with($phoneDigits, '9')) {
            $phone = '+63' . $phoneDigits;
        } elseif (strlen($phoneDigits) === 11 && str_starts_with($phoneDigits, '09')) {
            $phone = '+63' . substr($phoneDigits, 1);
        } elseif (strlen($phoneDigits) === 12 && str_starts_with($phoneDigits, '63')) {
            $phone = '+' . $phoneDigits;
        } else {
            $phone = '+639171234567';
        }

        $returnUrl = url('/tenant/payments/paymongo/success?billing_id=' . (int) $billing->id);

        try {
            $amountCentavos = (int) round($remaining * 100);
            $description = 'Tuition payment — Billing #' . (int) $billing->id;

            $intentResp = $this->payMongoService->createPaymentIntent($amountCentavos, $description);
            $intentId = $intentResp['data']['id'] ?? null;
            if (! $intentId) {
                throw new \RuntimeException('PayMongo did not return a payment intent ID.');
            }

            $request->session()->put('paymongo_intent_' . (int) $billing->id, $intentId);

            $pmResp = $this->payMongoService->createEwalletPaymentMethod($wallet, $name, $email, $phone);
            $pmId = $pmResp['data']['id'] ?? null;
            if (! $pmId) {
                throw new \RuntimeException('PayMongo did not return a payment method ID.');
            }

            $attach = $this->payMongoService->attachPaymentMethod($intentId, $pmId, $returnUrl);
            $attrs = $attach['data']['attributes'] ?? [];
            $status = $attrs['status'] ?? '';

            if ($status === 'succeeded') {
                $request->session()->forget('paymongo_intent_' . (int) $billing->id);
                $this->billingWorkflowService->submitPayment(
                    billing: $billing,
                    studentUserId: (int) $billing->student_user_id,
                    amount: $remaining,
                    referenceNo: $intentId,
                );
                return redirect($returnUrl)->with('status', 'Payment submitted. Awaiting cashier verification.');
            }

            $redirectUrl = $attrs['next_action']['redirect']['url'] ?? null;
            if ($redirectUrl) {
                return redirect()->away($redirectUrl);
            }

            throw new \RuntimeException('No redirect URL from PayMongo. Status: ' . $status);
        } catch (\Throwable $e) {
            report($e);
            return redirect('/tenant/billing/' . (int) $billing->id . '/review')
                ->withErrors(['payment' => 'Payment gateway error: ' . $e->getMessage()]);
        }
    }

    public function paymongoPaymentSuccess(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $studentUserId = (int) $request->attributes->get('actor_user_id');
        $billingId = (int) $request->query('billing_id');

        $billing = Billing::query()
            ->where('id', $billingId)
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->first();

        if ($billing === null) {
            return redirect('/tenant/payments')->withErrors(['payment' => 'Invalid payment session.']);
        }

        $intentId = $request->session()->get('paymongo_intent_' . $billingId);
        if (! $intentId) {
            return redirect('/tenant/payments')->with('status', 'Payment submitted. Awaiting cashier verification.');
        }

        try {
            $intent = $this->payMongoService->getPaymentIntent($intentId);
            $status = $intent['data']['attributes']['status'] ?? '';
            if ($status === 'succeeded') {
                $remaining = max((float) $billing->amount_due - (float) $billing->amount_paid, 0.0);
                if ($remaining > 0.0) {
                    $this->billingWorkflowService->submitPayment(
                        billing: $billing,
                        studentUserId: (int) $billing->student_user_id,
                        amount: $remaining,
                        referenceNo: $intentId,
                    );
                }
            }
        } finally {
            $request->session()->forget('paymongo_intent_' . $billingId);
        }

        return redirect('/tenant/payments')->with('status', 'Payment submitted. Awaiting cashier verification.');
    }

    public function createBillingCategory(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) ($request->attributes->get('actor_user_id') ?? 0);

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        BillingCategory::query()->firstOrCreate([
            'school_id' => $schoolId,
            'name' => trim($payload['name']),
        ], [
            'status' => 'active',
            'created_by_user_id' => $actorUserId > 0 ? $actorUserId : null,
        ]);

        return back()->with('status', 'Billing category created.');
    }

    public function createBillingGroup(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) ($request->attributes->get('actor_user_id') ?? 0);

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'program_id' => ['nullable', 'integer', 'min:1'],
            'year_level' => ['nullable', 'string', 'max:40'],
            'billing_rule_ids' => ['required', 'array', 'min:1'],
            'billing_rule_ids.*' => ['integer', 'min:1'],
        ]);

        $group = BillingGroup::query()->create([
            'school_id' => $schoolId,
            'name' => trim($payload['name']),
            'program_id' => isset($payload['program_id']) ? (int) $payload['program_id'] : null,
            'year_level' => $payload['year_level'] ?? null,
            'status' => 'active',
            'created_by_user_id' => $actorUserId > 0 ? $actorUserId : null,
        ]);

        $ruleIds = collect($payload['billing_rule_ids'])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $validRuleIds = BillingRule::query()
            ->where('school_id', $schoolId)
            ->whereIn('id', $ruleIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        foreach (array_values($validRuleIds) as $i => $ruleId) {
            BillingGroupItem::query()->create([
                'billing_group_id' => (int) $group->id,
                'billing_rule_id' => (int) $ruleId,
                'sort_order' => $i,
            ]);
        }

        return back()->with('status', 'Billing group created.');
    }

    public function updateBillingGroup(Request $request, BillingGroup $group): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $group->school_id !== $schoolId) {
            return back()->withErrors(['billing_group' => 'Billing group does not belong to your school.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'program_id' => ['nullable', 'integer', 'min:1'],
            'year_level' => ['nullable', 'string', 'max:40'],
            'billing_rule_ids' => ['required', 'array', 'min:1'],
            'billing_rule_ids.*' => ['integer', 'min:1'],
        ]);

        $group->update([
            'name' => trim($payload['name']),
            'program_id' => isset($payload['program_id']) ? (int) $payload['program_id'] : null,
            'year_level' => $payload['year_level'] ?? null,
        ]);

        $ruleIds = collect($payload['billing_rule_ids'])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $validRuleIds = BillingRule::query()
            ->where('school_id', $schoolId)
            ->whereIn('id', $ruleIds)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        BillingGroupItem::query()
            ->where('billing_group_id', (int) $group->id)
            ->delete();

        foreach (array_values($validRuleIds) as $i => $ruleId) {
            BillingGroupItem::query()->create([
                'billing_group_id' => (int) $group->id,
                'billing_rule_id' => (int) $ruleId,
                'sort_order' => $i,
            ]);
        }

        return back()->with('status', 'Billing group updated.');
    }

    public function deleteBillingGroup(Request $request, BillingGroup $group): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $group->school_id !== $schoolId) {
            return back()->withErrors(['billing_group' => 'Billing group does not belong to your school.']);
        }

        $group->delete(); // billing_group_items cascades

        return back()->with('status', 'Billing group deleted.');
    }

    public function paymentsPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $studentUserId = (int) $request->attributes->get('actor_user_id');

        // Clean up orphaned, unpaid bills left behind after admins delete billing rules.
        // When a BillingRule is deleted, `billing.billing_rule_id` becomes NULL (nullOnDelete).
        // If there are no payments yet, it's safe to remove these bills so they don't keep showing up.
        $orphanBills = Billing::query()
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->whereNull('billing_rule_id')
            ->whereIn('payment_status', ['unpaid', 'partial', 'paid_unverified'])
            ->where('charge_type', '!=', 'tuition')
            ->get();

        foreach ($orphanBills as $bill) {
            $hasPayments = Payment::query()
                ->where('billing_id', (int) $bill->id)
                ->exists();
            if (! $hasPayments) {
                $bill->delete();
            }
        }

        // Ensure bills exist for the student's active enrollment(s).
        $activeStatuses = ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'];
        // Generate bills only once per semester (latest enrollment per semester),
        // otherwise duplicate enrollment rows can create duplicate bills.
        $enrollments = Enrollment::query()
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->whereIn('status', $activeStatuses)
            ->orderByDesc('id')
            ->get()
            ->groupBy(fn (Enrollment $e) => (int) $e->semester_id)
            ->map(fn ($g) => $g->first())
            ->values();

        foreach ($enrollments as $enrollment) {
            $this->billingWorkflowService->generateForEnrollment($enrollment);
        }

        $myBilling = Billing::query()
            ->with(['payments', 'rule'])
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->where(function ($q): void {
                // Hide bills for disabled (inactive) billing rules, but keep historical bills whose rules were deleted (billing_rule_id is null).
                $q->whereNull('billing_rule_id')
                    ->orWhereHas('rule', fn ($r) => $r->where('status', 'active'));
            })
            // Bills tab should only show items still needing payment/verification.
            // Fully settled/verified items should appear under Transactions instead.
            ->whereIn('payment_status', ['unpaid', 'partial', 'paid_unverified'])
            ->whereColumn('amount_paid', '<', 'amount_due')
            ->orderByDesc('id')
            ->get();

        $transactions = Payment::query()
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->orderByDesc('id')
            ->limit(300)
            ->get();

        return view('tenant.payments', compact('myBilling', 'transactions'));
    }

    public function updateEnrollmentFee(Request $request, FinanceFeeSetting $feeSetting): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $feeSetting->school_id !== $schoolId) {
            return back()->withErrors(['billing' => 'Fee setting does not belong to your school.']);
        }

        $payload = $request->validate([
            'enrollment_fee' => ['required', 'numeric', 'min:0'],
        ]);

        $feeSetting->enrollment_fee = (float) $payload['enrollment_fee'];
        $feeSetting->save();

        return back()->with('status', 'Enrollment fee updated.');
    }

    public function saveGeneralEnrollmentFee(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'enrollment_fee' => ['required', 'numeric', 'min:0'],
            'price_per_course_unit' => ['nullable', 'numeric', 'min:0'],
        ]);

        $setting = FinanceFeeSetting::query()->firstOrNew([
            'school_id' => $schoolId,
            'semester_id' => null,
            'academic_year_id' => null,
            'program_id' => null,
        ]);

        if (! $setting->exists) {
            $setting->created_by_user_id = $actorUserId > 0 ? $actorUserId : null;
            $setting->status = 'active';
            $setting->tuition_fee = (float) ($setting->tuition_fee ?? 0);
        }

        $setting->enrollment_fee = (float) $payload['enrollment_fee'];
        if (array_key_exists('price_per_course_unit', $payload)) {
            $setting->price_per_course_unit = $payload['price_per_course_unit'] !== null
                ? (float) $payload['price_per_course_unit']
                : null;
        }
        $setting->save();

        if (array_key_exists('price_per_course_unit', $payload) && $setting->price_per_course_unit !== null) {
            $this->recalculateCoursePricesByUnitRate($schoolId, (float) $setting->price_per_course_unit);
        }

        return back()->with('status', 'General enrollment fee saved.');
    }

    public function updateCoursePrice(Request $request, Subject $subject): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $subject->school_id !== $schoolId) {
            return back()->withErrors(['course_price' => 'Course does not belong to your school.']);
        }

        $pricePerCourseUnit = $this->resolveGeneralPricePerCourseUnit($schoolId);
        if ($pricePerCourseUnit !== null) {
            $subject->price_per_subject = round((float) $subject->units * $pricePerCourseUnit, 2);
            $subject->save();

            return back()->with('status', 'Course price recalculated from the school price per course unit.');
        }

        $payload = $request->validate([
            'price_per_subject' => ['required', 'numeric', 'min:0'],
        ]);

        $subject->price_per_subject = (float) $payload['price_per_subject'];
        $subject->save();

        return back()->with('status', 'Course price updated.');
    }

    public function createBillingRule(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'charge_type' => ['required', 'in:tuition,misc_fee,lab_fee,penalty,other'],
            'billing_category_id' => ['nullable', 'integer', 'exists:billing_categories,id'],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:10'],
            'scope_type' => ['required', 'in:program,department,section,student,all'],
            'scope_id' => ['nullable', 'integer', 'min:1'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $schoolId = (int) $request->attributes->get('active_school_id');
        $semesterBelongsToSchool = Semester::query()
            ->where('school_id', $schoolId)
            ->whereKey((int) $payload['semester_id'])
            ->exists();
        if (! $semesterBelongsToSchool) {
            return back()->withErrors(['semester_id' => 'Selected semester does not belong to your school.'])->withInput();
        }

        $scopeColumns = $this->resolveScopeColumns(
            schoolId: $schoolId,
            scopeType: (string) $payload['scope_type'],
            scopeId: isset($payload['scope_id']) ? (int) $payload['scope_id'] : null,
        );

        $actorUserId = (int) ($request->attributes->get('actor_user_id') ?? auth()->id() ?? 0);

        BillingRule::query()->create([
            'school_id' => $schoolId,
            'semester_id' => (int) $payload['semester_id'],
            'charge_type' => $payload['charge_type'],
            'billing_category_id' => isset($payload['billing_category_id']) ? (int) $payload['billing_category_id'] : null,
            'year_level' => isset($payload['year_level']) ? (int) $payload['year_level'] : null,
            ...$scopeColumns,
            'amount' => $payload['amount'],
            'description' => $payload['description'] ?? null,
            'created_by_finance_user_id' => $actorUserId ?: auth()->id(),
        ]);

        return back()->with('status', 'Billing rule created.');
    }

    public function updateBillingRule(Request $request, BillingRule $rule): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $rule->school_id !== $schoolId) {
            return back()->withErrors(['billing_rule' => 'Billing rule does not belong to your school.']);
        }

        $payload = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'charge_type'  => ['required', 'in:tuition,misc_fee,lab_fee,penalty,other'],
            'amount'       => ['required', 'numeric', 'min:0'],
            'status'       => ['nullable', 'in:active,inactive'],
        ]);

        $rule->update([
            'description' => $payload['description'],
            'charge_type' => $payload['charge_type'],
            'amount'      => $payload['amount'],
            'status'      => $payload['status'] ?? 'active',
        ]);

        return back()->with('status', 'Billing rule updated.');
    }

    public function deleteBillingRule(Request $request, BillingRule $rule): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $rule->school_id !== $schoolId) {
            return back()->withErrors(['billing_rule' => 'Billing rule does not belong to your school.']);
        }

        // If this rule is used inside any billing group templates, remove those references first.
        BillingGroupItem::query()
            ->where('billing_rule_id', (int) $rule->id)
            ->delete();

        // Remove already-generated bills for students.
        // - If there are NO payments yet → safe to delete the billing rows.
        // - If there are payments → keep history, but void the bill.
        $billings = Billing::query()
            ->where('school_id', $schoolId)
            ->where('billing_rule_id', (int) $rule->id)
            ->get();

        foreach ($billings as $billing) {
            $hasPayments = Payment::query()
                ->where('billing_id', (int) $billing->id)
                ->exists();

            if (! $hasPayments) {
                $billing->delete();
                continue;
            }

            $billing->payment_status = 'void';
            $billing->clearance_status = 'not_cleared';
            $billing->save();
        }

        $rule->delete();

        return back()->with('status', 'Billing rule deleted.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // CASHIER
    // ══════════════════════════════════════════════════════════════════════════

    public function cashierPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $financeSetting = FinanceSetting::query()->firstOrCreate(
            ['school_id' => $schoolId],
            ['auto_approve_payments' => false],
        );
        $autoApprovePaymentsEnabled = (bool) $financeSetting->auto_approve_payments;

        $pendingPayments = Payment::query()
            ->with(['billing', 'student'])
            ->whereHas('billing', function ($query) use ($schoolId): void {
                $query->where('school_id', $schoolId);
            })
            ->whereIn('status', ['submitted'])
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $confirmedPayments = Payment::query()
            ->with(['billing', 'student'])
            ->whereHas('billing', function ($query) use ($schoolId): void {
                $query->where('school_id', $schoolId);
            })
            ->where('status', 'verified')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $forClearance = Billing::query()
            ->with(['payments', 'student'])
            ->where('school_id', $schoolId)
            ->where('payment_status', 'verified')
            ->where('clearance_status', 'not_cleared')
            ->orderByDesc('id')
            ->get();

        return view('tenant.cashier', compact('pendingPayments', 'confirmedPayments', 'autoApprovePaymentsEnabled', 'forClearance'));
    }

    public function cashierToggleAutoApprovePayments(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);

        $setting = FinanceSetting::query()->firstOrCreate(
            ['school_id' => $schoolId],
            ['auto_approve_payments' => false],
        );

        $setting->auto_approve_payments = (bool) $payload['enabled'];
        $setting->updated_by_user_id = $actorUserId > 0 ? $actorUserId : null;
        $setting->save();

        return back()->with('status', 'Auto approve payment is now ' . ($setting->auto_approve_payments ? 'ON' : 'OFF') . '.');
    }

    public function cashierCreatePayment(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'student_user_id' => ['required', 'integer', 'exists:users,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:255'],
            'payment_type' => ['required', 'in:cash,card,bank_transfer,wallet,other'],
            'reference_no' => ['nullable', 'string', 'max:80'],
        ]);

        $transactionId = 'TXN-' . strtoupper(bin2hex(random_bytes(6))) . '-' . time();

        CashierPayment::create([
            'school_id' => $schoolId,
            'student_user_id' => (int) $payload['student_user_id'],
            'transaction_id' => $transactionId,
            'description' => $payload['description'] ?? null,
            'amount' => (float) $payload['amount'],
            'payment_type' => $payload['payment_type'],
            'reference_no' => $payload['reference_no'] ?? null,
            'status' => 'completed',
            'processed_by_user_id' => $actorUserId,
        ]);

        return back()->with('status', 'Payment recorded successfully.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // DISCOUNT
    // ══════════════════════════════════════════════════════════════════════════

    public function discountPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $discounts = Discount::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->get();

        return view('tenant.discount', compact('discounts'));
    }

    public function createDiscount(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:amount,percentage'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'placement' => ['required', 'in:regular,admission,all'],
        ]);

        Discount::create([
            'school_id' => $schoolId,
            'name' => $payload['name'],
            'type' => $payload['type'],
            'amount' => $payload['type'] === 'amount' ? ($payload['amount'] ?? 0) : null,
            'percentage' => $payload['type'] === 'percentage' ? ($payload['percentage'] ?? 0) : null,
            'placement' => $payload['placement'],
            'status' => 'active',
            'created_by_user_id' => $actorUserId,
        ]);

        return back()->with('status', 'Discount created successfully.');
    }

    public function updateDiscount(Request $request, Discount $discount): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $discount->school_id !== $schoolId) {
            return back()->withErrors(['discount' => 'Discount does not belong to your school.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:amount,percentage'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'placement' => ['required', 'in:regular,admission,all'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $discount->update([
            'name' => $payload['name'],
            'type' => $payload['type'],
            'amount' => $payload['type'] === 'amount' ? ($payload['amount'] ?? 0) : null,
            'percentage' => $payload['type'] === 'percentage' ? ($payload['percentage'] ?? 0) : null,
            'placement' => $payload['placement'],
            'status' => $payload['status'] ?? 'active',
        ]);

        return back()->with('status', 'Discount updated successfully.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // SCHOLARSHIP
    // ══════════════════════════════════════════════════════════════════════════

    public function scholarshipPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $scholarships = Scholarship::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->get();

        return view('tenant.scholarship', compact('scholarships'));
    }

    public function createScholarship(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:amount,percentage'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'placement' => ['required', 'in:regular,admission,all'],
        ]);

        Scholarship::create([
            'school_id' => $schoolId,
            'name' => $payload['name'],
            'type' => $payload['type'],
            'amount' => $payload['type'] === 'amount' ? ($payload['amount'] ?? 0) : null,
            'percentage' => $payload['type'] === 'percentage' ? ($payload['percentage'] ?? 0) : null,
            'placement' => $payload['placement'],
            'status' => 'active',
            'created_by_user_id' => $actorUserId,
        ]);

        return back()->with('status', 'Scholarship created successfully.');
    }

    public function updateScholarship(Request $request, Scholarship $scholarship): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $scholarship->school_id !== $schoolId) {
            return back()->withErrors(['scholarship' => 'Scholarship does not belong to your school.']);
        }

        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:amount,percentage'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'placement' => ['required', 'in:regular,admission,all'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $scholarship->update([
            'name' => $payload['name'],
            'type' => $payload['type'],
            'amount' => $payload['type'] === 'amount' ? ($payload['amount'] ?? 0) : null,
            'percentage' => $payload['type'] === 'percentage' ? ($payload['percentage'] ?? 0) : null,
            'placement' => $payload['placement'],
            'status' => $payload['status'] ?? 'active',
        ]);

        return back()->with('status', 'Scholarship updated successfully.');
    }

    // ══════════════════════════════════════════════════════════════════════════
    // STUDENT WALLET
    // ══════════════════════════════════════════════════════════════════════════

    public function studentWalletPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $students = User::query()
            ->whereHas('userRoles', function ($q) use ($schoolId): void {
                $q->where('school_id', $schoolId)
                    ->where('is_active', true)
                    ->whereHas('role', fn ($r) => $r->where('code', 'student'));
            })
            ->with(['studentWallets' => fn ($q) => $q->where('school_id', $schoolId)])
            ->orderBy('full_name')
            ->limit(500)
            ->get();

        return view('tenant.student-wallet', compact('students'));
    }

    public function addStudentWallet(Request $request, User $user): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $payload = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $wallet = StudentWallet::firstOrCreate(
            ['school_id' => $schoolId, 'student_user_id' => $user->id],
            ['balance' => 0]
        );

        $wallet->increment('balance', (float) $payload['amount']);
        $wallet->update(['last_transaction_at' => now()]);

        return back()->with('status', 'Wallet credited successfully.');
    }

    public function submitPayment(Request $request, Billing $billing): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');
        $roleCodes = (array) ($request->attributes->get('role_codes') ?? []);
        $isFinanceUser = in_array('finance_staff', $roleCodes, true) || in_array('school_admin', $roleCodes, true);

        if ((int) $billing->school_id !== $schoolId) {
            return back()->withErrors(['payment' => 'Billing does not belong to your school.']);
        }
        if (! $isFinanceUser && (int) $billing->student_user_id !== $actorUserId) {
            return back()->withErrors(['payment' => 'You can only submit payments for your own billing records.']);
        }

        $payload = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'reference_no' => ['required', 'string', 'max:80'],
        ]);

        $payment = $this->billingWorkflowService->submitPayment(
            billing: $billing,
            studentUserId: (int) $billing->student_user_id,
            amount: (float) $payload['amount'],
            referenceNo: (string) $payload['reference_no'],
        );

        if ($payment->status === 'verified') {
            return back()->with('status', 'Payment auto-approved.');
        }

        return back()->with('status', 'Payment submitted for verification.');
    }

    public function verifyPayment(Request $request, Payment $payment): RedirectResponse
    {
        $billing = $payment->billing;
        if ($billing === null || (int) $billing->school_id !== (int) $request->attributes->get('active_school_id')) {
            return back()->withErrors(['payment' => 'Payment does not belong to your school.']);
        }

        $payload = $request->validate([
            'approved' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:verified,rejected'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        $resolvedStatus = null;
        if (array_key_exists('approved', $payload) && $payload['approved'] !== null) {
            $resolvedStatus = ((bool) $payload['approved']) ? 'verified' : 'rejected';
        } elseif (! empty($payload['status'])) {
            $resolvedStatus = (string) $payload['status'];
        }

        if ($resolvedStatus === null) {
            return back()->withErrors(['status' => 'The status field is required.']);
        }

        $payment->status = $resolvedStatus;
        $payment->verified_at = now();
        $payment->verified_by_finance_user_id = (int) $request->attributes->get('actor_user_id');
        $payment->remarks = $payload['remarks'] ?? null;
        $payment->save();

        // Keep billing in sync with finance verification result.
        if ($resolvedStatus === 'verified') {
            $totalVerified = (float) Payment::query()
                ->where('billing_id', $billing->id)
                ->where('status', 'verified')
                ->sum('amount');

            $billing->amount_paid = $totalVerified;
            $billing->payment_status = 'verified';
            $billing->verified_by_finance_user_id = (int) $request->attributes->get('actor_user_id');
            $billing->verified_at = now();
            $billing->save();

            // Move student enrollments to registrar queue when semester billing is fully verified.
            $allVerifiedForSemester = Billing::query()
                ->where('school_id', (int) $billing->school_id)
                ->where('student_user_id', (int) $billing->student_user_id)
                ->where('semester_id', (int) $billing->semester_id)
                ->where('payment_status', '!=', 'verified')
                ->doesntExist();

            if ($allVerifiedForSemester) {
                Enrollment::query()
                    ->where('school_id', (int) $billing->school_id)
                    ->where('student_user_id', (int) $billing->student_user_id)
                    ->where('semester_id', (int) $billing->semester_id)
                    ->whereIn('status', ['selected', 'validated', 'billing_pending'])
                    ->update(['status' => 'payment_verified']);
            }
        } else {
            $billing->payment_status = ((float) $billing->amount_paid > 0) ? 'partial' : 'unpaid';
            $billing->save();
        }

        return back()->with('status', 'Payment ' . $resolvedStatus . '.');
    }

    public function issueClearance(Request $request, Billing $billing): RedirectResponse
    {
        if ((int) $billing->school_id !== (int) $request->attributes->get('active_school_id')) {
            return back()->withErrors(['billing' => 'Billing does not belong to your school.']);
        }

        $billing->clearance_status = 'pending_approval';
        $billing->cleared_at = now();
        $billing->cleared_by_finance_user_id = (int) $request->attributes->get('actor_user_id');
        $billing->save();

        return back()->with('status', 'Clearance issued. Awaiting registrar approval.');
    }

    public function approveClearance(Request $request, Billing $billing): RedirectResponse
    {
        if ((int) $billing->school_id !== (int) $request->attributes->get('active_school_id')) {
            return back()->withErrors(['billing' => 'Billing does not belong to your school.']);
        }

        if ($billing->clearance_status !== 'pending_approval') {
            return back()->withErrors(['billing' => 'Clearance is not pending approval.']);
        }

        $this->billingWorkflowService->approveClearance(
            billing: $billing,
            registrarUserId: (int) $request->attributes->get('actor_user_id'),
        );

        return back()->with('status', 'Clearance approved. Enrollment can now be confirmed.');
    }

    public function approveAllClearancesForStudent(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $registrarUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'student_user_id' => ['required', 'integer'],
            'semester_id' => ['required', 'integer'],
        ]);

        $studentUserId = (int) $payload['student_user_id'];
        $semesterId = (int) $payload['semester_id'];

        // Get all pending clearances for this student
        $pendingBillings = Billing::query()
            ->where('school_id', $schoolId)
            ->where('student_user_id', $studentUserId)
            ->where('semester_id', $semesterId)
            ->where('clearance_status', 'pending_approval')
            ->where('payment_status', 'verified')
            ->get();

        if ($pendingBillings->isEmpty()) {
            return back()->withErrors(['billing' => 'No pending clearances found for this student.']);
        }

        $approvedCount = 0;
        foreach ($pendingBillings as $billing) {
            try {
                $this->billingWorkflowService->approveClearance(
                    billing: $billing,
                    registrarUserId: $registrarUserId,
                );
                $approvedCount++;
            } catch (\Exception $e) {
                // Continue with other billings even if one fails
                continue;
            }
        }

        if ($approvedCount === 0) {
            return back()->withErrors(['billing' => 'Failed to approve any clearances.']);
        }

        return back()->with('status', "Approved {$approvedCount} clearance(s). All enrollments have been confirmed.");
    }

    public function gradesPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $userId = (int) $request->attributes->get('actor_user_id');
        $roleCodes = collect((array) ($request->attributes->get('role_codes') ?? []))
            ->map(fn ($role) => strtolower((string) $role))
            ->filter(fn ($role) => $role !== '')
            ->values()
            ->all();

        $isStudent = in_array('student', $roleCodes, true);
        $hasGradeStaffRole = count(array_intersect($roleCodes, ['school_admin', 'teacher', 'dean', 'registrar', 'registrar_staff'])) > 0;
        $isStudentGradesView = $isStudent && ! $hasGradeStaffRole;

        if ($isStudentGradesView) {
            $activeEnrollmentStatuses = ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'];

            $studentEnrollments = Enrollment::query()
                ->with(['offering.subject', 'section'])
                ->where('school_id', $schoolId)
                ->where('student_user_id', $userId)
                ->whereIn('status', $activeEnrollmentStatuses)
                ->orderByDesc('id')
                ->get();

            $classGroupNameById = $this->classGroupNamesForSectionIdentifiers(
                $schoolId,
                $studentEnrollments->pluck('section.identifier')->all()
            );

            $semesterNameMap = Semester::query()
                ->where('school_id', $schoolId)
                ->whereIn('id', $studentEnrollments->pluck('semester_id')->filter()->unique()->values())
                ->pluck('name', 'id');

            $gradesByEnrollment = Grade::query()
                ->where('school_id', $schoolId)
                ->where('student_user_id', $userId)
                ->orderByDesc('id')
                ->get()
                ->keyBy('enrollment_id');

            $studentGradeRows = $studentEnrollments->map(function (Enrollment $enrollment) use ($gradesByEnrollment, $semesterNameMap, $classGroupNameById): array {
                $grade = $gradesByEnrollment->get((int) $enrollment->id);
                $subject = $enrollment->offering?->subject;
                $section = $enrollment->section;

                return [
                    'enrollment_id' => (int) $enrollment->id,
                    'subject_code' => (string) ($subject?->code ?? 'N/A'),
                    'subject_title' => (string) ($subject?->title ?? 'Untitled Subject'),
                    'section_name' => $this->resolveClassGroupLabelFromSectionIdentifier(
                        $section?->identifier,
                        $classGroupNameById
                    ),
                    'semester_name' => (string) ($semesterNameMap[(int) $enrollment->semester_id] ?? 'N/A'),
                    'grade_value' => $grade?->grade_value,
                    'grade_status' => (string) ($grade?->status ?? 'pending'),
                    'released_at' => $grade?->released_at,
                ];
            });

            $teacherGrades = collect();
            $teacherEnrollments = collect();
            $deanQueue = collect();
            $registrarQueue = collect();
            $studentReleased = collect();
            $myGrades = collect();
            $pendingForDean = collect();
            $pendingForRegistrar = collect();
            $releasedGrades = collect();

            return view('tenant.grades', compact(
                'teacherGrades', 'teacherEnrollments', 'deanQueue', 'registrarQueue', 'studentReleased',
                'myGrades', 'pendingForDean', 'pendingForRegistrar', 'releasedGrades',
                'isStudentGradesView', 'studentGradeRows'
            ));
        }

        // Teacher's draft grades
        $teacherGrades = Grade::query()
            ->with(['offering.subject', 'student', 'enrollment'])
            ->where('school_id', $schoolId)
            ->where('teacher_user_id', $userId)
            ->orderByDesc('id')
            ->get();

        // Get teacher's subject assignments via ClassSessions (same as LMS)
        $teacherSessions = \App\Models\ClassSession::query()
            ->with(['subject', 'classGroup'])
            ->where('school_id', $schoolId)
            ->where('teacher_user_id', $userId)
            ->get();

        // Get unique subject-classGroup combinations
        $teacherSubjectClasses = $teacherSessions
            ->unique(function ($session) {
                return ($session->subject_id ?? 0) . '-' . ($session->class_group_id ?? 0);
            })
            ->map(function ($session) use ($schoolId) {
                // Get enrollments for this subject and class group
                $enrollments = \App\Models\Enrollment::query()
                    ->with(['student', 'offering.subject', 'section'])
                    ->where('school_id', $schoolId)
                    ->whereHas('offering', function ($q) use ($session) {
                        $q->where('subject_id', $session->subject_id);
                    })
                    ->whereHas('section', function ($q) use ($session) {
                        $q->where('identifier', 'CG-' . $session->class_group_id);
                    })
                    ->get();

                return [
                    'subject' => $session->subject,
                    'section' => $enrollments->first()?->section,
                    'class_group' => $session->classGroup,
                    'enrollments' => $enrollments,
                    'student_count' => $enrollments->count(),
                ];
            })
            ->values(); // Show all classes, even with 0 students

        // Get ALL enrollments for teacher's subjects for the modal
        $teacherEnrollments = \App\Models\Enrollment::query()
            ->with(['student', 'offering.subject', 'section'])
            ->where('school_id', $schoolId)
            ->whereHas('offering', function ($q) use ($teacherSessions) {
                $q->whereIn('subject_id', $teacherSessions->pluck('subject_id')->unique());
            })
            ->get();

        // Index grades by enrollment_id for easy lookup
        $teacherGradesByEnrollmentId = $teacherGrades->keyBy('enrollment_id');

        // Dean queue - grades submitted for dean approval
        $deanQueue = Grade::query()
            ->with(['subject', 'student'])
            ->where('school_id', $schoolId)
            ->where('status', 'submitted')
            ->orderByDesc('id')
            ->get();

        // Registrar queue - grades approved by dean
        $registrarQueue = Grade::query()
            ->with(['subject', 'student'])
            ->where('school_id', $schoolId)
            ->whereIn('status', ['dean_approved', 'registrar_finalized'])
            ->orderByDesc('id')
            ->get();

        // Student's released grades
        $studentReleased = Grade::query()
            ->with(['subject', 'student'])
            ->where('school_id', $schoolId)
            ->where('student_user_id', $userId)
            ->where('status', 'released')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        // For backward compatibility
        $myGrades = $studentReleased;
        $pendingForDean = $deanQueue;
        $pendingForRegistrar = $registrarQueue;
        $releasedGrades = Grade::query()
            ->with(['subject', 'student'])
            ->where('school_id', $schoolId)
            ->where('status', 'released')
            ->orderByDesc('id')
            ->limit(100)
            ->get();
        $isStudentGradesView = false;
        $studentGradeRows = collect();

        return view('tenant.grades', compact(
            'teacherGrades', 'teacherEnrollments', 'teacherSubjectClasses', 'teacherGradesByEnrollmentId',
            'deanQueue', 'registrarQueue', 'studentReleased',
            'myGrades', 'pendingForDean', 'pendingForRegistrar', 'releasedGrades',
            'isStudentGradesView', 'studentGradeRows'
        ));
    }

    public function gradeDraft(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'student_user_id' => ['required', 'integer', 'exists:users,id'],
            'prelim_grade' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'midterm_grade' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'finals_grade' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        try {
            $this->gradeWorkflowService->submitDraft(
                schoolId: (int) $request->attributes->get('active_school_id'),
                subjectId: (int) $payload['subject_id'],
                studentUserId: (int) $payload['student_user_id'],
                teacherUserId: (int) $request->attributes->get('actor_user_id'),
                prelimGrade: isset($payload['prelim_grade']) ? (float) $payload['prelim_grade'] : null,
                midtermGrade: isset($payload['midterm_grade']) ? (float) $payload['midterm_grade'] : null,
                finalsGrade: isset($payload['finals_grade']) ? (float) $payload['finals_grade'] : null,
            );

            return back()->with('status', 'Grade draft saved.');
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['grade' => $exception->getMessage()]);
        }
    }

    public function gradeSubmit(Request $request, Grade $grade): RedirectResponse
    {
        if ((int) $grade->school_id !== (int) $request->attributes->get('active_school_id')) {
            return back()->withErrors(['grade' => 'Grade does not belong to your school.']);
        }

        try {
            $this->gradeWorkflowService->submitForDeanApproval($grade);

            return back()->with('status', 'Grade submitted for dean approval.');
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['grade' => $exception->getMessage()]);
        }
    }

    public function gradeDeanDecision(Request $request, Grade $grade): RedirectResponse
    {
        if ((int) $grade->school_id !== (int) $request->attributes->get('active_school_id')) {
            return back()->withErrors(['grade' => 'Grade does not belong to your school.']);
        }

        $payload = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'remarks' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            if ($payload['decision'] === 'approve') {
                $this->gradeWorkflowService->deanApprove(
                    grade: $grade,
                    deanUserId: (int) $request->attributes->get('actor_user_id'),
                );
            } else {
                $this->gradeWorkflowService->deanReject(
                    grade: $grade,
                    deanUserId: (int) $request->attributes->get('actor_user_id'),
                    remarks: $payload['remarks'] ?? null,
                );
            }

            return back()->with('status', 'Grade ' . ($payload['decision'] === 'approve' ? 'approved' : 'rejected') . '.');
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['grade' => $exception->getMessage()]);
        }
    }

    public function gradeFinalize(Request $request, Grade $grade): RedirectResponse
    {
        if ((int) $grade->school_id !== (int) $request->attributes->get('active_school_id')) {
            return back()->withErrors(['grade' => 'Grade does not belong to your school.']);
        }

        try {
            $this->gradeWorkflowService->registrarFinalize(
                grade: $grade,
                registrarUserId: (int) $request->attributes->get('actor_user_id'),
            );

            return back()->with('status', 'Grade finalized.');
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['grade' => $exception->getMessage()]);
        }
    }

    public function gradeRelease(Request $request, Grade $grade): RedirectResponse
    {
        if ((int) $grade->school_id !== (int) $request->attributes->get('active_school_id')) {
            return back()->withErrors(['grade' => 'Grade does not belong to your school.']);
        }

        try {
            $this->gradeWorkflowService->registrarRelease(
                grade: $grade,
                registrarUserId: (int) $request->attributes->get('actor_user_id'),
            );

            return back()->with('status', 'Grade released to student.');
        } catch (\RuntimeException $exception) {
            return back()->withErrors(['grade' => $exception->getMessage()]);
        }
    }

    public function adminPage(Request $request): View
    {
        $roleCodes = collect((array) ($request->attributes->get('role_codes') ?? []))
            ->map(fn ($role): string => strtolower((string) $role))
            ->filter(fn ($role): bool => $role !== '')
            ->values()
            ->all();
        $canAccessAdminPage = count(array_intersect($roleCodes, ['school_admin', 'registrar', 'registrar_staff', 'dean'])) > 0;
        abort_unless($canAccessAdminPage, 403);

        $schoolId = (int) $request->attributes->get('active_school_id');

        $schoolUserIds = UserRole::query()
            ->where('school_id', $schoolId)
            ->distinct()
            ->pluck('user_id');
        $users = User::query()
            ->whereIn('id', $schoolUserIds)
            ->orderBy('full_name')
            ->limit(500)
            ->get();
        $roles = Role::query()->orderBy('name')->get();
        $activeAdminTab = (string) $request->query('tab', 'structure');
        
        // Curriculum data
        $colleges = College::query()->where('school_id', $schoolId)->orderBy('name')->get();
        $departments = Department::query()->with('college')->where('school_id', $schoolId)->orderBy('name')->get();
        $programs = Program::query()->with('department.college')->where('school_id', $schoolId)->orderBy('name')->get();
        $subjects = Subject::query()
            ->with(['prerequisites:id,code,title'])
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->limit(100)
            ->get();
        $pricePerCourseUnit = $this->resolveGeneralPricePerCourseUnit($schoolId);
        
        // Academic data
        $academicYears = AcademicYear::query()->where('school_id', $schoolId)->orderByDesc('name')->get();
        $semesters = Semester::query()->where('school_id', $schoolId)->orderByDesc('id')->get();
        $fixedSemesters = collect(['1ST', '2ND', 'SUMMER'])
            ->map(function (string $termCode) use ($semesters): ?array {
                $semester = $semesters->first(
                    fn (Semester $item): bool => strtoupper((string) $item->term_code) === $termCode
                );

                if (! $semester) {
                    return null;
                }

                $label = match ($termCode) {
                    '1ST' => 'First Semester',
                    '2ND' => 'Second Semester',
                    'SUMMER' => 'Summer',
                    default => $semester->name,
                };

                return [
                    'model' => $semester,
                    'label' => $label,
                ];
            })
            ->filter()
            ->values();
        $offerings = SubjectOffering::query()->with(['subject', 'semester', 'teacher'])->where('school_id', $schoolId)->orderByDesc('id')->limit(100)->get();
        $sections = Section::query()->with(['offering.subject'])->where('school_id', $schoolId)->orderByDesc('id')->limit(100)->get();
        $curriculumItems = ProgramCurriculumItem::query()
            ->with(['program.department', 'semester', 'subject'])
            ->where('school_id', $schoolId)
            ->orderBy('program_id')
            ->orderBy('year_level')
            ->orderBy('semester_id')
            ->orderBy('id')
            ->get();
        
        $teachers = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->join('users', 'users.id', '=', 'user_roles.user_id')
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.is_active', true)
            ->where('roles.code', 'teacher')
            ->select('users.id', 'users.full_name', 'users.email')
            ->distinct()
            ->orderBy('users.full_name')
            ->get();

        // Classes data (for classes tab)
        $classDayProfiles = ClassDayProfile::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        $classGroups = ClassGroup::query()
            ->with(['program', 'semester', 'profile'])
            ->withCount('sessions')
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $currentTermCode = strtoupper((string) (
            $request->query('term_code')
            ?? $request->session()->get('tenant_current_term_code')
            ?? '1ST'
        ));
        if (! in_array($currentTermCode, ['1ST', '2ND', 'SUMMER'], true)) {
            $currentTermCode = '1ST';
        }

        $currentTermLabel = match ($currentTermCode) {
            '1ST' => 'First Semester',
            '2ND' => 'Second Semester',
            'SUMMER' => 'Summer',
            default => 'First Semester',
        };
        $currentSchoolYearName = (string) (
            AcademicYear::query()
                ->where('school_id', $schoolId)
                ->where('status', 'active')
                ->value('name')
            ?? ($academicYears->first()->name ?? '')
        );

        $currentTermSemesterIds = $semesters
            ->filter(fn (Semester $s): bool => strtoupper((string) $s->term_code) === $currentTermCode)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $classGroupsCurrentTerm = $classGroups
            ->filter(fn (ClassGroup $g): bool => in_array((int) $g->semester_id, $currentTermSemesterIds, true))
            ->values();

        $selectedClassGroupId = max((int) $request->query('class_group_id', 0), 0);
        $selectedClassGroup = $selectedClassGroupId > 0
            ? $classGroups->first(fn (ClassGroup $g): bool => (int) $g->id === $selectedClassGroupId)
            : null;

        $classSessions = $selectedClassGroup
            ? ClassSession::query()
                ->with(['subject', 'teacher', 'classGroup.program', 'classGroup.semester'])
                ->where('school_id', $schoolId)
                ->where('class_group_id', (int) $selectedClassGroup->id)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
            : collect();

        $selectedTeacherUserId = max((int) $request->query('teacher_user_id', 0), 0);
        $selectedTeacher = $selectedTeacherUserId > 0
            ? $teachers->first(fn ($t): bool => (int) ($t->id ?? 0) === $selectedTeacherUserId)
            : null;

        $teacherWeeklySessions = $selectedTeacher
            ? ClassSession::query()
                ->with(['subject', 'classGroup.program', 'classGroup.semester'])
                ->where('school_id', $schoolId)
                ->where('teacher_user_id', $selectedTeacherUserId)
                ->when(
                    $selectedClassGroupId > 0,
                    fn ($query) => $query->where('class_group_id', $selectedClassGroupId)
                )
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
            : collect();

        $latestClassGenerationRun = ClassGenerationRun::query()
            ->where('school_id', $schoolId)
            ->when(
                $selectedClassGroupId > 0,
                fn ($query) => $query->where('class_group_id', $selectedClassGroupId)
            )
            ->orderByDesc('id')
            ->first();

        $subjectTeacherOptions = $subjects
            ->mapWithKeys(fn (Subject $subject): array => [(int) $subject->id => $teachers->values()->all()])
            ->all();

        // Roles/students data
        $userRolesByUserId = UserRole::query()
            ->with('role')
            ->where('school_id', $schoolId)
            ->whereIn('user_id', $users->pluck('id'))
            ->get()
            ->groupBy('user_id');

        $usersWithRoles = $users->map(function (User $user) use ($userRolesByUserId): array {
            return [
                'user' => $user,
                'roles' => $userRolesByUserId->get((int) $user->id, collect())->values(),
            ];
        })->values();

        $managedUsers = $usersWithRoles
            ->reject(function (array $entry): bool {
                return collect($entry['roles'])->contains(function ($userRole): bool {
                    return ($userRole->role->code ?? null) === 'student';
                });
            })
            ->values();

        $studentUsers = $usersWithRoles
            ->filter(function (array $entry): bool {
                return collect($entry['roles'])->contains(function ($userRole): bool {
                    return ($userRole->role->code ?? null) === 'student';
                });
            })
            ->values();

        $teacherSubjectMap = DB::table('teacher_subjects')
            ->where('school_id', $schoolId)
            ->select('teacher_user_id', 'subject_id')
            ->get()
            ->groupBy(fn ($row): int => (int) $row->teacher_user_id)
            ->map(fn ($rows): array => $rows->pluck('subject_id')->map(fn ($id): int => (int) $id)->unique()->values()->all())
            ->all();

        return view('tenant.admin', compact(
            'users', 'roles', 'subjects', 'offerings', 'teachers',
            'colleges', 'departments', 'programs', 'academicYears', 'semesters', 'fixedSemesters', 'sections', 'curriculumItems',
            'activeAdminTab', 'classGroups', 'classDayProfiles', 'classSessions', 'classGroupsCurrentTerm',
            'currentTermCode', 'currentTermLabel', 'currentSchoolYearName', 'selectedClassGroupId', 'selectedClassGroup',
            'selectedTeacherUserId', 'selectedTeacher', 'teacherWeeklySessions', 'latestClassGenerationRun',
            'subjectTeacherOptions', 'managedUsers', 'studentUsers', 'teacherSubjectMap', 'pricePerCourseUnit'
        ));
    }

    public function adminClassGroupsPage(Request $request): View
    {
        $roleCodes = collect((array) ($request->attributes->get('role_codes') ?? []))
            ->map(fn ($role): string => strtolower((string) $role))
            ->filter(fn ($role): bool => $role !== '')
            ->values()
            ->all();
        $canAccessAdminPage = count(array_intersect($roleCodes, ['school_admin', 'registrar', 'registrar_staff', 'dean'])) > 0;
        abort_unless($canAccessAdminPage, 403);

        $schoolId = (int) $request->attributes->get('active_school_id');
        $semesters = Semester::query()->where('school_id', $schoolId)->orderByDesc('id')->get();
        $currentTermCode = strtoupper((string) (
            $request->query('term_code')
            ?? $request->session()->get('tenant_current_term_code')
            ?? '1ST'
        ));
        if (! in_array($currentTermCode, ['1ST', '2ND', 'SUMMER'], true)) {
            $currentTermCode = '1ST';
        }
        $currentTermLabel = match ($currentTermCode) {
            '1ST' => 'First Semester',
            '2ND' => 'Second Semester',
            'SUMMER' => 'Summer',
            default => 'First Semester',
        };
        $currentTermSemesterIds = $semesters
            ->filter(fn (Semester $s): bool => strtoupper((string) $s->term_code) === $currentTermCode)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $classGroups = ClassGroup::query()
            ->with(['program', 'semester', 'profile'])
            ->withCount('sessions')
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $classGroupsCurrentTerm = $classGroups
            ->filter(fn (ClassGroup $g): bool => in_array((int) $g->semester_id, $currentTermSemesterIds, true))
            ->values();

        $sortBy = in_array($request->query('sort'), ['name', 'program', 'year', 'sessions'], true)
            ? $request->query('sort')
            : 'year';
        $sortDir = strtolower((string) $request->query('dir')) === 'desc' ? 'desc' : 'asc';

        $classGroupsCurrentTerm = match ($sortBy) {
            'name' => $sortDir === 'desc'
                ? $classGroupsCurrentTerm->sortByDesc(fn (ClassGroup $g): string => strtoupper((string) $g->name))->values()
                : $classGroupsCurrentTerm->sortBy(fn (ClassGroup $g): string => strtoupper((string) $g->name))->values(),
            'program' => $sortDir === 'desc'
                ? $classGroupsCurrentTerm->sortByDesc(fn (ClassGroup $g): string => strtoupper(($g->program->code ?? '') . ' ' . ($g->program->name ?? '')))->values()
                : $classGroupsCurrentTerm->sortBy(fn (ClassGroup $g): string => strtoupper(($g->program->code ?? '') . ' ' . ($g->program->name ?? '')))->values(),
            'year' => $sortDir === 'desc'
                ? $classGroupsCurrentTerm->sortByDesc(fn (ClassGroup $g): int => (int) $g->year_level)->values()
                : $classGroupsCurrentTerm->sortBy(fn (ClassGroup $g): int => (int) $g->year_level)->values(),
            'sessions' => $sortDir === 'desc'
                ? $classGroupsCurrentTerm->sortByDesc(fn (ClassGroup $g): int => (int) ($g->sessions_count ?? 0))->values()
                : $classGroupsCurrentTerm->sortBy(fn (ClassGroup $g): int => (int) ($g->sessions_count ?? 0))->values(),
            default => $classGroupsCurrentTerm,
        };

        return view('tenant.class-groups', compact('classGroupsCurrentTerm', 'currentTermLabel', 'currentTermCode', 'sortBy', 'sortDir'));
    }

    public function adminUpdateClassGroupSettings(Request $request, ClassGroup $group): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $group->school_id !== $schoolId) {
            abort(403);
        }

        $payload = $request->validate([
            'student_capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'is_enrollment_open' => ['required', 'in:0,1'],
        ]);

        $group->student_capacity = (int) $payload['student_capacity'];
        $group->is_enrollment_open = (int) $payload['is_enrollment_open'] === 1;
        $group->save();

        return redirect()->to(url('/tenant/admin/class-groups'))
            ->with('status', 'Class group settings saved.');
    }

    public function adminAddStructurePage(Request $request): View
    {
        $roleCodes = collect((array) ($request->attributes->get('role_codes') ?? []))
            ->map(fn ($role): string => strtolower((string) $role))
            ->filter(fn ($role): bool => $role !== '')
            ->values()
            ->all();
        $canAccessAdminPage = count(array_intersect($roleCodes, ['school_admin', 'registrar', 'registrar_staff', 'dean'])) > 0;
        abort_unless($canAccessAdminPage, 403);

        $schoolId = (int) $request->attributes->get('active_school_id');
        $colleges = College::query()->where('school_id', $schoolId)->orderBy('name')->get();
        $departments = Department::query()->with('college')->where('school_id', $schoolId)->orderBy('name')->get();

        return view('tenant.admin-add-structure', compact('colleges', 'departments'));
    }

    public function adminWeeklyTimetablePage(Request $request): View
    {
        $roleCodes = collect((array) ($request->attributes->get('role_codes') ?? []))
            ->map(fn ($role): string => strtolower((string) $role))
            ->filter(fn ($role): bool => $role !== '')
            ->values()
            ->all();
        $canAccessAdminPage = count(array_intersect($roleCodes, ['school_admin', 'registrar', 'registrar_staff', 'dean'])) > 0;
        abort_unless($canAccessAdminPage, 403);

        $schoolId = (int) $request->attributes->get('active_school_id');
        $semesters = Semester::query()->where('school_id', $schoolId)->orderByDesc('id')->get();
        $currentTermCode = strtoupper((string) (
            $request->query('term_code')
            ?? $request->session()->get('tenant_current_term_code')
            ?? '1ST'
        ));
        if (! in_array($currentTermCode, ['1ST', '2ND', 'SUMMER'], true)) {
            $currentTermCode = '1ST';
        }
        $currentTermLabel = match ($currentTermCode) {
            '1ST' => 'First Semester',
            '2ND' => 'Second Semester',
            'SUMMER' => 'Summer',
            default => 'First Semester',
        };
        $currentTermSemesterIds = $semesters
            ->filter(fn (Semester $s): bool => strtoupper((string) $s->term_code) === $currentTermCode)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $classGroups = ClassGroup::query()
            ->with(['program', 'semester', 'profile'])
            ->withCount('sessions')
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->limit(200)
            ->get();
        $classGroupsCurrentTerm = $classGroups
            ->filter(fn (ClassGroup $g): bool => in_array((int) $g->semester_id, $currentTermSemesterIds, true))
            ->values();

        $selectedClassGroupId = max((int) $request->query('class_group_id', 0), 0);
        $selectedClassGroup = $selectedClassGroupId > 0
            ? $classGroups->first(fn (ClassGroup $g): bool => (int) $g->id === $selectedClassGroupId)
            : null;

        $classSessions = $selectedClassGroup
            ? ClassSession::query()
                ->with(['subject', 'teacher', 'classGroup.program', 'classGroup.semester'])
                ->where('school_id', $schoolId)
                ->where('class_group_id', (int) $selectedClassGroup->id)
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
            : collect();

        $teachers = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->join('users', 'users.id', '=', 'user_roles.user_id')
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.is_active', true)
            ->where('roles.code', 'teacher')
            ->select('users.id', 'users.full_name', 'users.email')
            ->distinct()
            ->orderBy('users.full_name')
            ->get();

        $subjects = Subject::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->limit(200)
            ->get();
        $subjectTeacherOptions = $subjects
            ->mapWithKeys(fn (Subject $subject): array => [(int) $subject->id => $teachers->values()->all()])
            ->all();

        return view('tenant.admin-weekly-timetable', compact(
            'classGroupsCurrentTerm', 'selectedClassGroupId', 'selectedClassGroup', 'classSessions',
            'subjectTeacherOptions', 'currentTermLabel', 'currentTermCode'
        ));
    }

    public function adminTeacherSchedulePage(Request $request): View
    {
        $roleCodes = collect((array) ($request->attributes->get('role_codes') ?? []))
            ->map(fn ($role): string => strtolower((string) $role))
            ->filter(fn ($role): bool => $role !== '')
            ->values()
            ->all();
        $canAccessAdminPage = count(array_intersect($roleCodes, ['school_admin', 'registrar', 'registrar_staff', 'dean'])) > 0;
        abort_unless($canAccessAdminPage, 403);

        $schoolId = (int) $request->attributes->get('active_school_id');
        $semesters = Semester::query()->where('school_id', $schoolId)->orderByDesc('id')->get();
        $currentTermCode = strtoupper((string) (
            $request->query('term_code')
            ?? $request->session()->get('tenant_current_term_code')
            ?? '1ST'
        ));
        if (! in_array($currentTermCode, ['1ST', '2ND', 'SUMMER'], true)) {
            $currentTermCode = '1ST';
        }
        $currentTermLabel = match ($currentTermCode) {
            '1ST' => 'First Semester',
            '2ND' => 'Second Semester',
            'SUMMER' => 'Summer',
            default => 'First Semester',
        };
        $currentTermSemesterIds = $semesters
            ->filter(fn (Semester $s): bool => strtoupper((string) $s->term_code) === $currentTermCode)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $classGroups = ClassGroup::query()
            ->with(['program', 'semester'])
            ->where('school_id', $schoolId)
            ->orderByDesc('id')
            ->limit(200)
            ->get();
        $classGroupsCurrentTerm = $classGroups
            ->filter(fn (ClassGroup $g): bool => in_array((int) $g->semester_id, $currentTermSemesterIds, true))
            ->values();

        $teachers = DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->join('users', 'users.id', '=', 'user_roles.user_id')
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.is_active', true)
            ->where('roles.code', 'teacher')
            ->select('users.id', 'users.full_name', 'users.email')
            ->distinct()
            ->orderBy('users.full_name')
            ->get();

        $selectedTeacherUserId = max((int) $request->query('teacher_user_id', 0), 0);
        $selectedTeacher = $selectedTeacherUserId > 0
            ? $teachers->first(fn ($t): bool => (int) ($t->id ?? 0) === $selectedTeacherUserId)
            : null;
        $selectedClassGroupId = max((int) $request->query('class_group_id', 0), 0);

        $teacherWeeklySessions = $selectedTeacher
            ? ClassSession::query()
                ->with(['subject', 'classGroup.program', 'classGroup.semester'])
                ->where('school_id', $schoolId)
                ->where('teacher_user_id', $selectedTeacherUserId)
                ->when(
                    $selectedClassGroupId > 0,
                    fn ($query) => $query->where('class_group_id', $selectedClassGroupId)
                )
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
            : collect();

        return view('tenant.admin-teacher-schedule', compact(
            'teachers', 'classGroupsCurrentTerm', 'selectedTeacherUserId', 'selectedTeacher',
            'selectedClassGroupId', 'teacherWeeklySessions', 'currentTermLabel', 'currentTermCode'
        ));
    }

    public function adminSetCurrentTerm(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $payload = $request->validate([
            'school_year' => ['required', 'regex:/^\d{4}\s*-\s*\d{4}$/'],
            'term_code' => ['required', 'in:1ST,2ND'],
        ]);

        preg_match('/^(\d{4})\s*-\s*(\d{4})$/', $payload['school_year'], $matches);
        $startYear = isset($matches[1]) ? (int) $matches[1] : null;
        $endYear = isset($matches[2]) ? (int) $matches[2] : null;

        if (! $startYear || ! $endYear || $endYear <= $startYear) {
            return back()->withErrors(['school_year' => 'School year must be in format YYYY-YYYY and end year must be greater.']);
        }

        $schoolYearName = $startYear . '-' . $endYear;

        $academicYear = AcademicYear::query()->firstOrCreate(
            [
                'school_id' => $schoolId,
                'name' => $schoolYearName,
            ],
            [
                'start_date' => sprintf('%04d-06-01', $startYear),
                'end_date' => sprintf('%04d-05-31', $endYear),
                'status' => 'planned',
            ]
        );

        AcademicYear::query()
            ->where('school_id', $schoolId)
            ->where('id', '!=', $academicYear->id)
            ->where('status', 'active')
            ->update(['status' => 'planned']);

        $academicYear->status = 'active';
        $academicYear->save();

        $termCode = $payload['term_code'];
        $request->session()->put('tenant_current_term_code', $termCode);

        return redirect('/tenant/admin?tab=classes&term_code=' . $termCode)
            ->with('status', 'Current term updated.');
    }

    public function adminGenerateSemesterSchedules(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
        ]);

        $semester = Semester::query()
            ->where('school_id', $schoolId)
            ->find((int) $payload['semester_id']);
        if ($semester === null) {
            return back()->withErrors(['semester_id' => 'Selected semester does not belong to your school.']);
        }

        try {
            $report = $this->classesSchedulingService->generateForSemester(
                schoolId: $schoolId,
                semesterId: (int) $semester->id,
                actorUserId: $actorUserId,
            );

            return redirect('/tenant/admin?tab=classes&term_code=' . strtoupper((string) $semester->term_code))
                ->with('status', 'Schedules generated for semester.')
                ->with('classes_generate_report', $report);
        } catch (\RuntimeException $exception) {
            return redirect('/tenant/admin?tab=classes&term_code=' . strtoupper((string) $semester->term_code))
                ->withErrors(['classes_generate' => $exception->getMessage()]);
        }
    }

    public function adminClearAllGeneratedSchedules(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $deletedDraftSessions = ClassSession::query()
            ->where('school_id', $schoolId)
            ->where('status', 'draft')
            ->delete();

        ClassGenerationRun::query()
            ->where('school_id', $schoolId)
            ->delete();

        return redirect('/tenant/admin?tab=classes')
            ->with('status', 'Cleared generated schedules. Removed ' . (int) $deletedDraftSessions . ' draft sessions.');
    }

    public function adminAssignRole(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_id' => ['required', 'integer', 'exists:roles,id'],
        ]);

        UserRole::query()->updateOrCreate(
            [
                'user_id' => (int) $payload['user_id'],
                'school_id' => $schoolId,
                'role_id' => (int) $payload['role_id'],
            ],
            [
                'is_active' => true,
                'assigned_by_user_id' => $actorUserId,
                'assigned_at' => now(),
            ]
        );

        $roleCode = Role::query()->whereKey((int) $payload['role_id'])->value('code');
        if ($roleCode === 'school_admin') {
            $this->assignAllNonStudentRolesForSchoolAdmin(
                schoolId: $schoolId,
                userId: (int) $payload['user_id'],
                actorUserId: $actorUserId,
            );
        }

        return back()->with('status', 'Role assigned.');
    }

    public function adminAssignRolesBulk(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['integer', 'exists:roles,id'],
        ]);

        $selectedRoleIds = collect($payload['role_ids'] ?? [])
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        $selectedRoleCodes = Role::query()
            ->whereIn('id', $selectedRoleIds->all())
            ->pluck('code')
            ->map(fn ($code): string => (string) $code)
            ->all();

        if (in_array('school_admin', $selectedRoleCodes, true)) {
            $this->assignAllNonStudentRolesForSchoolAdmin(
                schoolId: $schoolId,
                userId: (int) $payload['user_id'],
                actorUserId: $actorUserId,
            );

            return back()->with('status', 'School admin defaults applied: all non-student roles activated.');
        }

        foreach ($selectedRoleIds as $roleId) {
            UserRole::query()->updateOrCreate(
                [
                    'user_id' => (int) $payload['user_id'],
                    'school_id' => $schoolId,
                    'role_id' => $roleId,
                ],
                [
                    'is_active' => true,
                    'assigned_by_user_id' => $actorUserId,
                    'assigned_at' => now(),
                ]
            );
        }

        $deactivateQuery = UserRole::query()
            ->where('user_id', (int) $payload['user_id'])
            ->where('school_id', $schoolId);

        if ($selectedRoleIds->isNotEmpty()) {
            $deactivateQuery->whereNotIn('role_id', $selectedRoleIds->all());
        }

        $deactivateQuery->update(['is_active' => false]);

        return back()->with('status', 'School staffs updated.');
    }

    public function adminSyncTeacherSubjects(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => ['integer', 'distinct', 'exists:subjects,id'],
        ]);

        $teacherUserId = (int) $payload['user_id'];
        $selectedSubjectIds = collect($payload['subject_ids'] ?? [])
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        $belongsToSchool = UserRole::query()
            ->where('school_id', $schoolId)
            ->where('user_id', $teacherUserId)
            ->exists();
        if (! $belongsToSchool) {
            return back()->withErrors(['teacher_subjects' => 'Selected user does not belong to your school.']);
        }

        $isActiveTeacher = UserRole::query()
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.user_id', $teacherUserId)
            ->where('user_roles.is_active', true)
            ->where('roles.code', 'teacher')
            ->exists();
        if (! $isActiveTeacher) {
            return back()->withErrors(['teacher_subjects' => 'Selected user must have an active teacher role.']);
        }

        $ownedSubjectIds = Subject::query()
            ->where('school_id', $schoolId)
            ->whereIn('id', $selectedSubjectIds->all())
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->values();
        if ($ownedSubjectIds->count() !== $selectedSubjectIds->count()) {
            return back()->withErrors(['teacher_subjects' => 'One or more selected subjects do not belong to this school.']);
        }

        DB::transaction(function () use ($schoolId, $actorUserId, $teacherUserId, $selectedSubjectIds): void {
            DB::table('teacher_subjects')
                ->where('school_id', $schoolId)
                ->where('teacher_user_id', $teacherUserId)
                ->when(
                    $selectedSubjectIds->isNotEmpty(),
                    fn ($query) => $query->whereNotIn('subject_id', $selectedSubjectIds->all())
                )
                ->delete();

            if ($selectedSubjectIds->isEmpty()) {
                return;
            }

            $now = now();
            $rows = $selectedSubjectIds
                ->map(fn (int $subjectId): array => [
                    'school_id' => $schoolId,
                    'teacher_user_id' => $teacherUserId,
                    'subject_id' => $subjectId,
                    'assigned_by_user_id' => $actorUserId,
                    'assigned_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
                ->all();

            DB::table('teacher_subjects')->upsert(
                $rows,
                ['school_id', 'teacher_user_id', 'subject_id'],
                ['assigned_by_user_id', 'assigned_at', 'updated_at']
            );
        });

        return back()->with('status', 'Teachable subjects updated.');
    }

    public function adminCreateUser(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:40'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
        ]);

        $user = User::query()->create([
            'full_name' => $payload['full_name'],
            'email' => $payload['email'],
            'password_hash' => Hash::make($payload['password']),
            'status' => 'active',
            'phone' => $payload['phone'] ?? null,
        ]);

        if (isset($payload['role_id'])) {
            UserRole::query()->updateOrCreate(
                [
                    'user_id' => (int) $user->id,
                    'school_id' => $schoolId,
                    'role_id' => (int) $payload['role_id'],
                ],
                [
                    'is_active' => true,
                    'assigned_by_user_id' => $actorUserId,
                    'assigned_at' => now(),
                ]
            );

            $roleCode = Role::query()->whereKey((int) $payload['role_id'])->value('code');
            if ($roleCode === 'school_admin') {
                $this->assignAllNonStudentRolesForSchoolAdmin(
                    schoolId: $schoolId,
                    userId: (int) $user->id,
                    actorUserId: $actorUserId,
                );
            }
        }

        return back()->with('status', 'User account created' . (isset($payload['role_id']) ? ' and role assigned.' : '.'));
    }

    public function adminUpdateUserStatus(Request $request, User $user): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $roleCodes = (array) ($request->attributes->get('role_codes') ?? []);

        $isSchoolAdmin = in_array('school_admin', $roleCodes, true);
        $isRegistrar = in_array('registrar_staff', $roleCodes, true) || in_array('registrar', $roleCodes, true);
        if (! $isSchoolAdmin && ! $isRegistrar) {
            return back()->withErrors(['permission' => 'Only registrar or school admin can update account status.']);
        }

        $belongsToSchool = UserRole::query()
            ->where('school_id', $schoolId)
            ->where('user_id', (int) $user->id)
            ->exists();
        if (! $belongsToSchool) {
            return back()->withErrors(['user' => 'User does not belong to your school.']);
        }

        $isStudentAccount = UserRole::query()
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.user_id', (int) $user->id)
            ->where('roles.code', 'student')
            ->exists();
        if ($isRegistrar && ! $isStudentAccount) {
            return back()->withErrors(['user' => 'Registrar can only activate/deactivate student accounts.']);
        }

        $payload = $request->validate([
            'status' => ['required', 'in:active,disabled,inactive'],
        ]);

        $targetStatus = (string) $payload['status'];
        if ($targetStatus === 'inactive') {
            $targetStatus = 'disabled';
        }

        $user->status = $targetStatus;
        $user->save();

        if ($targetStatus === 'active') {
            $request->session()->forget('pending_student_activation');
        }

        return back()->with('status', 'Account status updated to ' . $targetStatus . '.');
    }

    public function schoolPage(Request $request): View
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $school = School::query()->with('profile')->where('id', $schoolId)->firstOrFail();

        return view('tenant.school-page', [
            'school' => $school,
            'active' => 'school-page',
        ]);
    }

    public function updateSchoolPage(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $school = School::query()->where('id', $schoolId)->firstOrFail();

        $allowedThemes = ['blue', 'green', 'indigo', 'slate', 'teal', 'amber', 'rose', 'purple', 'emerald', 'sky'];
        $payload = $request->validate([
            'name' => ['nullable', 'string', 'max:150'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'theme' => ['nullable', 'string', 'max:40', Rule::in($allowedThemes)],
            // Cropped data (from Cropper.js)
            'logo_data' => ['nullable', 'string', 'max:10485760'],
            'cover_data' => ['nullable', 'string', 'max:10485760'],
            // Fallback: direct file uploads if cropping is unavailable
            'logo' => ['nullable', 'image', 'max:5120'],
            'cover' => ['nullable', 'image', 'max:10240'],
            'remove_logo' => ['nullable', 'in:1,yes,true'],
            'remove_cover' => ['nullable', 'in:1,yes,true'],
            // Public page content
            'intro' => ['nullable', 'string', 'max:2000'],
            'tag_primary' => ['nullable', 'string', 'max:120'],
            'tag_neutral' => ['nullable', 'string', 'max:120'],
            'tag_accent' => ['nullable', 'string', 'max:120'],
            'fact1_label' => ['nullable', 'string', 'max:80'],
            'fact1_value' => ['nullable', 'string', 'max:80'],
            'fact1_caption' => ['nullable', 'string', 'max:160'],
            'fact2_label' => ['nullable', 'string', 'max:80'],
            'fact2_value' => ['nullable', 'string', 'max:80'],
            'fact2_caption' => ['nullable', 'string', 'max:160'],
            'fact3_label' => ['nullable', 'string', 'max:80'],
            'fact3_value' => ['nullable', 'string', 'max:80'],
            'fact3_caption' => ['nullable', 'string', 'max:160'],
            'campus_title' => ['nullable', 'string', 'max:160'],
            'campus_bullet1' => ['nullable', 'string', 'max:255'],
            'campus_bullet2' => ['nullable', 'string', 'max:255'],
            'campus_bullet3' => ['nullable', 'string', 'max:255'],
            'campus_bullet4' => ['nullable', 'string', 'max:255'],
        ]);

        $dir = "schools/{$schoolId}";

        if (! empty($payload['remove_logo'])) {
            if ($school->logo_path && Storage::disk('public')->exists($school->logo_path)) {
                Storage::disk('public')->delete($school->logo_path);
            }
            $school->logo_path = null;
        } elseif (! empty($payload['logo_data']) && preg_match('/^data:image\/(jpeg|png|gif|webp);base64,/', $payload['logo_data'], $m)) {
            if ($school->logo_path && Storage::disk('public')->exists($school->logo_path)) {
                Storage::disk('public')->delete($school->logo_path);
            }
            $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $payload['logo_data']), true);
            if ($data !== false && strlen($data) > 0 && strlen($data) <= 5 * 1024 * 1024) {
                $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                $path = "{$dir}/logo.{$ext}";
                Storage::disk('public')->put($path, $data);
                $school->logo_path = $path;
            }
        } elseif ($request->hasFile('logo')) {
            if ($school->logo_path && Storage::disk('public')->exists($school->logo_path)) {
                Storage::disk('public')->delete($school->logo_path);
            }
            $file = $request->file('logo');
            $ext = $file ? ($file->getClientOriginalExtension() ?: 'jpg') : 'jpg';
            $path = $file ? $file->storeAs($dir, 'logo.' . $ext, 'public') : null;
            if ($path) {
                $school->logo_path = $path;
            }
        }

        if (! empty($payload['remove_cover'])) {
            if ($school->cover_image_path && Storage::disk('public')->exists($school->cover_image_path)) {
                Storage::disk('public')->delete($school->cover_image_path);
            }
            $school->cover_image_path = null;
        } elseif (! empty($payload['cover_data']) && preg_match('/^data:image\/(jpeg|png|gif|webp);base64,/', $payload['cover_data'], $m)) {
            if ($school->cover_image_path && Storage::disk('public')->exists($school->cover_image_path)) {
                Storage::disk('public')->delete($school->cover_image_path);
            }
            $data = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $payload['cover_data']), true);
            if ($data !== false && strlen($data) > 0 && strlen($data) <= 5 * 1024 * 1024) {
                $ext = $m[1] === 'jpeg' ? 'jpg' : $m[1];
                $path = "{$dir}/cover.{$ext}";
                Storage::disk('public')->put($path, $data);
                $school->cover_image_path = $path;
            }
        } elseif ($request->hasFile('cover')) {
            if ($school->cover_image_path && Storage::disk('public')->exists($school->cover_image_path)) {
                Storage::disk('public')->delete($school->cover_image_path);
            }
            $file = $request->file('cover');
            $ext = $file ? ($file->getClientOriginalExtension() ?: 'jpg') : 'jpg';
            $path = $file ? $file->storeAs($dir, 'cover.' . $ext, 'public') : null;
            if ($path) {
                $school->cover_image_path = $path;
            }
        }

        if (array_key_exists('theme', $payload)) {
            $school->theme = $payload['theme'] ?: null;
        }
        if (array_key_exists('name', $payload) && $payload['name'] !== null) {
            $school->name = $payload['name'];
        }
        if (array_key_exists('short_description', $payload)) {
            $school->short_description = $payload['short_description'] ?? '';
        }

        $school->save();

        // Update or create public profile content
        $profile = $school->profile ?: new SchoolProfile(['school_id' => $schoolId]);
        $profile->intro = $payload['intro'] ?? $profile->intro;
        $profile->tag_primary = $payload['tag_primary'] ?? $profile->tag_primary;
        $profile->tag_neutral = $payload['tag_neutral'] ?? $profile->tag_neutral;
        $profile->tag_accent = $payload['tag_accent'] ?? $profile->tag_accent;
        $profile->fact1_label = $payload['fact1_label'] ?? $profile->fact1_label;
        $profile->fact1_value = $payload['fact1_value'] ?? $profile->fact1_value;
        $profile->fact1_caption = $payload['fact1_caption'] ?? $profile->fact1_caption;
        $profile->fact2_label = $payload['fact2_label'] ?? $profile->fact2_label;
        $profile->fact2_value = $payload['fact2_value'] ?? $profile->fact2_value;
        $profile->fact2_caption = $payload['fact2_caption'] ?? $profile->fact2_caption;
        $profile->fact3_label = $payload['fact3_label'] ?? $profile->fact3_label;
        $profile->fact3_value = $payload['fact3_value'] ?? $profile->fact3_value;
        $profile->fact3_caption = $payload['fact3_caption'] ?? $profile->fact3_caption;
        $profile->campus_title = $payload['campus_title'] ?? $profile->campus_title;
        $profile->campus_bullet1 = $payload['campus_bullet1'] ?? $profile->campus_bullet1;
        $profile->campus_bullet2 = $payload['campus_bullet2'] ?? $profile->campus_bullet2;
        $profile->campus_bullet3 = $payload['campus_bullet3'] ?? $profile->campus_bullet3;
        $profile->campus_bullet4 = $payload['campus_bullet4'] ?? $profile->campus_bullet4;
        $profile->save();

        return redirect('/tenant/school-page')->with('status', 'School page updated.');
    }

    private function resolveEnrollmentSemester(int $schoolId): ?Semester
    {
        return Semester::query()
            ->where('school_id', $schoolId)
            ->whereIn('status', ['enrollment_open', 'in_progress'])
            ->orderByRaw("CASE WHEN status = 'enrollment_open' THEN 0 WHEN status = 'in_progress' THEN 1 ELSE 9 END")
            ->orderByDesc('start_date')
            ->first();
    }

    /**
     * Build selectable schedule options by subject for a specific program/year/semester.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildSubjectScheduleRows(int $schoolId, int $semesterId, int $programId, int $yearLevel): array
    {
        $classGroups = ClassGroup::query()
            ->where('school_id', $schoolId)
            ->where('semester_id', $semesterId)
            ->where('program_id', $programId)
            ->where('year_level', $yearLevel)
            ->where('status', '!=', 'archived')
            ->orderBy('name')
            ->get();

        if ($classGroups->isEmpty()) {
            return [];
        }

        $classGroupById = $classGroups->keyBy('id');
        $classGroupIds = $classGroups->pluck('id')->map(fn ($id): int => (int) $id)->all();

        $sessions = ClassSession::query()
            ->with(['subject:id,code,title,units', 'teacher:id,full_name'])
            ->where('school_id', $schoolId)
            ->whereIn('class_group_id', $classGroupIds)
            ->whereIn('status', ['draft', 'locked'])
            ->orderBy('subject_id')
            ->orderBy('class_group_id')
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        if ($sessions->isEmpty()) {
            return [];
        }

        $subjectIds = $sessions
            ->pluck('subject_id')
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        $offerings = SubjectOffering::query()
            ->where('school_id', $schoolId)
            ->where('semester_id', $semesterId)
            ->whereIn('subject_id', $subjectIds)
            ->orderByRaw("CASE WHEN status = 'open' THEN 0 WHEN status = 'draft' THEN 1 ELSE 9 END")
            ->orderBy('id')
            ->get()
            ->groupBy('subject_id')
            ->map(fn ($rows) => $rows->first());

        $sections = Section::query()
            ->where('school_id', $schoolId)
            ->whereIn('subject_offering_id', $offerings->pluck('id')->filter()->all())
            ->get();
        $sectionsByOfferingAndIdentifier = [];
        foreach ($sections as $section) {
            $sectionsByOfferingAndIdentifier[(int) $section->subject_offering_id . '|' . (string) $section->identifier] = $section;
        }

        $activeStatuses = ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'];
        $enrollmentCountsBySectionId = Enrollment::query()
            ->where('school_id', $schoolId)
            ->where('semester_id', $semesterId)
            ->whereIn('status', $activeStatuses)
            ->whereIn('section_id', $sections->pluck('id')->all())
            ->select('section_id', DB::raw('COUNT(*) as total'))
            ->groupBy('section_id')
            ->pluck('total', 'section_id')
            ->map(fn ($total): int => (int) $total)
            ->all();

        $rowsBySubject = [];
        foreach ($sessions->groupBy('subject_id') as $subjectId => $subjectSessions) {
            $subjectId = (int) $subjectId;
            $subject = $subjectSessions->first()?->subject;
            if (! $subject) {
                continue;
            }

            $row = [
                'subject_id' => $subjectId,
                'subject_code' => (string) $subject->code,
                'subject_title' => (string) $subject->title,
                'units' => (float) $subject->units,
                'options' => [],
            ];

            foreach ($subjectSessions->groupBy('class_group_id') as $classGroupId => $groupSessions) {
                $classGroupId = (int) $classGroupId;
                /** @var ClassGroup|null $classGroup */
                $classGroup = $classGroupById->get($classGroupId);
                if (! $classGroup) {
                    continue;
                }

                $offering = $offerings->get($subjectId);
                $sectionIdentifier = $this->buildSectionIdentifierForClassGroup($classGroupId);
                $section = $offering
                    ? ($sectionsByOfferingAndIdentifier[(int) $offering->id . '|' . $sectionIdentifier] ?? null)
                    : null;
                $enrolledCount = $section ? (int) ($enrollmentCountsBySectionId[(int) $section->id] ?? 0) : 0;
                $capacity = max((int) ($classGroup->student_capacity ?? 0), 1);
                $remaining = max($capacity - $enrolledCount, 0);

                $sessionBlocks = $groupSessions->map(function (ClassSession $session): array {
                    $start = (string) substr((string) $session->start_time, 0, 5);
                    $end = (string) substr((string) $session->end_time, 0, 5);
                    $dayOfWeek = (int) $session->day_of_week;
                    $dayLabel = match ($dayOfWeek) {
                        1 => 'Mon',
                        2 => 'Tue',
                        3 => 'Wed',
                        4 => 'Thu',
                        5 => 'Fri',
                        6 => 'Sat',
                        7 => 'Sun',
                        default => 'Day ' . $dayOfWeek,
                    };

                    return [
                        'day_of_week' => $dayOfWeek,
                        'day' => $dayLabel,
                        'start_time' => $start,
                        'end_time' => $end,
                        'start_min' => ((int) substr($start, 0, 2) * 60) + (int) substr($start, 3, 2),
                        'end_min' => ((int) substr($end, 0, 2) * 60) + (int) substr($end, 3, 2),
                        'teacher_name' => (string) ($session->teacher?->full_name ?? 'TBA'),
                    ];
                })->values()->all();

                $row['options'][] = [
                    'subject_id' => $subjectId,
                    'subject_code' => (string) $subject->code,
                    'subject_title' => (string) $subject->title,
                    'class_group_id' => $classGroupId,
                    'class_group_name' => (string) $classGroup->name,
                    'year_level' => (int) $classGroup->year_level,
                    'capacity' => $capacity,
                    'enrolled_count' => $enrolledCount,
                    'remaining' => $remaining,
                    'sessions' => $sessionBlocks,
                ];
            }

            $row['options'] = collect($row['options'])
                ->sortBy(fn (array $option): string => (string) ($option['class_group_name'] ?? ''))
                ->values()
                ->all();

            if ($row['options'] !== []) {
                $rowsBySubject[$subjectId] = $row;
            }
        }

        return collect($rowsBySubject)
            ->sortBy(fn (array $row): string => (string) ($row['subject_code'] ?? ''))
            ->values()
            ->all();
    }

    /**
     * @param array<int, array<string, mixed>> $selectedOptions
     */
    private function detectScheduleConflictMessage(array $selectedOptions): ?string
    {
        $placed = [];
        foreach ($selectedOptions as $option) {
            $subjectCode = (string) ($option['subject_code'] ?? 'SUBJ');
            $classGroupName = (string) ($option['class_group_name'] ?? '?');
            foreach ((array) ($option['sessions'] ?? []) as $session) {
                $day = (int) ($session['day_of_week'] ?? 0);
                $startMin = (int) ($session['start_min'] ?? 0);
                $endMin = (int) ($session['end_min'] ?? 0);
                foreach ($placed as $existing) {
                    if ($day !== (int) $existing['day_of_week']) {
                        continue;
                    }
                    if ($startMin < (int) $existing['end_min'] && $endMin > (int) $existing['start_min']) {
                        return 'Schedule conflict between '
                            . $subjectCode . ' (' . ($session['day'] ?? 'Day') . ' ' . ($session['start_time'] ?? '') . '-' . ($session['end_time'] ?? '') . ', Group ' . $classGroupName . ') and '
                            . $existing['subject_code'] . ' (' . $existing['day'] . ' ' . $existing['start_time'] . '-' . $existing['end_time'] . ', Group ' . $existing['class_group_name'] . ').';
                    }
                }

                $placed[] = [
                    'subject_code' => $subjectCode,
                    'class_group_name' => $classGroupName,
                    'day_of_week' => $day,
                    'day' => (string) ($session['day'] ?? 'Day'),
                    'start_time' => (string) ($session['start_time'] ?? ''),
                    'end_time' => (string) ($session['end_time'] ?? ''),
                    'start_min' => $startMin,
                    'end_min' => $endMin,
                ];
            }
        }

        return null;
    }

    private function buildSectionIdentifierForClassGroup(int $classGroupId): string
    {
        return 'CG-' . $classGroupId;
    }

    /**
     * @param array<int, string> $sectionIdentifiers
     * @return array<int, string>
     */
    private function classGroupNamesForSectionIdentifiers(int $schoolId, array $sectionIdentifiers): array
    {
        $classGroupIds = collect($sectionIdentifiers)
            ->map(fn ($identifier): int => $this->extractClassGroupIdFromSectionIdentifier((string) $identifier))
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        if ($classGroupIds === []) {
            return [];
        }

        return ClassGroup::query()
            ->where('school_id', $schoolId)
            ->whereIn('id', $classGroupIds)
            ->pluck('name', 'id')
            ->map(fn ($name): string => (string) $name)
            ->all();
    }

    /**
     * @param array<int, string> $classGroupNameById
     */
    private function resolveClassGroupLabelFromSectionIdentifier(?string $sectionIdentifier, array $classGroupNameById): string
    {
        $identifier = trim((string) $sectionIdentifier);
        if ($identifier === '') {
            return 'N/A';
        }

        $classGroupId = $this->extractClassGroupIdFromSectionIdentifier($identifier);
        if ($classGroupId > 0 && isset($classGroupNameById[$classGroupId])) {
            return (string) $classGroupNameById[$classGroupId];
        }

        return $identifier;
    }

    private function extractClassGroupIdFromSectionIdentifier(string $identifier): int
    {
        if (preg_match('/^CG-(\d+)$/i', trim($identifier), $matches) === 1) {
            return (int) ($matches[1] ?? 0);
        }

        return 0;
    }

    /**
     * @return array{program_id:?int,department_id:?int,section_id:?int,scope_student_user_id:?int}
     */
    private function resolveScopeColumns(int $schoolId, string $scopeType, ?int $scopeId): array
    {
        $columns = [
            'program_id' => null,
            'department_id' => null,
            'section_id' => null,
            'scope_student_user_id' => null,
        ];

        if ($scopeType === 'all') {
            return $columns;
        }

        if ($scopeId === null || $scopeId <= 0) {
            throw ValidationException::withMessages([
                'scope_id' => 'Scope ID is required for the selected scope type.',
            ]);
        }

        $scopeExists = match ($scopeType) {
            'program' => Program::query()
                ->where('school_id', $schoolId)
                ->where('id', $scopeId)
                ->exists(),
            'department' => Department::query()
                ->where('school_id', $schoolId)
                ->where('id', $scopeId)
                ->exists(),
            'section' => Section::query()
                ->where('school_id', $schoolId)
                ->where('id', $scopeId)
                ->exists(),
            'student' => UserRole::query()
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.school_id', $schoolId)
                ->where('user_roles.user_id', $scopeId)
                ->where('user_roles.is_active', true)
                ->where('roles.code', 'student')
                ->exists(),
            default => false,
        };

        if (! $scopeExists) {
            throw ValidationException::withMessages([
                'scope_id' => 'Scope record does not belong to the active school.',
            ]);
        }

        return match ($scopeType) {
            'program' => [...$columns, 'program_id' => $scopeId],
            'department' => [...$columns, 'department_id' => $scopeId],
            'section' => [...$columns, 'section_id' => $scopeId],
            'student' => [...$columns, 'scope_student_user_id' => $scopeId],
            default => $columns,
        };
    }

    private function resolveSystemActorUserId(int $schoolId, int $fallbackUserId): int
    {
        $candidate = (int) DB::table('user_roles')
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.is_active', true)
            ->whereIn('roles.code', ['school_admin', 'registrar_staff', 'dean', 'finance_staff', 'teacher'])
            ->orderByRaw("CASE roles.code
                WHEN 'school_admin' THEN 0
                WHEN 'registrar_staff' THEN 1
                WHEN 'dean' THEN 2
                WHEN 'finance_staff' THEN 3
                WHEN 'teacher' THEN 4
                ELSE 9 END")
            ->orderBy('user_roles.id')
            ->value('user_roles.user_id');

        return $candidate > 0 ? $candidate : $fallbackUserId;
    }

    private function generateStudentNo(int $schoolId): string
    {
        $prefix = 'S' . now()->format('ym');
        for ($i = 0; $i < 5; $i++) {
            $candidate = $prefix . str_pad((string) random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            $exists = DB::table('student_profiles')
                ->where('school_id', $schoolId)
                ->where('student_no', $candidate)
                ->exists();
            if (! $exists) {
                return $candidate;
            }
        }

        return $prefix . now()->format('His');
    }

    private function assignAllNonStudentRolesForSchoolAdmin(int $schoolId, int $userId, int $actorUserId): void
    {
        $nonStudentRoleIds = Role::query()
            ->where('code', '!=', 'student')
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        foreach ($nonStudentRoleIds as $roleId) {
            UserRole::query()->updateOrCreate(
                [
                    'user_id' => $userId,
                    'school_id' => $schoolId,
                    'role_id' => $roleId,
                ],
                [
                    'is_active' => true,
                    'assigned_by_user_id' => $actorUserId,
                    'assigned_at' => now(),
                ]
            );
        }

        $studentRoleId = Role::query()->where('code', 'student')->value('id');
        if ($studentRoleId !== null) {
            UserRole::query()
                ->where('user_id', $userId)
                ->where('school_id', $schoolId)
                ->where('role_id', (int) $studentRoleId)
                ->update(['is_active' => false]);
        }
    }

    public function adminCreateAcademicYear(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:planned,active,closed'],
        ]);

        AcademicYear::query()->create([
            'school_id' => (int) $request->attributes->get('active_school_id'),
            'name' => $payload['name'],
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'status' => $payload['status'],
        ]);

        return back()->with('status', 'Academic year created.');
    }

    public function adminCreateCollege(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:150'],
        ]);

        College::query()->create([
            'school_id' => (int) $request->attributes->get('active_school_id'),
            'code' => $payload['code'],
            'name' => $payload['name'],
            'status' => 'active',
        ]);

        return back()->with('status', 'College created.');
    }

    public function adminCreateDepartment(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $payload = $request->validate([
            'college_id' => ['nullable', 'integer', 'exists:colleges,id'],
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:150'],
        ]);

        $collegeId = null;
        if (isset($payload['college_id'])) {
            $collegeId = College::query()
                ->where('school_id', $schoolId)
                ->whereKey((int) $payload['college_id'])
                ->value('id');
        } else {
            $collegeId = College::query()
                ->where('school_id', $schoolId)
                ->orderBy('id')
                ->value('id');
        }

        if ($collegeId === null) {
            return back()->withErrors(['department' => 'No college found for this school.']);
        }

        Department::query()->create([
            'school_id' => $schoolId,
            'college_id' => (int) $collegeId,
            'code' => $payload['code'],
            'name' => $payload['name'],
            'status' => 'active',
        ]);

        return back()->with('status', 'Department created.');
    }

    public function adminCreateProgram(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $payload = $request->validate([
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:150'],
            'degree_level' => ['required', 'in:certificate,diploma,bachelor,master,doctorate'],
            'max_units_per_semester' => ['nullable', 'numeric', 'min:1', 'max:99'],
        ]);

        $departmentBelongsToSchool = Department::query()
            ->where('school_id', $schoolId)
            ->whereKey((int) $payload['department_id'])
            ->exists();
        if (! $departmentBelongsToSchool) {
            return back()->withErrors(['department_id' => 'Department does not belong to your school.'])->withInput();
        }

        Program::query()->create([
            'school_id' => $schoolId,
            'department_id' => (int) $payload['department_id'],
            'code' => $payload['code'],
            'name' => $payload['name'],
            'degree_level' => $payload['degree_level'],
            'max_units_per_semester' => $payload['max_units_per_semester'] ?? 24.0,
            'status' => 'active',
        ]);

        return back()->with('status', 'Program created.');
    }

    public function adminCreateSubject(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $payload = $request->validate([
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')->where(fn ($query) => $query->where('school_id', $schoolId)),
            ],
            'title' => ['required', 'string', 'max:200'],
            'units' => ['required', 'numeric', 'min:0.1'],
            'price_per_subject' => ['nullable', 'numeric', 'min:0'],
            'weekly_hours' => ['required', 'numeric', 'min:0.1'],
            'duration_weeks' => ['required', 'integer', 'min:1'],
            'prerequisite_subject_ids' => ['nullable', 'array'],
            'prerequisite_subject_ids.*' => ['nullable', 'integer', 'distinct', 'exists:subjects,id'],
        ]);

        if (isset($payload['department_id'])) {
            $departmentBelongsToSchool = Department::query()
                ->where('school_id', $schoolId)
                ->whereKey((int) $payload['department_id'])
                ->exists();
            if (! $departmentBelongsToSchool) {
                return back()->withErrors(['department_id' => 'Department does not belong to your school.'])->withInput();
            }
        }

        $rawPrerequisiteIds = collect($payload['prerequisite_subject_ids'] ?? [])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values();

        $subjectPrice = $this->resolveSubjectPriceForSchool(
            schoolId: $schoolId,
            units: (float) $payload['units'],
            fallbackPrice: (float) ($payload['price_per_subject'] ?? 0)
        );

        if ($rawPrerequisiteIds->isNotEmpty()) {
            $ownedPrerequisiteIds = Subject::query()
                ->where('school_id', $schoolId)
                ->whereIn('id', $rawPrerequisiteIds->all())
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();

            if (count($ownedPrerequisiteIds) !== $rawPrerequisiteIds->count()) {
                return back()->withErrors(['prerequisite_subject_ids' => 'Prerequisites must belong to your school.'])->withInput();
            }
        }

        $subject = Subject::query()->create([
            'school_id' => $schoolId,
            'department_id' => isset($payload['department_id']) ? (int) $payload['department_id'] : null,
            'code' => $payload['code'],
            'title' => $payload['title'],
            'units' => $payload['units'],
            'price_per_subject' => $subjectPrice,
            'weekly_hours' => $payload['weekly_hours'],
            'duration_weeks' => $payload['duration_weeks'],
            'status' => 'active',
        ]);

        $prerequisiteIds = $rawPrerequisiteIds
            ->reject(fn (int $id): bool => $id === (int) $subject->id)
            ->unique()
            ->values();

        if ($prerequisiteIds->isNotEmpty()) {
            $rows = $prerequisiteIds
                ->map(fn (int $prerequisiteId): array => [
                    'school_id' => $schoolId,
                    'subject_id' => (int) $subject->id,
                    'prerequisite_subject_id' => $prerequisiteId,
                ])
                ->all();

            DB::table('subject_prerequisites')->upsert(
                $rows,
                ['school_id', 'subject_id', 'prerequisite_subject_id'],
                []
            );
        }

        return back()->with('status', 'Subject created.');
    }

    public function adminUpdateSubject(Request $request, Subject $subject): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        if ((int) $subject->school_id !== $schoolId) {
            return back()->withErrors(['subject' => 'Subject does not belong to your school.']);
        }

        $payload = $request->validate([
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')
                    ->where(fn ($query) => $query->where('school_id', $schoolId))
                    ->ignore((int) $subject->id),
            ],
            'title' => ['required', 'string', 'max:200'],
            'units' => ['required', 'numeric', 'min:0.1'],
            'price_per_subject' => ['nullable', 'numeric', 'min:0'],
            'weekly_hours' => ['required', 'numeric', 'min:0.1'],
            'duration_weeks' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
            'prerequisite_subject_ids' => ['nullable', 'array'],
            'prerequisite_subject_ids.*' => ['nullable', 'integer', 'distinct', 'exists:subjects,id'],
        ]);

        if (isset($payload['department_id'])) {
            $departmentBelongsToSchool = Department::query()
                ->where('school_id', $schoolId)
                ->whereKey((int) $payload['department_id'])
                ->exists();
            if (! $departmentBelongsToSchool) {
                return back()->withErrors(['department_id' => 'Department does not belong to your school.'])->withInput();
            }
        }

        $subjectPrice = $this->resolveSubjectPriceForSchool(
            schoolId: $schoolId,
            units: (float) $payload['units'],
            fallbackPrice: (float) ($payload['price_per_subject'] ?? 0)
        );

        $subject->department_id = isset($payload['department_id']) ? (int) $payload['department_id'] : null;
        $subject->code = $payload['code'];
        $subject->title = $payload['title'];
        $subject->units = $payload['units'];
        $subject->price_per_subject = $subjectPrice;
        $subject->weekly_hours = $payload['weekly_hours'];
        $subject->duration_weeks = (int) $payload['duration_weeks'];
        $subject->status = $payload['status'];
        $subject->save();

        $prerequisiteIds = collect($payload['prerequisite_subject_ids'] ?? [])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id): int => (int) $id)
            ->filter(fn (int $id): bool => $id > 0 && $id !== (int) $subject->id)
            ->unique()
            ->values();

        if ($prerequisiteIds->isNotEmpty()) {
            $ownedPrerequisiteIds = Subject::query()
                ->where('school_id', $schoolId)
                ->whereIn('id', $prerequisiteIds->all())
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
                ->all();

            if (count($ownedPrerequisiteIds) !== $prerequisiteIds->count()) {
                return back()->withErrors(['prerequisite_subject_ids' => 'Prerequisites must belong to your school.'])->withInput();
            }
        }

        DB::table('subject_prerequisites')
            ->where('school_id', $schoolId)
            ->where('subject_id', (int) $subject->id)
            ->delete();

        if ($prerequisiteIds->isNotEmpty()) {
            $rows = $prerequisiteIds
                ->map(fn (int $prerequisiteId): array => [
                    'school_id' => $schoolId,
                    'subject_id' => (int) $subject->id,
                    'prerequisite_subject_id' => $prerequisiteId,
                ])
                ->all();

            DB::table('subject_prerequisites')->insert($rows);
        }

        return back()->with('status', 'Subject updated.');
    }

    public function adminAddCurriculumItem(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');
        $actorUserId = (int) $request->attributes->get('actor_user_id');

        $payload = $request->validate([
            'program_id' => ['required', 'integer', 'exists:programs,id'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'year_level' => ['required', 'integer', 'min:1', 'max:8'],
        ]);

        $program = Program::query()->where('school_id', $schoolId)->find((int) $payload['program_id']);
        $semester = Semester::query()->where('school_id', $schoolId)->find((int) $payload['semester_id']);
        $subject = Subject::query()->where('school_id', $schoolId)->find((int) $payload['subject_id']);
        if ($program === null || $semester === null || $subject === null) {
            return back()->withErrors(['curriculum' => 'Selected program/semester/subject must belong to your school.']);
        }

        ProgramCurriculumItem::query()->updateOrCreate(
            [
                'school_id' => $schoolId,
                'program_id' => (int) $payload['program_id'],
                'semester_id' => (int) $payload['semester_id'],
                'subject_id' => (int) $payload['subject_id'],
                'year_level' => (int) $payload['year_level'],
            ],
            [
                'status' => 'active',
                'created_by_user_id' => $actorUserId,
            ]
        );

        return back()->with('status', 'Subject added to curriculum plan.');
    }

    public function adminRemoveCurriculumItem(Request $request, ProgramCurriculumItem $curriculumItem): RedirectResponse
    {
        if ((int) $curriculumItem->school_id !== (int) $request->attributes->get('active_school_id')) {
            return back()->withErrors(['curriculum' => 'Curriculum item does not belong to your school.']);
        }

        $curriculumItem->delete();

        return back()->with('status', 'Curriculum item removed.');
    }

    public function adminCreateSemester(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $payload = $request->validate([
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'term_code' => ['required', 'in:1ST,2ND,SUMMER'],
            'name' => ['required', 'string', 'max:30'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:planned,enrollment_open,in_progress,closed'],
        ]);

        $academicYearBelongsToSchool = AcademicYear::query()
            ->where('school_id', $schoolId)
            ->whereKey((int) $payload['academic_year_id'])
            ->exists();
        if (! $academicYearBelongsToSchool) {
            return back()->withErrors(['academic_year_id' => 'Academic year does not belong to your school.'])->withInput();
        }

        Semester::query()->create([
            'school_id' => $schoolId,
            'academic_year_id' => (int) $payload['academic_year_id'],
            'term_code' => $payload['term_code'],
            'name' => $payload['name'],
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'status' => $payload['status'],
        ]);

        return back()->with('status', 'Semester created.');
    }

    public function adminCreateOffering(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $payload = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'teacher_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'schedule_summary' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:draft,open,closed,cancelled'],
        ]);

        $subject = Subject::query()
            ->where('school_id', $schoolId)
            ->find((int) $payload['subject_id']);
        if ($subject === null) {
            return back()->withErrors(['subject_id' => 'Subject does not belong to your school.'])->withInput();
        }

        $semester = Semester::query()
            ->where('school_id', $schoolId)
            ->find((int) $payload['semester_id']);
        if ($semester === null) {
            return back()->withErrors(['semester_id' => 'Semester does not belong to your school.'])->withInput();
        }

        $teacherUserId = isset($payload['teacher_user_id']) ? (int) $payload['teacher_user_id'] : null;
        if ($teacherUserId !== null) {
            $isActiveTeacher = UserRole::query()
                ->join('roles', 'roles.id', '=', 'user_roles.role_id')
                ->where('user_roles.school_id', $schoolId)
                ->where('user_roles.user_id', $teacherUserId)
                ->where('user_roles.is_active', true)
                ->where('roles.code', 'teacher')
                ->exists();
            if (! $isActiveTeacher) {
                return back()->withErrors(['teacher_user_id' => 'Selected teacher has no active teacher role in this school.'])->withInput();
            }

            $teacherCanTeachSubject = DB::table('teacher_subjects')
                ->where('school_id', $schoolId)
                ->where('teacher_user_id', $teacherUserId)
                ->where('subject_id', (int) $subject->id)
                ->exists();
            if (! $teacherCanTeachSubject) {
                return back()->withErrors(['teacher_user_id' => 'Selected teacher is not assigned to this subject.'])->withInput();
            }
        }

        SubjectOffering::query()->create([
            'school_id' => $schoolId,
            'subject_id' => (int) $subject->id,
            'semester_id' => (int) $semester->id,
            'assigned_teacher_user_id' => $teacherUserId,
            'schedule_summary' => $payload['schedule_summary'] ?? null,
            'status' => $payload['status'],
            'created_by_user_id' => (int) $request->attributes->get('actor_user_id'),
        ]);

        return back()->with('status', 'Subject offering created.');
    }

    public function adminCreateSection(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $payload = $request->validate([
            'subject_offering_id' => ['required', 'integer', 'exists:subject_offerings,id'],
            'identifier' => ['required', 'string', 'max:20'],
            'max_capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'status' => ['required', 'in:open,closed,cancelled'],
        ]);

        $offeringBelongsToSchool = SubjectOffering::query()
            ->where('school_id', $schoolId)
            ->whereKey((int) $payload['subject_offering_id'])
            ->exists();
        if (! $offeringBelongsToSchool) {
            return back()->withErrors(['subject_offering_id' => 'Subject offering does not belong to your school.'])->withInput();
        }

        Section::query()->create([
            'school_id' => $schoolId,
            'subject_offering_id' => (int) $payload['subject_offering_id'],
            'identifier' => $payload['identifier'],
            'max_capacity' => (int) $payload['max_capacity'],
            'status' => $payload['status'],
        ]);

        return back()->with('status', 'Section created.');
    }

    public function deanAssignTeacher(Request $request): RedirectResponse
    {
        $schoolId = (int) $request->attributes->get('active_school_id');

        $payload = $request->validate([
            'subject_offering_id' => ['required', 'integer', 'exists:subject_offerings,id'],
            'teacher_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $subjectOffering = SubjectOffering::query()->findOrFail((int) $payload['subject_offering_id']);

        if ((int) $subjectOffering->school_id !== $schoolId) {
            return back()->withErrors(['admin' => 'Offering does not belong to your school.']);
        }

        $teacherUserId = (int) $payload['teacher_user_id'];
        $isActiveTeacher = UserRole::query()
            ->join('roles', 'roles.id', '=', 'user_roles.role_id')
            ->where('user_roles.school_id', $schoolId)
            ->where('user_roles.user_id', $teacherUserId)
            ->where('user_roles.is_active', true)
            ->where('roles.code', 'teacher')
            ->exists();
        if (! $isActiveTeacher) {
            return back()->withErrors(['teacher_user_id' => 'Selected user has no active teacher role in this school.'])->withInput();
        }

        $teacherCanTeachSubject = DB::table('teacher_subjects')
            ->where('school_id', $schoolId)
            ->where('teacher_user_id', $teacherUserId)
            ->where('subject_id', (int) $subjectOffering->subject_id)
            ->exists();
        if (! $teacherCanTeachSubject) {
            return back()->withErrors(['teacher_user_id' => 'Selected teacher is not assigned to this subject.'])->withInput();
        }

        $subjectOffering->assigned_teacher_user_id = (int) $payload['teacher_user_id'];
        $subjectOffering->save();

        return back()->with('status', 'Teacher assigned to offering.');
    }

    private function resolveGeneralPricePerCourseUnit(int $schoolId): ?float
    {
        $pricePerCourseUnit = FinanceFeeSetting::query()
            ->where('school_id', $schoolId)
            ->where('status', 'active')
            ->whereNull('semester_id')
            ->whereNull('academic_year_id')
            ->whereNull('program_id')
            ->orderByDesc('id')
            ->value('price_per_course_unit');

        return $pricePerCourseUnit !== null ? (float) $pricePerCourseUnit : null;
    }

    private function resolveSubjectPriceForSchool(int $schoolId, float $units, float $fallbackPrice): float
    {
        $pricePerCourseUnit = $this->resolveGeneralPricePerCourseUnit($schoolId);
        if ($pricePerCourseUnit === null) {
            return round($fallbackPrice, 2);
        }

        return round($units * $pricePerCourseUnit, 2);
    }

    private function recalculateCoursePricesByUnitRate(int $schoolId, float $pricePerCourseUnit): void
    {
        $pricePerCourseUnitSql = number_format($pricePerCourseUnit, 4, '.', '');

        Subject::query()
            ->where('school_id', $schoolId)
            ->update([
                'price_per_subject' => DB::raw('ROUND(units * ' . $pricePerCourseUnitSql . ', 2)'),
            ]);
    }
}
