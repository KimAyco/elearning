@extends('layouts.app')

@section('title', 'Grades — School Portal')

@section('content')
<div class="app-shell">
    @include('tenant.partials.sidebar', ['active' => 'grades'])

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">{{ ($isStudentGradesView ?? false) ? 'My Grades' : 'Grade Management' }}</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
                    <span>{{ auth()->user()->full_name ?? 'User' }}</span>
                </div>
            </div>
        </header>

        <main class="page-body">
            @if($isStudentGradesView)
                {{-- Student View: My Grades --}}
                <div class="card">
                    <div class="card-header">
                        <h2>My Released Grades</h2>
                    </div>

                    @if($studentGradeRows->isEmpty())
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p>No grades available yet.</p>
                        </div>
                    @else
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Subject Code</th>
                                        <th>Subject Title</th>
                                        <th>Section</th>
                                        <th>Semester</th>
                                        <th>Grade</th>
                                        <th>Status</th>
                                        <th>Released</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentGradeRows as $row)
                                        <tr>
                                            <td><strong>{{ $row['subject_code'] }}</strong></td>
                                            <td>{{ $row['subject_title'] }}</td>
                                            <td>{{ $row['section_name'] }}</td>
                                            <td>{{ $row['semester_name'] }}</td>
                                            <td>
                                                @if($row['grade_value'])
                                                    <strong class="fw-600">{{ $row['grade_value'] }}</strong>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusMap = [
                                                        'draft' => ['label' => 'Draft', 'color' => ''],
                                                        'submitted' => ['label' => 'Submitted', 'color' => 'blue'],
                                                        'dean_approved' => ['label' => 'Dean Approved', 'color' => 'green'],
                                                        'dean_rejected' => ['label' => 'Dean Rejected', 'color' => 'red'],
                                                        'registrar_finalized' => ['label' => 'Finalized', 'color' => 'purple'],
                                                        'released' => ['label' => 'Released', 'color' => 'green'],
                                                        'pending' => ['label' => 'Pending', 'color' => 'amber'],
                                                    ];
                                                    $status = $statusMap[$row['grade_status']] ?? ['label' => ucfirst($row['grade_status']), 'color' => ''];
                                                @endphp
                                                <span class="badge {{ $status['color'] }}">{{ $status['label'] }}</span>
                                            </td>
                                            <td class="text-sm text-muted">
                                                @if($row['released_at'])
                                                    {{ $row['released_at']->format('M j, Y') }}
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @else
                {{-- Teacher/Staff View: Grade Management --}}
                <div id="grade-alert-container"></div>

                <div class="card">
                    <div class="card-header">
                        <h2>
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Grade Entry - My Assigned Subjects
                        </h2>
                    </div>

                    @if(($teacherSubjectClasses ?? collect())->isEmpty())
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p>You have no assigned subject offerings with enrolled students.</p>
                            @if(config('app.debug'))
                                <div style="margin-top: 20px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.75rem; text-align: left;">
                                    <strong>Debug Info:</strong><br>
                                    Teacher Enrollments Count: {{ $teacherEnrollments->count() }}<br>
                                    Subject Classes Count: {{ ($teacherSubjectClasses ?? collect())->count() }}<br>
                                    @if($teacherEnrollments->count() > 0)
                                        <br><strong>Sample Enrollment:</strong><br>
                                        Subject ID: {{ $teacherEnrollments->first()->offering?->subject_id ?? 'N/A' }}<br>
                                        Section ID: {{ $teacherEnrollments->first()->section_id ?? 'N/A' }}<br>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="grid cols-3" style="padding: 20px;">
                            @foreach($teacherSubjectClasses as $subjectClass)
                                @php
                                    $subject = $subjectClass['subject'];
                                    $classGroup = $subjectClass['class_group'];
                                    $studentCount = $subjectClass['student_count'];
                                    $enrollments = $subjectClass['enrollments'];
                                    
                                    // Count grade statuses
                                    $draftCount = 0;
                                    $submittedCount = 0;
                                    $completedCount = 0;
                                    
                                    foreach ($enrollments as $enr) {
                                        $grade = $teacherGradesByEnrollmentId->get($enr->id);
                                        $status = $grade?->status ?? 'none';
                                        if (in_array($status, ['none', 'draft', 'dean_rejected'], true)) {
                                            $draftCount++;
                                        } elseif ($status === 'submitted') {
                                            $submittedCount++;
                                        } elseif (in_array($status, ['dean_approved', 'registrar_finalized', 'released'], true)) {
                                            $completedCount++;
                                        }
                                    }
                                @endphp
                                
                                <div class="card" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" 
                                     onclick="openSubjectGradeModal({{ $subject?->id ?? 0 }}, {{ $classGroup?->id ?? 0 }}, '{{ $subject?->code ?? 'N/A' }}', '{{ $classGroup?->name ?? 'N/A' }}')">
                                    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
                                        <div>
                                            <div style="font-size: 0.75rem; font-weight: 700; color: var(--accent); text-transform: uppercase; letter-spacing: 0.05em;">
                                                {{ $subject?->code ?? 'N/A' }}
                                            </div>
                                            <h3 style="margin: 4px 0 0; font-size: 1.1rem;">{{ $subject?->title ?? 'Untitled' }}</h3>
                                        </div>
                                        <span class="badge blue">{{ $studentCount }} students</span>
                                    </div>
                                    
                                    <div style="font-size: 0.875rem; color: var(--muted); margin-bottom: 16px;">
                                        <strong>Class:</strong> {{ $classGroup?->name ?? 'N/A' }}
                                    </div>
                                    
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        @if($draftCount > 0)
                                            <span class="badge amber">{{ $draftCount }} pending</span>
                                        @endif
                                        @if($submittedCount > 0)
                                            <span class="badge blue">{{ $submittedCount }} submitted</span>
                                        @endif
                                        @if($completedCount > 0)
                                            <span class="badge green">{{ $completedCount }} completed</span>
                                        @endif
                                    </div>
                                    
                                    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border); color: var(--accent); font-weight: 600; font-size: 0.875rem;">
                                        Click to manage grades →
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Grade Entry Modal --}}
                <div id="modal-subject-grades" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
                    <div style="background: white; border-radius: var(--radius-lg); max-width: 1200px; width: 90%; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid var(--border);">
                            <div>
                                <h2 style="margin: 0; font-size: 1.2rem;">Grade Entry</h2>
                                <p style="margin: 4px 0 0; font-size: 0.875rem; color: var(--muted);">
                                    <span id="modal-subject-title"></span> - <span id="modal-section-title"></span>
                                </p>
                            </div>
                            <button onclick="closeSubjectGradeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; padding: 4px 8px; color: var(--muted);">&times;</button>
                        </div>
                        
                        <div id="modal-grade-content" style="flex: 1; overflow-y: auto; padding: 24px;">
                            <!-- Content will be loaded here -->
                        </div>
                    </div>
                </div>

                @if(in_array('dean', $roleCodes ?? [], true) && $deanQueue->isNotEmpty())
                    <div class="card mt-20">
                        <div class="card-header">
                            <h2>
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Dean Review Queue
                            </h2>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Subject</th>
                                        <th>Grade</th>
                                        <th>Submitted By</th>
                                        <th>Submitted At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deanQueue as $grade)
                                        <tr>
                                            <td>{{ $grade->student?->full_name ?? 'N/A' }}</td>
                                            <td>{{ $grade->offering?->subject?->code ?? 'N/A' }}</td>
                                            <td><strong>{{ $grade->grade_value }}</strong></td>
                                            <td>Teacher</td>
                                            <td class="text-sm">{{ $grade->submitted_at?->format('M j, Y g:i A') ?? 'N/A' }}</td>
                                            <td>
                                                <span class="text-xs text-muted">Review pending</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                @if(in_array('registrar', $roleCodes ?? [], true) && $registrarQueue->isNotEmpty())
                    <div class="card mt-20">
                        <div class="card-header">
                            <h2>
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                </svg>
                                Registrar Queue
                            </h2>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Subject</th>
                                        <th>Grade</th>
                                        <th>Status</th>
                                        <th>Dean Decision</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registrarQueue as $grade)
                                        <tr>
                                            <td>{{ $grade->student?->full_name ?? 'N/A' }}</td>
                                            <td>{{ $grade->offering?->subject?->code ?? 'N/A' }}</td>
                                            <td><strong>{{ $grade->grade_value }}</strong></td>
                                            <td>
                                                <span class="badge {{ $grade->status === 'registrar_finalized' ? 'purple' : 'green' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $grade->status)) }}
                                                </span>
                                            </td>
                                            <td class="text-sm">{{ $grade->dean_decided_at?->format('M j, Y') ?? 'N/A' }}</td>
                                            <td>
                                                <span class="text-xs text-muted">Finalization pending</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </main>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // CSRF token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // Subject grades modal data
    const teacherEnrollments = @json($teacherEnrollments ?? []);
    const teacherGradesByEnrollmentId = @json($teacherGradesByEnrollmentId ?? []);

    // Open subject grade modal
    function openSubjectGradeModal(subjectId, classGroupId, subjectCode, className) {
        const modal = document.getElementById('modal-subject-grades');
        const content = document.getElementById('modal-grade-content');
        const titleEl = document.getElementById('modal-subject-title');
        const sectionEl = document.getElementById('modal-section-title');
        
        if (!modal || !content) return;
        
        titleEl.textContent = subjectCode;
        sectionEl.textContent = className;
        
        // Filter enrollments for this subject and class group (via section identifier CG-{id})
        const enrollments = teacherEnrollments.filter(e => {
            const sectionIdentifier = e.section?.identifier || '';
            return e.offering?.subject_id === subjectId && 
                   sectionIdentifier === `CG-${classGroupId}`;
        });
        
        if (enrollments.length === 0) {
            content.innerHTML = '<p style="text-align: center; color: var(--muted); padding: 40px;">No students enrolled.</p>';
        } else {
            content.innerHTML = generateGradeTable(enrollments);
        }
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    // Close subject grade modal
    function closeSubjectGradeModal() {
        const modal = document.getElementById('modal-subject-grades');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }
    
    // Generate grade table HTML
    function generateGradeTable(enrollments) {
        let html = `
            <div class="table-wrap">
                <table id="grades-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th style="width: 120px;">Grade</th>
                            <th style="width: 200px;">Remarks</th>
                            <th style="width: 120px;">Status</th>
                            <th style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        enrollments.forEach(enrollment => {
            const grade = teacherGradesByEnrollmentId[enrollment.id];
            const student = enrollment.student;
            const status = grade?.status || 'none';
            const canEdit = ['none', 'draft', 'dean_rejected'].includes(status);
            const canSubmit = grade && ['draft', 'dean_rejected'].includes(status);
            
            const statusMap = {
                'none': {label: 'Not Started', color: ''},
                'draft': {label: 'Draft', color: 'amber'},
                'submitted': {label: 'Submitted', color: 'blue'},
                'dean_approved': {label: 'Dean Approved', color: 'green'},
                'dean_rejected': {label: 'Dean Rejected', color: 'red'},
                'registrar_finalized': {label: 'Finalized', color: 'purple'},
                'released': {label: 'Released', color: 'green'},
            };
            const statusDisplay = statusMap[status] || {label: status, color: ''};
            
            html += `
                <tr data-enrollment-id="${enrollment.id}" 
                    data-grade-id="${grade?.id || ''}"
                    data-can-edit="${canEdit ? '1' : '0'}"
                    data-can-submit="${canSubmit ? '1' : '0'}">
                    <td>
                        <strong>${student?.full_name || 'N/A'}</strong><br>
                        <span class="text-xs text-muted">${student?.email || ''}</span>
                    </td>
                    <td>
                        <input type="text" 
                               class="grade-value-input" 
                               value="${grade?.grade_value || ''}"
                               placeholder="e.g. 1.0"
                               maxlength="10"
                               style="width: 100%; padding: 6px 8px; font-size: 0.875rem;"
                               ${canEdit ? '' : 'readonly'}>
                    </td>
                    <td>
                        <input type="text" 
                               class="grade-remarks-input" 
                               value="${grade?.submitted_remarks || ''}"
                               placeholder="Optional"
                               maxlength="255"
                               style="width: 100%; padding: 6px 8px; font-size: 0.875rem;"
                               ${canEdit ? '' : 'readonly'}>
                    </td>
                    <td>
                        <span class="badge ${statusDisplay.color} grade-status-badge">
                            ${statusDisplay.label}
                        </span>
                    </td>
                    <td>
                        ${canEdit ? `
                            <button class="btn btn-save-draft sm secondary" style="margin-right: 4px;">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                Save
                            </button>
                        ` : ''}
                        ${canSubmit ? `
                            <button class="btn btn-submit-grade sm success">
                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Submit
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
        `;
        
        return html;
    }
    
    // Close modal on escape key or backdrop click
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSubjectGradeModal();
    });
    
    document.getElementById('modal-subject-grades')?.addEventListener('click', (e) => {
        if (e.target.id === 'modal-subject-grades') {
            closeSubjectGradeModal();
        }
    });

    // Show alert message
    function showAlert(message, type = 'success') {
        const container = document.getElementById('grade-alert-container');
        if (!container) return;

        const alert = document.createElement('div');
        alert.className = `alert ${type}`;
        alert.innerHTML = `
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' ? 
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>'}
            </svg>
            <span>${message}</span>
        `;
        container.appendChild(alert);

        setTimeout(() => {
            alert.style.transition = 'opacity .5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    }

    // Update row status badge
    function updateRowStatus(row, status, gradeId) {
        const statusMap = {
            'none': {label: 'Not Started', color: ''},
            'draft': {label: 'Draft', color: 'amber'},
            'submitted': {label: 'Submitted', color: 'blue'},
            'dean_approved': {label: 'Dean Approved', color: 'green'},
            'dean_rejected': {label: 'Dean Rejected', color: 'red'},
            'registrar_finalized': {label: 'Finalized', color: 'purple'},
            'released': {label: 'Released', color: 'green'},
        };

        const statusInfo = statusMap[status] || {label: status, color: ''};
        const badge = row.querySelector('.grade-status-badge');
        if (badge) {
            badge.className = `badge ${statusInfo.color} grade-status-badge`;
            badge.textContent = statusInfo.label;
        }

        // Update row attributes
        if (gradeId) {
            row.dataset.gradeId = gradeId;
        }

        // Update edit/submit capabilities
        const canEdit = ['none', 'draft', 'dean_rejected'].includes(status);
        const canSubmit = gradeId && ['draft', 'dean_rejected'].includes(status);
        
        row.dataset.canEdit = canEdit ? '1' : '0';
        row.dataset.canSubmit = canSubmit ? '1' : '0';

        // Update input readonly state
        const gradeInput = row.querySelector('.grade-value-input');
        const remarksInput = row.querySelector('.grade-remarks-input');
        if (gradeInput) gradeInput.readOnly = !canEdit;
        if (remarksInput) remarksInput.readOnly = !canEdit;

        // Update buttons
        updateRowButtons(row, canEdit, canSubmit);
    }

    // Update row buttons based on permissions
    function updateRowButtons(row, canEdit, canSubmit) {
        const actionsCell = row.querySelector('td:last-child');
        if (!actionsCell) return;

        let buttonsHtml = '';
        
        if (canEdit) {
            buttonsHtml += `
                <button class="btn btn-save-draft sm secondary" style="margin-right: 4px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Save
                </button>
            `;
        }
        
        if (canSubmit) {
            buttonsHtml += `
                <button class="btn btn-submit-grade sm success">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Submit
                </button>
            `;
        }

        actionsCell.innerHTML = buttonsHtml || '<span class="text-xs text-muted">No actions</span>';
    }

    // Save draft grade
    async function saveDraftGrade(row) {
        const enrollmentId = row.dataset.enrollmentId;
        const gradeValue = row.querySelector('.grade-value-input').value.trim();
        const remarks = row.querySelector('.grade-remarks-input').value.trim();

        const saveBtn = row.querySelector('.btn-save-draft');
        if (saveBtn) {
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span>Saving...</span>';
        }

        try {
            const response = await fetch('/api/tenant/grades/draft', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    enrollment_id: parseInt(enrollmentId),
                    grade_value: gradeValue || null,
                    submitted_remarks: remarks || null,
                }),
            });

            const data = await response.json();

            if (response.ok) {
                showAlert('Grade draft saved successfully.', 'success');
                updateRowStatus(row, 'draft', data.data?.id);
            } else {
                showAlert(data.message || 'Failed to save grade draft.', 'error');
            }
        } catch (error) {
            console.error('Save draft error:', error);
            showAlert('An error occurred while saving the grade draft.', 'error');
        } finally {
            if (saveBtn && row.dataset.canEdit === '1') {
                saveBtn.disabled = false;
                saveBtn.innerHTML = `
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                    </svg>
                    Save
                `;
            }
        }
    }

    // Submit grade
    async function submitGrade(row) {
        const gradeId = row.dataset.gradeId;
        const gradeValue = row.querySelector('.grade-value-input').value.trim();

        if (!gradeId) {
            showAlert('Please save the grade as draft first.', 'warning');
            return;
        }

        if (!gradeValue) {
            showAlert('Please enter a grade value before submitting.', 'warning');
            return;
        }

        if (!confirm('Are you sure you want to submit this grade? It will be sent for dean review.')) {
            return;
        }

        const submitBtn = row.querySelector('.btn-submit-grade');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span>Submitting...</span>';
        }

        try {
            const response = await fetch(`/api/tenant/grades/${gradeId}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();

            if (response.ok) {
                showAlert('Grade submitted successfully for dean review.', 'success');
                updateRowStatus(row, 'submitted', gradeId);
            } else {
                showAlert(data.message || 'Failed to submit grade.', 'error');
            }
        } catch (error) {
            console.error('Submit grade error:', error);
            showAlert('An error occurred while submitting the grade.', 'error');
        } finally {
            if (submitBtn && row.dataset.canSubmit === '1') {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Submit
                `;
            }
        }
    }

    // Event delegation for grade actions (works with dynamically loaded content)
    document.addEventListener('DOMContentLoaded', () => {
        document.addEventListener('click', (e) => {
            const saveBtn = e.target.closest('.btn-save-draft');
            const submitBtn = e.target.closest('.btn-submit-grade');

            if (saveBtn) {
                e.preventDefault();
                const row = saveBtn.closest('tr');
                if (row) saveDraftGrade(row);
            } else if (submitBtn) {
                e.preventDefault();
                const row = submitBtn.closest('tr');
                if (row) submitGrade(row);
            }
        });
    });
</script>
@endpush

