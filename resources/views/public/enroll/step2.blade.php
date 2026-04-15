@extends('layouts.app')

@section('title', 'Enrollment - Step 2: Select Program and Preferred Schedule')

@section('content')
@php
    $enrollThemeKey = strtolower((string) ($school->theme ?? 'blue'));
    $enrollThemeMap = [
        'blue' => ['accent' => '#2563eb', 'accent_h' => '#1d4ed8', 'accent_l' => '#eff6ff', 'accent_l2' => '#dbeafe', 'bg_1' => '#eff6ff', 'bg_2' => '#f4f7fe', 'bg_3' => '#eef6ff'],
        'green' => ['accent' => '#15803d', 'accent_h' => '#166534', 'accent_l' => '#ecfdf5', 'accent_l2' => '#dcfce7', 'bg_1' => '#ecfdf5', 'bg_2' => '#f0fdf4', 'bg_3' => '#ecfdf5'],
        'indigo' => ['accent' => '#4f46e5', 'accent_h' => '#4338ca', 'accent_l' => '#eef2ff', 'accent_l2' => '#e0e7ff', 'bg_1' => '#eef2ff', 'bg_2' => '#f5f3ff', 'bg_3' => '#eef2ff'],
        'slate' => ['accent' => '#475569', 'accent_h' => '#334155', 'accent_l' => '#f1f5f9', 'accent_l2' => '#e2e8f0', 'bg_1' => '#f1f5f9', 'bg_2' => '#f8fafc', 'bg_3' => '#f1f5f9'],
        'teal' => ['accent' => '#0f766e', 'accent_h' => '#0f766e', 'accent_l' => '#f0fdfa', 'accent_l2' => '#ccfbf1', 'bg_1' => '#f0fdfa', 'bg_2' => '#f7fffd', 'bg_3' => '#ecfeff'],
        'amber' => ['accent' => '#d97706', 'accent_h' => '#b45309', 'accent_l' => '#fffbeb', 'accent_l2' => '#fde68a', 'bg_1' => '#fffbeb', 'bg_2' => '#fffbf1', 'bg_3' => '#fef3c7'],
        'rose' => ['accent' => '#e11d48', 'accent_h' => '#be123c', 'accent_l' => '#fff1f2', 'accent_l2' => '#fecdd3', 'bg_1' => '#fff1f2', 'bg_2' => '#fff5f7', 'bg_3' => '#ffe4e6'],
        'purple' => ['accent' => '#7c3aed', 'accent_h' => '#6d28d9', 'accent_l' => '#f5f3ff', 'accent_l2' => '#ddd6fe', 'bg_1' => '#f5f3ff', 'bg_2' => '#faf5ff', 'bg_3' => '#f3e8ff'],
        'emerald' => ['accent' => '#059669', 'accent_h' => '#047857', 'accent_l' => '#ecfdf5', 'accent_l2' => '#a7f3d0', 'bg_1' => '#ecfdf5', 'bg_2' => '#f0fdf4', 'bg_3' => '#d1fae5'],
        'sky' => ['accent' => '#0284c7', 'accent_h' => '#0369a1', 'accent_l' => '#e0f2fe', 'accent_l2' => '#bae6fd', 'bg_1' => '#e0f2fe', 'bg_2' => '#f0f9ff', 'bg_3' => '#e0f2fe'],
    ];
    $enrollTheme = $enrollThemeMap[$enrollThemeKey] ?? $enrollThemeMap['blue'];
@endphp
<div style="min-height:100vh; --accent:{{ $enrollTheme['accent'] }}; --accent-h:{{ $enrollTheme['accent_h'] }}; --accent-l:{{ $enrollTheme['accent_l'] }}; --accent-l2:{{ $enrollTheme['accent_l2'] }}; background:linear-gradient(160deg,{{ $enrollTheme['bg_1'] }} 0%,{{ $enrollTheme['bg_2'] }} 55%,{{ $enrollTheme['bg_3'] }} 100%);">
    <nav style="background:rgba(255,255,255,.9); backdrop-filter: blur(10px); border-bottom:1px solid var(--border); position:sticky; top:0; z-index:40;">
        <div style="max-width:1180px; margin:0 auto; padding:0 24px; height:62px; display:flex; align-items:center; justify-content:space-between; gap:12px;">
            <a href="{{ route('school.enroll', ['school_code' => $school->school_code]) }}" style="display:flex; align-items:center; gap:10px; text-decoration:none; min-width:0;">
                <div style="width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,var(--accent),var(--accent-h)); display:flex; align-items:center; justify-content:center; box-shadow:0 8px 18px rgba(15,23,42,.18); flex-shrink:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <span style="font-weight:700; color:var(--ink); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $school->name }}</span>
            </a>
            <span class="badge blue">Step 2 of 4</span>
        </div>
    </nav>

    <div style="max-width:1180px; margin:0 auto; padding:40px 24px 56px;">
        <div style="display:flex; align-items:center; justify-content:center; gap:8px; margin-bottom:30px; flex-wrap:wrap;">
            @foreach(['Personal Info', 'Select Program', 'Review', 'Payment'] as $i => $step)
            @php $stepNum = $i + 1; @endphp
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.78rem; font-weight:700; {{ $stepNum <= 2 ? 'background:linear-gradient(135deg,var(--accent),var(--accent-h)); color:#fff;' : 'background:var(--surface-2); color:var(--muted); border:1px solid var(--border);' }}">
                    @if ($stepNum < 2)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        {{ $stepNum }}
                    @endif
                </div>
                <span style="font-size:0.84rem; font-weight:700; {{ $stepNum <= 2 ? 'color:var(--ink);' : 'color:var(--muted);' }}">{{ $step }}</span>
                @if ($stepNum < 4)
                <div style="width:28px; height:2px; background:{{ $stepNum < 2 ? 'var(--accent)' : 'var(--border)' }};"></div>
                @endif
            </div>
            @endforeach
        </div>

        <div style="background:var(--accent-l); border:1px solid var(--accent-l2); border-radius:14px; padding:14px 18px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
            <div style="display:flex; align-items:center; gap:10px; min-width:0;">
                <strong style="color:var(--accent); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $applicant['full_name'] }}</strong>
                <span style="color:var(--muted);">|</span>
                <span style="color:var(--ink-2); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $applicant['email'] }}</span>
            </div>
            @if ($program)
                <span class="badge blue">{{ $program->name }}</span>
            @else
                <span class="badge">Program not selected yet</span>
            @endif
        </div>

        <div style="display:grid; grid-template-columns: 1.1fr .9fr; gap:14px; margin-bottom:16px;">
            <div style="background:#fff; border:1px solid var(--border); border-radius:16px; padding:18px 20px; box-shadow:var(--shadow-md);">
                <h1 style="margin:0; font-size:1.34rem; line-height:1.2;">Build Your Ideal Weekly Schedule</h1>
                <p style="margin:8px 0 0; color:var(--muted); font-size:0.88rem;">
                    Pick one class schedule per subject. Conflicts and full slots are automatically prevented.
                </p>
                <p style="margin:10px 0 0; font-size:0.84rem; color:var(--ink-2);">
                    Semester: <strong>{{ $semester?->name ?? 'Current Semester' }}</strong>
                </p>
            </div>
            <div style="background:linear-gradient(135deg,var(--accent-h),var(--accent)); border-radius:16px; padding:16px 18px; color:#ffffff; box-shadow:0 10px 26px rgba(15,23,42,.24); display:flex; flex-direction:column; justify-content:center;">
                <div style="font-size:0.75rem; letter-spacing:.08em; text-transform:uppercase; opacity:.9;">Selection Progress</div>
                <div id="selection-counter" style="font-size:1.5rem; font-weight:800; line-height:1.1; margin-top:4px;">0 / 0</div>
                <div id="selection-hint" style="font-size:0.82rem; margin-top:6px; opacity:.95;">Select program and load schedules to start.</div>
            </div>
        </div>

        <div style="background:white; border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow-lg); overflow:hidden;">
            <div style="padding:20px 24px 8px; border-bottom:1px solid var(--border);">
                <form method="get" action="{{ route('enroll.step2', ['school_code' => $school->school_code]) }}" class="stack">
                    <div class="inline" style="margin-bottom:6px;">
                        <div class="form-group">
                            <label for="program_id_preview">Program <span style="color:var(--red);">*</span></label>
                            <select id="program_id_preview" name="program_id" required>
                                <option value="">Select a program</option>
                                @foreach ($programs as $item)
                                    @php
                                        $levelsForProgram = (array) ($yearLevelsByProgram[(int) $item->id] ?? []);
                                    @endphp
                                    <option
                                        value="{{ $item->id }}"
                                        data-year-levels='@json(array_values($levelsForProgram))'
                                        {{ (int) ($selectedProgramId ?? 0) === (int) $item->id ? 'selected' : '' }}
                                    >
                                        {{ $item->name }} ({{ strtoupper($item->degree_level) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="year_level_preview">Year Level <span style="color:var(--red);">*</span></label>
                            <select id="year_level_preview" name="year_level" required>
                                <option value="">Select year level</option>
                                @foreach(($availableYearLevels ?? []) as $level)
                                    <option value="{{ $level }}" {{ (int) ($selectedYearLevel ?? 0) === (int) $level ? 'selected' : '' }}>
                                        Year {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display:flex; justify-content:flex-end;">
                        <button class="btn sm primary" type="submit">Load Schedules</button>
                    </div>
                </form>
            </div>

            <form method="post" action="{{ route('enroll.step2', ['school_code' => $school->school_code]) }}" style="padding:14px 24px 24px;" id="subject-selection-form">
                @csrf
                <input type="hidden" name="program_id" value="{{ (int) ($selectedProgramId ?? 0) }}">
                <input type="hidden" name="year_level" value="{{ (int) ($selectedYearLevel ?? 0) }}">

                @if ($errors->any())
                    <div class="alert error" style="margin-bottom:16px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                @if ((int) ($selectedProgramId ?? 0) <= 0 || (int) ($selectedYearLevel ?? 0) <= 0)
                    <div style="padding:14px; border:1px dashed var(--border); border-radius:12px; color:var(--muted); background:#f8fbff;">
                        Select your program and year level, then click <strong>Load Schedules</strong>.
                    </div>
                @elseif (empty($subjectScheduleRows ?? []))
                    <div style="padding:14px; border:1px dashed var(--border); border-radius:12px; color:var(--muted); background:#f8fbff;">
                        No generated schedules found for this program/year level. Ask admin to generate schedules first.
                    </div>
                @else
                    @php
                        $hasFullSubject = collect($subjectScheduleRows)->contains(function ($row) {
                            return collect((array) ($row['options'] ?? []))
                                ->filter(fn ($option) => (int) ($option['remaining'] ?? 0) > 0)
                                ->isEmpty();
                        });
                    @endphp

                    <div style="display:grid; grid-template-columns:260px minmax(0,1fr); gap:14px; align-items:start;" id="scheduler-layout">
                        <aside style="position:sticky; top:84px; background:#f8fbff; border:1px solid #dbe7ff; border-radius:14px; padding:12px; max-height:72vh; overflow:auto;">
                            <div style="font-size:0.78rem; color:#4f6b95; letter-spacing:.06em; text-transform:uppercase; font-weight:800;">Subject Checklist</div>
                            <div id="conflict-panel" style="display:none; margin-top:8px; border:1px solid #fecaca; background:#fff1f2; color:#9f1239; border-radius:10px; padding:10px;">
                                <div style="font-weight:800; font-size:0.77rem; text-transform:uppercase; letter-spacing:.04em;">Schedule Conflicts</div>
                                <div id="conflict-summary" style="font-size:0.78rem; margin-top:4px;"></div>
                                <div id="conflict-list" style="font-size:0.75rem; margin-top:6px; display:grid; gap:4px;"></div>
                            </div>
                            <div style="margin-top:8px; display:grid; gap:8px;" id="subject-checklist">
                                @foreach(($subjectScheduleRows ?? []) as $row)
                                    @php
                                        $subjectId = (int) ($row['subject_id'] ?? 0);
                                        $selectedGroupId = (int) old('subject_choice.' . $subjectId, $selectedSubjectChoice[$subjectId] ?? 0);
                                        $optionsCount = collect((array) ($row['options'] ?? []))->filter(fn ($option) => (int) ($option['remaining'] ?? 0) > 0)->count();
                                    @endphp
                                    <button type="button" data-scroll-subject="{{ $subjectId }}" style="text-align:left; border:1px solid #d4e1fb; background:white; border-radius:10px; padding:10px; cursor:pointer; width:100%;">
                                        <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                                            <strong style="color:#16315f; font-size:0.84rem;">{{ $row['subject_code'] ?? 'SUBJ' }}</strong>
                                            <span data-check-status="{{ $subjectId }}" style="font-size:0.72rem; color:{{ $selectedGroupId > 0 ? '#16a34a' : '#64748b' }}; font-weight:700;">{{ $selectedGroupId > 0 ? 'Selected' : 'Pending' }}</span>
                                        </div>
                                        <div data-check-options="{{ $subjectId }}" style="font-size:0.74rem; color:#5b6b87; margin-top:3px;">
                                            {{ $optionsCount }} available option{{ $optionsCount === 1 ? '' : 's' }}
                                        </div>
                                        <div
                                            data-check-conflict="{{ $subjectId }}"
                                            style="display:block; font-size:0.71rem; color:#64748b; margin-top:4px; line-height:1.25;"
                                        >Waiting for selection</div>
                                    </button>
                                @endforeach
                            </div>
                        </aside>

                        <div style="display:grid; gap:14px;" id="subject-list">
                            @foreach(($subjectScheduleRows ?? []) as $row)
                                @php
                                    $subjectId = (int) ($row['subject_id'] ?? 0);
                                    $selectedGroupId = (int) old('subject_choice.' . $subjectId, $selectedSubjectChoice[$subjectId] ?? 0);
                                @endphp
                                <section
                                    data-subject-card="{{ $subjectId }}"
                                    data-subject-code="{{ $row['subject_code'] ?? 'SUBJ' }}"
                                    data-subject-index="{{ $loop->index }}"
                                    style="border:1px solid var(--border); border-radius:14px; overflow:hidden; background:white; display:none;"
                                >
                                    <div style="padding:12px 14px; border-bottom:1px solid var(--border); background:#f8fbff; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                                        <div>
                                            <div style="font-weight:800; color:var(--ink);">{{ $row['subject_code'] ?? 'SUBJ' }}</div>
                                            <div style="font-size:0.82rem; color:var(--ink-2);">{{ $row['subject_title'] ?? '' }} | {{ number_format((float) ($row['units'] ?? 0), 1) }} units</div>
                                        </div>
                                        <span style="font-size:0.74rem; font-weight:700; color:#1d4ed8; background:#dbeafe; border:1px solid #bfdbfe; border-radius:999px; padding:5px 10px;">
                                            Choose one option
                                        </span>
                                    </div>

                                    <div style="padding:12px; display:grid; gap:10px;">
                                        @foreach(($row['options'] ?? []) as $option)
                                            @php
                                                $optionGroupId = (int) ($option['class_group_id'] ?? 0);
                                                $isFull = (int) ($option['remaining'] ?? 0) <= 0;
                                                $inputId = 'subject_choice_' . $subjectId . '_' . $optionGroupId;
                                            @endphp
                                            <label for="{{ $inputId }}" style="display:block; border:1px solid {{ $selectedGroupId === $optionGroupId ? '#3b82f6' : 'var(--border)' }}; border-radius:12px; padding:12px; background:{{ $isFull ? '#fff5f5' : ($selectedGroupId === $optionGroupId ? '#eff6ff' : '#fbfdff') }}; cursor:{{ $isFull ? 'not-allowed' : 'pointer' }};">
                                                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                                                    <div style="display:flex; gap:10px; align-items:flex-start;">
                                                        <input
                                                            id="{{ $inputId }}"
                                                            type="radio"
                                                            name="subject_choice[{{ $subjectId }}]"
                                                            value="{{ $optionGroupId }}"
                                                            {{ $selectedGroupId === $optionGroupId ? 'checked' : '' }}
                                                            {{ $isFull ? 'disabled' : '' }}
                                                            required
                                                            data-subject-radio="{{ $subjectId }}"
                                                            data-subject-code="{{ $row['subject_code'] ?? 'SUBJ' }}"
                                                            data-group-name="{{ $option['class_group_name'] ?? '?' }}"
                                                            data-sessions='@json($option["sessions"] ?? [])'
                                                            style="margin-top:4px;"
                                                        >
                                                        <div>
                                                            <div style="font-weight:800; color:var(--ink);">Group {{ $option['class_group_name'] ?? '?' }}</div>
                                                            <div style="display:grid; gap:3px; margin-top:6px;">
                                                                @foreach(($option['sessions'] ?? []) as $session)
                                                                    <div style="font-size:0.82rem; color:var(--ink-2);">
                                                                        <strong>{{ $session['day'] ?? 'Day' }}</strong>
                                                                        {{ $session['start_time'] ?? '' }}-{{ $session['end_time'] ?? '' }}
                                                                        <span style="color:var(--muted);">| {{ $session['teacher_name'] ?? 'TBA' }}</span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="text-align:right; min-width:120px;">
                                                        <span class="badge {{ $isFull ? 'red' : 'blue' }}">{{ (int) ($option['enrolled_count'] ?? 0) }} / {{ (int) ($option['capacity'] ?? 0) }}</span>
                                                        <div style="margin-top:6px; font-size:0.74rem; color:{{ $isFull ? 'var(--red)' : 'var(--muted)' }}; font-weight:700;">
                                                            {{ $isFull ? 'Full' : ((int) ($option['remaining'] ?? 0) . ' slots left') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach

                            <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; border:1px solid var(--border); border-radius:12px; background:#f8fbff; padding:10px 12px;">
                                <button type="button" id="subject-prev" class="btn sm" style="border:1px solid #bfdbfe; background:white; color:#1d4ed8;">
                                    <- Previous Subject
                                </button>
                                <div id="subject-position" style="font-size:0.82rem; color:var(--ink-2); font-weight:700;">Subject 0 of 0</div>
                                <button type="button" id="subject-next" class="btn sm primary">
                                    Next Subject ->
                                </button>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:14px; padding:12px; border:1px dashed var(--border); border-radius:10px; background:#f8fbff; color:var(--ink-2); font-size:0.82rem;">
                        Capacity per subject schedule follows the selected class group capacity. Conflicts are checked before you proceed.
                    </div>
                    @if($hasFullSubject)
                        <div style="margin-top:10px; padding:12px; border:1px dashed #fecaca; border-radius:10px; background:#fff1f2; color:#9f1239; font-size:0.82rem;">
                            One or more subjects is already full in all available schedule options. Please contact registrar/admin.
                        </div>
                    @endif
                @endif

                <div style="display:flex; gap:12px; margin-top:20px; flex-wrap:wrap;">
                    <a href="{{ route('enroll.step1', ['school_code' => $school->school_code]) }}"
                       style="display:inline-flex; align-items:center; gap:6px; padding:12px 20px; background:white; color:var(--ink-2); border:1.5px solid var(--border); border-radius:12px; font-size:0.9rem; font-weight:600; text-decoration:none;">
                        <- Back
                    </a>
                    <button
                        class="btn primary lg"
                        type="submit"
                        id="continue-btn"
                        style="flex:1; min-width:180px;"
                        {{ empty($subjectScheduleRows ?? []) || ($hasFullSubject ?? false) ? 'disabled' : '' }}
                    >
                        Continue to Review
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer style="text-align:center; padding:28px 24px; color:var(--muted); font-size:0.8rem; border-top:1px solid var(--border); background:white;">
        <p>(c) {{ date('Y') }} EduPlatform. University SaaS E-Learning System.</p>
    </footer>
</div>

<style>
@media (max-width: 980px) {
    #scheduler-layout { grid-template-columns: 1fr !important; }
    #scheduler-layout aside { position: static !important; max-height: none !important; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const programSelect = document.getElementById('program_id_preview');
    const yearLevelSelect = document.getElementById('year_level_preview');
    const counterEl = document.getElementById('selection-counter');
    const hintEl = document.getElementById('selection-hint');
    const continueBtn = document.getElementById('continue-btn');
    const conflictPanel = document.getElementById('conflict-panel');
    const conflictSummary = document.getElementById('conflict-summary');
    const conflictList = document.getElementById('conflict-list');
    const prevBtn = document.getElementById('subject-prev');
    const nextBtn = document.getElementById('subject-next');
    const subjectPosition = document.getElementById('subject-position');

    if (programSelect && yearLevelSelect) {
        const yearLevelsByProgram = @json($yearLevelsByProgram ?? []);
        const serverSelectedProgramId = String(@json((int) ($selectedProgramId ?? 0)));
        const serverAvailableYearLevels = @json($availableYearLevels ?? []);
        const rebuildYearLevels = () => {
            const selectedProgramId = String(programSelect.value || '');
            const previousValue = String(yearLevelSelect.value || '');
            const rawLevels = yearLevelsByProgram[selectedProgramId]
                ?? yearLevelsByProgram[Number(selectedProgramId)]
                ?? null;

            let levels = [];
            if (Array.isArray(rawLevels)) {
                levels = rawLevels;
            } else if (rawLevels && typeof rawLevels === 'object') {
                levels = Object.values(rawLevels);
            }

            if (levels.length === 0 && selectedProgramId === serverSelectedProgramId && Array.isArray(serverAvailableYearLevels)) {
                levels = serverAvailableYearLevels;
            }

            if (levels.length === 0) {
                levels = [1, 2, 3, 4];
            }

            levels = Array.from(new Set(levels.map((value) => Number(value)).filter((value) => Number.isFinite(value) && value > 0)));

            yearLevelSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = levels.length > 0 ? 'Select year level' : 'No available year level';
            yearLevelSelect.appendChild(placeholder);

            levels.forEach((level) => {
                const option = document.createElement('option');
                option.value = String(level);
                option.textContent = 'Year ' + String(level);
                if (String(level) === previousValue) {
                    option.selected = true;
                }
                yearLevelSelect.appendChild(option);
            });
        };

        programSelect.addEventListener('change', rebuildYearLevels);
        rebuildYearLevels();
    }

    const radios = Array.from(document.querySelectorAll('input[data-subject-radio]'));
    const subjectCards = Array.from(document.querySelectorAll('[data-subject-card]'));
    const checklistButtons = Array.from(document.querySelectorAll('[data-scroll-subject]'));
    const checklistStatus = new Map(
        Array.from(document.querySelectorAll('[data-check-status]')).map((el) => [String(el.getAttribute('data-check-status') || ''), el])
    );
    const checklistConflict = new Map(
        Array.from(document.querySelectorAll('[data-check-conflict]')).map((el) => [String(el.getAttribute('data-check-conflict') || ''), el])
    );

    const totalSubjects = subjectCards.length;
    let activeSubjectIndex = 0;

    const showActiveSubject = (requestedIndex) => {
        if (totalSubjects <= 0) {
            if (subjectPosition) {
                subjectPosition.textContent = 'Subject 0 of 0';
            }
            return;
        }

        activeSubjectIndex = Math.max(0, Math.min(requestedIndex, totalSubjects - 1));
        subjectCards.forEach((card, idx) => {
            card.style.display = idx === activeSubjectIndex ? 'block' : 'none';
        });

        if (subjectPosition) {
            subjectPosition.textContent = 'Subject ' + (activeSubjectIndex + 1) + ' of ' + totalSubjects;
        }
        if (prevBtn) {
            prevBtn.disabled = activeSubjectIndex <= 0;
        }
        if (nextBtn) {
            nextBtn.disabled = activeSubjectIndex >= totalSubjects - 1;
        }
    };

    const selectedCount = () => {
        const chosenBySubject = new Set();
        radios.forEach((radio) => {
            if (radio.checked) {
                chosenBySubject.add(String(radio.getAttribute('data-subject-radio') || ''));
            }
        });
        return chosenBySubject.size;
    };

    const detectConflicts = () => {
        const selected = radios.filter((radio) => radio.checked).map((radio) => {
            let sessions = [];
            try {
                const rawSessions = String(radio.getAttribute('data-sessions') || '[]');
                const normalizedSessions = rawSessions
                    .replace(/&quot;/g, '"')
                    .replace(/&#34;/g, '"')
                    .replace(/&amp;/g, '&');
                sessions = JSON.parse(normalizedSessions);
            } catch (error) {
                sessions = [];
            }
            return {
                subjectId: String(radio.getAttribute('data-subject-radio') || ''),
                subjectCode: String(radio.getAttribute('data-subject-code') || 'SUBJ'),
                groupName: String(radio.getAttribute('data-group-name') || '?'),
                sessions: Array.isArray(sessions) ? sessions : [],
            };
        });

        const conflicts = [];
        const conflictSubjects = new Set();
        const conflictBySubject = new Map();

        const addConflictDetail = (subjectId, detail) => {
            if (!conflictBySubject.has(subjectId)) {
                conflictBySubject.set(subjectId, new Set());
            }
            conflictBySubject.get(subjectId).add(detail);
        };

        for (let i = 0; i < selected.length; i += 1) {
            for (let j = i + 1; j < selected.length; j += 1) {
                const a = selected[i];
                const b = selected[j];
                a.sessions.forEach((sa) => {
                    b.sessions.forEach((sb) => {
                        const sameDay = Number(sa.day_of_week || 0) === Number(sb.day_of_week || 0);
                        const aStart = Number(sa.start_min || 0);
                        const aEnd = Number(sa.end_min || 0);
                        const bStart = Number(sb.start_min || 0);
                        const bEnd = Number(sb.end_min || 0);
                        const overlap = aStart < bEnd && aEnd > bStart;
                        if (!sameDay || !overlap) {
                            return;
                        }
                        const dayLabel = String(sa.day || sb.day || 'Day');
                        const timeLabel = String(sa.start_time || '') + '-' + String(sa.end_time || '');
                        conflicts.push({
                            message: a.subjectCode + ' (Group ' + a.groupName + ') vs ' + b.subjectCode + ' (Group ' + b.groupName + ') on ' + dayLabel + ' ' + timeLabel,
                            aSubjectId: a.subjectId,
                            bSubjectId: b.subjectId,
                        });

                        addConflictDetail(a.subjectId, dayLabel + ' ' + timeLabel + ' with ' + b.subjectCode);
                        addConflictDetail(b.subjectId, dayLabel + ' ' + timeLabel + ' with ' + a.subjectCode);

                        conflictSubjects.add(a.subjectId);
                        conflictSubjects.add(b.subjectId);
                    });
                });
            }
        }

        return { conflicts, conflictSubjects, conflictBySubject };
    };

    const updateProgress = () => {
        const count = selectedCount();
        const conflictState = detectConflicts();
        const hasConflict = conflictState.conflicts.length > 0;

        if (counterEl) {
            counterEl.textContent = count + ' / ' + totalSubjects;
        }
        if (hintEl) {
            if (totalSubjects === 0) {
                hintEl.textContent = 'Select program and load schedules to start.';
            } else if (hasConflict) {
                hintEl.textContent = 'Resolve schedule conflicts before continuing.';
            } else if (count < totalSubjects) {
                hintEl.textContent = 'Choose one schedule option for each subject.';
            } else {
                hintEl.textContent = 'All subjects selected. You can continue to review.';
            }
        }
        if (continueBtn && totalSubjects > 0) {
            continueBtn.disabled = count < totalSubjects || hasConflict;
        }

        checklistButtons.forEach((btn) => {
            const subjectId = String(btn.getAttribute('data-scroll-subject') || '');
            const isSelected = radios.some((radio) => String(radio.getAttribute('data-subject-radio') || '') === subjectId && radio.checked);
            const isConflict = conflictState.conflictSubjects.has(subjectId);
            const statusEl = checklistStatus.get(subjectId);
            const conflictEl = checklistConflict.get(subjectId);
            const conflictDetails = conflictState.conflictBySubject.has(subjectId)
                ? Array.from(conflictState.conflictBySubject.get(subjectId))
                : [];
            if (isConflict) {
                btn.style.borderColor = '#fca5a5';
                btn.style.background = '#fff1f2';
                if (statusEl) {
                    statusEl.textContent = 'Conflict';
                    statusEl.style.color = '#b91c1c';
                }
                if (conflictEl) {
                    conflictEl.style.display = 'block';
                    const preview = conflictDetails.slice(0, 2).join(' | ');
                    const remainingCount = Math.max(conflictDetails.length - 2, 0);
                    conflictEl.textContent = 'Conflict: ' + preview + (remainingCount > 0 ? (' | +' + remainingCount + ' more') : '');
                    conflictEl.style.color = '#b91c1c';
                }
            } else if (isSelected) {
                btn.style.borderColor = '#86efac';
                btn.style.background = '#f0fdf4';
                if (statusEl) {
                    statusEl.textContent = 'Selected';
                    statusEl.style.color = '#16a34a';
                }
                if (conflictEl) {
                    conflictEl.style.display = 'block';
                    conflictEl.textContent = 'No time conflict';
                    conflictEl.style.color = '#166534';
                }
            } else {
                btn.style.borderColor = '#d4e1fb';
                btn.style.background = '#ffffff';
                if (statusEl) {
                    statusEl.textContent = 'Pending';
                    statusEl.style.color = '#64748b';
                }
                if (conflictEl) {
                    conflictEl.style.display = 'block';
                    conflictEl.textContent = 'Waiting for selection';
                    conflictEl.style.color = '#64748b';
                }
            }
        });

        subjectCards.forEach((card) => {
            const subjectId = String(card.getAttribute('data-subject-card') || '');
            if (conflictState.conflictSubjects.has(subjectId)) {
                card.style.borderColor = '#fca5a5';
                card.style.boxShadow = 'inset 0 0 0 1px #fecaca';
            } else {
                card.style.borderColor = 'var(--border)';
                card.style.boxShadow = 'none';
            }
        });

        if (conflictPanel && conflictSummary && conflictList) {
            if (!hasConflict) {
                conflictPanel.style.display = 'none';
                conflictSummary.textContent = '';
                conflictList.innerHTML = '';
            } else {
                conflictPanel.style.display = 'block';
                conflictSummary.textContent = String(conflictState.conflicts.length) + ' conflict(s) detected';
                conflictList.innerHTML = '';
                conflictState.conflicts.slice(0, 8).forEach((item) => {
                    const row = document.createElement('div');
                    row.textContent = item.message;
                    conflictList.appendChild(row);
                });
            }
        }
    };

    radios.forEach((radio) => {
        radio.addEventListener('change', updateProgress);
    });

    checklistButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const subjectId = btn.getAttribute('data-scroll-subject');
            if (!subjectId) {
                return;
            }
            const target = document.querySelector('[data-subject-card="' + subjectId + '"]');
            if (target) {
                const idx = Number(target.getAttribute('data-subject-index') || '0');
                showActiveSubject(Number.isFinite(idx) ? idx : 0);
            }
        });
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            showActiveSubject(activeSubjectIndex - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            showActiveSubject(activeSubjectIndex + 1);
        });
    }

    const firstPendingIndex = subjectCards.findIndex((card) => {
        const subjectId = String(card.getAttribute('data-subject-card') || '');
        return !radios.some((radio) => String(radio.getAttribute('data-subject-radio') || '') === subjectId && radio.checked);
    });
    showActiveSubject(firstPendingIndex >= 0 ? firstPendingIndex : 0);

    updateProgress();
});
</script>
@endsection
