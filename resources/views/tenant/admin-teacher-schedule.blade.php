@extends('layouts.app')

@section('title', 'Teacher Weekly Schedule - Admin')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'admin', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body teacher-schedule-page">
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <a href="{{ url('/tenant/admin?tab=classes') }}">Admin · Classes</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <span>Teacher Schedule</span>
                </div>
                <h1>Teacher Weekly Schedule</h1>
                <p>View teaching load by teacher for {{ $currentTermLabel ?? 'current semester' }}.</p>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Show Teacher</h2>
                    <form id="teacher-view-form" method="get" action="{{ url('/tenant/admin/classes/teacher-schedule') }}" class="inline class-filter-form" style="gap:8px; align-items:center;">
                        <label for="teacher-view-select" class="text-muted" style="font-size:0.8rem;">Select teacher</label>
                        <select id="teacher-view-select" name="teacher_user_id" style="min-width:220px;">
                            <option value="">Select teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ (int) $selectedTeacherUserId === (int) $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name }}{{ $teacher->email ? ' | ' . $teacher->email : '' }}
                                </option>
                            @endforeach
                        </select>
                        @if(request('term_code'))
                            <input type="hidden" name="term_code" value="{{ request('term_code') }}">
                        @endif
                    </form>
                    @if($selectedTeacher)
                        <span class="badge blue">{{ $selectedTeacher->full_name }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if(!$selectedTeacher)
                        <p class="text-muted">Select a teacher to show weekly teaching schedule.</p>
                    @else
                        @php
                            $gridDays = [1 => 'Mon', 2 => 'Tue', 3 => 'Wed', 4 => 'Thu', 5 => 'Fri'];
                            $teacherSessionsByDay = $teacherWeeklySessions->groupBy('day_of_week');
                            $teacherGridStartMin = $teacherWeeklySessions->isNotEmpty()
                                ? $teacherWeeklySessions->map(function ($session) {
                                    return ((int) substr((string) $session->start_time, 0, 2) * 60) + (int) substr((string) $session->start_time, 3, 2);
                                })->min()
                                : 420;
                            $teacherGridEndMin = $teacherWeeklySessions->isNotEmpty()
                                ? $teacherWeeklySessions->map(function ($session) {
                                    return ((int) substr((string) $session->end_time, 0, 2) * 60) + (int) substr((string) $session->end_time, 3, 2);
                                })->max()
                                : 1020;
                            $teacherGridStartMin = max(360, ((int) floor($teacherGridStartMin / 60)) * 60);
                            $teacherGridEndMin = min(1320, ((int) ceil($teacherGridEndMin / 60)) * 60);
                            if ($teacherGridEndMin <= $teacherGridStartMin) {
                                $teacherGridStartMin = 420;
                                $teacherGridEndMin = 1020;
                            }
                            $teacherGridSlot = 30;
                        @endphp
                        @if($teacherWeeklySessions->isEmpty())
                            <p class="text-muted">No scheduled sessions assigned for this teacher yet.</p>
                        @else
                            <div class="weekly-grid-wrap">
                                <div class="weekly-grid">
                                    <div class="weekly-grid-head time-col">Time</div>
                                    @foreach($gridDays as $dayKey => $dayLabel)
                                        <div class="weekly-grid-head">{{ $dayLabel }}</div>
                                    @endforeach

                                    @for($slotMin = $teacherGridStartMin; $slotMin < $teacherGridEndMin; $slotMin += $teacherGridSlot)
                                        @php
                                            $slotLabel = ($slotMin % 60 === 0) ? sprintf('%02d:%02d', intdiv($slotMin, 60), 0) : '';
                                            $slotEnd = $slotMin + $teacherGridSlot;
                                        @endphp
                                        <div class="weekly-grid-time {{ $slotMin % 60 === 30 ? 'half' : 'hour' }}">{{ $slotLabel }}</div>
                                        @foreach($gridDays as $dayKey => $dayLabel)
                                            @php
                                                $hits = collect($teacherSessionsByDay->get($dayKey, []))->filter(function ($session) use ($slotMin, $slotEnd) {
                                                    $start = ((int) substr((string) $session->start_time, 0, 2) * 60) + (int) substr((string) $session->start_time, 3, 2);
                                                    return $start >= $slotMin && $start < $slotEnd;
                                                })->values();
                                            @endphp
                                            <div class="weekly-grid-cell {{ $slotMin % 60 === 30 ? 'half' : 'hour' }}">
                                                @foreach($hits as $hitIndex => $hit)
                                                    @php
                                                        $hitStart = ((int) substr((string) $hit->start_time, 0, 2) * 60) + (int) substr((string) $hit->start_time, 3, 2);
                                                        $hitEnd = ((int) substr((string) $hit->end_time, 0, 2) * 60) + (int) substr((string) $hit->end_time, 3, 2);
                                                        $durationMinutes = max($teacherGridSlot, $hitEnd - $hitStart);
                                                        $slotHeightPx = 38;
                                                        $topPx = (int) round((($hitStart - $slotMin) / $teacherGridSlot) * $slotHeightPx) + 2;
                                                        $heightPx = max(28, (int) round(($durationMinutes / $teacherGridSlot) * $slotHeightPx) - 4);
                                                        $insetPx = 2 + ((int) $hitIndex * 4);
                                                    @endphp
                                                    <div class="weekly-grid-chip weekly-grid-chip-span {{ $hit->session_type === 'lecture' ? 'lecture' : 'lab' }}" style="top: {{ $topPx }}px; height: {{ $heightPx }}px; left: {{ $insetPx }}px; right: {{ $insetPx }}px;">
                                                        <strong>{{ $hit->subject->code ?? 'SUBJ' }}</strong>
                                                        <span>{{ substr((string) $hit->start_time, 0, 5) }}-{{ substr((string) $hit->end_time, 0, 5) }}</span>
                                                        <small>{{ $hit->classGroup->name ?? 'Class Group' }}</small>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    @endfor
                                </div>
                            </div>
                        @endif
                        <div class="table-wrap">
                            <table class="class-session-table">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>Class Group</th>
                                        <th>Program</th>
                                        <th>Subject</th>
                                        <th>Session</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teacherWeeklySessions as $session)
                                        <tr>
                                            <td>{{ [1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat',7=>'Sun'][(int) $session->day_of_week] ?? $session->day_of_week }}</td>
                                            <td>{{ substr((string) $session->start_time, 0, 5) }} - {{ substr((string) $session->end_time, 0, 5) }}</td>
                                            <td>{{ $session->classGroup->name ?? 'N/A' }}</td>
                                            <td>{{ $session->classGroup->program->code ?? '' }} {{ $session->classGroup->semester->term_code ?? '' }}</td>
                                            <td>
                                                <span class="badge blue">{{ $session->subject->code ?? 'N/A' }}</span>
                                                {{ $session->subject->title ?? '' }}
                                            </td>
                                            <td><span class="badge {{ $session->session_type === 'lecture' ? 'green' : 'amber' }}">{{ strtoupper($session->session_type) }}</span></td>
                                            <td><span class="badge {{ $session->status === 'locked' ? 'purple' : 'blue' }}">{{ $session->status }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-muted">No sessions yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <p class="teacher-schedule-back">
                <a href="{{ url('/tenant/admin?tab=classes') }}" class="btn ghost">← Back to Classes</a>
            </p>
        </main>
    </div>
</div>

@push('styles')
<style>
.teacher-schedule-page .page-header { margin-bottom: 20px; }
.teacher-schedule-page .breadcrumb a { color: var(--accent); }
.teacher-schedule-back { margin-top: 20px; }
.class-filter-form { flex-wrap: wrap; margin: 0; border: 1px solid #dce6f6; background: #f8fbff; border-radius: 12px; padding: 8px 10px; gap: 8px; }
.class-filter-form select { min-height: 36px; border-radius: 10px; border-color: #c8d7f2; background: #fff; }
.class-filter-form label { font-weight: 600; }
.weekly-grid-wrap { border: 1px solid #d6e1f4; border-radius: 14px; margin-bottom: 12px; overflow: auto; background: linear-gradient(180deg, #fff 0%, #fbfdff 100%); }
.weekly-grid { display: grid; grid-template-columns: 110px repeat(5, minmax(180px, 1fr)); min-width: 980px; }
.weekly-grid-head { font-weight: 700; font-size: 0.82rem; letter-spacing: .03em; text-transform: uppercase; color: var(--muted); background: #f7faff; border-bottom: 1px solid var(--border); border-right: 1px solid var(--border); padding: 8px; position: sticky; top: 0; z-index: 4; }
.weekly-grid-head.time-col { left: 0; z-index: 6; }
.weekly-grid-time { border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 8px; font-size: 0.82rem; color: var(--muted); font-weight: 600; background: #fcfdff; position: sticky; left: 0; z-index: 3; }
.weekly-grid-time.hour { font-weight: 700; color: #3f5480; border-bottom-color: transparent; }
.weekly-grid-time.half { color: transparent; border-bottom-style: solid; border-bottom-color: var(--border); }
.weekly-grid-cell { border-right: 1px solid var(--border); border-bottom: 1px solid var(--border); min-height: 38px; height: 38px; padding: 0; position: relative; overflow: visible; background: rgba(255,255,255,0.7); }
.weekly-grid-cell.hour { border-bottom-color: transparent; }
.weekly-grid-chip { border-radius: 8px; padding: 4px 6px; display: grid; gap: 2px; font-size: 0.74rem; line-height: 1.2; }
.weekly-grid-chip-span { position: absolute; z-index: 5; box-sizing: border-box; overflow: hidden; box-shadow: 0 3px 10px rgba(15,23,42,0.1); border: 1px solid rgba(15,23,42,0.08); }
.weekly-grid-chip.lecture { background: #dcfce7; color: #166534; }
.weekly-grid-chip.lab { background: #fef3c7; color: #92400e; }
.teacher-schedule-page .table-wrap { border: 1px solid #d6e1f4; border-radius: 12px; overflow: auto; background: #fff; }
.teacher-schedule-page .class-session-table { margin: 0; }
.teacher-schedule-page .class-session-table th { position: sticky; top: 0; z-index: 2; background: #f3f8ff; }
.teacher-schedule-page .class-session-table tbody tr:nth-child(even) { background: #fbfdff; }
.teacher-schedule-page .class-session-table tbody tr:hover { background: #eef5ff; }
.class-session-table td { vertical-align: top; }
</style>
@endpush

@push('scripts')
<script>
(function() {
    var form = document.getElementById('teacher-view-form');
    var select = document.getElementById('teacher-view-select');
    if (form && select) {
        var submitTimer;
        select.addEventListener('change', function() {
            if (submitTimer) clearTimeout(submitTimer);
            submitTimer = setTimeout(function() { form.submit(); }, 80);
        });
    }
})();
</script>
@endpush
@endsection
