@extends('layouts.app')

@section('title', 'Dashboard - School Portal')

@section('content')
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
<div class="app-shell">
    @include('tenant.partials.sidebar', ['active' => 'dashboard'])

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">Dashboard</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="avatar">
                        {{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}
                    </div>
                    <span>{{ auth()->user()->full_name ?? 'User' }}</span>
                </div>
            </div>
        </header>

        <main class="page-body dashboard-body">
            <div class="dashboard-welcome">
                <h1 class="dashboard-welcome-title">Welcome back</h1>
                <p class="dashboard-welcome-sub">Here's an overview of your school portal activity.</p>
                @if (!empty($studentProfile))
                    <div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">
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
            </div>

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

            <section class="dashboard-section">
                <div class="dashboard-card">
                    <div class="dashboard-card-header">
                        <h2 class="dashboard-card-title">
                            <span class="dashboard-card-icon dashboard-card-icon--purple">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                            </span>
                            Your Active Roles
                        </h2>
                    </div>
                    <div class="dashboard-roles">
                        @forelse($roleCodes as $role)
                            @php
                                $roleColors = [
                                    'student' => 'blue',
                                    'teacher' => 'green',
                                    'dean' => 'purple',
                                    'registrar_staff' => 'amber',
                                    'finance_staff' => 'red',
                                    'school_admin' => 'red',
                                    'student_pending' => 'amber',
                                ];
                                $color = $roleColors[strtolower($role)] ?? 'neutral';
                            @endphp
                            <span class="dashboard-badge dashboard-badge--{{ $color }}">{{ strtoupper($role) }}</span>
                        @empty
                            <span class="dashboard-muted">No roles assigned yet.</span>
                        @endforelse
                    </div>
                </div>
            </section>

            @if (in_array('teacher', (array) $roleCodes, true))
                <section class="dashboard-section">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h2 class="dashboard-card-title">
                                <span class="dashboard-card-icon dashboard-card-icon--green">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                    </svg>
                                </span>
                                Your Teachable Subjects
                            </h2>
                        </div>
                        @if (($teachableSubjects ?? collect())->isEmpty())
                            <p class="dashboard-muted dashboard-card-desc">No teachable subjects assigned yet. Contact school administration to assign your subject load.</p>
                        @else
                            <div class="dashboard-table-wrap">
                                <table class="dashboard-table">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Subject</th>
                                            <th>Weekly Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($teachableSubjects as $subject)
                                            <tr>
                                                <td><span class="dashboard-badge dashboard-badge--blue">{{ $subject->code }}</span></td>
                                                <td>{{ $subject->title }}</td>
                                                <td>{{ rtrim(rtrim(number_format((float) $subject->weekly_hours, 1, '.', ''), '0'), '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="dashboard-section">
                    <div class="dashboard-card">
                        <div class="dashboard-card-header">
                            <h2 class="dashboard-card-title">
                                <span class="dashboard-card-icon dashboard-card-icon--purple">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/>
                                        <path d="M3 10h18"/>
                                    </svg>
                                </span>
                                Weekly Timetable
                            </h2>
                            <span class="dashboard-badge dashboard-badge--purple">{{ (int) (($teacherWeeklyScheduleSummary['total_sessions'] ?? 0)) }} sessions</span>
                        </div>
                        <div class="dashboard-timetable-meta">
                            <span class="dashboard-badge dashboard-badge--blue">{{ (int) (($teacherWeeklyScheduleSummary['subjects'] ?? 0)) }} subjects</span>
                            <span class="dashboard-badge dashboard-badge--green">{{ (int) (($teacherWeeklyScheduleSummary['days_covered'] ?? 0)) }} days</span>
                            <span class="dashboard-badge dashboard-badge--amber">{{ (int) (($teacherWeeklyScheduleSummary['class_groups'] ?? 0)) }} class groups</span>
                        </div>

                    @php
                        $scheduleRows = collect($teacherWeeklyScheduleRows ?? []);
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

                    @if($scheduleRows->isEmpty())
                        <div class="dashboard-empty">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/>
                                <path d="M3 10h18"/>
                            </svg>
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
                                    @endphp
                                    <div class="dashboard-weekly-time {{ $slotMin % 60 === 30 ? 'half' : 'hour' }}">{{ $slotLabel }}</div>
                                    @foreach($gridDays as $dayKey => $dayLabel)
                                        @php
                                            $hits = collect($sessionsByDay->get($dayKey, []))
                                                ->filter(fn ($s) => (int) ($s['start_min'] ?? 0) >= $slotMin && (int) ($s['start_min'] ?? 0) < $slotEnd)
                                                ->values();
                                        @endphp
                                        <div class="dashboard-weekly-cell {{ $slotMin % 60 === 30 ? 'half' : 'hour' }}">
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
                                                <div class="dashboard-weekly-chip dashboard-weekly-chip--{{ $chipClass }}" style="top:{{ $topPx }}px; height:{{ $heightPx }}px; left:{{ $insetPx }}px; right:{{ $insetPx }}px;">
                                                    <strong>{{ $hit['subject_code'] ?? 'SUBJ' }}</strong>
                                                    <span>{{ $hit['start_time'] ?? '' }}-{{ $hit['end_time'] ?? '' }}</span>
                                                    <small>{{ $hit['class_group'] ?? 'N/A' }}</small>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                @endfor
                            </div>
                        </div>

                        <div class="dashboard-table-wrap">
                            <table class="dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>Subject</th>
                                        <th>Class Group</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($scheduleRows as $row)
                                        <tr>
                                            <td class="dashboard-table-cell--strong">{{ $row['day_label'] ?? 'N/A' }}</td>
                                            <td class="dashboard-table-cell--muted">{{ $row['time_label'] ?? 'N/A' }}</td>
                                            <td>
                                                <div class="dashboard-table-cell--strong">{{ $row['subject_title'] ?? 'Untitled Subject' }}</div>
                                                <span class="dashboard-badge dashboard-badge--blue dashboard-badge--sm">{{ $row['subject_code'] ?? 'N/A' }}</span>
                                            </td>
                                            <td><span class="dashboard-badge dashboard-badge--neutral">{{ $row['class_group'] ?? 'N/A' }}</span></td>
                                            <td><span class="dashboard-badge dashboard-badge--purple">{{ $row['session_type'] ?? 'CLASS' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ─── Dashboard theme: page body ────────────────────────────────────── */
.dashboard-body {
    --dash-bg: #f1f5f9;
    --dash-surface: #ffffff;
    --dash-border: #e2e8f0;
    --dash-ink: #0f172a;
    --dash-ink-2: #475569;
    --dash-muted: #64748b;
    --dash-accent: #2563eb;
    --dash-radius: 12px;
    --dash-radius-lg: 16px;
    --dash-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    --dash-shadow-md: 0 4px 12px rgba(15, 23, 42, 0.08);
    background: var(--dash-bg);
    padding: 28px 24px 40px;
    max-width: 100%;
    margin: 0 auto;
    box-sizing: border-box;
    overflow-x: hidden;
}

.dashboard-alert {
    max-width: 100%;
    border-radius: var(--dash-radius);
    margin-bottom: 20px;
}

.dashboard-welcome {
    margin-bottom: 28px;
}

.dashboard-welcome-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--dash-ink);
    margin: 0 0 4px;
    letter-spacing: -0.02em;
}

.dashboard-welcome-sub {
    font-size: 0.95rem;
    color: var(--dash-muted);
    margin: 0;
}

.dashboard-section {
    margin-bottom: 24px;
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
    border: 1px solid var(--dash-border);
    border-radius: var(--dash-radius-lg);
    padding: 22px 24px;
    box-shadow: var(--dash-shadow);
    transition: box-shadow 0.2s ease, border-color 0.2s ease;
}

.dashboard-card:hover {
    box-shadow: var(--dash-shadow-md);
}

.dashboard-card--highlight {
    border-color: rgba(37, 99, 235, 0.35);
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.06), rgba(124, 58, 237, 0.05));
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
}

.dashboard-badge--sm {
    font-size: 0.7rem;
    padding: 3px 8px;
}

.dashboard-badge--blue   { background: #dbeafe; color: #1d4ed8; }
.dashboard-badge--green  { background: #dcfce7; color: #15803d; }
.dashboard-badge--purple { background: #ede9fe; color: #6d28d9; }
.dashboard-badge--amber  { background: #fef3c7; color: #b45309; }
.dashboard-badge--red    { background: #fee2e2; color: #b91c1c; }
.dashboard-badge--neutral { background: #f1f5f9; color: var(--dash-ink-2); }

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

.dashboard-table-cell--strong { font-weight: 600; color: var(--dash-ink); }
.dashboard-table-cell--muted { color: var(--dash-muted); }

.dashboard-timetable-meta {
    padding: 0 4px 14px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
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
    .dashboard-body { padding: 20px 16px 32px; }
    .dashboard-actions { grid-template-columns: 1fr; }
    .dashboard-weekly-wrap { margin-left: 0; margin-right: 0; }
    .dashboard-weekly-grid { min-width: auto !important; }
}
</style>
@endpush
