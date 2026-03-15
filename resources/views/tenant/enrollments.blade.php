@extends('layouts.app')

@section('title', 'Enrollments â€” School Portal')

@section('content')
<div class="app-shell">
    @include('tenant.partials.sidebar', ['active' => 'enrollments'])

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">Enrollment Workflow</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
                    <span>{{ auth()->user()->full_name ?? 'User' }}</span>
                </div>
            </div>
        </header>

        <main class="page-body">
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">â€º</span>
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
                $registrarVerifiedCount = (int) (($financeVerifiedForRegistrar ?? collect())->count());
                $registrarGroupCount = (int) (($classGroupCapacityForRegistrar ?? collect())->count());
                $registrarInactiveCount = (int) (($inactiveStudentAccountsForRegistrar ?? collect())->count());
                $registrarStudentCount = (int) (($allStudentsForRegistrar ?? collect())->count());
                $pendingClearancesCount = (int) (($pendingClearancesForRegistrar ?? collect())->count());
            @endphp
            <div class="grid cols-3 mb-20">
                <div class="card">
                    <div class="card-header">
                        <h2>Verified Queue</h2>
                        <span class="badge green">{{ $registrarVerifiedCount }}</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h2>Pending Clearances</h2>
                        <span class="badge amber">{{ $pendingClearancesCount }}</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h2>Class Groups</h2>
                        <span class="badge blue">{{ $registrarGroupCount }}</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h2>Inactive Accounts</h2>
                        <span class="badge amber">{{ $registrarInactiveCount }}</span>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h2>Total Students</h2>
                        <span class="badge purple">{{ $registrarStudentCount }}</span>
                    </div>
                </div>
            </div>
            <div data-tabs="registrar-tables">
                <div class="tabs">
                    <button class="tab-btn active" data-tab="reg-verified" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        Verified Students
                    </button>
                    <button class="tab-btn" data-tab="reg-clearances" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                        Pending Clearances
                        @if($pendingClearancesCount > 0)
                            <span class="badge amber" style="margin-left:6px; font-size:0.65rem;">{{ $pendingClearancesCount }}</span>
                        @endif
                    </button>
                    <button class="tab-btn" data-tab="reg-groups" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7h18"/><path d="M3 12h18"/><path d="M3 17h18"/>
                        </svg>
                        Class Groups
                    </button>
                    <button class="tab-btn" data-tab="reg-inactive" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        </svg>
                        Inactive Accounts
                    </button>
                    <button class="tab-btn" data-tab="reg-students" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                        Students
                    </button>
                    <button class="tab-btn" data-tab="reg-staffs" type="button">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        </svg>
                        School Staffs
                    </button>
                </div>

                <div class="tab-panel active" data-panel="reg-verified">
            <div class="card mb-20">
                <div class="card-header">
                    <h2>
                        <div class="card-icon green">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        Finance Verified Students (Registrar)
                    </h2>
                    <span class="badge green">{{ ($financeVerifiedForRegistrar ?? collect())->count() }} verified</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($financeVerifiedForRegistrar ?? collect()) as $row)
                                <tr>
                                    <td style="color:var(--muted); font-size:0.8rem;">{{ $row->id }}</td>
                                    <td style="font-weight:600; color:var(--ink);">{{ $row->student->full_name ?? ('#' . $row->student_user_id) }}</td>
                                    <td>
                                        <div style="color:var(--ink-2);">{{ $row->student->email ?? 'N/A' }}</div>
                                        <div style="margin-top:4px;">
                                            <span class="badge {{ (($row->student->status ?? '') === 'active') ? 'green' : 'amber' }}">
                                                account {{ (($row->student->status ?? '') === 'active') ? 'active' : 'inactive' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($row->offering?->subject)
                                            <span class="badge blue">{{ $row->offering->subject->code }}</span>
                                            {{ $row->offering->subject->title ?? '' }}
                                        @else
                                            <span class="text-muted text-sm">â€”</span>
                                        @endif
                                    </td>
                                    <td><span class="badge green">{{ $row->status }}</span></td>
                                    <td>
                                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                            <form method="post" action="{{ url('/tenant/enrollments/' . $row->id . '/confirm') }}">
                                                @csrf
                                                <button class="btn success sm" type="submit">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                                    </svg>
                                                    Confirm Enrollment
                                                </button>
                                            </form>
                                            @if(($row->student->status ?? '') !== 'active')
                                                <form method="post" action="{{ url('/tenant/enrollments/students/' . $row->student_user_id . '/activate') }}">
                                                    @csrf
                                                    <button class="btn primary sm" type="submit">
                                                        Activate Account
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state" style="padding:24px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                            </svg>
                                            <p>No finance-verified students available for registrar confirmation.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
                </div>

                <div class="tab-panel" data-panel="reg-clearances">
            <div class="card mb-20">
                <div class="card-header">
                    <h2>
                        <div class="card-icon amber">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                            </svg>
                        </div>
                        Pending Clearance Approvals
                    </h2>
                    <span class="badge amber">{{ ($pendingClearancesForRegistrar ?? collect())->count() }} pending</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Cleared By</th>
                                <th>Cleared At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $groupedByStudent = ($pendingClearancesForRegistrar ?? collect())->groupBy('student_user_id');
                            @endphp
                            @forelse(($pendingClearancesForRegistrar ?? collect()) as $billing)
                                @php
                                    $studentBillings = $groupedByStudent[$billing->student_user_id] ?? collect();
                                    $isFirstForStudent = $studentBillings->first()?->id === $billing->id;
                                    $studentBillingCount = $studentBillings->count();
                                @endphp
                                <tr>
                                    <td style="color:var(--muted); font-size:0.8rem;">{{ $billing->id }}</td>
                                    <td>
                                        <div style="font-weight:600;">{{ $billing->student?->full_name ?? 'Student #' . $billing->student_user_id }}</div>
                                        <span class="badge" style="font-size:0.7rem;">ID: {{ $billing->student_user_id }}</span>
                                        @if($studentBillingCount > 1)
                                            <span class="badge amber" style="font-size:0.7rem; margin-left:4px;">{{ $studentBillingCount }} pending</span>
                                        @endif
                                    </td>
                                    <td>{{ $billing->description ?? '-' }}</td>
                                    <td style="font-weight:600;">₱ {{ number_format((float) $billing->amount_due, 2) }}</td>
                                    <td style="font-size:0.85rem; color:var(--muted);">
                                        {{ $billing->clearedByFinance?->full_name ?? 'Finance #' . $billing->cleared_by_finance_user_id }}
                                    </td>
                                    <td style="font-size:0.85rem; color:var(--muted);">
                                        {{ optional($billing->cleared_at)->format('M d, Y H:i') ?? '-' }}
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                            <form method="post" action="{{ url('/tenant/billing/' . $billing->id . '/clearance/approve') }}" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn success sm">Approve</button>
                                            </form>
                                            @if($isFirstForStudent && $studentBillingCount > 1)
                                                <form method="post" action="{{ url('/tenant/billing/clearance/approve-all-for-student') }}" style="display:inline;">
                                                    @csrf
                                                    <input type="hidden" name="student_user_id" value="{{ $billing->student_user_id }}">
                                                    <input type="hidden" name="semester_id" value="{{ $billing->semester_id }}">
                                                    <button type="submit" class="btn primary sm" title="Approve all {{ $studentBillingCount }} clearances for this student" style="font-weight:600;">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;">
                                                            <path d="M20 6L9 17l-5-5"/>
                                                        </svg>
                                                        Approve All ({{ $studentBillingCount }})
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state" style="padding:24px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                            </svg>
                                            <p>No pending clearances awaiting approval.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
                </div>

                <div class="tab-panel" data-panel="reg-groups">
            <div class="card mb-20">
                <div class="card-header">
                    <h2>
                        <div class="card-icon blue">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7h18"/><path d="M3 12h18"/><path d="M3 17h18"/>
                            </svg>
                        </div>
                        Class Group Capacity (Registrar)
                    </h2>
                    <span class="badge blue">{{ ($classGroupCapacityForRegistrar ?? collect())->count() }} groups</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Class Group</th>
                                <th>Program</th>
                                <th>Semester</th>
                                <th>Students Inside</th>
                                <th>Capacity</th>
                                <th>Available Slots</th>
                                <th>Enrollment</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($classGroupCapacityForRegistrar ?? collect()) as $group)
                                @php
                                    $insideCount = (int) ($group->students_inside_count ?? 0);
                                    $capacity = (int) ($group->student_capacity ?? 0);
                                    $available = max($capacity - $insideCount, 0);
                                    $isFull = $capacity > 0 && $insideCount >= $capacity;
                                @endphp
                                <tr>
                                    <td style="color:var(--muted); font-size:0.8rem;">{{ $group->id }}</td>
                                    <td style="font-weight:600; color:var(--ink);">{{ $group->name }} | Y{{ (int) $group->year_level }}</td>
                                    <td>
                                        <div style="color:var(--ink); font-weight:600;">{{ $group->program->code ?? 'N/A' }}</div>
                                        <div style="color:var(--ink-2); font-size:0.85rem;">{{ $group->program->name ?? 'N/A' }}</div>
                                    </td>
                                    <td>{{ $group->semester->name ?? 'N/A' }}</td>
                                    <td><span class="badge blue">{{ $insideCount }}</span></td>
                                    <td><span class="badge">{{ $capacity }}</span></td>
                                    <td><span class="badge {{ $isFull ? 'red' : 'green' }}">{{ $available }}</span></td>
                                    <td>
                                        <span class="badge {{ (bool) ($group->is_enrollment_open ?? false) ? 'green' : 'amber' }}">
                                            {{ (bool) ($group->is_enrollment_open ?? false) ? 'open' : 'closed' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state" style="padding:24px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/>
                                            </svg>
                                            <p>No class groups found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
                </div>

                <div class="tab-panel" data-panel="reg-inactive">
            <div class="card mb-20">
                <div class="card-header">
                    <h2>
                        <div class="card-icon amber">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            </svg>
                        </div>
                        Inactive Student Accounts (Finance Verified)
                    </h2>
                    <span class="badge amber">{{ ($inactiveStudentAccountsForRegistrar ?? collect())->count() }} ready</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($inactiveStudentAccountsForRegistrar ?? collect()) as $student)
                                <tr>
                                    <td style="color:var(--muted); font-size:0.8rem;">{{ $student->id }}</td>
                                    <td style="font-weight:600; color:var(--ink);">{{ $student->full_name }}</td>
                                    <td style="color:var(--ink-2);">{{ $student->email }}</td>
                                    <td style="color:var(--ink-2);">{{ $student->phone ?? 'N/A' }}</td>
                                    <td><span class="badge amber">inactive</span></td>
                                    <td>
                                        <form method="post" action="{{ url('/tenant/enrollments/students/' . $student->id . '/activate') }}">
                                            @csrf
                                            <button class="btn primary sm" type="submit">
                                                Activate Account
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state" style="padding:24px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                            </svg>
                                            <p>No finance-verified inactive student accounts found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
                </div>

                <div class="tab-panel" data-panel="reg-students">
            <div class="card mb-20">
                <div class="card-header">
                    <h2>
                        <div class="card-icon blue">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            </svg>
                        </div>
                        All Student Records (Registrar)
                    </h2>
                    <span class="badge blue">{{ ($allStudentsForRegistrar ?? collect())->count() }} students</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($allStudentsForRegistrar ?? collect()) as $student)
                                <tr>
                                    <td style="color:var(--muted); font-size:0.8rem;">{{ $student->id }}</td>
                                    <td style="font-weight:600; color:var(--ink);">{{ $student->full_name }}</td>
                                    <td style="color:var(--ink-2);">{{ $student->email }}</td>
                                    <td style="color:var(--ink-2);">{{ $student->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ ($student->status ?? '') === 'active' ? 'green' : 'amber' }}">
                                            {{ ($student->status ?? '') === 'active' ? 'active' : 'inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if(($student->status ?? '') !== 'active')
                                            <form method="post" action="{{ url('/tenant/enrollments/students/' . $student->id . '/activate') }}">
                                                @csrf
                                                <button class="btn primary sm" type="submit">Activate Account</button>
                                            </form>
                                        @else
                                            <span class="text-muted">â€”</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state" style="padding:24px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                            </svg>
                                            <p>No students found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
                </div>

                <div class="tab-panel" data-panel="reg-staffs">
            <div class="card mb-20">
                <div class="card-header">
                    <h2>
                        <div class="card-icon purple">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        School Staff Records (Registrar)
                    </h2>
                    <span class="badge purple">{{ ($schoolStaffForRegistrar ?? collect())->count() }} staffs</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Roles</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($schoolStaffForRegistrar ?? collect()) as $staff)
                                <tr>
                                    <td style="color:var(--muted); font-size:0.8rem;">{{ $staff['id'] }}</td>
                                    <td style="font-weight:600; color:var(--ink);">{{ $staff['full_name'] }}</td>
                                    <td style="color:var(--ink-2);">{{ $staff['email'] }}</td>
                                    <td style="color:var(--ink-2);">{{ $staff['phone'] !== '' ? $staff['phone'] : 'N/A' }}</td>
                                    <td>
                                        @foreach(($staff['roles'] ?? []) as $roleCode)
                                            <span class="badge blue" style="margin-right:6px;">{{ strtoupper((string) $roleCode) }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="badge {{ ($staff['status'] ?? '') === 'active' ? 'green' : 'amber' }}">
                                            {{ ($staff['status'] ?? '') === 'active' ? 'active' : 'inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state" style="padding:24px;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                            </svg>
                                            <p>No school staffs found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
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

            <div class="card" id="my-enrollments">
                <div class="card-header">
                    <h2>
                        <div class="card-icon blue">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                            </svg>
                        </div>
                        My Enrollments
                    </h2>
                    <span class="badge blue">{{ $myEnrollments->count() }} total</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Subject</th>
                                <th>Class Group</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myEnrollments as $row)
                                <tr>
                                    <td style="color:var(--muted); font-size:0.8rem;">{{ $row->id }}</td>
                                    <td>
                                        @if($row->offering?->subject)
                                            <div style="font-weight:600; color:var(--ink);">{{ $row->offering->subject->title }}</div>
                                            <span class="badge blue" style="font-size:0.65rem; margin-top:2px;">{{ $row->offering->subject->code }}</span>
                                        @else
                                            <span class="text-muted text-sm">â€”</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($row->section)
                                            <span class="badge">{{ $row->class_group_label ?? $row->section->identifier }}</span>
                                        @else
                                            <span class="text-muted text-sm">â€”</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'confirmed'  => 'green',
                                                'pending'    => 'amber',
                                                'selected'   => 'blue',
                                                'billing_pending' => 'amber',
                                                'payment_verified' => 'green',
                                                'enrolled' => 'green',
                                                'dropped'    => 'red',
                                                'cancelled'  => 'red',
                                            ];
                                            $statusColor = $statusColors[$row->status] ?? '';
                                        @endphp
                                        <span class="badge {{ $statusColor }}">{{ $row->status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="empty-state">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                                <polyline points="14 2 14 8 20 8"/>
                                            </svg>
                                            <p>You have no enrollment records yet. Submit an enrollment request above.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

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
                                @foreach($scheduleRows as $row)
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
                @endif
            </div>
            @endif
        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
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

@media (max-width: 900px) {
    .weekly-grid-wrap {
        margin-left: 12px;
        margin-right: 12px;
    }
}
</style>
@endpush

