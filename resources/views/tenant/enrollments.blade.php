@extends('layouts.app')

@section('title', 'Enrollments - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'enrollments', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body">
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">></span>
                    <span>Enrollments</span>
                </div>
                <h1>Enrollment Workflow</h1>
                <p>Student selection, validation, and registrar confirmation.</p>
            </div>

            @if(session('status'))
                <div class="alert success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- â”€â”€ Enrollment Pipeline Visualization â”€â”€ --}}
            {{-- Finance Verified Students --}}
            @if($canRegistrarViewAllRecords ?? false)
            @php
                $registrarVerifiedCount = method_exists($financeVerifiedForRegistrar ?? null, 'total')
                    ? (int) $financeVerifiedForRegistrar->total()
                    : (int) (($financeVerifiedForRegistrar ?? collect())->count());
                $registrarGroupCount = method_exists($classGroupCapacityForRegistrar ?? null, 'total')
                    ? (int) $classGroupCapacityForRegistrar->total()
                    : (int) (($classGroupCapacityForRegistrar ?? collect())->count());
                $registrarInactiveCount = method_exists($inactiveStudentAccountsForRegistrar ?? null, 'total')
                    ? (int) $inactiveStudentAccountsForRegistrar->total()
                    : (int) (($inactiveStudentAccountsForRegistrar ?? collect())->count());
                $registrarStudentCount = method_exists($allStudentsForRegistrar ?? null, 'total')
                    ? (int) $allStudentsForRegistrar->total()
                    : (int) (($allStudentsForRegistrar ?? collect())->count());
                $pendingClearancesCount = (int) (($pendingClearancesForRegistrar ?? collect())->count());
            @endphp
            <div class="enroll-kpi-grid">
                <div class="enroll-kpi-card">
                    <svg class="enroll-kpi-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <div class="enroll-kpi-num enroll-kpi-num--green">{{ $registrarVerifiedCount }}</div>
                    <div class="enroll-kpi-label">Verified Queue</div>
                </div>
                <div class="enroll-kpi-card">
                    <svg class="enroll-kpi-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    <div class="enroll-kpi-num enroll-kpi-num--amber">{{ $pendingClearancesCount }}</div>
                    <div class="enroll-kpi-label">Pending Clearances</div>
                </div>
                <div class="enroll-kpi-card">
                    <svg class="enroll-kpi-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 7h18"/><path d="M3 12h18"/><path d="M3 17h18"/>
                    </svg>
                    <div class="enroll-kpi-num enroll-kpi-num--blue">{{ $registrarGroupCount }}</div>
                    <div class="enroll-kpi-label">Class Groups</div>
                </div>
                <div class="enroll-kpi-card">
                    <svg class="enroll-kpi-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    </svg>
                    <div class="enroll-kpi-num enroll-kpi-num--amber">{{ $registrarInactiveCount }}</div>
                    <div class="enroll-kpi-label">Inactive Accounts</div>
                </div>
                <div class="enroll-kpi-card">
                    <svg class="enroll-kpi-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                    <div class="enroll-kpi-num enroll-kpi-num--purple">{{ $registrarStudentCount }}</div>
                    <div class="enroll-kpi-label">Total Students</div>
                </div>
                @php
                    $schoolStaffKpiCount = method_exists($schoolStaffForRegistrar ?? null, 'total')
                        ? (int) $schoolStaffForRegistrar->total()
                        : (int) (($schoolStaffForRegistrar ?? collect())->count());
                @endphp
                <div class="enroll-kpi-card">
                    <svg class="enroll-kpi-icon" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    </svg>
                    <div class="enroll-kpi-num enroll-kpi-num--green">{{ $schoolStaffKpiCount }}</div>
                    <div class="enroll-kpi-label">School Staffs</div>
                </div>
            </div>
            <div data-tabs="registrar-tables">
                <div class="enroll-tabs">
                    <button class="enroll-tab-btn active" data-tab="reg-verified" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        Verified Students
                    </button>
                    <button class="enroll-tab-btn" data-tab="reg-clearances" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        Pending Clearances
                        @if($pendingClearancesCount > 0)
                            <span class="enroll-tab-badge enroll-tab-badge--amber">{{ $pendingClearancesCount }}</span>
                        @endif
                    </button>
                    <button class="enroll-tab-btn" data-tab="reg-groups" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7h18"/><path d="M3 12h18"/><path d="M3 17h18"/>
                        </svg>
                        Class Groups
                    </button>
                    <button class="enroll-tab-btn" data-tab="reg-inactive" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        </svg>
                        Inactive Accounts
                    </button>
                    <button class="enroll-tab-btn" data-tab="reg-students" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                        Students
                    </button>
                    <button class="enroll-tab-btn" data-tab="reg-staffs" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        </svg>
                        School Staffs
                    </button>
                </div>

                <div class="tab-panel active" data-panel="reg-verified">
            <div id="registrar-verified-root">
                @include('tenant.partials.enrollments-registrar-verified-section', [
                    'financeVerifiedForRegistrar' => $financeVerifiedForRegistrar,
                    'registrarVerifiedSearch' => $registrarVerifiedSearch ?? '',
                ])
            </div>
                </div>

                <div class="tab-panel" data-panel="reg-clearances">
            <div id="registrar-clearances-root">
                @include('tenant.partials.enrollments-registrar-clearances-section', [
                    'pendingClearancesForRegistrar' => $pendingClearancesForRegistrar,
                    'registrarClearanceSearch' => $registrarClearanceSearch ?? '',
                ])
            </div>
                </div>

                <div class="tab-panel" data-panel="reg-groups">
            <div id="registrar-class-groups-root">
                @include('tenant.partials.enrollments-registrar-class-groups-section', [
                    'classGroupCapacityForRegistrar' => $classGroupCapacityForRegistrar,
                    'registrarClassGroupSearch' => $registrarClassGroupSearch ?? '',
                ])
            </div>
                </div>

                <div class="tab-panel" data-panel="reg-inactive">
            <div id="registrar-inactive-root">
                @include('tenant.partials.enrollments-registrar-inactive-section', [
                    'inactiveStudentAccountsForRegistrar' => $inactiveStudentAccountsForRegistrar,
                    'registrarInactiveSearch' => $registrarInactiveSearch ?? '',
                ])
            </div>
                </div>

                <div class="tab-panel" data-panel="reg-students">
            <div id="registrar-all-students-root">
                @include('tenant.partials.enrollments-registrar-all-students-section', [
                    'allStudentsForRegistrar' => $allStudentsForRegistrar,
                    'registrarAllStudentsSearch' => $registrarAllStudentsSearch ?? '',
                ])
            </div>
                </div>

                <div class="tab-panel" data-panel="reg-staffs">
            <div id="registrar-staff-root">
                @include('tenant.partials.enrollments-registrar-staff-section', [
                    'schoolStaffForRegistrar' => $schoolStaffForRegistrar,
                    'registrarStaffSearch' => $registrarStaffSearch ?? '',
                ])
            </div>
                </div>
            </div>
            @else
            @if($showProgramSelectionCard ?? false)
            <div class="card mb-20">
                <div class="card-header">
                    <h2>
                        <div class="card-icon purple">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7h18"/><path d="M3 12h18"/><path d="M3 17h18"/>
                            </svg>
                        </div>
                        Select Program, Subjects, and Schedules
                    </h2>
                    <span class="badge purple">{{ $semesterForStudentEnrollment?->name ?? 'No open semester' }}</span>
                </div>

                @if(!$semesterForStudentEnrollment)
                    <div class="empty-state" style="padding:24px;">
                        <p>No semester is currently open for enrollment.</p>
                    </div>
                @else
                    <form method="get" action="{{ url('/tenant/enrollments') }}" style="padding:0 16px 12px; display:grid; grid-template-columns:1fr 180px auto; gap:10px; align-items:end;">
                        <div>
                            <label style="display:block; margin-bottom:6px; font-size:0.78rem; color:var(--muted);">Program</label>
                            <select name="program_id" required>
                                <option value="">Select program</option>
                                @foreach(($programsForStudent ?? collect()) as $program)
                                    <option value="{{ (int) $program->id }}" {{ (int) ($selectedProgramId ?? 0) === (int) $program->id ? 'selected' : '' }}>
                                        {{ $program->code }} - {{ $program->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label style="display:block; margin-bottom:6px; font-size:0.78rem; color:var(--muted);">Year Level</label>
                            <select name="year_level" required>
                                <option value="">Year</option>
                                @foreach(($availableYearLevels ?? []) as $yearLevel)
                                    <option value="{{ (int) $yearLevel }}" {{ (int) ($selectedYearLevel ?? 0) === (int) $yearLevel ? 'selected' : '' }}>
                                        Year {{ (int) $yearLevel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn primary sm" type="submit" name="load_schedules" value="1">Load Schedules</button>
                    </form>

                    @if(($shouldLoadSchedules ?? false) && empty($subjectScheduleRows ?? []))
                        <div class="empty-state" style="padding:12px 16px 16px;">
                            <p>No schedules found for the selected program/year.</p>
                        </div>
                    @endif

                    @if(!empty($subjectScheduleRows ?? []))
                        <form method="post" action="{{ url('/tenant/enrollments/plan') }}" style="padding:8px 16px 16px;">
                            @csrf
                            <input type="hidden" name="program_id" value="{{ (int) ($selectedProgramId ?? 0) }}">
                            <input type="hidden" name="year_level" value="{{ (int) ($selectedYearLevel ?? 0) }}">

                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Schedule Choice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(($subjectScheduleRows ?? []) as $row)
                                            @php
                                                $subjectId = (int) ($row['subject_id'] ?? 0);
                                                $options = (array) ($row['options'] ?? []);
                                                $selectedGroupId = (int) (($selectedSubjectChoice[$subjectId] ?? old('subject_choice.' . $subjectId)) ?? 0);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div style="font-weight:600; color:var(--ink);">{{ $row['subject_title'] ?? '' }}</div>
                                                    <span class="badge blue">{{ $row['subject_code'] ?? '' }}</span>
                                                </td>
                                                <td>
                                                    <select name="subject_choice[{{ $subjectId }}]" required>
                                                        <option value="">Select schedule</option>
                                                        @foreach($options as $opt)
                                                            @php
                                                                $gid = (int) ($opt['class_group_id'] ?? 0);
                                                                $isFull = (int) ($opt['remaining'] ?? 0) <= 0;
                                                                $blocks = collect((array) ($opt['sessions'] ?? []))
                                                                    ->map(fn ($s) => ($s['day'] ?? 'Day') . ' ' . ($s['start_time'] ?? '') . '-' . ($s['end_time'] ?? ''))
                                                                    ->implode(', ');
                                                            @endphp
                                                            <option value="{{ $gid }}" {{ $selectedGroupId === $gid ? 'selected' : '' }} {{ $isFull ? 'disabled' : '' }}>
                                                                {{ $opt['class_group_name'] ?? 'Group' }} | {{ $blocks }} | Slots: {{ (int) ($opt['remaining'] ?? 0) }}/{{ (int) ($opt['capacity'] ?? 0) }}{{ $isFull ? ' (FULL)' : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div style="display:flex; justify-content:flex-end; margin-top:12px;">
                                <button class="btn success" type="submit">Save Selections and Generate Tuition Billing</button>
                            </div>
                        </form>
                    @endif
                @endif
            </div>
            @endif
            @endif

            <div class="my-enroll-card" id="my-enrollments">
                {{-- Card header --}}
                <div class="my-enroll-header">
                    <div class="my-enroll-header-left">
                        <span class="my-enroll-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </span>
                        <h2 class="my-enroll-title">My Enrollments</h2>
                        <span class="my-enroll-count">{{ $myEnrollments->count() }} TOTAL</span>
                    </div>
                    {{-- Search (UI only — JS filter) --}}
                    <div class="my-enroll-search-wrap">
                        <svg class="my-enroll-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input id="my-enroll-search" type="text" class="my-enroll-search-input" placeholder="Search subject or code…" oninput="filterMyEnrollments(this.value)">
                    </div>
                    {{-- Status filter --}}
                    <select id="my-enroll-status-filter" class="my-enroll-filter-select" onchange="filterMyEnrollments(document.getElementById('my-enroll-search').value)">
                        <option value="">All Statuses</option>
                        <option value="enrolled">Enrolled</option>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="billing_pending">Billing Pending</option>
                        <option value="dropped">Dropped</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                {{-- Table --}}
                <div class="my-enroll-table-wrap">
                    <table class="my-enroll-table" id="my-enroll-table">
                        <thead>
                            <tr>
                                <th class="my-enroll-th-num">#</th>
                                <th>Subject</th>
                                <th>Class Group</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myEnrollments as $row)
                                @php
                                    $statusColors = [
                                        'confirmed'        => 'green',
                                        'pending'          => 'amber',
                                        'selected'         => 'blue',
                                        'billing_pending'  => 'amber',
                                        'payment_verified' => 'green',
                                        'enrolled'         => 'green',
                                        'dropped'          => 'red',
                                        'cancelled'        => 'red',
                                    ];
                                    $sColor = $statusColors[$row->status] ?? 'neutral';
                                    $subjectTitle = $row->offering?->subject?->title ?? '';
                                    $subjectCode  = $row->offering?->subject?->code  ?? '';
                                @endphp
                                <tr class="my-enroll-row"
                                    data-subject="{{ strtolower($subjectTitle . ' ' . $subjectCode) }}"
                                    data-status="{{ $row->status }}">
                                    <td class="my-enroll-td-num">{{ $row->id }}</td>
                                    <td class="my-enroll-td-subject">
                                        @if($row->offering?->subject)
                                            <div class="my-enroll-subject-name">{{ $subjectTitle }}</div>
                                            <span class="my-enroll-chip my-enroll-chip--blue">{{ $subjectCode }}</span>
                                        @else
                                            <span class="my-enroll-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->section)
                                            <span class="my-enroll-chip my-enroll-chip--neutral">{{ $row->class_group_label ?? $row->section->identifier }}</span>
                                        @else
                                            <span class="my-enroll-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="my-enroll-status my-enroll-status--{{ $sColor }}">
                                            @php
                                                $statusIcons = [
                                                    'enrolled'         => '<circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/>',
                                                    'confirmed'        => '<circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/>',
                                                    'payment_verified' => '<circle cx="12" cy="12" r="10"/><path d="M9 12l2 2 4-4"/>',
                                                    'pending'          => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
                                                    'billing_pending'  => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
                                                    'selected'         => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
                                                    'dropped'          => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
                                                    'cancelled'        => '<circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>',
                                                ];
                                                $icon = $statusIcons[$row->status] ?? '';
                                            @endphp
                                            @if($icon)
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
                                            @endif
                                            {{ ucwords(str_replace('_', ' ', $row->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="my-enroll-empty">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                            </svg>
                                            <p>No enrollment records yet.</p>
                                            <p>Submit an enrollment request above to get started.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="my-enroll-no-results" id="my-enroll-no-results" style="display:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <p>No subjects match your search.</p>
                    </div>
                </div>
            </div>

            <script>
            function filterMyEnrollments(q) {
                const query  = (q || '').toLowerCase().trim();
                const status = document.getElementById('my-enroll-status-filter')?.value || '';
                const rows   = document.querySelectorAll('#my-enroll-table .my-enroll-row');
                let visible  = 0;
                rows.forEach(row => {
                    const subj  = (row.dataset.subject || '').toLowerCase();
                    const stat  = (row.dataset.status  || '').toLowerCase();
                    const matchQ = !query  || subj.includes(query);
                    const matchS = !status || stat === status;
                    const show   = matchQ && matchS;
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                });
                const noRes = document.getElementById('my-enroll-no-results');
                if (noRes) noRes.style.display = (visible === 0 && rows.length > 0) ? 'flex' : 'none';
            }
            </script>

            @if(!($canRegistrarViewAllRecords ?? false))
            <div class="card" id="weekly-class-schedule" style="margin-top:20px;">
                <div class="card-header">
                    <h2>
                        <div class="card-icon purple">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/>
                                <path d="M3 10h18"/>
                            </svg>
                        </div>
                        Weekly Class Schedule
                    </h2>
                    <span class="badge purple">{{ (int) (($studentWeeklyScheduleSummary['total_sessions'] ?? 0)) }} sessions</span>
                </div>

                <div style="padding:0 16px 12px; display:flex; gap:8px; flex-wrap:wrap;">
                    <span class="badge blue">{{ (int) (($studentWeeklyScheduleSummary['subjects'] ?? 0)) }} subjects</span>
                    <span class="badge green">{{ (int) (($studentWeeklyScheduleSummary['days_covered'] ?? 0)) }} days</span>
                    <span class="badge amber">{{ (int) (($studentWeeklyScheduleSummary['class_groups'] ?? 0)) }} class groups</span>
                </div>

                @php
                    $scheduleRows = collect($studentWeeklyScheduleRows ?? []);
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
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 2v4"/><path d="M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/>
                            <path d="M3 10h18"/>
                        </svg>
                        <p>No weekly schedule available yet for your current enrolled subjects.</p>
                    </div>
                @else
                    <div class="weekly-grid-wrap">
                        <div class="weekly-grid" style="grid-template-columns:110px repeat({{ count($gridDays) }}, minmax(180px, 1fr)); min-width:{{ 110 + (count($gridDays) * 180) }}px;">
                            <div class="weekly-grid-head time-col">Time</div>
                            @foreach($gridDays as $dayKey => $dayLabel)
                                <div class="weekly-grid-head">{{ $dayLabel }}</div>
                            @endforeach

                            @for($slotMin = $gridStartMin; $slotMin < $gridEndMin; $slotMin += $gridSlot)
                                @php
                                    $slotLabel = ($slotMin % 60 === 0) ? sprintf('%02d:%02d', intdiv($slotMin, 60), 0) : '';
                                    $slotEnd = $slotMin + $gridSlot;
                                @endphp
                                <div class="weekly-grid-time {{ $slotMin % 60 === 30 ? 'half' : 'hour' }}">{{ $slotLabel }}</div>
                                @foreach($gridDays as $dayKey => $dayLabel)
                                    @php
                                        $hits = collect($sessionsByDay->get($dayKey, []))
                                            ->filter(fn ($s) => (int) ($s['start_min'] ?? 0) >= $slotMin && (int) ($s['start_min'] ?? 0) < $slotEnd)
                                            ->values();
                                    @endphp
                                    <div class="weekly-grid-cell {{ $slotMin % 60 === 30 ? 'half' : 'hour' }}">
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
                                            <div class="weekly-grid-chip weekly-grid-chip-span {{ $chipClass }}" style="top:{{ $topPx }}px; height:{{ $heightPx }}px; left:{{ $insetPx }}px; right:{{ $insetPx }}px;">
                                                <strong>{{ $hit['subject_code'] ?? 'SUBJ' }}</strong>
                                                <span>{{ $hit['start_time'] ?? '' }}-{{ $hit['end_time'] ?? '' }}</span>
                                                <small>{{ $hit['teacher_name'] ?? 'TBA' }}</small>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endfor
                        </div>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Time</th>
                                    <th>Subject</th>
                                    <th>Class Group</th>
                                    <th>Type</th>
                                    <th>Teacher</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($studentWeeklyScheduleTable ?? collect()) as $row)
                                    <tr>
                                        <td style="font-weight:600; color:var(--ink);">{{ $row['day_label'] ?? 'N/A' }}</td>
                                        <td style="color:var(--ink-2);">{{ $row['time_label'] ?? 'N/A' }}</td>
                                        <td>
                                            <div style="font-weight:600; color:var(--ink);">{{ $row['subject_title'] ?? 'Untitled Subject' }}</div>
                                            <span class="badge blue" style="font-size:0.65rem; margin-top:2px;">{{ $row['subject_code'] ?? 'N/A' }}</span>
                                        </td>
                                        <td><span class="badge">{{ $row['class_group'] ?? 'N/A' }}</span></td>
                                        <td><span class="badge purple">{{ $row['session_type'] ?? 'CLASS' }}</span></td>
                                        <td style="color:var(--ink-2);">{{ $row['teacher_name'] ?? 'TBA' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if(($studentWeeklyScheduleTable ?? null) instanceof \Illuminate\Contracts\Pagination\Paginator && $studentWeeklyScheduleTable->hasPages())
                        <div style="padding: 10px 16px 16px;">
                            {{ $studentWeeklyScheduleTable->onEachSide(1)->links() }}
                        </div>
                    @endif
                @endif
            </div>
            @endif
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const verifiedRoot = document.getElementById('registrar-verified-root');
    const clearancesRoot = document.getElementById('registrar-clearances-root');
    const classGroupsRoot = document.getElementById('registrar-class-groups-root');
    const inactiveRoot = document.getElementById('registrar-inactive-root');
    const allStudentsRoot = document.getElementById('registrar-all-students-root');
    const staffRoot = document.getElementById('registrar-staff-root');
    if (!verifiedRoot && !clearancesRoot && !classGroupsRoot && !inactiveRoot && !allStudentsRoot && !staffRoot) return;

    const SEARCH_DEBOUNCE_MS = 480;
    let verifiedAbortController = null;
    let clearancesAbortController = null;
    let classGroupsAbortController = null;
    let inactiveAbortController = null;
    let allStudentsAbortController = null;
    let staffAbortController = null;
    let debounceTimer = null;
    let clearanceDebounceTimer = null;
    let classGroupDebounceTimer = null;
    let inactiveDebounceTimer = null;
    let allStudentsDebounceTimer = null;
    let staffDebounceTimer = null;

    const captureSearchInputState = (inputId) => {
        const el = document.getElementById(inputId);
        if (!el || el.tagName !== 'INPUT') return null;
        const v = el.value;
        return {
            id: inputId,
            value: v,
            selectionStart: typeof el.selectionStart === 'number' ? el.selectionStart : v.length,
            selectionEnd: typeof el.selectionEnd === 'number' ? el.selectionEnd : v.length,
            keepFocus: document.activeElement === el,
        };
    };

    const applySearchInputState = (state) => {
        if (!state) return;
        const el = document.getElementById(state.id);
        if (!el || el.tagName !== 'INPUT') return;
        el.value = state.value;
        if (!state.keepFocus) return;
        el.focus();
        const len = state.value.length;
        const s = Math.min(state.selectionStart, len);
        const e = Math.min(state.selectionEnd, len);
        try {
            el.setSelectionRange(s, e);
        } catch (err) { /* ignore */ }
    };

    const buildUrl = (sourceUrl = null) => {
        const base = sourceUrl ? new URL(sourceUrl, window.location.origin) : new URL(window.location.href);
        const form = document.getElementById('registrar-verified-search-form');
        const params = new URLSearchParams(base.search);
        const query = String((form?.querySelector('[name="registrar_verified_q"]')?.value ?? '')).trim();

        params.set('partial', 'registrar-verified');
        if (query) params.set('registrar_verified_q', query);
        else params.delete('registrar_verified_q');
        if (!sourceUrl) params.delete('registrar_verified_page');

        base.search = params.toString();
        return base;
    };

    const replaceHistory = (sourceUrl = null) => {
        const next = buildUrl(sourceUrl);
        next.searchParams.delete('partial');
        window.history.replaceState({}, '', next.toString());
    };

    const fetchVerifiedSection = async (sourceUrl = null) => {
        if (!verifiedRoot) return;
        const targetUrl = buildUrl(sourceUrl);
        if (verifiedAbortController) verifiedAbortController.abort();
        verifiedAbortController = new AbortController();

        try {
            const response = await fetch(targetUrl.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: verifiedAbortController.signal,
            });
            if (!response.ok) return;
            const html = await response.text();
            const searchState = captureSearchInputState('registrar-verified-search');
            verifiedRoot.innerHTML = html;
            replaceHistory(sourceUrl);
            applySearchInputState(searchState);
        } catch (error) {
            if (error.name !== 'AbortError') console.error(error);
        }
    };

    const buildClearancesUrl = (sourceUrl = null) => {
        const base = sourceUrl ? new URL(sourceUrl, window.location.origin) : new URL(window.location.href);
        const form = document.getElementById('registrar-clearance-search-form');
        const params = new URLSearchParams(base.search);
        const query = String((form?.querySelector('[name="registrar_clearance_q"]')?.value ?? '')).trim();

        params.set('partial', 'registrar-clearances');
        if (query) params.set('registrar_clearance_q', query);
        else params.delete('registrar_clearance_q');
        if (!sourceUrl) params.delete('registrar_clearance_page');

        base.search = params.toString();
        return base;
    };

    const replaceClearancesHistory = (sourceUrl = null) => {
        const next = buildClearancesUrl(sourceUrl);
        next.searchParams.delete('partial');
        window.history.replaceState({}, '', next.toString());
    };

    const fetchClearancesSection = async (sourceUrl = null) => {
        if (!clearancesRoot) return;
        const targetUrl = buildClearancesUrl(sourceUrl);
        if (clearancesAbortController) clearancesAbortController.abort();
        clearancesAbortController = new AbortController();

        try {
            const response = await fetch(targetUrl.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: clearancesAbortController.signal,
            });
            if (!response.ok) return;
            const html = await response.text();
            const searchState = captureSearchInputState('registrar-clearance-search');
            clearancesRoot.innerHTML = html;
            replaceClearancesHistory(sourceUrl);
            applySearchInputState(searchState);
        } catch (error) {
            if (error.name !== 'AbortError') console.error(error);
        }
    };

    const buildClassGroupsUrl = (sourceUrl = null) => {
        const base = sourceUrl ? new URL(sourceUrl, window.location.origin) : new URL(window.location.href);
        const form = document.getElementById('registrar-class-group-search-form');
        const params = new URLSearchParams(base.search);
        const query = String((form?.querySelector('[name="registrar_class_group_q"]')?.value ?? '')).trim();

        params.set('partial', 'registrar-class-groups');
        if (query) params.set('registrar_class_group_q', query);
        else params.delete('registrar_class_group_q');
        if (!sourceUrl) params.delete('registrar_class_group_page');

        base.search = params.toString();
        return base;
    };

    const replaceClassGroupsHistory = (sourceUrl = null) => {
        const next = buildClassGroupsUrl(sourceUrl);
        next.searchParams.delete('partial');
        window.history.replaceState({}, '', next.toString());
    };

    const fetchClassGroupsSection = async (sourceUrl = null) => {
        if (!classGroupsRoot) return;
        const targetUrl = buildClassGroupsUrl(sourceUrl);
        if (classGroupsAbortController) classGroupsAbortController.abort();
        classGroupsAbortController = new AbortController();

        try {
            const response = await fetch(targetUrl.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: classGroupsAbortController.signal,
            });
            if (!response.ok) return;
            const html = await response.text();
            const searchState = captureSearchInputState('registrar-class-group-search');
            classGroupsRoot.innerHTML = html;
            replaceClassGroupsHistory(sourceUrl);
            applySearchInputState(searchState);
        } catch (error) {
            if (error.name !== 'AbortError') console.error(error);
        }
    };

    const buildInactiveUrl = (sourceUrl = null) => {
        const base = sourceUrl ? new URL(sourceUrl, window.location.origin) : new URL(window.location.href);
        const form = document.getElementById('registrar-inactive-search-form');
        const params = new URLSearchParams(base.search);
        const query = String((form?.querySelector('[name="registrar_inactive_q"]')?.value ?? '')).trim();

        params.set('partial', 'registrar-inactive');
        if (query) params.set('registrar_inactive_q', query);
        else params.delete('registrar_inactive_q');
        if (!sourceUrl) params.delete('registrar_inactive_page');

        base.search = params.toString();
        return base;
    };

    const replaceInactiveHistory = (sourceUrl = null) => {
        const next = buildInactiveUrl(sourceUrl);
        next.searchParams.delete('partial');
        window.history.replaceState({}, '', next.toString());
    };

    const fetchInactiveSection = async (sourceUrl = null) => {
        if (!inactiveRoot) return;
        const targetUrl = buildInactiveUrl(sourceUrl);
        if (inactiveAbortController) inactiveAbortController.abort();
        inactiveAbortController = new AbortController();

        try {
            const response = await fetch(targetUrl.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: inactiveAbortController.signal,
            });
            if (!response.ok) return;
            const html = await response.text();
            const searchState = captureSearchInputState('registrar-inactive-search');
            inactiveRoot.innerHTML = html;
            replaceInactiveHistory(sourceUrl);
            applySearchInputState(searchState);
        } catch (error) {
            if (error.name !== 'AbortError') console.error(error);
        }
    };

    const buildAllStudentsUrl = (sourceUrl = null) => {
        const base = sourceUrl ? new URL(sourceUrl, window.location.origin) : new URL(window.location.href);
        const form = document.getElementById('registrar-all-students-search-form');
        const params = new URLSearchParams(base.search);
        const query = String((form?.querySelector('[name="registrar_all_students_q"]')?.value ?? '')).trim();

        params.set('partial', 'registrar-all-students');
        if (query) params.set('registrar_all_students_q', query);
        else params.delete('registrar_all_students_q');
        if (!sourceUrl) params.delete('registrar_all_students_page');

        base.search = params.toString();
        return base;
    };

    const replaceAllStudentsHistory = (sourceUrl = null) => {
        const next = buildAllStudentsUrl(sourceUrl);
        next.searchParams.delete('partial');
        window.history.replaceState({}, '', next.toString());
    };

    const fetchAllStudentsSection = async (sourceUrl = null) => {
        if (!allStudentsRoot) return;
        const targetUrl = buildAllStudentsUrl(sourceUrl);
        if (allStudentsAbortController) allStudentsAbortController.abort();
        allStudentsAbortController = new AbortController();

        try {
            const response = await fetch(targetUrl.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: allStudentsAbortController.signal,
            });
            if (!response.ok) return;
            const html = await response.text();
            const searchState = captureSearchInputState('registrar-all-students-search');
            allStudentsRoot.innerHTML = html;
            replaceAllStudentsHistory(sourceUrl);
            applySearchInputState(searchState);
        } catch (error) {
            if (error.name !== 'AbortError') console.error(error);
        }
    };

    const buildStaffUrl = (sourceUrl = null) => {
        const base = sourceUrl ? new URL(sourceUrl, window.location.origin) : new URL(window.location.href);
        const form = document.getElementById('registrar-staff-search-form');
        const params = new URLSearchParams(base.search);
        const query = String((form?.querySelector('[name="registrar_staff_q"]')?.value ?? '')).trim();

        params.set('partial', 'registrar-staff');
        if (query) params.set('registrar_staff_q', query);
        else params.delete('registrar_staff_q');
        if (!sourceUrl) params.delete('registrar_staff_page');

        base.search = params.toString();
        return base;
    };

    const replaceStaffHistory = (sourceUrl = null) => {
        const next = buildStaffUrl(sourceUrl);
        next.searchParams.delete('partial');
        window.history.replaceState({}, '', next.toString());
    };

    const fetchStaffSection = async (sourceUrl = null) => {
        if (!staffRoot) return;
        const targetUrl = buildStaffUrl(sourceUrl);
        if (staffAbortController) staffAbortController.abort();
        staffAbortController = new AbortController();

        try {
            const response = await fetch(targetUrl.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: staffAbortController.signal,
            });
            if (!response.ok) return;
            const html = await response.text();
            const searchState = captureSearchInputState('registrar-staff-search');
            staffRoot.innerHTML = html;
            replaceStaffHistory(sourceUrl);
            applySearchInputState(searchState);
        } catch (error) {
            if (error.name !== 'AbortError') console.error(error);
        }
    };

    document.body.addEventListener('input', (event) => {
        const input = event.target.closest('#registrar-verified-search');
        if (!input) return;
        if (debounceTimer) window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(() => fetchVerifiedSection(), SEARCH_DEBOUNCE_MS);
    });

    document.body.addEventListener('submit', (event) => {
        const form = event.target.closest('#registrar-verified-search-form');
        if (!form) return;
        event.preventDefault();
        fetchVerifiedSection();
    });
    document.body.addEventListener('input', (event) => {
        const input = event.target.closest('#registrar-clearance-search');
        if (!input) return;
        if (clearanceDebounceTimer) window.clearTimeout(clearanceDebounceTimer);
        clearanceDebounceTimer = window.setTimeout(() => fetchClearancesSection(), SEARCH_DEBOUNCE_MS);
    });

    document.body.addEventListener('submit', (event) => {
        const form = event.target.closest('#registrar-clearance-search-form');
        if (!form) return;
        event.preventDefault();
        fetchClearancesSection();
    });
    document.body.addEventListener('input', (event) => {
        const input = event.target.closest('#registrar-class-group-search');
        if (!input) return;
        if (classGroupDebounceTimer) window.clearTimeout(classGroupDebounceTimer);
        classGroupDebounceTimer = window.setTimeout(() => fetchClassGroupsSection(), SEARCH_DEBOUNCE_MS);
    });

    document.body.addEventListener('submit', (event) => {
        const form = event.target.closest('#registrar-class-group-search-form');
        if (!form) return;
        event.preventDefault();
        fetchClassGroupsSection();
    });
    document.body.addEventListener('input', (event) => {
        const input = event.target.closest('#registrar-inactive-search');
        if (!input) return;
        if (inactiveDebounceTimer) window.clearTimeout(inactiveDebounceTimer);
        inactiveDebounceTimer = window.setTimeout(() => fetchInactiveSection(), SEARCH_DEBOUNCE_MS);
    });

    document.body.addEventListener('submit', (event) => {
        const form = event.target.closest('#registrar-inactive-search-form');
        if (!form) return;
        event.preventDefault();
        fetchInactiveSection();
    });
    document.body.addEventListener('input', (event) => {
        const input = event.target.closest('#registrar-all-students-search');
        if (!input) return;
        if (allStudentsDebounceTimer) window.clearTimeout(allStudentsDebounceTimer);
        allStudentsDebounceTimer = window.setTimeout(() => fetchAllStudentsSection(), SEARCH_DEBOUNCE_MS);
    });

    document.body.addEventListener('submit', (event) => {
        const form = event.target.closest('#registrar-all-students-search-form');
        if (!form) return;
        event.preventDefault();
        fetchAllStudentsSection();
    });
    document.body.addEventListener('input', (event) => {
        const input = event.target.closest('#registrar-staff-search');
        if (!input) return;
        if (staffDebounceTimer) window.clearTimeout(staffDebounceTimer);
        staffDebounceTimer = window.setTimeout(() => fetchStaffSection(), SEARCH_DEBOUNCE_MS);
    });

    document.body.addEventListener('submit', (event) => {
        const form = event.target.closest('#registrar-staff-search-form');
        if (!form) return;
        event.preventDefault();
        fetchStaffSection();
    });

    document.body.addEventListener('click', (event) => {
        const link = event.target.closest('#registrar-verified-root .cashier-pager a');
        if (!link) return;
        event.preventDefault();
        fetchVerifiedSection(link.href);
    });
    document.body.addEventListener('click', (event) => {
        const link = event.target.closest('#registrar-clearances-root .cashier-pager a');
        if (!link) return;
        event.preventDefault();
        fetchClearancesSection(link.href);
    });
    document.body.addEventListener('click', (event) => {
        const link = event.target.closest('#registrar-class-groups-root .cashier-pager a');
        if (!link) return;
        event.preventDefault();
        fetchClassGroupsSection(link.href);
    });
    document.body.addEventListener('click', (event) => {
        const link = event.target.closest('#registrar-inactive-root .cashier-pager a');
        if (!link) return;
        event.preventDefault();
        fetchInactiveSection(link.href);
    });
    document.body.addEventListener('click', (event) => {
        const link = event.target.closest('#registrar-all-students-root .cashier-pager a');
        if (!link) return;
        event.preventDefault();
        fetchAllStudentsSection(link.href);
    });
    document.body.addEventListener('click', (event) => {
        const link = event.target.closest('#registrar-staff-root .cashier-pager a');
        if (!link) return;
        event.preventDefault();
        fetchStaffSection(link.href);
    });
});
</script>
@endpush

@push('styles')
<style>
/* ─── KPI stat cards ─────────────────────────────────────────────────── */
.enroll-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 14px;
    margin-bottom: 22px;
}

.enroll-kpi-card {
    background: #ffffff;
    border: 1px solid rgba(34, 197, 94, 0.22);
    border-radius: 16px;
    padding: 18px 16px 16px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
    box-shadow: 0 2px 12px rgba(15, 23, 42, 0.05);
    transition: box-shadow 0.2s, border-color 0.2s;
}

.enroll-kpi-card:hover {
    box-shadow: 0 4px 20px rgba(21, 128, 61, 0.12);
    border-color: rgba(34, 197, 94, 0.4);
}

.enroll-kpi-icon {
    color: #16a34a;
    opacity: 0.7;
    flex-shrink: 0;
}

.enroll-kpi-num {
    font-family: var(--font-sans, 'Inter', system-ui, -apple-system, sans-serif);
    font-size: 2rem;
    font-weight: 900;
    line-height: 1;
    margin-top: 2px;
}

.enroll-kpi-num--green  { color: #15803d; }
.enroll-kpi-num--amber  { color: #b45309; }
.enroll-kpi-num--blue   { color: #2563eb; }
.enroll-kpi-num--purple { color: #6d28d9; }

.enroll-kpi-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #64748b;
    line-height: 1.3;
}

/* ─── Tab strip ──────────────────────────────────────────────────────── */
.enroll-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 16px;
    background: transparent;
    border: none;
    padding: 0;
}

.enroll-tab-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 999px;
    border: 1px solid rgba(34, 197, 94, 0.3);
    background: #ffffff;
    color: #334155;
    font-size: 0.82rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, color 0.15s, border-color 0.15s;
}

.enroll-tab-btn:hover {
    background: rgba(34, 197, 94, 0.08);
    color: #15803d;
    border-color: rgba(34, 197, 94, 0.55);
}

.enroll-tab-btn.active {
    background: linear-gradient(135deg, #15803d 0%, #14532d 100%);
    color: #ffffff;
    border-color: transparent;
    box-shadow: 0 3px 10px rgba(21, 101, 52, 0.25);
}

.enroll-tab-badge--amber {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 1px 7px;
    border-radius: 999px;
    font-size: 0.65rem;
    font-weight: 800;
    background: #fef3c7;
    color: #b45309;
    border: 1px solid #fde68a;
}

.enroll-tab-btn.active .enroll-tab-badge--amber {
    background: rgba(255,255,255,0.25);
    color: #fff;
    border-color: rgba(255,255,255,0.4);
}

/* ─── Section cards ──────────────────────────────────────────────────── */
.tenant-ui-mock .card {
    background: linear-gradient(180deg, rgba(240, 253, 244, 0.45) 0%, #ffffff 100%);
    border: 1px solid rgba(34, 197, 94, 0.24);
    border-radius: 18px;
    box-shadow: 0 2px 14px rgba(15, 23, 42, 0.05);
    overflow: hidden;
}

.tenant-ui-mock .card-header {
    border-bottom: 1px solid rgba(34, 197, 94, 0.18);
    padding: 16px 20px;
    background: transparent;
}

.tenant-ui-mock .card-header h2 {
    font-size: 0.98rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    color: #0f172a;
}

.tenant-ui-mock .card-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tenant-ui-mock .card-icon.green  { background: #dcfce7; color: #15803d; }
.tenant-ui-mock .card-icon.amber  { background: #fef3c7; color: #b45309; }
.tenant-ui-mock .card-icon.blue   { background: #dbeafe; color: #1d4ed8; }
.tenant-ui-mock .card-icon.purple { background: #ede9fe; color: #6d28d9; }
.tenant-ui-mock .card-icon.red    { background: #fee2e2; color: #b91c1c; }

/* table inside enroll cards */
.tenant-ui-mock .table-wrap {
    border-radius: 0 0 16px 16px;
    border: none;
    overflow-x: auto;
}

.tenant-ui-mock .table-wrap table thead tr {
    background: #f8fafc;
}

.tenant-ui-mock .table-wrap table thead th {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #64748b;
    padding: 10px 14px;
    border-bottom: 1px solid rgba(34, 197, 94, 0.18);
}

.tenant-ui-mock .table-wrap table tbody td {
    padding: 11px 14px;
    border-bottom: 1px solid rgba(226, 232, 240, 0.6);
    font-size: 0.875rem;
    vertical-align: middle;
}

.tenant-ui-mock .table-wrap table tbody tr:last-child td {
    border-bottom: none;
}

.tenant-ui-mock .table-wrap table tbody tr:hover td {
    background: rgba(34, 197, 94, 0.04);
}

.enroll-verified-search-row {
    padding: 12px 16px 0;
}

.enroll-clearance-search-row {
    padding: 12px 16px 0;
}

.enroll-class-group-search-row {
    padding: 12px 16px 0;
}

.enroll-all-students-search-row {
    padding: 12px 16px 0;
}

.enroll-inactive-search-row {
    padding: 12px 16px 0;
}

.enroll-registrar-staff-search-row {
    padding: 12px 16px 0;
}

#registrar-verified-search-form {
    display: flex;
    gap: 10px;
    align-items: stretch;
    width: 100%;
}

#registrar-clearance-search-form {
    display: flex;
    gap: 10px;
    align-items: stretch;
    width: 100%;
}

#registrar-class-group-search-form {
    display: flex;
    gap: 10px;
    align-items: stretch;
    width: 100%;
}

#registrar-inactive-search-form {
    display: flex;
    gap: 10px;
    align-items: stretch;
    width: 100%;
}

#registrar-all-students-search-form {
    display: flex;
    gap: 10px;
    align-items: stretch;
    width: 100%;
}

#registrar-staff-search-form {
    display: flex;
    gap: 10px;
    align-items: stretch;
    width: 100%;
}

.enroll-verified-search-input {
    flex: 1 1 auto;
    border: 1px solid rgba(34, 197, 94, 0.24);
    border-radius: 10px;
    min-height: 42px;
    padding: 10px 12px;
    font-size: 0.9rem;
    outline: none;
    background: #fff;
}

.enroll-clearance-search-input {
    flex: 1 1 auto;
    border: 1px solid rgba(251, 191, 36, 0.4);
    border-radius: 10px;
    min-height: 42px;
    padding: 10px 12px;
    font-size: 0.9rem;
    outline: none;
    background: #fff;
}

.enroll-verified-search-input:focus {
    border-color: rgba(34, 197, 94, 0.55);
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.12);
}

.enroll-clearance-search-input:focus {
    border-color: rgba(217, 119, 6, 0.55);
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
}

.enroll-class-group-search-input {
    flex: 1 1 auto;
    border: 1px solid rgba(37, 99, 235, 0.35);
    border-radius: 10px;
    min-height: 42px;
    padding: 10px 12px;
    font-size: 0.9rem;
    outline: none;
    background: #fff;
}

.enroll-class-group-search-input:focus {
    border-color: rgba(37, 99, 235, 0.65);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.enroll-verified-search-btn {
    border: 1px solid #22c55e;
    background: #16a34a;
    color: #fff;
    border-radius: 10px;
    min-height: 42px;
    padding: 0 16px;
    font-weight: 700;
    cursor: pointer;
    transition: background .16s ease, border-color .16s ease;
}

.enroll-clearance-search-btn {
    border: 1px solid #d97706;
    background: #b45309;
    color: #fff;
    border-radius: 10px;
    min-height: 42px;
    padding: 0 16px;
    font-weight: 700;
    cursor: pointer;
    transition: background .16s ease, border-color .16s ease;
}

.enroll-verified-search-btn:hover {
    background: #15803d;
    border-color: #15803d;
}

.enroll-clearance-search-btn:hover {
    background: #92400e;
    border-color: #92400e;
}

.enroll-class-group-search-btn {
    border: 1px solid #2563eb;
    background: #2563eb;
    color: #fff;
    border-radius: 10px;
    min-height: 42px;
    padding: 0 16px;
    font-weight: 700;
    cursor: pointer;
    transition: background .16s ease, border-color .16s ease;
}

.enroll-class-group-search-btn:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
}

.enroll-all-students-search-input {
    flex: 1 1 auto;
    border: 1px solid rgba(37, 99, 235, 0.35);
    border-radius: 10px;
    min-height: 42px;
    padding: 10px 12px;
    font-size: 0.9rem;
    outline: none;
    background: #fff;
}

.enroll-all-students-search-input:focus {
    border-color: rgba(37, 99, 235, 0.65);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.enroll-all-students-search-btn {
    border: 1px solid #2563eb;
    background: #2563eb;
    color: #fff;
    border-radius: 10px;
    min-height: 42px;
    padding: 0 16px;
    font-weight: 700;
    cursor: pointer;
    transition: background .16s ease, border-color .16s ease;
}

.enroll-all-students-search-btn:hover {
    background: #1d4ed8;
    border-color: #1d4ed8;
}

.enroll-inactive-search-input {
    flex: 1 1 auto;
    border: 1px solid rgba(251, 191, 36, 0.4);
    border-radius: 10px;
    min-height: 42px;
    padding: 10px 12px;
    font-size: 0.9rem;
    outline: none;
    background: #fff;
}

.enroll-inactive-search-input:focus {
    border-color: rgba(217, 119, 6, 0.55);
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
}

.enroll-inactive-search-btn {
    border: 1px solid #d97706;
    background: #b45309;
    color: #fff;
    border-radius: 10px;
    min-height: 42px;
    padding: 0 16px;
    font-weight: 700;
    cursor: pointer;
    transition: background .16s ease, border-color .16s ease;
}

.enroll-inactive-search-btn:hover {
    background: #92400e;
    border-color: #92400e;
}

.enroll-registrar-staff-search-input {
    flex: 1 1 auto;
    border: 1px solid rgba(109, 40, 217, 0.28);
    border-radius: 10px;
    min-height: 42px;
    padding: 10px 12px;
    font-size: 0.9rem;
    outline: none;
    background: #fff;
}

.enroll-registrar-staff-search-input:focus {
    border-color: rgba(109, 40, 217, 0.65);
    box-shadow: 0 0 0 3px rgba(196, 181, 253, 0.35);
}

.enroll-registrar-staff-search-btn {
    border: 1px solid #6d28d9;
    background: #6d28d9;
    color: #fff;
    border-radius: 10px;
    min-height: 42px;
    padding: 0 16px;
    font-weight: 700;
    cursor: pointer;
    transition: background .16s ease, border-color .16s ease;
}

.enroll-registrar-staff-search-btn:hover {
    background: #5b21b6;
    border-color: #5b21b6;
}

.cashier-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 12px 16px 16px;
    border-top: 1px solid rgba(226, 232, 240, 0.7);
    background: #f9fafb;
}

.cashier-pager {
    width: 100%;
    display: flex;
    justify-content: center;
}

.cashier-pager-inner {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 6px 10px;
    font-size: 0.875rem;
    font-weight: 500;
}

.cashier-pager-step {
    color: #64748b;
    text-decoration: none;
    padding: 4px 2px;
    border: none;
    background: none;
    cursor: pointer;
    font: inherit;
}

.cashier-pager-step:hover:not(.disabled) {
    color: #0f172a;
    text-decoration: underline;
}

.cashier-pager-step.disabled {
    color: #cbd5e1;
    cursor: default;
    text-decoration: none;
}

.cashier-pager-ellipsis {
    color: #94a3b8;
    padding: 0 4px;
    user-select: none;
}

.cashier-pager-page {
    min-width: 2rem;
    height: 2rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0 6px;
    border-radius: 6px;
    color: #64748b;
    text-decoration: none;
    font-weight: 600;
    transition: background 0.15s, color 0.15s;
}

.cashier-pager-page:hover:not(.is-active) {
    background: rgba(14, 165, 233, 0.12);
    color: #0369a1;
}

.cashier-pager-page.is-active {
    background: #0ea5e9;
    color: #ffffff;
    box-shadow: 0 1px 3px rgba(14, 165, 233, 0.45);
    cursor: default;
}

/* page header */
.tenant-ui-mock .page-header h1 {
    font-size: 1.35rem;
    font-weight: 800;
    color: #0f172a;
}

.tenant-ui-mock .page-header p {
    color: #64748b;
    font-size: 0.9rem;
    margin-top: 4px;
}

/* remove old cols-3 grid override */
.tenant-ui-mock main.page-body .grid.cols-3.mb-20 { display: none !important; }

/* ─── Weekly grid (unchanged) ────────────────────────────────────────── */
.weekly-grid-wrap {
    border: 1px solid #d6e1f4;
    border-radius: 14px;
    margin: 0 16px 12px;
    overflow: auto;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
}

.weekly-grid {
    display: grid;
}

.weekly-grid-head {
    font-weight: 700;
    font-size: 0.82rem;
    letter-spacing: .03em;
    text-transform: uppercase;
    color: var(--muted);
    background: #f7faff;
    border-bottom: 1px solid var(--border);
    border-right: 1px solid var(--border);
    padding: 8px;
    position: sticky;
    top: 0;
    z-index: 4;
}

.weekly-grid-head.time-col {
    left: 0;
    z-index: 6;
}

.weekly-grid-time {
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    padding: 8px;
    font-size: 0.82rem;
    color: var(--muted);
    font-weight: 600;
    background: #fcfdff;
    position: sticky;
    left: 0;
    z-index: 3;
}

.weekly-grid-time.hour {
    font-weight: 700;
    color: #3f5480;
    border-bottom-color: transparent;
}

.weekly-grid-time.half {
    color: transparent;
    border-bottom-color: var(--border);
}

.weekly-grid-cell {
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    min-height: 38px;
    height: 38px;
    padding: 0;
    position: relative;
    overflow: visible;
    background: rgba(255, 255, 255, 0.7);
}

.weekly-grid-cell.hour {
    border-bottom-color: transparent;
}

.weekly-grid-chip {
    border-radius: 8px;
    padding: 4px 6px;
    display: grid;
    gap: 2px;
    font-size: 0.74rem;
    line-height: 1.2;
}

.weekly-grid-chip-span {
    position: absolute;
    z-index: 5;
    box-sizing: border-box;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(15, 23, 42, 0.10);
    border: 1px solid rgba(15, 23, 42, 0.08);
}

.weekly-grid-chip.lecture {
    background: #dcfce7;
    color: #166534;
}

.weekly-grid-chip.lab {
    background: #fef3c7;
    color: #92400e;
}

/* ─── Enrollment KPI cards (Verified Queue etc.) ───────────────────────── */
main.page-body .grid.cols-3.mb-20 {
    gap: 18px;
    margin-bottom: 22px;
}

main.page-body .grid.cols-3.mb-20 .card {
    padding: 16px 18px;
    border-radius: 16px;
    border-color: rgba(226, 232, 240, 0.95);
    box-shadow: 0 10px 26px rgba(2, 6, 23, 0.06);
    background: linear-gradient(180deg, rgba(255,255,255,0.98) 0%, rgba(248,250,252,0.9) 100%);
}

main.page-body .grid.cols-3.mb-20 .card-header {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

main.page-body .grid.cols-3.mb-20 .card-header h2 {
    font-size: 0.92rem;
    font-weight: 800;
    color: var(--ink);
}

main.page-body .grid.cols-3.mb-20 .card-header h2 {
    margin: 0;
}

main.page-body .grid.cols-3.mb-20 .badge {
    font-size: 0.75rem;
    font-weight: 900;
    letter-spacing: 0;
    text-transform: none;
    min-width: 44px;
    height: 26px;
    justify-content: center;
    padding-left: 10px;
    padding-right: 10px;
}

main.page-body .grid.cols-3.mb-20 .badge.green  { background: rgba(22, 163, 74, 0.12); color: #15803d; border-color: rgba(134, 239, 172, 0.55); }
main.page-body .grid.cols-3.mb-20 .badge.amber  { background: rgba(180, 83, 9, 0.12); color: #b45309; border-color: rgba(253, 230, 138, 0.7); }
main.page-body .grid.cols-3.mb-20 .badge.blue   { background: rgba(37, 99, 235, 0.10); color: #2563eb; border-color: rgba(191, 219, 254, 0.95); }
main.page-body .grid.cols-3.mb-20 .badge.purple { background: rgba(109, 40, 217, 0.10); color: #6d28d9; border-color: rgba(196, 181, 253, 0.95); }

@media (max-width: 900px) {
    #registrar-verified-search-form {
        flex-direction: column;
    }
    #registrar-clearance-search-form {
        flex-direction: column;
    }
    #registrar-class-group-search-form {
        flex-direction: column;
    }
    #registrar-inactive-search-form {
        flex-direction: column;
    }
    #registrar-all-students-search-form {
        flex-direction: column;
    }
    #registrar-staff-search-form {
        flex-direction: column;
    }

    .weekly-grid-wrap {
        margin-left: 12px;
        margin-right: 12px;
    }
}

/* ═══════════════════════════════════════════════════════════════
   MY ENROLLMENTS — Moodle-style student table
   ══════════════════════════════════════════════════════════════ */
.my-enroll-card {
    background: #ffffff;
    border: 1px solid color-mix(in srgb, var(--admin-primary, #334155) 14%, #e2e8f0);
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 1px 4px rgba(15,23,42,0.05);
    margin-bottom: 22px;
}

/* Header */
.my-enroll-header {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfc;
}
.my-enroll-header-left {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 0;
}
.my-enroll-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: #dbeafe;
    color: #2563eb;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.my-enroll-title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
}
.my-enroll-count {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    padding: 3px 9px;
    border-radius: 999px;
    background: #dbeafe;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
}

/* Search */
.my-enroll-search-wrap {
    position: relative;
    flex-shrink: 0;
}
.my-enroll-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}
.my-enroll-search-input {
    padding: 7px 12px 7px 32px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.82rem;
    color: #1e293b;
    background: #ffffff;
    width: 220px;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.my-enroll-search-input:focus {
    border-color: var(--admin-primary, #334155);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary, #334155) 18%, transparent);
}
.my-enroll-filter-select {
    padding: 7px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.82rem;
    color: #1e293b;
    background: #ffffff;
    outline: none;
    cursor: pointer;
    transition: border-color 0.15s;
    flex-shrink: 0;
}
.my-enroll-filter-select:focus { border-color: var(--admin-primary, #334155); }

/* Table */
.my-enroll-table-wrap {
    overflow-x: auto;
}
.my-enroll-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}
.my-enroll-table thead {
    background: #f8fafc;
    border-bottom: 2px solid #f1f5f9;
}
.my-enroll-table th {
    padding: 11px 16px;
    text-align: left;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    color: #64748b;
}
.my-enroll-th-num { width: 56px; }
.my-enroll-table td {
    padding: 13px 16px;
    border-bottom: 1px solid #f8fafc;
    vertical-align: middle;
}
.my-enroll-table tbody tr:last-child td { border-bottom: none; }
.my-enroll-row { transition: background 0.14s; }
.my-enroll-row:hover { background: #f8fafc; }

.my-enroll-td-num {
    color: #94a3b8;
    font-size: 0.78rem;
    font-weight: 500;
    font-variant-numeric: tabular-nums;
}
.my-enroll-td-subject { min-width: 200px; }
.my-enroll-subject-name {
    font-weight: 600;
    color: #0f172a;
    font-size: 0.875rem;
    margin-bottom: 4px;
}
.my-enroll-muted { color: #94a3b8; font-size: 0.82rem; }

/* Chips */
.my-enroll-chip {
    display: inline-flex;
    align-items: center;
    border-radius: 6px;
    font-size: 0.68rem;
    font-weight: 700;
    padding: 2px 7px;
    letter-spacing: 0.04em;
    border: 1px solid transparent;
}
.my-enroll-chip--blue    { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
.my-enroll-chip--neutral { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }

/* Status badges */
.my-enroll-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 700;
    padding: 4px 11px 4px 8px;
    border: 1px solid transparent;
    letter-spacing: 0.02em;
}
.my-enroll-status--green   { background: #dcfce7; color: #15803d; border-color: #bbf7d0; }
.my-enroll-status--amber   { background: #fef3c7; color: #b45309; border-color: #fde68a; }
.my-enroll-status--blue    { background: #dbeafe; color: #1d4ed8; border-color: #bfdbfe; }
.my-enroll-status--red     { background: #fee2e2; color: #b91c1c; border-color: #fecaca; }
.my-enroll-status--neutral { background: #f1f5f9; color: #475569; border-color: #e2e8f0; }

/* Empty / no results */
.my-enroll-empty {
    padding: 40px 24px;
    text-align: center;
    color: #94a3b8;
}
.my-enroll-empty svg {
    width: 40px; height: 40px;
    display: block;
    margin: 0 auto 14px;
    opacity: 0.4;
}
.my-enroll-empty p {
    margin: 0 0 4px;
    font-size: 0.875rem;
    color: #64748b;
}
.my-enroll-no-results {
    padding: 32px 24px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    color: #94a3b8;
}
.my-enroll-no-results svg {
    width: 36px; height: 36px;
    opacity: 0.4;
}
.my-enroll-no-results p { margin: 0; font-size: 0.875rem; color: #64748b; }

@media (max-width: 640px) {
    .my-enroll-header { flex-direction: column; align-items: flex-start; }
    .my-enroll-search-input { width: 100%; }
    .my-enroll-filter-select { width: 100%; }
}
</style>
@endpush

