@extends('layouts.app')

@section('title', 'Enrollment - Step 3: Review Prospectus')

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
<div style="min-height:100vh; --accent:{{ $enrollTheme['accent'] }}; --accent-h:{{ $enrollTheme['accent_h'] }}; --accent-l:{{ $enrollTheme['accent_l'] }}; --accent-l2:{{ $enrollTheme['accent_l2'] }}; background: linear-gradient(160deg, {{ $enrollTheme['bg_1'] }} 0%, {{ $enrollTheme['bg_2'] }} 50%, {{ $enrollTheme['bg_3'] }} 100%);">
    <nav style="background: rgba(255,255,255,.85); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); position: sticky; top:0; z-index:50;">
        <div style="max-width:980px; margin:0 auto; padding:0 24px; height:60px; display:flex; align-items:center; justify-content:space-between;">
            <a href="{{ route('school.enroll', ['school_code' => $school->school_code]) }}" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                <div style="width:34px; height:34px; background:linear-gradient(135deg,var(--accent),var(--accent-h)); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <span style="font-weight:700; font-size:1rem; color:var(--ink);">{{ $school->name }}</span>
            </a>
            <span class="badge blue">Step 3 of 4</span>
        </div>
    </nav>

    <div style="max-width:980px; margin:0 auto; padding:48px 24px 64px;">
        <div style="display:flex; align-items:center; justify-content:center; gap:8px; margin-bottom:36px;">
            @foreach(['Personal Info', 'Select Program', 'Review', 'Payment'] as $i => $step)
            @php $stepNum = $i + 1; @endphp
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; {{ $stepNum <= 3 ? 'background:linear-gradient(135deg,var(--accent),var(--accent-h)); color:#fff;' : 'background:var(--surface-2); color:var(--muted); border:1px solid var(--border);' }}">
                    @if ($stepNum < 3)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        {{ $stepNum }}
                    @endif
                </div>
                <span style="font-size:0.78rem; font-weight:600; {{ $stepNum <= 3 ? 'color:var(--ink);' : 'color:var(--muted);' }}">{{ $step }}</span>
                @if ($stepNum < 4)
                <div style="width:24px; height:2px; background:{{ $stepNum < 3 ? 'var(--accent)' : 'var(--border)' }}; margin-left:4px;"></div>
                @endif
            </div>
            @endforeach
        </div>

        <div style="background:white; border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow-lg); overflow:hidden; margin-bottom:24px;">
            <div style="padding:24px 28px; border-bottom:1px solid var(--border); background:linear-gradient(135deg,var(--accent),var(--accent-h));">
                <h1 style="margin:0 0 4px; font-size:1.2rem; color:#fff;">Prospectus Summary</h1>
                <p style="margin:0; font-size:0.85rem; color:rgba(255,255,255,.8);">
                    Review your selected schedule preferences before proceeding to payment.
                </p>
            </div>

            <div style="padding:24px 28px; border-bottom:1px solid var(--border);">
                <h3 style="margin:0 0 16px; font-size:0.95rem; color:var(--ink);">Student Information</h3>
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">
                    <div>
                        <span style="font-size:0.8rem; color:var(--muted); display:block; margin-bottom:4px;">Full Name</span>
                        <span style="font-weight:600; color:var(--ink);">{{ $applicant['full_name'] }}</span>
                    </div>
                    <div>
                        <span style="font-size:0.8rem; color:var(--muted); display:block; margin-bottom:4px;">Email</span>
                        <span style="font-weight:600; color:var(--ink);">{{ $applicant['email'] }}</span>
                    </div>
                    <div>
                        <span style="font-size:0.8rem; color:var(--muted); display:block; margin-bottom:4px;">Program</span>
                        <span style="font-weight:600; color:var(--ink);">{{ $program?->name ?? 'N/A' }}</span>
                    </div>
                    <div>
                        <span style="font-size:0.8rem; color:var(--muted); display:block; margin-bottom:4px;">Year Level</span>
                        <span style="font-weight:600; color:var(--ink);">Year {{ (int) ($applicant['year_level'] ?? 0) }}</span>
                    </div>
                    <div>
                        <span style="font-size:0.8rem; color:var(--muted); display:block; margin-bottom:4px;">Semester</span>
                        <span style="font-weight:600; color:var(--ink);">{{ $semester?->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div style="padding:24px 28px; border-bottom:1px solid var(--border);">
                <h3 style="margin:0 0 16px; font-size:0.95rem; color:var(--ink);">Selected Subject Schedules</h3>
                @if (empty($selectedScheduleOptions ?? []))
                    <p style="margin:0; color:var(--muted); font-size:0.85rem;">No schedule selections found.</p>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Class Group</th>
                                    <th>Schedule</th>
                                    <th>Capacity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($selectedScheduleOptions ?? []) as $option)
                                    <tr>
                                        <td>
                                            <div style="font-weight:700; color:var(--accent);">{{ $option['subject_code'] ?? 'SUBJ' }}</div>
                                            <div style="color:var(--ink-2);">{{ $option['subject_title'] ?? '' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge blue">{{ $option['class_group_name'] ?? '?' }}</span>
                                        </td>
                                        <td>
                                            <div style="display:grid; gap:2px;">
                                                @foreach(($option['sessions'] ?? []) as $session)
                                                    <div style="font-size:0.82rem; color:var(--ink-2);">
                                                        <strong>{{ $session['day'] ?? 'Day' }}</strong>
                                                        {{ $session['start_time'] ?? '' }}-{{ $session['end_time'] ?? '' }}
                                                        <span style="color:var(--muted);">• {{ $session['teacher_name'] ?? 'TBA' }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ ((int) ($option['remaining'] ?? 0)) <= 0 ? 'red' : 'green' }}">
                                                {{ (int) ($option['enrolled_count'] ?? 0) }} / {{ (int) ($option['capacity'] ?? 0) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="background:var(--surface-2);">
                                    <td colspan="3" style="text-align:right; font-weight:700;">Total Units</td>
                                    <td style="font-weight:700; color:var(--accent);">{{ number_format((float) ($totalUnits ?? 0), 1) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            <form method="post" action="{{ route('enroll.step3', ['school_code' => $school->school_code]) }}" style="padding:24px 28px;">
                @csrf
                <div style="display:flex; align-items:flex-start; gap:12px; margin-bottom:20px; padding:16px; background:var(--accent-l2); border-radius:12px;">
                    <input type="checkbox" name="confirm" id="confirm" required style="margin-top:3px; width:18px; height:18px; cursor:pointer;">
                    <label for="confirm" style="font-size:0.88rem; color:var(--ink-2); cursor:pointer; line-height:1.6;">
                        I confirm that the selected schedule preferences above are correct and I wish to proceed.
                    </label>
                </div>

                <div style="display:flex; gap:12px;">
                    <a href="{{ route('enroll.step2', ['school_code' => $school->school_code]) }}"
                       style="display:inline-flex; align-items:center; gap:6px; padding:12px 20px; background:white; color:var(--ink-2); border:1.5px solid var(--border); border-radius:12px; font-size:0.9rem; font-weight:600; text-decoration:none;">
                        <- Back
                    </a>
                    <button class="btn primary lg" type="submit" style="flex:1;">
                        proceed to payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer style="text-align:center; padding:32px 24px; color:var(--muted); font-size:0.8rem; border-top:1px solid var(--border); background:white;">
        <p>(c) {{ date('Y') }} EduPlatform. University SaaS E-Learning System.</p>
    </footer>
</div>
@endsection

