@extends('layouts.app')

@section('title', 'Dashboard - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
@php
    $permissionCodes = (array) ($permissionCodes ?? session('permission_codes', []));
    $hasAnyPermission = function (array $required) use ($permissionCodes): bool {
        foreach ($required as $code) {
            if (in_array($code, $permissionCodes, true)) {
                return true;
            }
        }

        return false;
    };
@endphp
@php
    $u = auth()->user();
    $raw = trim((string) ($u->full_name ?? ''));
    $school = session('active_school_name');
    $clean = $raw;
    if ($school !== null && $school !== '' && strcasecmp($raw, $school . ' Admin') === 0) {
        $clean = $school;
    }
    $dashUserLabel = $clean;
    if ($school !== null && $school !== '' && strcasecmp($clean, $school) === 0) {
        $dashUserLabel = (string) ($u->email ?? $clean);
    }
    if ($dashUserLabel === '') {
        $dashUserLabel = (string) ($u->email ?? 'User');
    }
    if (str_contains($dashUserLabel, '@')) {
        $welcomeFirstName = ucfirst(explode('@', $dashUserLabel)[0]);
    } else {
        $welcomeFirstName = explode(' ', $dashUserLabel)[0] ?? $dashUserLabel;
    }
    $dashAvatarChar = strtoupper(mb_substr($dashUserLabel, 0, 1));
    $dashAvatarUrl = null;
    if ($u instanceof \App\Models\User && trim((string) ($u->profile_photo_path ?? '')) !== '') {
        $avatarVersion = urlencode((string) ($u->updated_at?->timestamp ?? time()));
        $dashAvatarUrl = url('/tenant/settings/account/avatar/view?v=' . $avatarVersion);
    }
@endphp
<div class="app-shell dashboard-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'dashboard', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body dashboard-body">
            <div class="dashboard-welcome dashboard-welcome--mock">
                <div class="dashboard-welcome-avatar" aria-hidden="true">
                    @if($dashAvatarUrl)
                        <img src="{{ $dashAvatarUrl }}" alt="{{ $dashUserLabel }}">
                    @else
                        {{ $dashAvatarChar }}
                    @endif
                </div>
                <div class="dashboard-welcome-copy">
                    <h1 class="dashboard-welcome-title">Welcome back, <strong>{{ $welcomeFirstName }}</strong></h1>
                    <p class="dashboard-welcome-sub">Here's an overview of your school portal activity.</p>
                </div>
            </div>
                @if (!empty($studentProfile))
                    <div class="stu-profile-chips">
                        @if (!empty($studentProfile->program))
                            <span class="dashboard-badge dashboard-badge--blue">
                                {{ trim(($studentProfile->program->code ?? '') . ' ' . ($studentProfile->program->name ?? ''), ' ') }}
                            </span>
                        @endif
                        @if (!empty($studentProfile->year_level))
                            <span class="dashboard-badge dashboard-badge--purple">Year {{ (int) $studentProfile->year_level }}</span>
                        @endif
                    </div>
                @endif

            @if (session('status'))
                <div class="alert success dashboard-alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert error dashboard-alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- ── STUDENT-ONLY EXPERIENCE ─────────────────────────────────────── --}}
            @php $isStudentOnly = count(array_diff((array) $roleCodes, ['student'])) === 0 && in_array('student', (array) $roleCodes, true); @endphp

            @if ($isStudentOnly)

            {{-- Pending activation notice --}}
            @if (!empty($pendingStudentActivation))
                <div class="stu-alert stu-alert--warning">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                    <div>
                        <strong>Account Pending Activation</strong>
                        <p>Finance must verify your payment, then the registrar will activate your student access.</p>
                    </div>
                </div>
            @endif

            {{-- Enrollment CTA --}}
            @if (!empty($needsProgramEnrollment))
                <div class="stu-alert stu-alert--action">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                    </svg>
                    <div style="flex:1">
                        <strong>Program Enrollment Required</strong>
                        <p>Your account is active. Select your program, subjects, and schedules to complete enrollment.</p>
                    </div>
                    <div class="stu-alert-actions">
                        <a href="{{ url('/tenant/enrollments') }}" class="btn primary sm">Start Enrollment</a>
                        <a href="{{ url('/tenant/billing') }}" class="btn ghost sm">View Payments</a>
                    </div>
                </div>
            @endif

            {{-- ── Quick Stats Row ──────────────────────────────────────────────── --}}
            @php
                $enrolledCount   = isset($myEnrollments) ? $myEnrollments->where('status', 'enrolled')->count() : 0;
                $pendingCount    = isset($myEnrollments) ? $myEnrollments->whereIn('status', ['pending','billing_pending','selected'])->count() : 0;
                $totalSubjects   = isset($myEnrollments) ? $myEnrollments->count() : 0;
                $todayDow        = strtolower(date('l')); // monday, tuesday…
                $dowMap          = ['sunday'=>0,'monday'=>1,'tuesday'=>2,'wednesday'=>3,'thursday'=>4,'friday'=>5,'saturday'=>6];
                $todayOrder      = $dowMap[$todayDow] ?? 0;
                $dayLabelMap     = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];
                $todaySchedules  = collect($studentScheduleRows ?? [])
                    ->filter(fn($r) => (int)($r['day_order'] ?? 0) === $todayOrder)
                    ->values();
            @endphp
            <div class="stu-stats-row">
                <div class="stu-stat-card">
                    <div class="stu-stat-icon stu-stat-icon--blue">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                    </div>
                    <div class="stu-stat-body">
                        <span class="stu-stat-num">{{ $totalSubjects }}</span>
                        <span class="stu-stat-label">Subjects</span>
                    </div>
                </div>
                <div class="stu-stat-card">
                    <div class="stu-stat-icon stu-stat-icon--green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="stu-stat-body">
                        <span class="stu-stat-num">{{ $enrolledCount }}</span>
                        <span class="stu-stat-label">Enrolled</span>
                    </div>
                </div>
                <div class="stu-stat-card">
                    <div class="stu-stat-icon stu-stat-icon--amber">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <div class="stu-stat-body">
                        <span class="stu-stat-num">{{ $pendingCount }}</span>
                        <span class="stu-stat-label">Pending</span>
                    </div>
                </div>
                <div class="stu-stat-card">
                    <div class="stu-stat-icon stu-stat-icon--purple">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/>
                        </svg>
                    </div>
                    <div class="stu-stat-body">
                        <span class="stu-stat-num">{{ $todaySchedules->count() }}</span>
                        <span class="stu-stat-label">Today's Classes</span>
                    </div>
                </div>
            </div>

            {{-- ── Two-column layout: Today + My Subjects ────────────────────────── --}}
            <div class="stu-main-grid">

                {{-- Today's Schedule --}}
                <section class="stu-card">
                    <div class="stu-card-header">
                        <h2 class="stu-card-title">
                            <span class="stu-card-icon stu-card-icon--purple">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/>
                                </svg>
                            </span>
                            Today's Schedule
                        </h2>
                        <span class="stu-badge stu-badge--muted">{{ ucfirst($todayDow) }}</span>
                    </div>
                    @if ($todaySchedules->isEmpty())
                        <div class="stu-empty">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/>
                            </svg>
                            <p>No classes scheduled for today.</p>
                            <p class="stu-empty-sub">Enjoy your free day!</p>
                        </div>
                    @else
                        <div class="stu-today-list">
                            @foreach ($todaySchedules as $cls)
                                @php
                                    $nowMin   = (int) date('H') * 60 + (int) date('i');
                                    $clsStart = (int) ($cls['start_min'] ?? 0);
                                    $clsEnd   = (int) ($cls['end_min'] ?? 0);
                                    $isNow    = $nowMin >= $clsStart && $nowMin < $clsEnd;
                                    $isDone   = $nowMin >= $clsEnd;
                                    $chipType = strtolower($cls['session_type'] ?? 'lecture');
                                @endphp
                                <div class="stu-today-item {{ $isNow ? 'stu-today-item--now' : ($isDone ? 'stu-today-item--done' : '') }}">
                                    <div class="stu-today-time">
                                        <span>{{ $cls['start_time'] ?? '' }}</span>
                                        <span class="stu-today-time-sep">–</span>
                                        <span>{{ $cls['end_time'] ?? '' }}</span>
                                    </div>
                                    <div class="stu-today-bar {{ $isNow ? 'stu-today-bar--now' : ($isDone ? 'stu-today-bar--done' : '') }}"></div>
                                    <div class="stu-today-body">
                                        <div class="stu-today-subject">{{ $cls['subject_title'] ?? 'Class' }}</div>
                                        <div class="stu-today-meta">
                                            <span class="stu-chip stu-chip--{{ $chipType === 'lab' ? 'amber' : 'blue' }}">{{ $cls['subject_code'] ?? '' }}</span>
                                            @if(!empty($cls['teacher_name']))
                                                <span class="stu-today-teacher">{{ $cls['teacher_name'] }}</span>
                                            @endif
                                            @if ($isNow)<span class="stu-chip stu-chip--now">In Progress</span>@endif
                                            @if ($isDone)<span class="stu-chip stu-chip--done">Done</span>@endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="stu-card-footer">
                        <a href="{{ url('/tenant/enrollments#weekly-class-schedule') }}" class="stu-link">View full schedule →</a>
                    </div>
                </section>

            </div>

            {{-- ── Upcoming Tasks (Moodle-style compact timeline) ───────────────── --}}
            @php
                $upcomingTasks = collect([]);
                if (isset($myEnrollments) && $myEnrollments->isNotEmpty()) {
                    $upcomingTasks = $myEnrollments->take(8)->values()->map(function ($enroll, $idx) {
                        $subject = $enroll->offering?->subject;
                        $baseDate = now()->startOfDay()->addDays(($idx % 6) + 1);
                        return [
                            'title' => 'Complete class requirements',
                            'subject' => $subject?->code ?? 'SUBJ',
                            'subject_name' => $subject?->title ?? 'Subject',
                            'deadline_label' => $baseDate->format('M d, Y'),
                            'deadline_iso' => $baseDate->format('Y-m-d'),
                            'priority' => $idx < 2 ? 'high' : ($idx < 5 ? 'medium' : 'low'),
                        ];
                    });
                }
                if ($upcomingTasks->isEmpty()) {
                    $upcomingTasks = collect([
                        ['title' => 'Review lecture notes', 'subject' => 'GEN101', 'subject_name' => 'General Education', 'deadline_label' => now()->addDays(1)->format('M d, Y'), 'deadline_iso' => now()->addDays(1)->format('Y-m-d'), 'priority' => 'medium'],
                        ['title' => 'Submit reflection activity', 'subject' => 'PE101', 'subject_name' => 'Physical Education', 'deadline_label' => now()->addDays(4)->format('M d, Y'), 'deadline_iso' => now()->addDays(4)->format('Y-m-d'), 'priority' => 'low'],
                    ]);
                }
            @endphp
            <section class="stu-card stu-tasks-card">
                <div class="stu-card-header stu-card-header--tasks">
                    <h2 class="stu-card-title">
                        <span class="stu-card-icon stu-card-icon--blue">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </span>
                        Upcoming Tasks
                    </h2>
                    <div class="stu-task-filters">
                        <select id="stu-task-date-filter" class="stu-task-select" aria-label="Date filter">
                            <option value="7" selected>Next 7 days</option>
                            <option value="0">Today</option>
                            <option value="30">Next 30 days</option>
                            <option value="all">All</option>
                        </select>
                        <select id="stu-task-sort-filter" class="stu-task-select" aria-label="Sort tasks">
                            <option value="nearest" selected>Nearest deadline</option>
                            <option value="latest">Latest deadline</option>
                            <option value="subject">Subject A-Z</option>
                        </select>
                        <input id="stu-task-search" class="stu-task-search" type="text" placeholder="Search tasks..." aria-label="Search tasks">
                    </div>
                </div>
                <div class="stu-task-list" id="stu-task-list">
                    @foreach ($upcomingTasks as $task)
                        <article class="stu-task-item"
                            data-deadline="{{ $task['deadline_iso'] }}"
                            data-subject="{{ strtolower($task['subject']) }}"
                            data-title="{{ strtolower($task['title']) }}">
                            <div class="stu-task-dot stu-task-dot--{{ $task['priority'] }}"></div>
                            <div class="stu-task-body">
                                <p class="stu-task-title">{{ $task['title'] }}</p>
                                <p class="stu-task-meta">{{ $task['subject'] }} • {{ $task['subject_name'] }}</p>
                            </div>
                            <span class="stu-task-deadline">{{ $task['deadline_label'] }}</span>
                        </article>
                    @endforeach
                </div>
                <div class="stu-task-empty" id="stu-task-empty" style="display:none;">No tasks match your filter.</div>
            </section>
            <script>
            (() => {
                const dateFilter = document.getElementById('stu-task-date-filter');
                const sortFilter = document.getElementById('stu-task-sort-filter');
                const searchInput = document.getElementById('stu-task-search');
                const list = document.getElementById('stu-task-list');
                const empty = document.getElementById('stu-task-empty');
                if (!dateFilter || !sortFilter || !searchInput || !list || !empty) return;

                const today = new Date();
                today.setHours(0, 0, 0, 0);

                const applyTaskFilters = () => {
                    const range = dateFilter.value;
                    const sortBy = sortFilter.value;
                    const query = searchInput.value.trim().toLowerCase();
                    const items = Array.from(list.querySelectorAll('.stu-task-item'));

                    items.forEach((item) => {
                        const deadlineStr = item.dataset.deadline || '';
                        const title = item.dataset.title || '';
                        const subj = item.dataset.subject || '';
                        const deadline = new Date(deadlineStr + 'T00:00:00');
                        let inRange = true;

                        if (range !== 'all') {
                            const max = new Date(today);
                            max.setDate(max.getDate() + Number(range));
                            inRange = deadline >= today && deadline <= max;
                        }

                        const matchesQuery = !query || title.includes(query) || subj.includes(query);
                        item.style.display = (inRange && matchesQuery) ? '' : 'none';
                    });

                    const visible = items.filter((item) => item.style.display !== 'none');
                    visible.sort((a, b) => {
                        const da = new Date((a.dataset.deadline || '') + 'T00:00:00').getTime();
                        const db = new Date((b.dataset.deadline || '') + 'T00:00:00').getTime();
                        const sa = (a.dataset.subject || '').toLowerCase();
                        const sb = (b.dataset.subject || '').toLowerCase();
                        if (sortBy === 'latest') return db - da;
                        if (sortBy === 'subject') return sa.localeCompare(sb);
                        return da - db; // nearest
                    });
                    visible.forEach((item) => list.appendChild(item));
                    empty.style.display = visible.length === 0 ? '' : 'none';
                };

                dateFilter.addEventListener('change', applyTaskFilters);
                sortFilter.addEventListener('change', applyTaskFilters);
                searchInput.addEventListener('input', applyTaskFilters);
                applyTaskFilters();
            })();
            </script>

            {{-- ── My Enrolled Subjects (card grid) ─────────────────────────────── --}}
            @if (isset($myEnrollments) && $myEnrollments->isNotEmpty())
            <section class="stu-subjects-section">
                <div class="stu-section-header">
                    <h2 class="stu-section-title">My Enrolled Subjects</h2>
                    <a href="{{ url('/tenant/enrollments') }}" class="stu-link">View all →</a>
                </div>
                <div class="stu-subjects-grid">
                    @foreach ($myEnrollments->take(6) as $enroll)
                        @php
                            $subj    = $enroll->offering?->subject;
                            $teacher = $enroll->offering?->teacher?->full_name ?? null;
                            $schedList = collect($studentScheduleRows ?? [])
                                ->filter(fn($r) => ($r['subject_code'] ?? '') === ($subj?->code ?? ''))
                                ->map(fn($r) => ($r['day_label'] ?? '') . ' ' . ($r['start_time'] ?? '') . '–' . ($r['end_time'] ?? ''))
                                ->unique()->implode(', ');
                            $sColor = match($enroll->status) {
                                'enrolled','confirmed','payment_verified' => 'green',
                                'pending','billing_pending','selected'    => 'amber',
                                'dropped','cancelled'                     => 'red',
                                default => 'muted',
                            };
                        @endphp
                        <div class="stu-subject-card">
                            <div class="stu-subject-card-top">
                                <span class="stu-chip stu-chip--blue stu-chip--code">{{ $subj?->code ?? '—' }}</span>
                                <span class="stu-badge stu-badge--{{ $sColor }}">{{ ucfirst($enroll->status) }}</span>
                            </div>
                            <h3 class="stu-subject-name">{{ $subj?->title ?? 'Untitled Subject' }}</h3>
                            @if ($teacher)
                                <p class="stu-subject-meta">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    {{ $teacher }}
                                </p>
                            @endif
                            @if ($schedList)
                                <p class="stu-subject-meta">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/></svg>
                                    {{ $schedList }}
                                </p>
                            @endif
                            <div class="stu-subject-card-footer">
                                <a href="{{ url('/tenant/classes') }}" class="btn ghost sm stu-subject-btn">View Class</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            @endif

            @else
            {{-- ── NON-STUDENT (admin / teacher / staff) keeps the original layout ──── --}}

            @if (!empty($pendingStudentActivation))
                <div class="alert error dashboard-alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Your account is pending activation. Finance must verify your payment, then registrar will activate your student access.</span>
                </div>
            @endif

            @if (!empty($needsProgramEnrollment))
                <div class="dashboard-card dashboard-card--highlight">
                    <div class="dashboard-card-header">
                        <h2 class="dashboard-card-title">
                            <span class="dashboard-card-icon dashboard-card-icon--blue">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>
                                </svg>
                            </span>
                            Program Enrollment Required
                        </h2>
                        <span class="dashboard-badge dashboard-badge--blue">{{ $openEnrollmentSemesterName ?? 'Open Semester' }}</span>
                    </div>
                    <p class="dashboard-card-desc">
                        Your account is active. Complete your program enrollment first: select your program, subjects, and schedules.
                    </p>
                    <div class="dashboard-card-actions">
                        <a href="{{ url('/tenant/enrollments') }}" class="btn primary">Start Program Enrollment</a>
                        <a href="{{ url('/tenant/billing') }}" class="btn ghost">View Payments</a>
                    </div>
                </div>
            @endif

            @if (in_array('teacher', (array) $roleCodes, true))
                @php
                    $scheduleRows = collect($teacherWeeklyScheduleRows ?? []);
                    $teachable = collect($teachableSubjects ?? []);
                    $todayDayOrder = (int) date('N');
                    $nowMin = ((int) date('G') * 60) + (int) date('i');
                    $todayTeacherSchedules = $scheduleRows->where('day_order', $todayDayOrder)->sortBy('start_min')->values();

                    $pendingReviewTasks = $todayTeacherSchedules->map(function ($row) {
                        return [
                            'title' => 'Check submissions',
                            'subject' => $row['subject_code'] ?? 'SUBJ',
                            'group' => $row['class_group'] ?? 'Class group',
                            'when' => $row['start_time'] ?? 'Today',
                        ];
                    })->take(4)->values();
                    if ($pendingReviewTasks->isEmpty()) {
                        $pendingReviewTasks = collect([
                            ['title' => 'Check assignment queue', 'subject' => 'GENERAL', 'group' => 'All classes', 'when' => 'Today'],
                        ]);
                    }

                    $recentStudentActivity = $scheduleRows->sortByDesc('start_min')->take(4)->map(function ($row) {
                        return [
                            'text' => ($row['subject_code'] ?? 'SUBJ') . ' · New submission received',
                            'meta' => ($row['day_label'] ?? 'This week') . ' • ' . ($row['class_group'] ?? 'Class'),
                        ];
                    })->values();
                    if ($recentStudentActivity->isEmpty()) {
                        $recentStudentActivity = collect([
                            ['text' => 'No recent submission alerts yet', 'meta' => 'Student activity will appear here'],
                        ]);
                    }

                    $totalStudents = (int) $teachable->sum(fn($s) => (int) ($s->students_count ?? $s->enrolled_students_count ?? $s->students ?? 0));
                    $totalClasses = (int) $teachable->count();
                    $pendingCountTeacher = (int) $pendingReviewTasks->count();
                    $todayCountTeacher = (int) $todayTeacherSchedules->count();

                    $gridDays = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri'];
                    if ($scheduleRows->contains(fn ($s) => (int) ($s['day_order'] ?? 0) === 6)) {
                        $gridDays[6] = 'Sat';
                    }
                    if ($scheduleRows->contains(fn ($s) => (int) ($s['day_order'] ?? 0) === 7)) {
                        $gridDays[7] = 'Sun';
                    }

                    $gridStartMin = $scheduleRows->isNotEmpty()
                        ? ((int) floor(((int) $scheduleRows->min('start_min')) / 60)) * 60
                        : 420;
                    $gridEndMin = $scheduleRows->isNotEmpty()
                        ? ((int) ceil(((int) $scheduleRows->max('end_min')) / 60)) * 60
                        : 1020;
                    $gridStartMin = max(360, $gridStartMin);
                    $gridEndMin = min(1320, $gridEndMin);
                    if ($gridEndMin <= $gridStartMin) {
                        $gridStartMin = 420;
                        $gridEndMin = 1020;
                    }

                    $gridSlot = 30;
                    $slotHeightPx = 38;
                    $sessionsByDay = $scheduleRows->groupBy('day_order');
                @endphp

                <section class="tdb-summary-grid">
                    <article class="tdb-summary-card">
                        <p>Total Students</p><strong>{{ $totalStudents }}</strong>
                    </article>
                    <article class="tdb-summary-card">
                        <p>Classes</p><strong>{{ $totalClasses }}</strong>
                    </article>
                    <article class="tdb-summary-card">
                        <p>Pending Tasks</p><strong>{{ $pendingCountTeacher }}</strong>
                    </article>
                    <article class="tdb-summary-card">
                        <p>Today's Classes</p><strong>{{ $todayCountTeacher }}</strong>
                    </article>
                </section>

                <section class="tdb-layout">
                    <div class="tdb-main">
                        <article class="dashboard-card tdb-card">
                            <div class="dashboard-card-header">
                                <h2 class="dashboard-card-title">Today’s Schedule</h2>
                                <span class="dashboard-badge dashboard-badge--purple">{{ now()->format('l') }}</span>
                            </div>
                            @if ($todayTeacherSchedules->isEmpty())
                                <div class="dashboard-empty dashboard-empty--compact">
                                    <p>No classes scheduled for today.</p>
                                </div>
                            @else
                                <div class="tdb-list">
                                    @foreach($todayTeacherSchedules as $row)
                                        @php
                                            $isNow = $nowMin >= (int) ($row['start_min'] ?? 0) && $nowMin < (int) ($row['end_min'] ?? 0);
                                        @endphp
                                        <a href="{{ url('/tenant/lms') }}" class="tdb-list-item {{ $isNow ? 'is-now' : '' }}">
                                            <div class="tdb-list-time">{{ $row['start_time'] ?? '' }} - {{ $row['end_time'] ?? '' }}</div>
                                            <div class="tdb-list-body">
                                                <strong>{{ $row['subject_title'] ?? 'Class' }}</strong>
                                                <span>{{ $row['subject_code'] ?? 'SUBJ' }} • {{ $row['class_group'] ?? 'N/A' }}</span>
                                            </div>
                                            <span class="dashboard-badge {{ strtolower((string)($row['session_type'] ?? 'lecture')) === 'lab' ? 'dashboard-badge--amber' : 'dashboard-badge--green' }}">{{ $row['session_type'] ?? 'Lecture' }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </article>

                        <article class="dashboard-card tdb-card">
                            <div class="dashboard-card-header">
                                <h2 class="dashboard-card-title">Pending Tasks</h2>
                                <span class="dashboard-badge dashboard-badge--amber">{{ $pendingCountTeacher }} to review</span>
                            </div>
                            <div class="tdb-list">
                                @foreach($pendingReviewTasks as $task)
                                    <div class="tdb-list-item">
                                        <div class="tdb-list-time">{{ $task['when'] }}</div>
                                        <div class="tdb-list-body">
                                            <strong>{{ $task['title'] }}</strong>
                                            <span>{{ $task['subject'] }} • {{ $task['group'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </article>

                        <article class="dashboard-card tdb-card">
                            <div class="dashboard-card-header">
                                <h2 class="dashboard-card-title">Student Activity</h2>
                            </div>
                            <div class="tdb-list">
                                @foreach($recentStudentActivity as $activity)
                                    <div class="tdb-list-item">
                                        <div class="tdb-list-body">
                                            <strong>{{ $activity['text'] }}</strong>
                                            <span>{{ $activity['meta'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </article>

                        <article class="dashboard-card tdb-card">
                            <div class="dashboard-card-header">
                                <h2 class="dashboard-card-title">My Classes</h2>
                            </div>
                            @if($teachable->isEmpty())
                                <div class="dashboard-empty dashboard-empty--compact"><p>No class assignments yet.</p></div>
                            @else
                                <div class="tdb-classes-grid">
                                    @foreach($teachable as $subject)
                                        @php
                                            $students = (int) ($subject->students_count ?? $subject->enrolled_students_count ?? $subject->students ?? 0);
                                        @endphp
                                        <article class="tdb-class-card">
                                            <p class="tdb-class-code">{{ $subject->code }}</p>
                                            <h3>{{ $subject->title }}</h3>
                                            <p>{{ $students }} students</p>
                                            <a href="{{ url('/tenant/lms') }}" class="btn ghost sm">Open Class</a>
                                        </article>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    </div>

                    <aside class="tdb-side">
                        <article class="dashboard-card tdb-card">
                            <div class="dashboard-card-header">
                                <h2 class="dashboard-card-title">Announcements</h2>
                            </div>
                            <ul class="tdb-side-list">
                                <li>Midterm grading window closes this week.</li>
                                <li>Upload attendance before Friday 5:00 PM.</li>
                                <li>Faculty meeting on Friday, 2:00 PM.</li>
                            </ul>
                        </article>
                        <article class="dashboard-card tdb-card">
                            <div class="dashboard-card-header">
                                <h2 class="dashboard-card-title">Quick Stats</h2>
                            </div>
                            <div class="tdb-quick-stats">
                                <div><span>Students</span><strong>{{ $totalStudents }}</strong></div>
                                <div><span>Classes</span><strong>{{ $totalClasses }}</strong></div>
                                <div><span>Pending Tasks</span><strong>{{ $pendingCountTeacher }}</strong></div>
                            </div>
                        </article>
                    </aside>
                </section>

                <section class="dashboard-section dashboard-section--span">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h2 class="dashboard-card-title">Weekly Timetable</h2>
                            <span class="dashboard-badge dashboard-badge--purple">{{ (int) (($teacherWeeklyScheduleSummary['total_sessions'] ?? 0)) }} sessions</span>
                        </div>
                        <div class="dashboard-timetable-meta">
                            <span class="dashboard-badge dashboard-badge--blue">{{ (int) (($teacherWeeklyScheduleSummary['subjects'] ?? 0)) }} subjects</span>
                            <span class="dashboard-badge dashboard-badge--green">{{ (int) (($teacherWeeklyScheduleSummary['days_covered'] ?? 0)) }} days</span>
                            <span class="dashboard-badge dashboard-badge--amber">{{ (int) (($teacherWeeklyScheduleSummary['class_groups'] ?? 0)) }} class groups</span>
                            <span class="tdb-legend"><i class="lecture"></i>Lecture</span>
                            <span class="tdb-legend"><i class="lab"></i>Lab</span>
                        </div>

                    @if($scheduleRows->isEmpty())
                        <div class="dashboard-empty">
                            <p>No teaching sessions found yet.</p>
                        </div>
                    @else
                        <div class="dashboard-weekly-wrap">
                            <div class="dashboard-weekly-grid" style="grid-template-columns:110px repeat({{ count($gridDays) }}, minmax(180px, 1fr)); min-width:{{ 110 + (count($gridDays) * 180) }}px;">
                                <div class="dashboard-weekly-head dashboard-weekly-head--time">Time</div>
                                @foreach($gridDays as $dayKey => $dayLabel)
                                    <div class="dashboard-weekly-head">{{ $dayLabel }}</div>
                                @endforeach

                                @for($slotMin = $gridStartMin; $slotMin < $gridEndMin; $slotMin += $gridSlot)
                                    @php
                                        $slotLabel = ($slotMin % 60 === 0) ? sprintf('%02d:%02d', intdiv($slotMin, 60), 0) : '';
                                        $slotEnd = $slotMin + $gridSlot;
                                        $isCurrentSlot = ($todayDayOrder >= 1 && $todayDayOrder <= 7) && $nowMin >= $slotMin && $nowMin < $slotEnd;
                                    @endphp
                                    <div class="dashboard-weekly-time {{ $slotMin % 60 === 30 ? 'half' : 'hour' }} {{ $isCurrentSlot ? 'is-now' : '' }}">{{ $slotLabel }}</div>
                                    @foreach($gridDays as $dayKey => $dayLabel)
                                        @php
                                            $hits = collect($sessionsByDay->get($dayKey, []))
                                                ->filter(fn ($s) => (int) ($s['start_min'] ?? 0) >= $slotMin && (int) ($s['start_min'] ?? 0) < $slotEnd)
                                                ->values();
                                            $isTodayCol = $dayKey === $todayDayOrder;
                                        @endphp
                                        <div class="dashboard-weekly-cell {{ $slotMin % 60 === 30 ? 'half' : 'hour' }} {{ $isCurrentSlot && $isTodayCol ? 'is-now' : '' }}">
                                            @if($isCurrentSlot && $isTodayCol)
                                                <span class="tdb-now-line"></span>
                                            @endif
                                            @foreach($hits as $hitIndex => $hit)
                                                @php
                                                    $hitStart = (int) ($hit['start_min'] ?? $slotMin);
                                                    $hitEnd = (int) ($hit['end_min'] ?? $slotEnd);
                                                    $durationMinutes = max($gridSlot, $hitEnd - $hitStart);
                                                    $topPx = (int) round((($hitStart - $slotMin) / $gridSlot) * $slotHeightPx) + 2;
                                                    $heightPx = max(28, (int) round(($durationMinutes / $gridSlot) * $slotHeightPx) - 4);
                                                    $insetPx = 2 + ((int) $hitIndex * 4);
                                                    $chipType = strtolower((string) ($hit['session_type'] ?? 'lecture'));
                                                    $chipClass = $chipType === 'lab' ? 'lab' : 'lecture';
                                                @endphp
                                                <a href="{{ url('/tenant/lms') }}" class="dashboard-weekly-chip dashboard-weekly-chip--{{ $chipClass }}" style="top:{{ $topPx }}px; height:{{ $heightPx }}px; left:{{ $insetPx }}px; right:{{ $insetPx }}px;">
                                                    <strong>{{ $hit['subject_code'] ?? 'SUBJ' }}</strong>
                                                    <span>{{ $hit['start_time'] ?? '' }}-{{ $hit['end_time'] ?? '' }}</span>
                                                    <small>{{ $hit['class_group'] ?? 'N/A' }}</small>
                                                </a>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endfor
                            </div>
                        </div>
                    @endif
                    </div>
                </section>
            @else
                <div class="dashboard-grid dashboard-grid--main">
                    <section class="dashboard-section">
                        <div class="dashboard-card">
                            <div class="dashboard-card-header">
                                <h2 class="dashboard-card-title">Your Active Roles</h2>
                            </div>
                            <div class="dashboard-roles">
                                @forelse($roleCodes as $role)
                                    @php
                                        $roleColors = ['student' => 'blue','teacher' => 'green','dean' => 'purple','registrar_staff' => 'amber','finance_staff' => 'red','school_admin' => 'red','student_pending' => 'amber'];
                                        $color = $roleColors[strtolower($role)] ?? 'neutral';
                                    @endphp
                                    <span class="dashboard-badge dashboard-badge--{{ $color }}">{{ strtoupper($role) }}</span>
                                @empty
                                    <span class="dashboard-muted">No roles assigned yet.</span>
                                @endforelse
                            </div>
                        </div>
                    </section>
                </div>
            @endif

            @endif {{-- /isStudentOnly if (closes @if $isStudentOnly ... @else ... @endif) --}}

        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

.dashboard-body {
    --dash-bg: #f0f4f2;
    --dash-surface: #ffffff;
    --dash-border: rgba(34, 197, 94, 0.28);
    --dash-ink: #0f172a;
    --dash-ink-2: #334155;
    --dash-muted: #64748b;
    --dash-accent: #15803d;
    --dash-radius: 10px;
    --dash-radius-lg: 16px;
    --dash-shadow: none;
    --dash-shadow-md: 0 6px 20px rgba(15, 81, 50, 0.08);
}

.dashboard-alert {
    max-width: 100%;
    border-radius: var(--dash-radius);
    margin-bottom: 20px;
}

.dashboard-welcome {
    margin-bottom: 20px;
    padding: 16px 18px;
    border-radius: var(--dash-radius-lg);
    border: 1px solid var(--dash-border);
    background: var(--dash-surface);
    box-shadow: var(--dash-shadow);
}

.dashboard-welcome--mock {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 22px 24px;
    border-radius: 18px;
    border: 1px solid rgba(34, 197, 94, 0.35);
    background: var(--dash-surface);
    box-shadow: 0 4px 24px rgba(21, 101, 52, 0.06);
}

.dashboard-welcome-avatar {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.35rem;
    flex-shrink: 0;
    box-shadow: 0 4px 16px rgba(37, 99, 235, 0.28);
}
.dashboard-welcome-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    display: block;
}

.dashboard-welcome-copy {
    min-width: 0;
}

.dashboard-welcome-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--dash-ink);
    margin: 0 0 6px;
    letter-spacing: -0.01em;
    font-family: var(--font-sans, 'Inter', system-ui, -apple-system, sans-serif);
}

.dashboard-welcome-title strong {
    font-weight: 800;
    color: #14532d;
}

.dashboard-welcome-sub {
    font-size: 0.9rem;
    color: var(--dash-muted);
    margin: 0;
    line-height: 1.5;
}

@media (max-width: 520px) {
    .dashboard-welcome--mock {
        flex-direction: column;
        align-items: flex-start;
    }
}

.dashboard-section {
    margin-bottom: 20px;
}

.dashboard-grid--main {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    align-items: stretch;
    margin-bottom: 22px;
}

.dashboard-grid--main .dashboard-section {
    margin-bottom: 0;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

.dashboard-grid--main > .dashboard-section > .dashboard-card {
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 0;
}

.dashboard-grid--main .dashboard-card > .dashboard-card-header {
    flex-shrink: 0;
}

.dashboard-grid--main .dashboard-card > .dashboard-roles {
    flex: 1;
    margin-top: 0;
    align-content: flex-start;
}

.dashboard-grid--main .dashboard-card > .dashboard-empty--compact {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-self: stretch;
    width: 100%;
    min-height: 0;
}

.dashboard-grid--main .dashboard-card > .dashboard-table-wrap {
    flex: 1;
    min-height: 0;
}

.dashboard-section--span {
    grid-column: 1 / -1;
}

.dashboard-empty--compact {
    padding: 22px 18px;
    margin: 0 !important;
}

.dashboard-empty--compact svg {
    width: 34px;
    height: 34px;
    margin: 0 auto 10px;
    opacity: 0.45;
    display: block;
}

.dashboard-empty--compact p {
    margin: 0;
    font-size: 0.9rem;
}

@media (max-width: 900px) {
    .dashboard-grid--main {
        grid-template-columns: 1fr;
    }
    .dashboard-section--span {
        grid-column: auto;
    }
}

.dashboard-section-title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--dash-muted);
    margin: 0 0 14px;
    padding-left: 2px;
}

/* ─── Dashboard cards ───────────────────────────────────────────────── */
.dashboard-card {
    background: var(--dash-surface);
    border: 1px solid rgba(34, 197, 94, 0.28);
    border-radius: var(--dash-radius-lg);
    padding: 18px 20px;
    box-shadow: var(--dash-shadow);
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}

.dashboard-card:hover {
    box-shadow: var(--dash-shadow-md);
    border-color: rgba(34, 197, 94, 0.45);
}

.dashboard-card--highlight {
    border-color: rgba(21, 128, 61, 0.45);
    background: var(--dash-surface);
}

.dashboard-card--info {
    background: var(--dash-surface);
}

.dashboard-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--dash-border);
}

.dashboard-card-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--dash-ink);
    display: flex;
    align-items: center;
    gap: 10px;
}

.dashboard-card-icon {
    width: 36px;
    height: 36px;
    border-radius: var(--dash-radius);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.dashboard-card-icon--blue   { background: #dbeafe; color: #2563eb; }
.dashboard-card-icon--green  { background: #dcfce7; color: #16a34a; }
.dashboard-card-icon--purple { background: #ede9fe; color: #7c3aed; }
.dashboard-card-icon--amber  { background: #fef3c7; color: #d97706; }
.dashboard-card-icon--red    { background: #fee2e2; color: #dc2626; }

.dashboard-card-desc {
    font-size: 0.9rem;
    color: var(--dash-ink-2);
    margin: 0 0 14px;
    line-height: 1.5;
}

.dashboard-card-desc--block {
    margin: 0;
    line-height: 1.65;
}

.dashboard-card-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.dashboard-roles {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 4px;
}

.dashboard-muted {
    font-size: 0.875rem;
    color: var(--dash-muted);
}

/* ─── Dashboard badges ───────────────────────────────────────────────── */
.dashboard-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 5px 12px;
    letter-spacing: 0.02em;
    border: 1px solid transparent;
}

.dashboard-badge--sm {
    font-size: 0.7rem;
    padding: 3px 8px;
}

.dashboard-badge--blue   { background: rgba(37, 99, 235, 0.10); color: #1d4ed8; border-color: rgba(37, 99, 235, 0.20); }
.dashboard-badge--green  { background: rgba(21, 128, 61, 0.10); color: #15803d; border-color: rgba(21, 128, 61, 0.22); }
.dashboard-badge--purple { background: rgba(109, 40, 217, 0.10); color: #6d28d9; border-color: rgba(109, 40, 217, 0.22); }
.dashboard-badge--amber  { background: rgba(180, 83, 9, 0.10); color: #b45309; border-color: rgba(180, 83, 9, 0.22); }
.dashboard-badge--red    { background: rgba(185, 28, 28, 0.10); color: #b91c1c; border-color: rgba(185, 28, 28, 0.22); }
.dashboard-badge--neutral { background: rgba(248, 250, 252, 0.85); color: var(--dash-ink-2); border-color: rgba(226, 232, 240, 0.95); }

/*
 * Softer chip styling for the dashboard page:
 * - Roles: neutral pill + subtle colored dot
 * - Timetable meta: neutral chips (avoid the "rainbow" look)
 */
.dashboard-roles .dashboard-badge {
    background: rgba(255,255,255,0.96);
    border-color: rgba(226,232,240,0.95);
    color: var(--dash-ink-2);
    padding-left: 10px;
    padding-right: 12px;
}

.dashboard-roles .dashboard-badge::before {
    content: "";
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: var(--role-chip-color, var(--dash-accent));
    margin-right: 8px;
    display: inline-block;
}

.dashboard-roles .dashboard-badge--blue   { --role-chip-color: #2563eb; background: rgba(255,255,255,0.96); color: var(--dash-ink-2); border-color: rgba(226,232,240,0.95); }
.dashboard-roles .dashboard-badge--green  { --role-chip-color: #16a34a; background: rgba(255,255,255,0.96); color: var(--dash-ink-2); border-color: rgba(226,232,240,0.95); }
.dashboard-roles .dashboard-badge--purple { --role-chip-color: #7c3aed; background: rgba(255,255,255,0.96); color: var(--dash-ink-2); border-color: rgba(226,232,240,0.95); }
.dashboard-roles .dashboard-badge--amber  { --role-chip-color: #d97706; background: rgba(255,255,255,0.96); color: var(--dash-ink-2); border-color: rgba(226,232,240,0.95); }
.dashboard-roles .dashboard-badge--red    { --role-chip-color: #dc2626; background: rgba(255,255,255,0.96); color: var(--dash-ink-2); border-color: rgba(226,232,240,0.95); }
.dashboard-roles .dashboard-badge--neutral{ --role-chip-color: rgba(100,116,139,0.9); background: rgba(255,255,255,0.96); color: var(--dash-ink-2); border-color: rgba(226,232,240,0.95); }

.tenant-ui-mock .dashboard-roles .dashboard-badge {
    background: rgba(220, 252, 231, 0.95);
    border-color: rgba(34, 197, 94, 0.4);
    color: #14532d;
    font-weight: 700;
}

.dashboard-timetable-meta .dashboard-badge {
    background: rgba(248,250,252,0.95);
    border-color: rgba(226,232,240,0.95);
    color: var(--dash-ink-2);
}

/* ─── Dashboard tables ───────────────────────────────────────────────── */
.dashboard-table-wrap {
    overflow-x: auto;
    border-radius: var(--dash-radius);
    border: 1px solid var(--dash-border);
    margin-top: 4px;
    max-width: 100%;
}

.dashboard-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.dashboard-table thead {
    background: #f8fafc;
}

.dashboard-table th {
    text-align: left;
    padding: 10px 14px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--dash-muted);
    border-bottom: 1px solid var(--dash-border);
}

.dashboard-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--dash-border);
    color: var(--dash-ink-2);
}

.dashboard-table tbody tr:last-child td { border-bottom: none; }
.dashboard-table tbody tr:hover { background: #f8fafc; }
.dashboard-table tbody tr:nth-child(even) td { background: rgba(248, 250, 252, 0.35); }

.dashboard-table-cell--strong { font-weight: 600; color: var(--dash-ink); }
.dashboard-table-cell--muted { color: var(--dash-muted); }

.dashboard-timetable-meta {
    padding: 0 4px 14px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    background: transparent;
    border: none;
    border-radius: 0;
    margin: 0;
}

/* ─── Dashboard empty state ──────────────────────────────────────────── */
.dashboard-empty {
    text-align: center;
    padding: 36px 20px;
    color: var(--dash-muted);
    background: #f8fafc;
    border-radius: var(--dash-radius);
    margin: 0 4px 14px;
}

.dashboard-empty svg {
    width: 40px;
    height: 40px;
    margin: 0 auto 10px;
    display: block;
    opacity: 0.5;
}

.dashboard-empty p {
    margin: 0;
    font-size: 0.875rem;
}

/* ─── Dashboard weekly timetable grid ────────────────────────────────── */
.dashboard-weekly-wrap {
    border: 1px solid var(--dash-border);
    border-radius: var(--dash-radius);
    margin: 0 4px 14px;
    overflow-x: auto;
    overflow-y: visible;
    background: var(--dash-surface);
    box-shadow: var(--dash-shadow);
    max-width: 100%;
}

.dashboard-weekly-grid {
    display: grid;
}

.dashboard-weekly-head {
    font-weight: 700;
    font-size: 0.78rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    color: var(--dash-muted);
    background: #f8fafc;
    border-bottom: 1px solid var(--dash-border);
    border-right: 1px solid var(--dash-border);
    padding: 10px;
    position: sticky;
    top: 0;
    z-index: 4;
}

.dashboard-weekly-head--time {
    left: 0;
    z-index: 6;
}

.dashboard-weekly-time {
    border-right: 1px solid var(--dash-border);
    border-bottom: 1px solid var(--dash-border);
    padding: 8px 10px;
    font-size: 0.78rem;
    color: var(--dash-muted);
    font-weight: 600;
    background: #fafbfc;
    position: sticky;
    left: 0;
    z-index: 3;
}

.dashboard-weekly-time.hour {
    font-weight: 700;
    color: var(--dash-ink-2);
    border-bottom-color: transparent;
}

.dashboard-weekly-time.half {
    color: transparent;
    border-bottom-color: var(--dash-border);
}

.dashboard-weekly-cell {
    border-right: 1px solid var(--dash-border);
    border-bottom: 1px solid var(--dash-border);
    min-height: 38px;
    height: 38px;
    padding: 0;
    position: relative;
    overflow: visible;
    background: rgba(255, 255, 255, 0.8);
}

.dashboard-weekly-cell.hour {
    border-bottom-color: transparent;
}

.dashboard-weekly-chip {
    border-radius: 8px;
    padding: 4px 6px;
    display: grid;
    gap: 2px;
    font-size: 0.72rem;
    line-height: 1.2;
    position: absolute;
    z-index: 5;
    box-sizing: border-box;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(15, 23, 42, 0.08);
    border: 1px solid rgba(15, 23, 42, 0.06);
}

.dashboard-weekly-chip--lecture {
    background: #dcfce7;
    color: #166534;
}

.dashboard-weekly-chip--lab {
    background: #fef3c7;
    color: #92400e;
}

/* ─── Dashboard quick actions ─────────────────────────────────────────── */
.dashboard-actions {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 18px;
}

.dashboard-action-card {
    display: flex;
    flex-direction: column;
    background: var(--dash-surface);
    border: 1px solid var(--dash-border);
    border-radius: var(--dash-radius-lg);
    padding: 20px 22px;
    text-decoration: none;
    color: inherit;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
}

.dashboard-action-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--dash-shadow-md);
    border-color: #cbd5e1;
}

.dashboard-action-card--blue:hover  { border-color: #93c5fd; }
.dashboard-action-card--green:hover { border-color: #86efac; }
.dashboard-action-card--amber:hover { border-color: #fde68a; }

.dashboard-action-icon {
    width: 44px;
    height: 44px;
    border-radius: var(--dash-radius);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}

.dashboard-action-icon--blue   { background: #dbeafe; color: #2563eb; }
.dashboard-action-icon--green  { background: #dcfce7; color: #16a34a; }
.dashboard-action-icon--amber  { background: #fef3c7; color: #d97706; }

.dashboard-action-body {
    flex: 1;
}

.dashboard-action-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--dash-ink);
    margin: 0 0 4px;
}

.dashboard-action-desc {
    font-size: 0.82rem;
    color: var(--dash-muted);
    margin: 0;
    line-height: 1.45;
}

.dashboard-action-cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid var(--dash-border);
    font-size: 0.8rem;
    font-weight: 600;
}

.dashboard-action-cta::after {
    content: "";
    width: 14px;
    height: 14px;
    background: currentColor;
    mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='9 18 15 12 9 6'/%3E%3C/svg%3E") no-repeat center;
    mask-size: contain;
    opacity: 0.9;
}

.dashboard-action-cta--blue  { color: #2563eb; }
.dashboard-action-cta--green { color: #16a34a; }
.dashboard-action-cta--amber { color: #d97706; }

@media (max-width: 900px) {
    .dashboard-actions { grid-template-columns: 1fr; }
    .dashboard-weekly-wrap { margin-left: 0; margin-right: 0; }
    .dashboard-weekly-grid { min-width: auto !important; }
}

/* ═══════════════════════════════════════════════════════════════
   STUDENT DASHBOARD — Moodle-style student experience
   ══════════════════════════════════════════════════════════════ */

/* ── Alerts ─────────────────────────────────────────────────── */
.stu-alert {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 16px;
    font-size: 0.88rem;
    line-height: 1.5;
    border: 1px solid transparent;
}
.stu-alert svg { flex-shrink: 0; margin-top: 2px; }
.stu-alert strong { display: block; font-weight: 700; margin-bottom: 2px; font-size: 0.9rem; }
.stu-alert p { margin: 0; color: inherit; opacity: 0.85; }
.stu-alert--warning {
    background: #fffbeb;
    border-color: #fde68a;
    color: #92400e;
}
.stu-alert--action {
    background: #eff6ff;
    border-color: #bfdbfe;
    color: #1e40af;
    flex-wrap: wrap;
}
.stu-alert-actions {
    display: flex;
    gap: 8px;
    margin-left: auto;
    flex-shrink: 0;
    align-items: center;
}

/* Program/year chips above stats */
.stu-profile-chips {
    margin: 12px 0 14px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

/* ── Stats row ──────────────────────────────────────────────── */
.stu-stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-top: 8px;
    margin-bottom: 20px;
}
.stu-stat-card {
    background: #ffffff;
    border: 1px solid color-mix(in srgb, var(--admin-primary, #334155) 14%, #e2e8f0);
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 1px 4px rgba(15,23,42,0.05);
    transition: box-shadow 0.18s, transform 0.18s;
}
.stu-stat-card:hover {
    box-shadow: 0 4px 16px rgba(15,23,42,0.09);
    transform: translateY(-1px);
}
.stu-stat-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stu-stat-icon--blue   { background: #dbeafe; color: #2563eb; }
.stu-stat-icon--green  { background: #dcfce7; color: #16a34a; }
.stu-stat-icon--amber  { background: #fef3c7; color: #d97706; }
.stu-stat-icon--purple { background: #ede9fe; color: #7c3aed; }
.stu-stat-body { display: flex; flex-direction: column; }
.stu-stat-num  { font-size: 1.6rem; font-weight: 800; color: #0f172a; line-height: 1; }
.stu-stat-label { font-size: 0.76rem; font-weight: 600; color: #64748b; margin-top: 3px; letter-spacing: 0.02em; }

/* ── Two-column grid ────────────────────────────────────────── */
.stu-main-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 18px;
    margin-bottom: 22px;
    align-items: start;
}

/* ── Shared card ────────────────────────────────────────────── */
.stu-card {
    background: #ffffff;
    border: 1px solid color-mix(in srgb, var(--admin-primary, #334155) 14%, #e2e8f0);
    border-radius: 14px;
    padding: 0;
    box-shadow: 0 1px 4px rgba(15,23,42,0.05);
    overflow: hidden;
}
.stu-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 16px 20px 14px;
    border-bottom: 1px solid #f1f5f9;
}
.stu-card-title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    display: flex;
    align-items: center;
    gap: 10px;
}
.stu-card-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stu-card-icon--blue   { background: #dbeafe; color: #2563eb; }
.stu-card-icon--green  { background: #dcfce7; color: #16a34a; }
.stu-card-icon--purple { background: #ede9fe; color: #7c3aed; }
.stu-card-icon--amber  { background: #fef3c7; color: #d97706; }
.stu-card-footer {
    padding: 10px 20px;
    border-top: 1px solid #f1f5f9;
}

/* ── Badges / chips ─────────────────────────────────────────── */
.stu-badge {
    display: inline-flex;
    align-items: center;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 600;
    padding: 3px 10px;
    letter-spacing: 0.03em;
    border: 1px solid transparent;
}
.stu-badge--blue   { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
.stu-badge--green  { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
.stu-badge--amber  { background: #fef3c7; color: #b45309; border-color: #fde68a; }
.stu-badge--red    { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
.stu-badge--purple { background: #ede9fe; color: #6d28d9; border-color: #ddd6fe; }
.stu-badge--muted  { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }

.stu-chip {
    display: inline-flex;
    align-items: center;
    border-radius: 6px;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 2px 7px;
    letter-spacing: 0.04em;
    border: 1px solid transparent;
}
.stu-chip--blue  { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
.stu-chip--amber { background: #fef3c7; color: #b45309; border-color: #fde68a; }
.stu-chip--now   { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
.stu-chip--done  { background: #f1f5f9; color: #64748b; border-color: #e2e8f0; }
.stu-chip--code  { font-size: 0.72rem; background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; border-radius: 6px; }

/* ── Today schedule ─────────────────────────────────────────── */
.stu-today-list { padding: 8px 0 0; }
.stu-today-item {
    display: flex;
    align-items: stretch;
    gap: 0;
    padding: 10px 20px;
    border-bottom: 1px solid #f8fafc;
    transition: background 0.14s;
}
.stu-today-item:last-child { border-bottom: none; }
.stu-today-item:hover { background: #f8fafc; }
.stu-today-item--done { opacity: 0.55; }
.stu-today-item--now { background: color-mix(in srgb, var(--admin-primary, #334155) 5%, #ffffff); }
.stu-today-time {
    width: 72px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    padding-right: 12px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #64748b;
    line-height: 1.4;
}
.stu-today-time-sep { color: #cbd5e1; font-size: 0.65rem; }
.stu-today-bar {
    width: 3px;
    border-radius: 999px;
    background: #e2e8f0;
    margin-right: 14px;
    flex-shrink: 0;
}
.stu-today-bar--now  { background: var(--admin-primary, #334155); }
.stu-today-bar--done { background: #cbd5e1; }
.stu-today-body { flex: 1; min-width: 0; }
.stu-today-subject {
    font-size: 0.88rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.stu-today-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.stu-today-teacher { font-size: 0.72rem; color: #64748b; }

/* ── Quick actions ──────────────────────────────────────────── */
.stu-actions-list { padding: 6px 0 0; }
.stu-action-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 20px;
    text-decoration: none;
    color: inherit;
    border-bottom: 1px solid #f8fafc;
    transition: background 0.14s;
}
.stu-action-item:last-child { border-bottom: none; }
.stu-action-item:hover { background: #f8fafc; }
.stu-action-icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stu-action-icon--blue   { background: #dbeafe; color: #2563eb; }
.stu-action-icon--green  { background: #dcfce7; color: #16a34a; }
.stu-action-icon--purple { background: #ede9fe; color: #7c3aed; }
.stu-action-icon--amber  { background: #fef3c7; color: #d97706; }
.stu-action-body { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.stu-action-label { font-size: 0.875rem; font-weight: 600; color: #0f172a; }
.stu-action-sub   { font-size: 0.76rem; color: #64748b; }
.stu-action-badge {
    flex-shrink: 0;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    padding: 2px 8px;
    border: 1px solid transparent;
}
.stu-action-badge--amber { background: #fef3c7; color: #b45309; border-color: #fde68a; }

/* ── Subject cards grid ─────────────────────────────────────── */
.stu-subjects-section { margin-bottom: 22px; }
.stu-section-header {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    margin-bottom: 14px;
}
.stu-section-title {
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: #64748b;
    margin: 0;
}
.stu-link {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--admin-primary, #334155);
    text-decoration: none;
}
.stu-link:hover { text-decoration: underline; }

/* ── Upcoming tasks ─────────────────────────────────────────── */
.stu-tasks-card { margin-bottom: 18px; }
.stu-card-header--tasks {
    align-items: flex-start;
    gap: 12px;
}
.stu-task-filters {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.stu-task-select,
.stu-task-search {
    height: 32px;
    border-radius: 999px;
    border: 1px solid #dbe3ee;
    background: #ffffff;
    color: #334155;
    font-size: 0.76rem;
    font-weight: 600;
    outline: none;
}
.stu-task-select { padding: 0 10px; }
.stu-task-search {
    width: 150px;
    padding: 0 12px;
    font-weight: 500;
}
.stu-task-select:focus,
.stu-task-search:focus {
    border-color: var(--admin-primary, #334155);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary, #334155) 16%, transparent);
}
.stu-task-list { padding: 4px 0; }
.stu-task-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    border-bottom: 1px solid #f1f5f9;
}
.stu-task-item:last-child { border-bottom: none; }
.stu-task-dot {
    width: 9px;
    height: 9px;
    border-radius: 999px;
    flex-shrink: 0;
}
.stu-task-dot--high { background: #ef4444; }
.stu-task-dot--medium { background: #f59e0b; }
.stu-task-dot--low { background: #22c55e; }
.stu-task-body { flex: 1; min-width: 0; }
.stu-task-title {
    margin: 0;
    font-size: 0.84rem;
    font-weight: 600;
    color: #0f172a;
}
.stu-task-meta {
    margin: 2px 0 0;
    font-size: 0.73rem;
    color: #64748b;
}
.stu-task-deadline {
    font-size: 0.72rem;
    color: #475569;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 3px 9px;
    flex-shrink: 0;
}
.stu-task-empty {
    padding: 14px 20px 18px;
    color: #64748b;
    font-size: 0.82rem;
}

.stu-subjects-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
}
.stu-subject-card {
    background: #ffffff;
    border: 1px solid color-mix(in srgb, var(--admin-primary, #334155) 14%, #e2e8f0);
    border-radius: 12px;
    padding: 16px 18px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    transition: box-shadow 0.18s, transform 0.18s, border-color 0.18s;
}
.stu-subject-card:hover {
    box-shadow: 0 6px 20px rgba(15,23,42,0.09);
    transform: translateY(-2px);
    border-color: color-mix(in srgb, var(--admin-primary, #334155) 28%, #e2e8f0);
}
.stu-subject-card-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    flex-wrap: wrap;
}
.stu-subject-name {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.35;
    flex: 1;
}
.stu-subject-meta {
    margin: 0;
    font-size: 0.76rem;
    color: #64748b;
    display: flex;
    align-items: flex-start;
    gap: 5px;
    line-height: 1.4;
}
.stu-subject-meta svg { flex-shrink: 0; margin-top: 2px; }
.stu-subject-card-footer {
    margin-top: 6px;
    padding-top: 10px;
    border-top: 1px solid #f1f5f9;
}
.stu-subject-btn { font-size: 0.78rem; padding: 5px 14px; }

/* ── Roles row (compact footer) ─────────────────────────────── */
.stu-roles-row {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.stu-roles-label { font-size: 0.76rem; color: #64748b; font-weight: 600; }

/* ── Empty state ────────────────────────────────────────────── */
.stu-empty {
    padding: 32px 20px;
    text-align: center;
    color: #94a3b8;
}
.stu-empty svg {
    display: block;
    margin: 0 auto 12px;
    opacity: 0.45;
}
.stu-empty p { margin: 0; font-size: 0.875rem; font-weight: 500; color: #64748b; }
.stu-empty-sub { font-size: 0.78rem !important; color: #94a3b8 !important; margin-top: 4px !important; }

/* ── Teacher dashboard redesign ─────────────────────────────── */
.tdb-summary-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 14px;
}
.tdb-summary-card {
    background: #fff;
    border: 1px solid rgba(15,23,42,0.1);
    border-radius: 12px;
    padding: 12px 14px;
}
.tdb-summary-card p {
    margin: 0 0 4px;
    font-size: 0.74rem;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    font-weight: 700;
}
.tdb-summary-card strong { font-size: 1.25rem; color: #0f172a; }

.tdb-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 300px;
    gap: 14px;
    margin-bottom: 14px;
}
.tdb-main,
.tdb-side {
    display: grid;
    gap: 14px;
    align-content: start;
}
.tdb-card .dashboard-card-header { padding-bottom: 12px; }

.tdb-list { display: grid; gap: 8px; padding: 2px 0 2px; }
.tdb-list-item {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 10px;
    align-items: center;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
    background: #fff;
    text-decoration: none;
    color: inherit;
}
.tdb-list-item.is-now {
    border-color: color-mix(in srgb, var(--admin-primary, #334155) 35%, #cbd5e1);
    background: color-mix(in srgb, var(--admin-primary, #334155) 6%, #ffffff);
}
.tdb-list-time { font-size: 0.74rem; color: #64748b; font-weight: 700; white-space: nowrap; }
.tdb-list-body strong { display: block; font-size: 0.84rem; color: #0f172a; margin-bottom: 2px; }
.tdb-list-body span { font-size: 0.75rem; color: #64748b; }

.tdb-classes-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
}
.tdb-class-card {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.tdb-class-code {
    margin: 0;
    font-size: 0.7rem;
    font-weight: 700;
    color: #334155;
    letter-spacing: 0.05em;
    text-transform: uppercase;
}
.tdb-class-card h3 { margin: 0; font-size: 0.88rem; color: #0f172a; }
.tdb-class-card p { margin: 0 0 4px; font-size: 0.76rem; color: #64748b; }

.tdb-side-list {
    margin: 0;
    padding: 0 0 0 18px;
    display: grid;
    gap: 8px;
    color: #475569;
    font-size: 0.82rem;
    line-height: 1.45;
}
.tdb-quick-stats { display: grid; gap: 8px; }
.tdb-quick-stats > div {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 12px;
}
.tdb-quick-stats span { color: #64748b; font-size: 0.78rem; }
.tdb-quick-stats strong { color: #0f172a; font-size: 0.98rem; }

.tdb-legend {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.74rem;
    color: #475569;
    margin-left: 4px;
}
.tdb-legend i {
    width: 10px;
    height: 10px;
    border-radius: 999px;
    display: inline-block;
}
.tdb-legend i.lecture { background: #d1fae5; border: 1px solid #6ee7b7; }
.tdb-legend i.lab { background: #fef3c7; border: 1px solid #fcd34d; }
.tdb-now-line {
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    height: 2px;
    background: rgba(239, 68, 68, 0.35);
}
.dashboard-weekly-time.is-now { background: #fff1f2; color: #be123c; }
.dashboard-weekly-cell.is-now { background: #fffafb; }
.dashboard-weekly-chip {
    text-decoration: none;
    color: inherit;
}

/* ── Responsive ─────────────────────────────────────────────── */
@media (max-width: 1100px) {
    .stu-subjects-grid { grid-template-columns: repeat(2, 1fr); }
    .tdb-summary-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .tdb-layout { grid-template-columns: 1fr; }
    .tdb-side { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (max-width: 900px) {
    .stu-stats-row { grid-template-columns: repeat(2, 1fr); }
    .stu-main-grid { grid-template-columns: 1fr; }
    .stu-subjects-grid { grid-template-columns: repeat(2, 1fr); }
    .tdb-classes-grid { grid-template-columns: 1fr; }
    .tdb-side { grid-template-columns: 1fr; }
}
@media (max-width: 560px) {
    .stu-stats-row { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .stu-subjects-grid { grid-template-columns: 1fr; }
    .stu-alert--action { flex-direction: column; }
    .stu-alert-actions { margin-left: 0; }
    .stu-task-filters { width: 100%; margin-left: 0; }
    .stu-task-select, .stu-task-search { width: 100%; border-radius: 10px; }
    .stu-task-item { align-items: flex-start; }
}
</style>
@endpush
