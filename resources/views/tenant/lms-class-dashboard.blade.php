@extends('layouts.app')

@section('title', 'Class Dashboard - LMS')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'lms', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body lms-class-dashboard">
            <div class="lms-dash-breadcrumb">
                <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                <span class="lms-bread-sep">›</span>
                <a href="{{ url('/tenant/lms') }}">LMS</a>
                <span class="lms-bread-sep">›</span>
                <span>{{ $classGroup->name ?? 'Class' }}</span>
                <span class="lms-bread-sep">›</span>
                <span>{{ $subject->code ?? 'SUBJ' }}</span>
            </div>

            <div class="mdl-course-head">
                <div class="mdl-course-head-main">
                    <div class="mdl-course-kicker">{{ $subject->code ?? 'SUBJ' }}</div>
                    <h1 class="mdl-course-title">{{ $subject->title ?? 'Course' }}</h1>
                    <div class="mdl-course-subtitle">{{ $classGroup->name ?? 'Class' }}</div>
                    @if (($courseTeachers ?? collect())->isNotEmpty())
                        <div class="mdl-course-teacher">
                            {{ ($courseTeachers->count() === 1) ? 'Teacher' : 'Teachers' }}:
                            <span class="mdl-course-teacher-names">{{ $courseTeachers->pluck('full_name')->filter()->join(', ') }}</span>
                        </div>
                    @endif
                </div>
                <div class="mdl-course-head-actions">
                    @php
                        $lessonResourceCount = collect($lessons ?? [])->sum(fn($ls) => collect($ls->modules ?? [])->count());
                        $totalResources = ($modules ?? collect())->count() + $lessonResourceCount;
                        $studentCount = (int) (($enrolledStudents ?? collect())->count());
                    @endphp
                    <span class="mdl-pill">{{ $studentCount }} students</span>
                    <span class="mdl-pill">{{ $totalResources }} resources</span>
                    <button type="button" class="btn ghost sm" data-fab-target="modal-edit-course">Edit Course</button>
                    <div class="mdl-activity-dropdown" data-activity-dropdown>
                        <button type="button" class="btn primary sm mdl-activity-trigger" data-activity-trigger>
                            Add Activity
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="mdl-activity-menu" data-activity-menu>
                            <button type="button" data-fab-target="modal-create-lesson">Section</button>
                            <button type="button" data-fab-target="modal-upload">Resource</button>
                            <button type="button" data-fab-target="modal-create-quiz">Quiz</button>
                            <button type="button" data-fab-target="modal-create-assignment">Assignment</button>
                        </div>
                    </div>
                </div>
            </div>

            @if (session('status'))
                <div class="lms-dash-alert lms-dash-alert-success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="lms-dash-alert lms-dash-alert-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @php $modulesList = ($modules ?? collect())->values(); @endphp
            <div class="mdl-layout">
                <aside class="mdl-side">
                    <div class="mdl-block">
                        <div class="mdl-block-title">Course index</div>
                        <div class="mdl-nav">
                            <a class="mdl-nav-item active" href="#section-overview">Overview</a>
                            <a class="mdl-nav-item" href="#section-announcements">Announcements</a>
                            @foreach (($lessons ?? collect()) as $lesson)
                                <a class="mdl-nav-item" href="#lesson-{{ $lesson->id }}">{{ Str::limit($lesson->title, 40) }}</a>
                            @endforeach
                            <a class="mdl-nav-item" href="#section-resources">Resources</a>
                            <button type="button" class="mdl-nav-item" data-fab-target="modal-view-students">Students</button>
                            <a class="mdl-nav-item" href="{{ url('/tenant/lms/classes/'.$classGroup->id.'/'.$subject->id.'/gradebook') }}">Grades</a>
                        </div>
                    </div>
                    <div class="mdl-block">
                        <div class="mdl-block-title">Course</div>
                        <div class="mdl-side-meta">
                            <div><span class="mdl-side-label">Class</span> <span class="mdl-side-value">{{ $classGroup->name ?? '—' }}</span></div>
                            <div><span class="mdl-side-label">Subject</span> <span class="mdl-side-value">{{ $subject->code ?? '—' }}</span></div>
                            <div><span class="mdl-side-label">Resources</span> <span class="mdl-side-value">{{ $modulesList->count() }}</span></div>
                        </div>
                    </div>

                    <div class="mdl-block">
                        <div class="mdl-block-title">Enrolled Students</div>
                        <div class="mdl-students-card">
                            <div class="mdl-students-count">{{ ($enrolledStudents ?? collect())->count() }} enrolled</div>
                            <div class="mdl-students-actions">
                                <button type="button" data-fab-target="modal-view-students">View</button>
                                <button type="button" data-fab-target="modal-message-students">Message</button>
                                <a href="{{ url('/tenant/lms/classes/'.$classGroup->id.'/'.$subject->id.'/gradebook') }}">Attendance</a>
                            </div>
                        </div>
                    </div>

                    <div class="mdl-block">
                        <div class="mdl-block-title">Analytics</div>
                        <a href="{{ url('/tenant/lms/classes/'.$classGroup->id.'/'.$subject->id.'/gradebook') }}" class="mdl-nav-item" style="display: flex; align-items: center; gap: 12px; padding: 16px 20px; text-decoration: none; color: #1e293b; transition: all 0.2s; border-top: 1px solid #f1f5f9;">
                            <div style="width: 36px; height: 36px; border-radius: 10px; background: #f0fdf4; color: #10B981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-size: 0.9rem; font-weight: 700; color: #0f172a;">Quiz Gradebook</div>
                                <div style="font-size: 0.75rem; color: #94a3b8;">View all student scores</div>
                            </div>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </div>
                </aside>

                <section class="mdl-main">
                    <div class="mdl-section" id="section-overview">
                        <button class="mdl-section-head" type="button" data-accordion-btn>
                            <span>Overview</span>
                            <svg class="mdl-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="mdl-section-body" data-accordion-panel>
                            <div class="mdl-info">
                                <div class="mdl-info-title">Teacher tools</div>
                                <div class="mdl-info-text">Upload files under “Add resource”. Students will see them in their course page.</div>
                            </div>

                            <div style="margin-top: 12px;">
                                @if (!empty($classroomMeeting?->meet_link))
                                    <div class="mdl-info" style="background: #eef2ff;">
                                        <div class="mdl-info-title">Classroom meeting</div>
                                        <div class="mdl-info-text" style="font-weight: 700; color: #1e293b;">
                                            {{ $classroomMeeting->title }}
                                        </div>
                                        @php $meetTzDash = config('app.timezone'); @endphp
                                        @if ($classroomMeeting->scheduled_start || $classroomMeeting->scheduled_end)
                                            <div style="margin-top: 12px; padding: 10px 12px; background: rgba(255,255,255,0.75); border: 1px solid #c7d2fe; border-radius: 10px;">
                                                <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #4338ca; margin-bottom: 8px;">When the meet runs</div>
                                                @if ($classroomMeeting->scheduled_start)
                                                    <div style="font-size: 0.85rem; color: #0f172a; margin-bottom: 4px;">
                                                        <span style="color:#64748b; font-weight: 700;">Opens</span>
                                                        {{ $classroomMeeting->scheduled_start->copy()->timezone($meetTzDash)->format('l, M j, Y \a\t g:i A') }}
                                                    </div>
                                                @endif
                                                @if ($classroomMeeting->scheduled_end)
                                                    <div style="font-size: 0.85rem; color: #0f172a;">
                                                        <span style="color:#64748b; font-weight: 700;">Ends</span>
                                                        {{ $classroomMeeting->scheduled_end->copy()->timezone($meetTzDash)->format('l, M j, Y \a\t g:i A') }}
                                                    </div>
                                                @endif
                                                @if ($classroomMeeting->restrictsLateEntry() && ($dl = $classroomMeeting->studentJoinDeadline()))
                                                    <div style="font-size: 0.8rem; color: #b45309; margin-top: 8px; padding-top: 8px; border-top: 1px dashed #fcd34d;">
                                                        <span style="font-weight: 800;">Student join closes:</span>
                                                        {{ $dl->copy()->timezone($meetTzDash)->format('l, M j, Y \a\t g:i A') }}
                                                        <span style="color:#64748b;"> ({{ (int) $classroomMeeting->late_entry_minutes }} min after start, or at class end if sooner)</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div style="margin-top: 10px; font-size: 0.8rem; color: #64748b;">No start/end time on file. Students only see the join link unless you recreate the meeting with Google Calendar (includes schedule).</div>
                                        @endif
                                        <div style="margin-top: 12px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                                            @if ($classroomMeeting->hasScheduledMeetEnded())
                                                <span class="btn sm primary" style="background:#e2e8f0; color:#64748b; border:none; cursor:not-allowed; pointer-events:none;">
                                                    Meeting ended
                                                </span>
                                                <div style="width:100%; font-size: 0.8rem; color: #64748b; line-height: 1.45;">The scheduled end time has passed. This class no longer shows an active Meet link for teachers or students.</div>
                                            @else
                                                <a href="{{ $classroomMeeting->teacherStartUrl() }}" target="_blank" rel="noopener" class="btn sm primary" style="background:#4F46E5; border:none;">
                                                    Start Google Meet
                                                </a>
                                            @endif
                                        </div>
                                        @if (! $classroomMeeting->hasScheduledMeetEnded())
                                        <div style="margin-top: 12px; padding: 10px 12px; background: rgba(255,255,255,0.9); border: 1px solid #c7d2fe; border-radius: 10px; font-size: 0.78rem; color: #475569; line-height: 1.5;">
                                            <strong style="color:#1e293b;">Same behavior as the gmeet demo</strong><br>
                                            The primary button opens <strong>Google Meet</strong> directly (like that app’s join link). If Meet shows <strong>Ask to join</strong>, switch the Gmail in the Meet tab (top-right) to <strong>{{ $googleAccountEmail ?? 'the account you connected' }}</strong>. Workspace knock/lobby rules are admin-controlled.
                                        </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="mdl-info" style="background:#f8fafc;">
                                        <div class="mdl-info-title">Classroom meeting</div>
                                        <div class="mdl-info-text">Use “Create classroom” to schedule a Google Meet for this class.</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mdl-section" id="section-announcements">
                        <button class="mdl-section-head" type="button" data-accordion-btn>
                            <span>Announcements</span>
                            <svg class="mdl-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="mdl-section-body" data-accordion-panel>
                            <div class="mdl-info">
                                <div class="mdl-info-title">No announcements yet</div>
                                <div class="mdl-info-text">Post weekly reminders and deadlines here for your students.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mdl-section" id="section-resources">
                        <button class="mdl-section-head" type="button" data-accordion-btn>
                            <span>Resources</span>
                            <svg class="mdl-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="mdl-section-body" data-accordion-panel>
                            @if ($modulesList->isEmpty())
                                <div class="lms-dash-empty" style="padding:18px;">
                                    <p style="margin:0;">No resources yet. Upload your first file to start building the course.</p>
                                </div>
                            @else
                                <div class="mdl-resources">
                                    @foreach ($modulesList as $m)
                                        @php
                                            $resourceViewUrl = ($m->type === 'file') ? url('/tenant/lms/modules/' . $m->id . '/view') : null;
                                            $resourceLinkUrl = null;
                                            if ($m->type === 'link') {
                                                $rawLink = trim((string) ($m->content ?? ''));
                                                if ($rawLink !== '') {
                                                    $resourceLinkUrl = preg_match('#^[a-z][a-z0-9+\-.]*://#i', $rawLink) ? $rawLink : ('https://' . ltrim($rawLink, '/'));
                                                }
                                            }
                                        @endphp
                                        <div class="mdl-resource">
                                            <div class="mdl-resource-icon" aria-hidden="true">
                                                @if($m->type === 'link')
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                                @elseif($m->type === 'doc')
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                                @elseif(str_contains($m->mime_type ?? '', 'video'))
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                                @elseif(str_contains($m->mime_type ?? '', 'image'))
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                @elseif(str_contains($m->mime_type ?? '', 'pdf'))
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                                @else
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                @endif
                                            </div>
                                            <div class="mdl-resource-main">
                                                <div class="mdl-resource-title">{{ $m->title }}</div>
                                                @if ($m->description)
                                                    <div class="mdl-resource-desc">{{ Str::limit($m->description, 120) }}</div>
                                                @endif
                                                <div class="mdl-resource-meta">
                                                    <span class="mdl-tag">{{ $m->created_at?->format('M d, Y') }}</span>
                                                    @if ($m->uploader?->full_name)
                                                        <span class="mdl-tag">By {{ $m->uploader->full_name }}</span>
                                                    @endif
                                                    @if ($m->type === 'file' && $m->mime_type)
                                                        <span class="mdl-tag">{{ $m->mime_type }}</span>
                                                    @elseif($m->type === 'link')
                                                        <span class="mdl-tag">External Link</span>
                                                    @elseif($m->type === 'doc')
                                                        <span class="mdl-tag">Document</span>
                                                    @endif
                                                </div>

                                                @if($m->type === 'file')
                                                    @if(str_contains($m->mime_type ?? '', 'image'))
                                                        <div class="lms-resource-preview image">
                                                            <img src="{{ $resourceViewUrl }}" alt="{{ $m->title }}" loading="lazy">
                                                        </div>
                                                    @elseif(str_contains($m->mime_type ?? '', 'video'))
                                                        <div class="lms-resource-preview video">
                                                            <video controls preload="metadata">
                                                                <source src="{{ $resourceViewUrl }}" type="{{ $m->mime_type }}">
                                                            </video>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="mdl-resource-actions">
                                                @if($m->type === 'link')
                                                    @if($resourceLinkUrl)
                                                        <a href="{{ $resourceLinkUrl }}" target="_blank" rel="noopener noreferrer" class="btn sm ghost">Visit Link</a>
                                                    @else
                                                        <span class="btn sm ghost" style="opacity:.65; pointer-events:none;">Invalid Link</span>
                                                    @endif
                                                @elseif($m->type === 'doc')
                                                    <button type="button" class="btn sm ghost" data-view-doc="{{ $m->id }}" data-doc-title="{{ $m->title }}" data-doc-content="{{ $m->content }}">View Doc</button>
                                                @elseif(str_contains($m->mime_type ?? '', 'pdf'))
                                                    <button type="button" class="btn sm ghost" data-view-pdf="{{ $resourceViewUrl }}">View Book</button>
                                                @else
                                                    <a href="{{ url('/tenant/lms/modules/' . $m->id . '/download') }}" class="btn sm ghost">Download</a>
                                                @endif
                                                @include('tenant.partials.lms-module-teacher-actions', ['m' => $m])
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mdl-add-section-wrap">
                        <button type="button" class="btn ghost sm" data-fab-target="modal-create-lesson">+ Add Section</button>
                    </div>
                    <div id="lms-section-reorder-bar" class="lms-section-reorder-bar" style="display:none;">
                        <div class="lms-section-reorder-bar__text">Sections order changed</div>
                        <button type="button" id="lms-apply-section-order" class="btn primary sm">Apply order</button>
                    </div>

                    <div id="lms-lessons-reorder-container" class="lms-lessons-reorder-container">
                    @foreach (($lessons ?? collect()) as $lesson)
                    <div class="mdl-section lms-section--draggable" id="lesson-{{ $lesson->id }}" data-draggable-lesson="1" data-lesson-id="{{ $lesson->id }}">
                        <div class="mdl-section-head-bar">
                            <button class="mdl-section-head" type="button" data-accordion-btn>
                                <span class="mdl-section-head-title">{{ $lesson->title }}</span>
                                <svg class="mdl-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            @include('tenant.partials.lms-lesson-teacher-actions', ['lesson' => $lesson])
                        </div>
                        <div class="mdl-section-body" data-accordion-panel>
                            @if (($lesson->modules ?? collect())->isEmpty())
                                <div class="lms-dash-empty" style="padding:18px;">
                                    <p style="margin:0;">No resources in this section yet.</p>
                                </div>
                            @else
                                <div class="mdl-resources">
                                    @foreach ($lesson->modules as $m)
                                        @php
                                            $resourceViewUrl = ($m->type === 'file') ? url('/tenant/lms/modules/' . $m->id . '/view') : null;
                                            $resourceLinkUrl = null;
                                            if ($m->type === 'link') {
                                                $rawLink = trim((string) ($m->content ?? ''));
                                                if ($rawLink !== '') {
                                                    $resourceLinkUrl = preg_match('#^[a-z][a-z0-9+\-.]*://#i', $rawLink) ? $rawLink : ('https://' . ltrim($rawLink, '/'));
                                                }
                                            }
                                        @endphp
                                        <div class="mdl-resource">
                                            <div class="mdl-resource-icon" aria-hidden="true">
                                                @if($m->type === 'link')
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                                @elseif($m->type === 'doc')
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                                @elseif(str_contains($m->mime_type ?? '', 'video'))
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                                @elseif(str_contains($m->mime_type ?? '', 'image'))
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                                @elseif(str_contains($m->mime_type ?? '', 'pdf'))
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                                                @else
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                                @endif
                                            </div>
                                            <div class="mdl-resource-main">
                                                <div class="mdl-resource-title">{{ $m->title }}</div>
                                                @if ($m->description)
                                                    <div class="mdl-resource-desc">{{ Str::limit($m->description, 120) }}</div>
                                                @endif
                                                <div class="mdl-resource-meta">
                                                    <span class="mdl-tag">{{ $m->created_at?->format('M d, Y') }}</span>
                                                    @if ($m->uploader?->full_name)
                                                        <span class="mdl-tag">By {{ $m->uploader->full_name }}</span>
                                                    @endif
                                                    @if ($m->type === 'file' && $m->mime_type)
                                                        <span class="mdl-tag">{{ $m->mime_type }}</span>
                                                    @elseif($m->type === 'link')
                                                        <span class="mdl-tag">External Link</span>
                                                    @elseif($m->type === 'doc')
                                                        <span class="mdl-tag">Document</span>
                                                    @endif
                                                </div>

                                                @if($m->type === 'file')
                                                    @if(str_contains($m->mime_type ?? '', 'image'))
                                                        <div class="lms-resource-preview image">
                                                            <img src="{{ $resourceViewUrl }}" alt="{{ $m->title }}" loading="lazy">
                                                        </div>
                                                    @elseif(str_contains($m->mime_type ?? '', 'video'))
                                                        <div class="lms-resource-preview video">
                                                            <video controls preload="metadata">
                                                                <source src="{{ $resourceViewUrl }}" type="{{ $m->mime_type }}">
                                                            </video>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="mdl-resource-actions">
                                                @if($m->type === 'link')
                                                    @if($resourceLinkUrl)
                                                        <a href="{{ $resourceLinkUrl }}" target="_blank" rel="noopener noreferrer" class="btn sm ghost">Visit Link</a>
                                                    @else
                                                        <span class="btn sm ghost" style="opacity:.65; pointer-events:none;">Invalid Link</span>
                                                    @endif
                                                @elseif($m->type === 'doc')
                                                    <button type="button" class="btn sm ghost" data-view-doc="{{ $m->id }}" data-doc-title="{{ $m->title }}" data-doc-content="{{ $m->content }}">View Doc</button>
                                                @elseif(str_contains($m->mime_type ?? '', 'pdf'))
                                                    <button type="button" class="btn sm ghost" data-view-pdf="{{ $resourceViewUrl }}">View Book</button>
                                                @else
                                                    <a href="{{ url('/tenant/lms/modules/' . $m->id . '/download') }}" class="btn sm ghost">Download</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if (isset($lesson->quizzes) && $lesson->quizzes->isNotEmpty())
                                <div class="lms-quiz-list" style="margin-top: 12px; border-top: 1px solid #eef2f7; padding-top: 12px;">
                                    @foreach ($lesson->quizzes as $quiz)
                                        <div class="mdl-resource quiz-type" style="border-left: 4px solid #10B981;">
                                            <div class="mdl-resource-icon" style="background: #ecfdf5; color: #10B981;">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                            </div>
                                            <div class="mdl-resource-main">
                                                <div class="mdl-resource-title">{{ $quiz->title }}</div>
                                                <div class="mdl-resource-meta">
                                                    <span class="mdl-tag">{{ $quiz->questions_count }} Qs</span>
                                                    @if($quiz->time_limit_minutes)
                                                        <span class="mdl-tag">{{ $quiz->time_limit_minutes }} mins</span>
                                                    @endif
                                                    @if($quiz->is_published)
                                                        <span class="mdl-tag" style="background: #dcfce7; color: #166534;">Published</span>
                                                    @else
                                                        <span class="mdl-tag" style="background: #fef9c3; color: #854d0e;">Draft</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mdl-resource-actions">
                                                <a href="{{ url('/tenant/lms/quizzes/'.$quiz->id.'/results') }}" class="btn sm ghost">Results</a>
                                                <button type="button" class="btn sm primary" style="background:#10B981; border:none;" data-add-question="{{ $quiz->id }}" data-quiz-title="{{ $quiz->title }}">Add Q</button>
                                                @if(!$quiz->is_published)
                                                    <form method="POST" action="{{ url('/tenant/lms/quizzes/'.$quiz->id.'/publish') }}" style="display:inline;">
                                                        @csrf
                                                        <button type="submit" class="btn sm primary">Publish</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="lms-section-add-resource-row">
                                <button
                                    type="button"
                                    class="lms-section-add-resource-btn"
                                    data-fab-target="modal-upload"
                                    data-upload-lesson-id="{{ $lesson->id }}"
                                    title="Add resource to {{ $lesson->title }}"
                                    aria-label="Add resource to {{ $lesson->title }}"
                                >
                                    <span class="lms-section-add-resource-glyph" aria-hidden="true">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="10" fill="#4C97FF" fill-opacity="0.18"/>
                                            <path d="M12 8v8M8 12h8" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </span>
                                    <span class="lms-section-add-resource-sr">Add resource to this section</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    </div>

                </section>
                <aside class="mdl-right">
                    <div class="mdl-block">
                        <div class="mdl-block-title">Course stats</div>
                        <div class="mdl-side-meta">
                            <div><span class="mdl-side-label">Students</span> <span class="mdl-side-value">{{ ($enrolledStudents ?? collect())->count() }}</span></div>
                            <div><span class="mdl-side-label">Sections</span> <span class="mdl-side-value">{{ ($lessons ?? collect())->count() }}</span></div>
                            <div><span class="mdl-side-label">Resources</span> <span class="mdl-side-value">{{ $totalResources ?? 0 }}</span></div>
                        </div>
                    </div>
                    <div class="mdl-block">
                        <div class="mdl-block-title">Upcoming deadlines</div>
                        <div class="mdl-side-meta">
                            <div><span class="mdl-side-value">No upcoming deadlines</span></div>
                        </div>
                    </div>
                    <div class="mdl-block">
                        <div class="mdl-block-title">Quick actions</div>
                        <div class="mdl-nav">
                            <button class="mdl-nav-item" type="button" data-fab-target="modal-create-quiz">Create quiz</button>
                            <button class="mdl-nav-item" type="button" data-fab-target="modal-upload">Upload resource</button>
                            <button class="mdl-nav-item" type="button" data-fab-target="modal-create-classroom">Schedule meet</button>
                        </div>
                    </div>
                </aside>
            </div>

            <!-- Modals -->
            <div id="modal-confirm-delete" class="lms-modal lms-modal--confirm" role="dialog" aria-modal="true" aria-labelledby="lms-confirm-dialog-title">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content lms-modal-content--confirm">
                    <div class="lms-modal-header">
                        <h2 id="lms-confirm-dialog-title" class="lms-modal-title">Confirm</h2>
                        <button type="button" class="lms-modal-close" data-modal-close aria-label="Close">&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <p id="lms-confirm-dialog-message" class="lms-confirm-message">Are you sure?</p>
                        <div class="lms-confirm-actions">
                            <button type="button" class="lms-confirm-cancel-btn" data-modal-close>Cancel</button>
                            <button type="button" class="lms-confirm-delete-btn" id="lms-confirm-dialog-confirm">Delete</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modal-create-lesson" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Add section</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <form method="post" action="{{ url('/tenant/lms/classes/' . $classGroup->id . '/' . $subject->id . '/lessons') }}" class="lms-upload-form">
                            @csrf
                            <div class="lms-form-group">
                                <label>Section title</label>
                                <input name="title" type="text" value="" required maxlength="140" placeholder="e.g. Module 1: Introduction or Week 1 — Orientation">
                            </div>
                            <button class="lms-btn-primary" type="submit">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                                Create section
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="modal-manage-section" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content" style="max-width: 520px;">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Section &amp; resources</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <p class="lms-input-hint" style="margin:0 0 14px; line-height:1.45;">Rename the section or edit/delete uploads that belong to it. Student-facing cards stay simple; only this panel has the extra controls.</p>
                        <form id="form-edit-lesson" method="post" action="" class="lms-upload-form">
                            @csrf
                            @method('PATCH')
                            <div class="lms-form-group">
                                <label>Section title</label>
                                <input id="edit-lesson-title" name="title" type="text" required maxlength="140" class="lms-input">
                            </div>
                            <button class="lms-btn-primary" type="submit" style="width:100%; margin-top:4px;">
                                <span class="lms-btn-text">Save section title</span>
                            </button>
                        </form>
                        <div style="margin: 20px 0 12px; padding-top: 16px; border-top: 1px solid #eef2f7;">
                            <div style="font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b;">Uploads in this section</div>
                        </div>
                        <div id="manage-section-resources-body" class="lms-manage-section-resource-list"></div>
                        <p id="manage-section-resources-empty" class="lms-input-hint" style="display:none; margin:8px 0 0;">No uploads in this section yet. Add files or links using the form below the section on the course page.</p>
                    </div>
                </div>
            </div>

            <div id="manage-section-module-rows-store" class="lms-hidden-template-store" aria-hidden="true">
                @foreach (($lessons ?? collect()) as $lesson)
                    @foreach (($lesson->modules ?? collect()) as $m)
                        <div class="lms-manage-mod-row" data-lesson-id="{{ $lesson->id }}">
                            <div class="lms-manage-mod-main">
                                <div class="lms-manage-mod-title">{{ $m->title }}</div>
                                @if ($m->description)
                                    <div class="lms-manage-mod-desc">{{ Str::limit($m->description, 80) }}</div>
                                @endif
                            </div>
                            <div class="lms-manage-mod-actions">
                                <button
                                    type="button"
                                    class="btn sm ghost"
                                    data-edit-module
                                    data-module-id="{{ $m->id }}"
                                    data-module-title="{{ e($m->title) }}"
                                    data-module-description="{{ e($m->description ?? '') }}"
                                    data-module-type="{{ e($m->type) }}"
                                    data-module-content="{{ e($m->content ?? '') }}"
                                >Edit</button>
                                <form
                                    method="post"
                                    action="{{ url('/tenant/lms/modules/' . $m->id) }}"
                                    class="lms-manage-mod-delete-form"
                                    data-confirm-delete
                                    data-confirm-title="Delete this resource?"
                                    data-confirm-message="This cannot be undone."
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn sm ghost" style="color:#b91c1c;">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <div id="modal-upload" class="lms-modal lms-modal--moodle">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Add Resource</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <p class="lms-dash-upload-hint">Add files, links, or create documents for students.</p>
                        <div class="lms-upload-shell">
                            <div class="lms-upload-shell__title">Quick Add</div>
                            <div class="lms-upload-shell__sub">Choose what you want to add to this section.</div>
                        </div>
                        <div class="lms-upload-quick-actions">
                            <button type="button" class="lms-upload-quick-action is-active" aria-current="true">
                                Upload resource
                            </button>
                            <button type="button" class="lms-upload-quick-action" data-upload-action-target="modal-create-quiz">
                                Create quiz
                            </button>
                            <button type="button" class="lms-upload-quick-action" data-upload-action-target="modal-create-classroom">
                                Schedule meet
                            </button>
                        </div>
                        <form id="lms-modal-upload-form" method="post" action="{{ url('/tenant/lms/classes/' . $classGroup->id . '/' . $subject->id . '/modules') }}" enctype="multipart/form-data" class="lms-upload-form">
                            @csrf
                            <div class="lms-form-group">
                                <label>Resource Type</label>
                                <div class="lms-type-selector">
                                    <label class="lms-type-option">
                                        <input type="radio" name="type" value="file" checked data-type-trigger>
                                        <div class="lms-type-card">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                                            <span>File / Media</span>
                                        </div>
                                    </label>
                                    <label class="lms-type-option">
                                        <input type="radio" name="type" value="link" data-type-trigger>
                                        <div class="lms-type-card">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                                            <span>Link</span>
                                        </div>
                                    </label>
                                    <label class="lms-type-option">
                                        <input type="radio" name="type" value="doc" data-type-trigger>
                                        <div class="lms-type-card">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                            <span>Document</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            @if (($lessons ?? collect())->isNotEmpty())
                            <div class="lms-form-group">
                                <label>Section <span class="lms-optional">(optional)</span></label>
                                <select id="lms-upload-lesson-select" name="lesson_id" class="lms-input">
                                    <option value="">— Ungrouped —</option>
                                    @foreach ($lessons as $lesson)
                                        <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="lms-form-group">
                                <label>Title</label>
                                <input name="title" type="text" value="{{ old('title') }}" required maxlength="140" placeholder="e.g. Chapter 1 PDF or Video Tutorial" class="lms-input">
                            </div>

                            <div class="lms-form-group">
                                <label>Description <span class="lms-optional">(optional)</span></label>
                                <textarea name="description" rows="2" maxlength="2000" placeholder="Short notes for students..." class="lms-input"></textarea>
                            </div>

                            <div id="type-field-file" class="lms-type-field">
                                <div class="lms-form-group">
                                    <label>Upload File (PDF, Video, Image, etc.)</label>
                                    <input name="file" type="file" class="lms-file-input">
                                    <p class="lms-input-hint">Max 100MB for media files.</p>
                                </div>
                            </div>

                            <div id="type-field-link" class="lms-type-field" style="display:none;">
                                <div class="lms-form-group">
                                    <label>URL / Link</label>
                                    <input name="content" type="url" placeholder="https://example.com/resource" class="lms-input">
                                </div>
                            </div>

                            <div id="type-field-doc" class="lms-type-field" style="display:none;">
                                <div class="lms-form-group">
                                    <label>Document Content</label>
                                    <textarea name="content" rows="6" placeholder="Write your document content here..." class="lms-input"></textarea>
                                </div>
                            </div>

                            <button class="lms-btn-primary" type="submit" style="width:100%; margin-top:12px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                                <span class="lms-btn-text">Add Resource</span>
                            </button>

                            <div class="lms-upload-progress-container" style="display:none; margin-top:20px;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.8rem; font-weight:700; color:#4C97FF;">
                                    <span>Uploading...</span>
                                    <span class="lms-upload-percentage">0%</span>
                                </div>
                                <div class="lms-progress-bg">
                                    <div class="lms-upload-progress-bar lms-progress-fill"></div>
                                </div>
                                <p style="font-size:0.7rem; color:#94a3b8; margin-top:6px; text-align:center;">Please don't close this window.</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="modal-edit-module" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Edit resource</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <form id="form-edit-module" method="post" action="" enctype="multipart/form-data" class="lms-upload-form">
                            @csrf
                            @method('PATCH')
                            <div class="lms-form-group">
                                <label>Title</label>
                                <input id="edit-module-title" name="title" type="text" required maxlength="140" class="lms-input">
                            </div>
                            <div class="lms-form-group">
                                <label>Description <span class="lms-optional">(optional)</span></label>
                                <textarea id="edit-module-description" name="description" rows="2" maxlength="2000" class="lms-input" placeholder="Short notes for students..."></textarea>
                            </div>
                            <div id="edit-module-field-link" class="lms-edit-module-type-field" style="display:none;">
                                <div class="lms-form-group">
                                    <label>URL / Link</label>
                                    <input id="edit-module-content-link" name="content" type="url" class="lms-input" placeholder="https://..." disabled>
                                </div>
                            </div>
                            <div id="edit-module-field-doc" class="lms-edit-module-type-field" style="display:none;">
                                <div class="lms-form-group">
                                    <label>Document content</label>
                                    <textarea id="edit-module-content-doc" name="content" rows="8" class="lms-input" placeholder="Document text..." disabled></textarea>
                                </div>
                            </div>
                            <div id="edit-module-field-file" class="lms-edit-module-type-field" style="display:none;">
                                <div class="lms-form-group">
                                    <label>Replace file <span class="lms-optional">(optional)</span></label>
                                    <input id="edit-module-file" name="file" type="file" class="lms-file-input" disabled>
                                    <p class="lms-input-hint">Leave empty to keep the current file.</p>
                                </div>
                            </div>
                            <button class="lms-btn-primary" type="submit" style="width:100%; margin-top:12px;">
                                <span class="lms-btn-text">Save changes</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="modal-create-quiz" class="lms-modal lms-modal--moodle">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Create New Quiz</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <form method="post" action="{{ url('/tenant/lms/classes/' . $classGroup->id . '/' . $subject->id . '/quizzes') }}" class="lms-upload-form">
                            @csrf
                            @if (($lessons ?? collect())->isNotEmpty())
                            <div class="lms-form-group">
                                <label>Section <span class="lms-optional">(optional)</span></label>
                                <select id="lms-quiz-lesson-select" name="lesson_id" class="lms-input">
                                    <option value="">— Ungrouped —</option>
                                    @foreach ($lessons as $lesson)
                                        <option value="{{ $lesson->id }}">{{ $lesson->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="lms-form-group">
                                <label>Quiz Title</label>
                                <input name="title" type="text" required maxlength="200" placeholder="e.g. Midterm Quiz" class="lms-input">
                            </div>
                            <div class="lms-form-group">
                                <label>Description</label>
                                <textarea name="description" rows="2" placeholder="Instructions for students..." class="lms-input"></textarea>
                            </div>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                                <div class="lms-form-group">
                                    <label>Time Limit (mins)</label>
                                    <input name="time_limit_minutes" type="number" placeholder="No limit" class="lms-input">
                                </div>
                                <div class="lms-form-group">
                                    <label>Due Date</label>
                                    <input name="due_date" type="datetime-local" class="lms-input">
                                </div>
                            </div>
                            <button class="lms-btn-primary" type="submit" style="width:100%; margin-top:12px;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                Create Quiz
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="modal-create-classroom" class="lms-modal lms-modal--moodle-meet">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content moodle-meet-modal">
                    <div class="lms-modal-header moodle-meet-modal__header">
                        <h2 class="lms-modal-title moodle-meet-modal__title">Create Google Meet classroom</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>

                    <div class="lms-modal-body moodle-meet-modal__body">
                        <div id="classroom-modal-step-google">
                            <div id="google-connect-section" class="moodle-google-banner moodle-google-banner--notice" style="display:none;">
                                <div class="moodle-google-banner__inner">
                                    <div class="moodle-google-banner__icon" aria-hidden="true">
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                        </svg>
                                    </div>
                                    <div class="moodle-google-banner__text">
                                        <div class="moodle-google-banner__heading">Connect Google Calendar</div>
                                        <p class="moodle-google-banner__desc">Connect the Google account you want as Meet <strong>host</strong> (often your school or personal Gmail). Open Meet signed into that same account.</p>
                                        <a id="google-connect-btn" href="{{ url('/tenant/lms/google/auth?class_group_id=' . $classGroup->id . '&subject_id=' . $subject->id) }}" class="moodle-btn moodle-btn--google">
                                            <svg class="moodle-google-icon-btn" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                            Continue with Google
                                        </a>
                                    </div>
                                </div>
                            </div>

                            @php
                                $googleAuthMeetReauthUrl = url('/tenant/lms/google/auth?' . http_build_query([
                                    'class_group_id' => $classGroup->id,
                                    'subject_id' => $subject->id,
                                    'reauth_for_meet' => '1',
                                ]));
                            @endphp

                            <div id="google-connected-section" class="moodle-google-banner moodle-google-banner--linked" style="display:none;">
                                <div class="moodle-google-banner__heading">Calendar linked</div>
                                <p class="moodle-google-banner__sub">Meet host is still your browser Gmail — use the same account when joining.</p>
                                <div id="google-connected-email" class="moodle-google-banner__email">
                                    @if (!empty($googleAccountEmail))
                                        Event owner on file: <strong>{{ $googleAccountEmail }}</strong> — in Meet, use <strong>Switch account</strong> and pick this address to be host.
                                    @else
                                        Complete Google sign-in below; the organizer Gmail will appear here when available.
                                    @endif
                                </div>
                                <a href="{{ $googleAuthMeetReauthUrl }}" class="moodle-btn moodle-btn--secondary-outline js-classroom-google-reauth">
                                    <svg class="moodle-google-icon-btn" width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                                    Reconnect Google
                                </a>
                                <p class="moodle-google-banner__hint">OAuth: choose the account that should own Calendar / Meet, then allow access.</p>
                                <div class="moodle-meet-modal__actions moodle-meet-modal__actions--proceed">
                                    <button type="button" id="classroom-proceed-btn" class="moodle-btn moodle-btn--primary" style="display: none;">
                                        Proceed to meeting details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <form id="form-create-classroom-meeting" class="moodle-meet-form" style="display: none;">
                            <div class="moodle-fitem">
                                <div class="moodle-fitem__label">
                                    <label for="classroom-title-input">Meeting title <abbr class="moodle-req" title="Required">*</abbr></label>
                                </div>
                                <div class="moodle-fitem__felement">
                                    <input id="classroom-title-input" type="text" class="moodle-textinput" placeholder="e.g. Week 1 lecture discussion" required>
                                </div>
                            </div>
                            <div class="moodle-fitem">
                                <div class="moodle-fitem__label">
                                    <label for="classroom-desc-input">Description</label>
                                </div>
                                <div class="moodle-fitem__felement">
                                    <textarea id="classroom-desc-input" class="moodle-textinput" rows="3" placeholder="Optional meeting description…"></textarea>
                                </div>
                            </div>
                            <div class="moodle-datetime-row">
                                <div class="moodle-datetime-col">
                                    <label class="moodle-datetime-col__label" for="classroom-start-input">Start <abbr class="moodle-req" title="Required">*</abbr></label>
                                    <input id="classroom-start-input" type="datetime-local" class="moodle-textinput" required>
                                </div>
                                <div class="moodle-datetime-col">
                                    <label class="moodle-datetime-col__label" for="classroom-end-input">End <abbr class="moodle-req" title="Required">*</abbr></label>
                                    <input id="classroom-end-input" type="datetime-local" class="moodle-textinput" required>
                                </div>
                            </div>
                            <div class="moodle-fitem">
                                <div class="moodle-fitem__label">
                                    <label for="classroom-late-entry-input">Late join window (minutes)</label>
                                </div>
                                <div class="moodle-fitem__felement">
                                    <p class="moodle-fitem__statictext">Students may join from the start time until this many minutes later (or until the scheduled end, whichever comes first). Leave blank to allow joins for the whole class.</p>
                                    <input id="classroom-late-entry-input" type="number" class="moodle-textinput moodle-textinput--narrow" min="1" max="300" step="1" placeholder="e.g. 15">
                                </div>
                            </div>

                            <div id="classroom-error-msg" class="moodle-alert moodle-alert--danger" style="display:none;"></div>
                            <div id="classroom-success-msg" class="moodle-alert moodle-alert--success" style="display:none;"></div>

                            <div class="moodle-meet-modal__actions moodle-meet-modal__actions--two-btns">
                                <button type="button" id="classroom-back-to-google-btn" class="moodle-btn moodle-btn--back moodle-btn--back-inline">
                                    ← Back
                                </button>

                                <button id="create-classroom-btn" type="submit" class="moodle-btn moodle-btn--primary">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 10l5 5-5 5"/><path d="M4 4v7a4 4 0 0 0 4 4h12"/></svg>
                                    <span id="create-classroom-btn-text">Create meeting</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="modal-add-question" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content" style="max-width: 600px;">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Add Question to <span id="add-q-quiz-title"></span></h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <form id="form-add-question" method="post" action="">
                            @csrf
                            <div class="lms-form-group">
                                <label>Question Type</label>
                                <select name="type" class="lms-input" id="q-type-selector">
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="essay">Essay (Manual Grade)</option>
                                </select>
                            </div>
                            <div class="lms-form-group">
                                <label>Question Text</label>
                                <textarea name="question_text" required rows="3" class="lms-input" placeholder="Enter your question here..."></textarea>
                            </div>
                            <div class="lms-form-group">
                                <label>Points</label>
                                <input name="points" type="number" required min="1" value="1" class="lms-input">
                            </div>

                            <div id="mc-options-container">
                                <label style="display:block; margin-bottom:8px; font-weight:700; font-size:0.85rem;">Choices (Mark the correct one)</label>
                                @for($i = 0; $i < 4; $i++)
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                                    <input type="radio" name="correct_choice_index" value="{{ $i }}" {{ $i == 0 ? 'checked' : '' }}>
                                    <input name="choices[{{ $i }}][text]" type="text" placeholder="Choice {{ $i + 1 }}" class="lms-input">
                                </div>
                                @endfor
                            </div>

                            <button class="lms-btn-primary" type="submit" style="width:100%; margin-top:12px; background:#10B981;">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Save Question
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="modal-view-pdf" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content" style="max-width: 900px; height: 90vh;">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">View Book</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body" style="height: calc(100% - 70px); padding: 0;">
                        <iframe id="pdf-viewer-frame" src="" width="100%" height="100%" style="border: none;"></iframe>
                    </div>
                </div>
            </div>
             <div id="modal-view-doc" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title" id="doc-viewer-title">Document View</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body" id="doc-viewer-content" style="max-height: 70vh; overflow-y: auto; line-height: 1.6; color: #334155;">
                        <!-- Content injected via JS -->
                    </div>
                </div>
            </div>

            <div id="modal-view-students" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content" style="max-width: 700px;">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Enrolled Students</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <div style="margin-bottom: 16px;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <p style="margin: 0; font-size: 0.875rem; color: #64748b;">
                                    {{ ($enrolledStudents ?? collect())->count() }} students enrolled in <strong>{{ $subject->code ?? 'Subject' }}</strong> - {{ $classGroup->name ?? 'Class' }}
                                </p>
                            </div>
                        </div>

                        @if(($enrolledStudents ?? collect())->isEmpty())
                            <div class="lms-dash-empty" style="padding: 24px;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin: 0 auto 12px; opacity: 0.3; display: block;">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                </svg>
                                <p style="margin: 0;">No students enrolled in this class yet.</p>
                            </div>
                        @else
                            <div style="max-height: 60vh; overflow-y: auto;">
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    @foreach($enrolledStudents as $student)
                                        <div style="display: flex; align-items: center; gap: 14px; padding: 14px; background: #fff; border: 1px solid #e8eef5; border-radius: 12px; transition: box-shadow 0.15s ease, border-color 0.15s ease;">
                                            <div style="width: 44px; height: 44px; border-radius: 50%; background: linear-gradient(145deg, #6366f1 0%, #8b5cf6 100%); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 700; font-size: 0.9rem; box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);">
                                                {{ strtoupper(substr($student->full_name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <div style="font-weight: 700; font-size: 0.95rem; color: #0f172a;">
                                                    {{ $student->full_name ?? 'Unknown Student' }}
                                                </div>
                                                @if($student->email)
                                                    <div style="font-size: 0.8rem; color: #64748b; margin-top: 2px;">
                                                        {{ $student->email }}
                                                    </div>
                                                @endif
                                                @if($student->studentProfile?->student_no)
                                                    <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 4px;">
                                                        <span style="font-weight: 600;">ID:</span> {{ $student->studentProfile->student_no }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div style="flex-shrink: 0;">
                                                <span style="display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 999px; font-size: 0.7rem; font-weight: 700; background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); color: #1d4ed8; border: 1px solid rgba(59,130,246,0.25);">
                                                    Active
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #eef2f7; display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: #64748b;">
                                <span>Total: {{ ($enrolledStudents ?? collect())->count() }} students</span>
                                <a href="{{ url('/tenant/grades') }}" style="color: #4C97FF; text-decoration: none; font-weight: 600;">
                                    View Grade Management →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div id="modal-create-assignment" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Create Assignment</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <div class="mdl-info">
                            <div class="mdl-info-title">Assignment module</div>
                            <div class="mdl-info-text">Assignment workflow can be connected here next. For now, use Quiz or Resource to publish coursework.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modal-edit-course" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Edit Course</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <div class="mdl-info">
                            <div class="mdl-info-title">{{ $subject->code ?? 'SUBJ' }} · {{ $classGroup->name ?? 'Class' }}</div>
                            <div class="mdl-info-text">Course edit options can be configured here (title, summary, visibility, and layout settings).</div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="modal-message-students" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Message Students</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <div class="mdl-info">
                            <div class="mdl-info-title">Broadcast message</div>
                            <div class="mdl-info-text">Messaging panel can be integrated here to notify all enrolled students about deadlines and updates.</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@endsection

@push('styles')
<style>
/* LMS class dashboard: page chrome from tenant-ui-mock; keep LMS-specific tokens below */
.tenant-ui-mock .page-body.lms-class-dashboard {
    padding: 20px 24px 32px;
}

.lms-topbar {
    height: 56px;
    background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
    border-bottom: 1px solid #e8eef5;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(76,151,255,0.06);
}
.lms-topbar-left, .lms-topbar-center, .lms-topbar-right { display: flex; align-items: center; gap: 12px; }
.lms-topbar-center { flex: 1; max-width: 320px; justify-content: flex-end; margin-left: auto; margin-right: 12px; }
.lms-topbar-menu {
    width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center;
    border: none; background: transparent; color: #4C97FF;
    border-radius: 10px;
    cursor: pointer;
}
.lms-topbar-menu:hover { background: linear-gradient(135deg, #e8f2ff 0%, #dbeafe 100%); }
.lms-topbar-brand { display: flex; align-items: center; gap: 10px; }
.lms-topbar-brand-icon {
    width: 32px; height: 32px;
    background: linear-gradient(145deg, #4C97FF 0%, #6BCFFF 100%);
    color: #fff;
    font-weight: 700; font-size: 0.9rem;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 8px rgba(76,151,255,0.25);
}
.lms-topbar-brand-text { font-weight: 600; font-size: 1rem; }
.brand-edu { color: #4C97FF; }
.brand-admin { color: #334155; margin-left: 1px; }
.lms-topbar-search {
    display: flex; align-items: center; gap: 8px;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px 12px;
    min-width: 140px;
}
.lms-topbar-search input { border: none; background: none; font-size: 0.875rem; color: #64748b; outline: none; width: 100%; }
.lms-search-icon { color: #94a3b8; flex-shrink: 0; }
.lms-topbar-icon {
    width: 40px; height: 40px;
    display: flex; align-items: center; justify-content: center;
    border: none; background: transparent; color: #64748b;
    border-radius: 10px;
    cursor: pointer;
}
.lms-topbar-icon:hover { background: #f1f5f9; color: #334155; }
.lms-topbar-icon-dot { position: relative; }
.lms-topbar-icon-dot::after {
    content: ''; position: absolute; top: 8px; right: 8px;
    width: 8px; height: 8px; border-radius: 50%;
    background: linear-gradient(135deg, #4C97FF, #6BCFFF);
    box-shadow: 0 0 0 2px #fff;
}
.lms-topbar-user { display: flex; align-items: center; gap: 8px; cursor: pointer; }
.lms-topbar-avatar {
    width: 32px; height: 32px;
    border-radius: 10px;
    background: linear-gradient(145deg, #4C97FF 0%, #6BCFFF 100%);
    color: #fff; font-weight: 600; font-size: 0.8rem;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 2px 6px rgba(76,151,255,0.3);
}
.lms-topbar-username { font-size: 0.875rem; font-weight: 500; color: #334155; }
.lms-topbar-chevron { color: #94a3b8; }

.lms-dash-breadcrumb {
    font-size: 0.8rem; color: #64748b; margin-bottom: 16px;
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
}
.lms-dash-breadcrumb a { color: #4C97FF; text-decoration: none; }
.lms-dash-breadcrumb a:hover { text-decoration: underline; }
.lms-bread-sep { color: #cbd5e1; }

.lms-dash-alert {
    display: flex; align-items: center; gap: 10px;
    padding: 12px 16px; border-radius: 10px;
    margin-bottom: 16px; font-size: 0.875rem;
}
.lms-dash-alert svg { flex-shrink: 0; width: 20px; height: 20px; }
.lms-dash-alert-success { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); color: #166534; border: 1px solid #bbf7d0; }
.lms-dash-alert-error { background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); color: #b91c1c; border: 1px solid #fecaca; }

.lms-dash-grid {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 20px;
    align-items: start;
}
@media (min-width: 1100px) {
    .lms-dash-grid { grid-template-columns: 1.2fr 1fr; }
    .lms-dash-card-course { grid-column: 2; grid-row: 1 / 3; align-self: start; }
}
@media (max-width: 900px) {
    .lms-dash-grid { grid-template-columns: 1fr; }
    .lms-dash-card-course { grid-column: auto; grid-row: auto; }
}

.lms-dash-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 4px 16px rgba(76,151,255,0.06);
    border: 1px solid rgba(76,151,255,0.08);
    overflow: hidden;
}
.lms-dash-card-modules .lms-dash-card-head {
    background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
    border-bottom-color: #e8f0fe;
}
.lms-dash-card-upload .lms-dash-card-head {
    background: linear-gradient(180deg, #fffbf5 0%, #fff8ee 100%);
    border-bottom: 1px solid #fef3e2;
}
.lms-dash-card-upload { border-color: rgba(255,183,77,0.15); box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 4px 16px rgba(255,210,91,0.08); }
.lms-dash-card-course {
    background: linear-gradient(180deg, #E0F2FF 0%, #f0f9ff 50%, #ffffff 100%);
    border-color: rgba(76,151,255,0.12);
    box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(76,151,255,0.08);
}
.lms-dash-card-course .lms-dash-card-head {
    background: linear-gradient(180deg, rgba(224,242,255,0.8) 0%, rgba(255,255,255,0.6) 100%);
    border-bottom: 1px solid rgba(76,151,255,0.12);
}
.lms-dash-card-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #f1f5f9;
}
.lms-dash-card-title {
    margin: 0; font-size: 1rem; font-weight: 700; color: #0f172a;
}
.lms-dash-card-head-actions { display: flex; align-items: center; gap: 8px; }
.lms-dash-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 24px; height: 24px;
    padding: 0 8px;
    font-size: 0.75rem; font-weight: 600; color: #2563e0;
    background: linear-gradient(135deg, #e0f2ff 0%, #dbeafe 100%);
    border-radius: 999px;
    border: 1px solid rgba(76,151,255,0.2);
}
.lms-dash-card-more {
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    border: none; background: transparent; color: #94a3b8;
    font-size: 1.2rem; line-height: 1; cursor: pointer;
    border-radius: 8px;
}
.lms-dash-card-more:hover { background: #f1f5f9; color: #64748b; }
.lms-dash-card-body { padding: 16px 20px 20px; }

.lms-dash-empty {
    padding: 24px; text-align: center;
    font-size: 0.875rem; color: #64748b;
}

.lms-module-list { display: flex; flex-direction: column; gap: 12px; }
.lms-module-item {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 14px 16px;
    background: #fff;
    border: 1px solid #e8eef5;
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    transition: box-shadow 0.15s ease, border-color 0.15s ease;
}
.lms-module-item:hover {
    box-shadow: 0 2px 10px rgba(76,151,255,0.1);
    border-color: rgba(76,151,255,0.15);
}
.lms-module-item-icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.lms-module-icon-blue {
    background: linear-gradient(145deg, #4C97FF 0%, #6BCFFF 100%);
    color: #fff;
    box-shadow: 0 2px 8px rgba(76,151,255,0.3);
}
.lms-module-icon-orange {
    background: linear-gradient(145deg, #FFDDC1 0%, #FFD4A2 100%);
    color: #e0782e;
    box-shadow: 0 2px 8px rgba(255,183,77,0.25);
}
.lms-module-item-content { flex: 1; min-width: 0; }
.lms-module-item-time { font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 4px; }
.lms-module-item-title { font-weight: 700; font-size: 0.95rem; color: #0f172a; }
.lms-module-item-desc { font-size: 0.8rem; color: #64748b; margin-top: 4px; line-height: 1.4; }
.lms-module-item-meta { font-size: 0.72rem; color: #94a3b8; margin-top: 8px; display: flex; gap: 8px; flex-wrap: wrap; }
.lms-module-mime { text-transform: uppercase; }
.lms-module-item-dl {
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 10px;
    color: #4C97FF;
    background: linear-gradient(135deg, #e0f2ff 0%, #dbeafe 100%);
    flex-shrink: 0;
    transition: background 0.15s, color 0.15s, box-shadow 0.15s;
}
.lms-module-item-dl:hover {
    background: linear-gradient(145deg, #4C97FF 0%, #3b82f6 100%);
    color: #fff;
    box-shadow: 0 2px 10px rgba(76,151,255,0.35);
}

.lms-dash-upload-hint { font-size: 0.8rem; color: #64748b; margin: 0 0 14px; }
.lms-upload-form { display: flex; flex-direction: column; gap: 14px; }
.lms-form-group label {
    display: block; font-size: 0.8rem; font-weight: 600; color: #334155; margin-bottom: 6px;
}
.lms-form-group input[type="text"],
.lms-form-group textarea {
    width: 100%; padding: 10px 12px;
    border: 1px solid color-mix(in srgb, var(--admin-primary, #4C97FF) 14%, #e2e8f0); border-radius: 10px;
    font-size: 0.875rem; color: #0f172a;
    background: #fff;
}
.lms-form-group input[type="text"]:focus,
.lms-form-group textarea:focus {
    outline: none;
    border-color: var(--admin-primary, #4C97FF);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary, #4C97FF) 22%, transparent);
}
.lms-optional { font-weight: 400; color: #94a3b8; }
.lms-file-input { font-size: 0.8rem; }
.lms-btn-primary {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 12px 20px;
    font-size: 0.875rem; font-weight: 600; color: #fff;
    background: linear-gradient(
        145deg,
        color-mix(in srgb, var(--admin-primary, #4C97FF) 85%, #ffffff 15%) 0%,
        var(--admin-primary, #4C97FF) 50%,
        color-mix(in srgb, var(--admin-primary, #4C97FF) 85%, #000000 15%) 100%
    );
    border: none; border-radius: 10px;
    cursor: pointer;
    box-shadow: 0 2px 10px color-mix(in srgb, var(--admin-primary, #4C97FF) 35%, transparent);
    transition: box-shadow 0.15s, transform 0.15s;
}
.lms-btn-primary:hover {
    box-shadow: 0 4px 16px color-mix(in srgb, var(--admin-primary, #4C97FF) 45%, transparent);
    transform: translateY(-1px);
}

.lms-course-block { margin-bottom: 12px; }
.lms-course-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #4C97FF; margin-bottom: 4px; }
.lms-course-name { font-size: 1rem; font-weight: 700; color: #0f172a; }
.lms-course-meta { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 12px; }
.lms-course-meta-item { font-size: 0.8rem; color: #64748b; }
.lms-course-note { font-size: 0.8rem; color: #64748b; margin: 0; line-height: 1.5; }

/* Moodle-ish layout blocks (shared with student lessons) */
.mdl-course-head {
    background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
    border: 1px solid rgba(76,151,255,0.18);
    border-radius: 16px;
    padding: 14px 16px;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}
.mdl-course-kicker { font-size: 0.78rem; font-weight: 800; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.04em; }
.mdl-course-title { margin: 3px 0 0; font-size: 1.15rem; font-weight: 900; color: #0f172a; }
.mdl-course-subtitle { margin-top: 4px; color: #64748b; font-size: 0.92rem; }
.mdl-course-teacher { margin-top: 6px; font-size: 0.88rem; color: #64748b; line-height: 1.4; }
.mdl-course-teacher-names { font-weight: 800; color: #0f172a; }
.mdl-pill {
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 999px;
    border: 1px solid rgba(76,151,255,0.22);
    background: linear-gradient(135deg, #e0f2ff 0%, #dbeafe 100%);
    color: #1d4ed8;
    font-weight: 800;
    font-size: 0.78rem;
    padding: 3px 10px;
    white-space: nowrap;
}
.mdl-layout { display: grid; grid-template-columns: 260px minmax(0,1fr) 260px; gap: 16px; align-items: start; }
@media (max-width: 1200px) { .mdl-layout { grid-template-columns: 240px minmax(0,1fr); } .mdl-right { grid-column: 1 / -1; display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; } }
@media (max-width: 980px) { .mdl-layout { grid-template-columns: 1fr; } .mdl-right { grid-template-columns: 1fr; } }
.mdl-side { display: flex; flex-direction: column; gap: 12px; }
.mdl-right { display: flex; flex-direction: column; gap: 12px; }
.mdl-block {
    background: #fff;
    border: 1px solid rgba(76,151,255,0.12);
    border-radius: 16px;
    padding: 12px;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}
.mdl-block-title { font-weight: 900; color: #0f172a; font-size: 0.95rem; margin-bottom: 10px; }
.mdl-nav { display: flex; flex-direction: column; gap: 6px; }
.mdl-nav-item {
    display: flex; align-items: center;
    padding: 8px 10px;
    border-radius: 12px;
    border: 1px solid transparent;
    color: #0f172a;
    text-decoration: none;
    font-size: 0.9rem;
    transition: background .15s, border-color .15s, color .15s;
}
.mdl-nav-item:hover { background: #f8fafc; border-color: #e2e8f0; }
.mdl-nav-item.active { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; font-weight: 800; }
.mdl-side-meta { display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem; }
.mdl-side-label { color: #64748b; font-weight: 700; }
.mdl-side-value { color: #0f172a; font-weight: 900; }
.mdl-students-card { border: 1px solid #e8eef5; border-radius: 12px; padding: 10px; background: #fff; }
.mdl-students-count { font-size: 0.82rem; font-weight: 700; color: #475569; margin-bottom: 8px; }
.mdl-students-actions { display: flex; flex-wrap: wrap; gap: 6px; }
.mdl-students-actions button,
.mdl-students-actions a {
    border: 1px solid #dbe4ef;
    background: #f8fafc;
    color: #334155;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 999px;
    padding: 5px 10px;
    text-decoration: none;
    cursor: pointer;
}
.mdl-students-actions button:hover,
.mdl-students-actions a:hover { background: #eef2ff; border-color: #c7d2fe; color: #3730a3; }

.mdl-main { display: flex; flex-direction: column; gap: 12px; }
.mdl-add-section-wrap {
    display: flex;
    justify-content: flex-end;
    margin-top: -2px;
}
.mdl-section {
    background: #fff;
    border: 1px solid rgba(76,151,255,0.12);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}
.mdl-section-head-bar {
    display: flex;
    align-items: stretch;
    gap: 0;
    background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
    border-bottom: 1px solid transparent;
}
.mdl-section.open .mdl-section-head-bar { border-bottom-color: #eef2f7; }
.mdl-section-head-bar .mdl-section-head-tools {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 10px 6px 4px;
    flex-shrink: 0;
    align-self: center;
}
.mdl-section-head-title { text-align: left; flex: 1; min-width: 0; }
.mdl-section-head {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 14px;
    background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
    border: none;
    cursor: pointer;
    font-weight: 900;
    color: #0f172a;
}
.mdl-section-head:hover { background: #f8fafc; }
.mdl-section-head-bar .mdl-section-head {
    flex: 1;
    min-width: 0;
    border-radius: 0;
    background: transparent;
}
.mdl-section-head-bar .mdl-section-head:hover { background: #f8fafc; }
.mdl-chevron { color: #64748b; transition: transform .15s ease; }
.mdl-section.open .mdl-chevron { transform: rotate(180deg); }
.mdl-section-body { padding: 12px 14px 14px; border-top: 1px solid #eef2f7; display: none; }
.mdl-section.open .mdl-section-body { display: block; }
.mdl-info {
    border: 1px solid #e2e8f0;
    background: #f8fafc;
    border-radius: 14px;
    padding: 12px;
}
.mdl-info-title { font-weight: 900; color: #0f172a; margin-bottom: 4px; }
.mdl-info-text { color: #475569; font-size: 0.92rem; }
.mdl-resources { display: flex; flex-direction: column; gap: 10px; }
.mdl-resource {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 12px;
    border-radius: 14px;
    border: 1px solid #e8eef5;
    background: #fff;
}
.mdl-resource-icon {
    width: 40px; height: 40px;
    border-radius: 12px;
    background: linear-gradient(145deg, #e0f2ff 0%, #dbeafe 100%);
    border: 1px solid rgba(59,130,246,0.25);
    color: #1d4ed8;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.mdl-resource-main { flex: 1; min-width: 0; }
.mdl-resource-title { font-weight: 900; color: #0f172a; }
.mdl-resource-desc { margin-top: 4px; color: #64748b; font-size: 0.9rem; }
.mdl-resource-meta { margin-top: 8px; display: flex; flex-wrap: wrap; gap: 6px; }
.mdl-tag {
    font-size: 0.75rem;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    padding: 2px 8px;
    font-weight: 700;
}
.mdl-resource-actions {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: flex-end;
}
.mdl-resource-manage-icons {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mdl-icon-form {
    display: inline-flex;
    margin: 0;
    padding: 0;
}
.mdl-icon-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: 8px;
    color: #64748b;
    cursor: pointer;
    transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
}
.mdl-icon-btn:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #cbd5e1;
}
.mdl-icon-btn--drag {
    cursor: grab;
}
.mdl-icon-btn--drag:active {
    cursor: grabbing;
}

/* Section reorder visuals */
.lms-lessons-reorder-container .mdl-section.lms-section--dragging {
    opacity: 0.6;
}
.lms-lessons-reorder-container .mdl-section.lms-section--drop-target {
    outline: 2px dashed var(--admin-primary, #4C97FF);
    outline-offset: -3px;
}

.lms-section-reorder-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border: 1px solid color-mix(in srgb, var(--admin-primary, #4C97FF) 18%, #e2e8f0);
    background: color-mix(in srgb, var(--admin-primary, #4C97FF) 6%, #ffffff);
    border-radius: 12px;
    margin: 10px 0 0;
}
.lms-section-reorder-bar__text {
    font-size: 0.85rem;
    font-weight: 800;
    color: #475569;
}

#lms-apply-section-order {
    background: var(--admin-primary, #4C97FF) !important;
    border-color: var(--admin-primary, #4C97FF) !important;
    color: #ffffff !important;
}
.mdl-icon-btn-danger:hover {
    background: #fef2f2;
    color: #b91c1c;
    border-color: #fecaca;
}
.lms-hidden-template-store {
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
.lms-manage-section-resource-list {
    display: flex;
    flex-direction: column;
    max-height: 280px;
    overflow-y: auto;
}
.lms-manage-mod-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #eef2f7;
}
.lms-manage-mod-row:last-child { border-bottom: none; }
.lms-manage-mod-main { min-width: 0; flex: 1; }
.lms-manage-mod-title { font-weight: 700; color: #0f172a; font-size: 0.92rem; }
.lms-manage-mod-desc { font-size: 0.8rem; color: #64748b; margin-top: 4px; line-height: 1.35; }
.lms-manage-mod-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.lms-manage-mod-delete-form { display: inline; margin: 0; padding: 0; }

.lms-section-add-resource-row {
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #eef2f7;
}
.lms-section-add-resource-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 2px;
    border: none;
    background: transparent;
    cursor: pointer;
    border-radius: 999px;
    font: inherit;
    color: #2563eb;
}
.lms-section-add-resource-btn:hover {
    background: rgba(76, 151, 255, 0.1);
}
.lms-section-add-resource-btn:focus-visible {
    outline: 2px solid #4C97FF;
    outline-offset: 2px;
}
.lms-section-add-resource-glyph {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.lms-section-add-resource-sr {
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

.mdl-course-head-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; justify-content: flex-end; }
.mdl-activity-dropdown { position: relative; }
.mdl-activity-trigger { display: inline-flex; align-items: center; gap: 6px; }
.mdl-activity-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    min-width: 170px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 12px 28px rgba(15,23,42,0.14);
    padding: 6px;
    display: none;
    z-index: 20;
}
.mdl-activity-dropdown.open .mdl-activity-menu { display: block; }
.mdl-activity-menu button {
    width: 100%;
    border: none;
    background: transparent;
    text-align: left;
    padding: 8px 10px;
    border-radius: 8px;
    color: #0f172a;
    font-weight: 600;
    font-size: 0.82rem;
    cursor: pointer;
}
.mdl-activity-menu button:hover { background: #f8fafc; }

/* Modal Styles */
.lms-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2000;
    display: none;
    align-items: center;
    justify-content: center;
}
.lms-modal.open {
    display: flex;
}
.lms-modal--confirm {
    z-index: 2100;
}
.lms-modal-content--confirm {
    max-width: 420px;
}
.lms-modal--confirm .lms-confirm-message {
    margin: 0;
    line-height: 1.55;
    color: #475569;
    font-size: 0.95rem;
}
.lms-modal--confirm .lms-confirm-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
    width: 100%;
    margin-top: 22px;
    flex-wrap: wrap;
}
.lms-modal--confirm .lms-confirm-cancel-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
}
.lms-modal--confirm .lms-confirm-cancel-btn:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #0f172a;
}
.lms-modal--confirm .lms-confirm-delete-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
    border: none;
    background: #dc2626;
    color: #fff;
    box-shadow: 0 2px 10px rgba(220, 38, 38, 0.35);
}
.lms-modal--confirm .lms-confirm-delete-btn:hover {
    background: #b91c1c;
    box-shadow: 0 4px 14px rgba(185, 28, 28, 0.4);
}
.lms-modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(4px);
}
.lms-modal-content {
    position: relative;
    background: #fff;
    width: 90%;
    max-width: 500px;
    border-radius: 20px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    animation: modal-slide-up 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
@keyframes modal-slide-up {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.lms-modal-header {
    padding: 20px 24px;
    background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
    border-bottom: 1px solid #eef2f7;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.lms-modal-title {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 800;
    color: #0f172a;
}
.lms-modal-close {
    border: none;
    background: transparent;
    font-size: 1.5rem;
    color: #94a3b8;
    cursor: pointer;
    line-height: 1;
    padding: 4px;
    border-radius: 8px;
    transition: background 0.2s, color 0.2s;
}
.lms-modal-close:hover {
    background: #f1f5f9;
    color: #334155;
}
.lms-modal-body {
    padding: 24px;
}

/* Moodle-style: Google Meet classroom modal */
.lms-modal--moodle-meet .moodle-meet-modal {
    max-width: 560px;
    border-radius: 4px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}
.lms-modal--moodle-meet .moodle-meet-modal__header {
    padding: 10px 14px;
    background: #f5f5f5;
    border-bottom: 1px solid #ddd;
}
.lms-modal--moodle-meet .moodle-meet-modal__header .lms-modal-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #333;
}
.lms-modal--moodle-meet .moodle-meet-modal__body {
    padding: 16px 18px 18px;
    background: #fff;
    font-size: 0.875rem;
    color: #333;
}
.moodle-google-banner {
    padding: 12px 14px;
    margin-bottom: 14px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #f8f9fa;
}
.moodle-google-banner--notice {
    background: #fffbeb;
    border-color: #e6d9a8;
}
.moodle-google-banner--linked {
    background: #f7faf7;
    border-color: #c5d9c5;
}
.moodle-google-banner__inner {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}
.moodle-google-banner__icon {
    width: 40px;
    height: 40px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    flex-shrink: 0;
}
.moodle-google-banner__heading {
    font-weight: 700;
    font-size: 0.9375rem;
    color: #333;
    margin-bottom: 6px;
}
.moodle-google-banner__sub {
    font-size: 0.8125rem;
    color: #555;
    margin: 0 0 8px;
    line-height: 1.45;
}
.moodle-google-banner__desc {
    font-size: 0.8125rem;
    color: #555;
    margin: 0 0 10px;
    line-height: 1.45;
}
.moodle-google-banner__email {
    font-size: 0.8125rem;
    color: #444;
    line-height: 1.45;
    margin-bottom: 10px;
}
.moodle-google-banner__hint {
    font-size: 0.75rem;
    color: #666;
    margin: 8px 0 0;
    line-height: 1.35;
}
.moodle-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 4px;
    padding: 7px 14px;
    cursor: pointer;
    text-decoration: none;
    border: 1px solid transparent;
    line-height: 1.3;
    font-family: inherit;
}
.moodle-google-icon-btn {
    flex-shrink: 0;
    display: block;
}
.moodle-btn--google {
    background: #fff;
    border-color: #dadce0;
    color: #3c4043;
    margin-top: 4px;
}
.moodle-btn--google:hover {
    background: #f8f9fa;
    border-color: #d2d3d6;
}
.moodle-btn--secondary-outline {
    background: #fff;
    border: 1px solid #aaa;
    color: #333;
}
.moodle-btn--secondary-outline:hover {
    background: #f0f0f0;
}
.moodle-btn--primary {
    background: var(--admin-primary, #0f6cbf);
    border-color: var(--admin-primary, #0f6cbf);
    color: #fff;
}
.moodle-btn--primary:hover {
    filter: brightness(0.95);
}
.moodle-btn--back {
    width: 100%;
    justify-content: flex-start;
    margin-bottom: 14px;
    background: #f5f5f5;
    border: 1px solid #bbb;
    color: #333;
}
.moodle-btn--back-inline {
    width: auto;
    margin-bottom: 0;
}
.moodle-meet-modal__actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid #ddd;
    gap: 8px;
    flex-wrap: wrap;
}
.moodle-meet-modal__actions--two-btns {
    justify-content: space-between;
    flex-wrap: nowrap;
}
.moodle-meet-modal__actions--proceed {
    border-top: none;
    padding-top: 0;
    margin-top: 12px;
    justify-content: flex-end;
}
.moodle-meet-form .moodle-fitem {
    display: flex;
    flex-wrap: wrap;
    gap: 8px 16px;
    margin-bottom: 0;
    align-items: flex-start;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}
.moodle-meet-form .moodle-fitem:last-of-type {
    border-bottom: none;
}
.moodle-fitem__label {
    flex: 0 0 150px;
    max-width: 100%;
    padding-top: 6px;
}
.moodle-fitem__label label {
    font-weight: 600;
    font-size: 0.875rem;
    color: #333;
}
.moodle-req {
    color: #ca3120;
    text-decoration: none;
    font-weight: 700;
    border: 0;
}
.moodle-fitem__felement {
    flex: 1 1 200px;
    min-width: 0;
}
.moodle-textinput {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid color-mix(in srgb, var(--admin-primary, #0f6cbf) 18%, #8f959f);
    border-radius: 4px;
    padding: 6px 8px;
    font-size: 0.875rem;
    color: #333;
    background: #fff;
    font-family: inherit;
}
.moodle-textinput:focus {
    border-color: var(--admin-primary, #0f6cbf);
    outline: none;
    box-shadow: 0 0 0 1px color-mix(in srgb, var(--admin-primary, #0f6cbf) 55%, transparent);
}
.moodle-textinput--narrow {
    max-width: 160px;
}
.moodle-fitem__statictext {
    font-size: 0.8125rem;
    color: #555;
    margin: 0 0 8px;
    line-height: 1.45;
}
.moodle-datetime-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px 16px;
    padding: 10px 0 12px;
    border-bottom: 1px solid #eee;
    margin-bottom: 0;
}
.moodle-datetime-col__label {
    display: block;
    font-weight: 600;
    font-size: 0.875rem;
    color: #333;
    margin-bottom: 6px;
}
@media (max-width: 520px) {
    .moodle-datetime-row {
        grid-template-columns: 1fr;
    }
    .moodle-fitem__label {
        flex-basis: 100%;
    }
}
.moodle-alert {
    margin-top: 12px;
    padding: 10px 12px;
    border-radius: 4px;
    font-size: 0.8125rem;
    line-height: 1.45;
    border: 1px solid;
}
.moodle-alert--danger {
    background: #f8d7da;
    border-color: #f1aeb5;
    color: #58151c;
}
.moodle-alert--success {
    background: #d4edda;
    border-color: #b8dabd;
    color: #155724;
}

/* Moodle-style: generic form modals (Quiz / Resource) */
.lms-modal--moodle .lms-modal-content {
    max-width: 560px;
    border-radius: 12px;
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.18);
}
.lms-modal--moodle .lms-modal-header {
    padding: 14px 18px;
    background: #f5f5f5;
    border-bottom: 1px solid #ddd;
}
.lms-modal--moodle .lms-modal-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: #333;
}
.lms-modal--moodle .lms-modal-body {
    padding: 16px 18px 18px;
    color: #333;
}
.lms-modal--moodle .lms-form-group label {
    font-size: 0.875rem;
    font-weight: 700;
    color: #333;
}
.lms-modal--moodle .lms-input,
.lms-modal--moodle .lms-form-group textarea,
.lms-modal--moodle .lms-form-group select,
.lms-modal--moodle .lms-form-group input[type="text"],
.lms-modal--moodle .lms-form-group input[type="number"],
.lms-modal--moodle .lms-form-group input[type="datetime-local"] {
    border-radius: 6px;
    padding: 8px 10px;
    border: 1px solid color-mix(in srgb, var(--admin-primary, #0f6cbf) 18%, #8f959f);
}
.lms-modal--moodle .lms-input:focus {
    border-color: var(--admin-primary, #0f6cbf);
    box-shadow: 0 0 0 1px color-mix(in srgb, var(--admin-primary, #0f6cbf) 45%, transparent);
}
.lms-modal--moodle .lms-btn-primary {
    background: var(--admin-primary, #0f6cbf) !important;
    background-image: none !important;
    border: none !important;
    box-shadow: none !important;
    border-radius: 8px;
    padding: 12px 20px;
}
.lms-modal--moodle .lms-btn-primary:hover {
    transform: none !important;
    box-shadow: none !important;
}
.lms-modal--moodle .lms-btn-primary svg {
    color: #fff;
}
.lms-modal--moodle .lms-type-card {
    border-radius: 10px;
    background: #fff;
    border: 2px solid #e5e7eb;
}
.lms-modal--moodle .lms-type-option input:checked + .lms-type-card {
    border-color: var(--admin-primary, #0f6cbf);
    background: color-mix(in srgb, var(--admin-primary, #0f6cbf) 18%, #ffffff);
}
.lms-modal--moodle .lms-type-card svg {
    color: #6b7280;
}
.lms-modal--moodle .lms-type-option input:checked + .lms-type-card svg {
    color: var(--admin-primary, #0f6cbf);
}
.lms-modal--moodle .lms-type-card span {
    color: #374151;
}
.lms-modal--moodle .lms-type-option input:checked + .lms-type-card span {
    color: var(--admin-primary, #0f6cbf);
}

/* Prevent the "Document" textarea from pushing the submit button off-screen */
#modal-upload #type-field-doc textarea[name="content"]{
    min-height: 160px;
    max-height: 220px;
    overflow: auto;
    resize: vertical;
}

/* Ensure the Add Resource modal remains usable on shorter screens.
   lms-modal-content has `overflow: hidden`, so we make the body scrollable. */
#modal-upload .lms-modal-content{
    max-height: calc(100vh - 140px);
    display: flex;
    flex-direction: column;
}
#modal-upload .lms-modal-body{
    overflow: auto;
    flex: 1 1 auto;
}
.lms-upload-quick-actions {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
    margin: 12px 0 18px;
}
.lms-upload-shell {
    margin-top: 6px;
    padding: 10px 12px;
    border: 1px solid #dbe6f8;
    border-radius: 12px;
    background: linear-gradient(180deg, #f8fbff 0%, #f4f9ff 100%);
}
.lms-upload-shell__title {
    font-size: 0.82rem;
    font-weight: 800;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.lms-upload-shell__sub {
    margin-top: 3px;
    font-size: 0.82rem;
    color: #64748b;
}
.lms-upload-quick-action {
    border: 1px solid #d9e2f5;
    background: #f8fbff;
    color: #1e293b;
    border-radius: 12px;
    min-height: 44px;
    font-size: 0.86rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.18s ease;
}
.lms-upload-quick-action:hover {
    border-color: #93c5fd;
    background: #eff6ff;
}
.lms-upload-quick-action.is-active,
.lms-upload-quick-action[aria-current="true"] {
    border-color: #22c55e;
    background: #ecfdf3;
    color: #15803d;
    cursor: default;
}
@media (max-width: 700px) {
    .lms-upload-quick-actions {
        grid-template-columns: 1fr;
    }
}

.lms-input {
    width: 100%; padding: 10px 12px;
    border: 1px solid color-mix(in srgb, var(--admin-primary, #4C97FF) 14%, #e2e8f0); border-radius: 10px;
    font-size: 0.875rem; color: #0f172a;
    background: #fff;
    outline: none;
}
.lms-input:focus {
    border-color: var(--admin-primary, #4C97FF);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary, #4C97FF) 22%, transparent);
}
.lms-input-hint { font-size: 0.75rem; color: #94a3b8; margin-top: 4px; }

.lms-type-selector {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 8px;
}
.lms-type-option { cursor: pointer; }
.lms-type-option input { display: none; }
.lms-type-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    border: 2px solid #f1f5f9;
    border-radius: 12px;
    background: #f8fafc;
    transition: all 0.2s;
    text-align: center;
}
.lms-type-card svg { color: #94a3b8; }
.lms-type-card span { font-size: 0.75rem; font-weight: 700; color: #64748b; }

.lms-type-option input:checked + .lms-type-card {
    border-color: #4C97FF;
    background: #eff6ff;
}
.lms-type-option input:checked + .lms-type-card svg { color: #4C97FF; }
.lms-type-option input:checked + .lms-type-card span { color: #1d4ed8; }

.lms-resource-preview {
    margin-top: 12px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
}
.lms-resource-preview.image img {
    display: block;
    max-width: 100%;
    height: auto;
    max-height: 300px;
    margin: 0 auto;
}
.lms-resource-preview.video video {
    display: block;
    width: 100%;
    max-height: 300px;
    background: #000;
}
.lms-resource-preview.doc {
    padding: 16px;
    font-size: 0.95rem;
    line-height: 1.6;
    color: #334155;
    background: #fff;
    max-height: 300px;
    overflow-y: auto;
}
.lms-resource-preview.pdf iframe {
    display: block;
    border: none;
}

/* Progress Bar */
.lms-progress-bg {
    width: 100%;
    height: 10px;
    background: #f1f5f9;
    border-radius: 5px;
    overflow: hidden;
}
.lms-progress-fill {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, #4C97FF 0%, #2563eb 100%);
    border-radius: 5px;
    transition: width 0.2s ease;
}
</style>
@endpush

@push('scripts')
<script>
    (function () {
        let pendingConfirmForm = null;

        const sections = document.querySelectorAll('.mdl-section');
        sections.forEach((s, i) => {
            if (i === 0) s.classList.add('open');
            const btn = s.querySelector('[data-accordion-btn]');
            if (!btn) return;
            btn.addEventListener('click', () => {
                s.classList.toggle('open');
            });
        });

        // ── Section reorder (drag & drop) ─────────────────────────────────────
        const lessonsReorderContainer = document.getElementById('lms-lessons-reorder-container');
        const reorderBar = document.getElementById('lms-section-reorder-bar');
        const applyOrderBtn = document.getElementById('lms-apply-section-order');
        const reorderEndpoint = "{{ url('/tenant/lms/classes/' . $classGroup->id . '/' . $subject->id . '/lessons/reorder') }}";
        const lmsReorderCsrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        if (lessonsReorderContainer && reorderBar && applyOrderBtn && reorderEndpoint) {
            let draggedSection = null;
            let dirty = false;

            const getLessonIdsInDomOrder = () => (
                Array.from(lessonsReorderContainer.querySelectorAll('[data-draggable-lesson="1"]'))
                    .map(el => parseInt(el.getAttribute('data-lesson-id') || '0', 10))
                    .filter(Boolean)
            );

            const sectionEls = Array.from(lessonsReorderContainer.querySelectorAll('[data-draggable-lesson="1"]'));

            const clearDropTargets = () => {
                sectionEls.forEach((el) => el.classList.remove('lms-section--drop-target'));
            };

            // Add drag listeners to handles
            lessonsReorderContainer.querySelectorAll('[data-drag-lesson-handle]').forEach((handle) => {
                handle.addEventListener('dragstart', (e) => {
                    draggedSection = handle.closest('[data-draggable-lesson="1"]');
                    if (!draggedSection) return;
                    draggedSection.classList.add('lms-section--dragging');
                    clearDropTargets();
                    dirty = true;
                    reorderBar.style.display = 'flex';
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', String(draggedSection.dataset.lessonId || ''));
                });

                handle.addEventListener('dragend', () => {
                    if (draggedSection) draggedSection.classList.remove('lms-section--dragging');
                    draggedSection = null;
                    clearDropTargets();
                });
            });

            // Dragover + drop on each section
            sectionEls.forEach((targetSection) => {
                targetSection.addEventListener('dragover', (e) => {
                    if (!draggedSection) return;
                    if (targetSection === draggedSection) return;
                    e.preventDefault();
                    clearDropTargets();
                    targetSection.classList.add('lms-section--drop-target');
                });

                targetSection.addEventListener('drop', (e) => {
                    if (!draggedSection) return;
                    e.preventDefault();
                    clearDropTargets();

                    if (targetSection === draggedSection) return;

                    const rect = targetSection.getBoundingClientRect();
                    const moveAfter = (e.clientY - rect.top) > (rect.height / 2);

                    if (moveAfter && targetSection.nextSibling) {
                        lessonsReorderContainer.insertBefore(draggedSection, targetSection.nextSibling);
                    } else {
                        lessonsReorderContainer.insertBefore(draggedSection, targetSection);
                    }

                    dirty = true;
                    reorderBar.style.display = 'flex';
                });
            });

            applyOrderBtn.addEventListener('click', async () => {
                if (!dirty) return;
                const lessonIds = getLessonIdsInDomOrder();
                if (lessonIds.length < 1) return;

                applyOrderBtn.disabled = true;
                const originalText = applyOrderBtn.textContent;
                applyOrderBtn.textContent = 'Applying...';

                try {
                    const res = await fetch(reorderEndpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': lmsReorderCsrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ lesson_ids: lessonIds }),
                    });

                    window.location.reload();
                    // If reload doesn't happen (cached), fall back to original endpoint
                    if (!res) {
                        window.location.href = reorderEndpoint;
                    }
                } catch (err) {
                    console.error(err);
                    alert('Failed to save section order.');
                } finally {
                    applyOrderBtn.disabled = false;
                    applyOrderBtn.textContent = originalText;
                }
            });
        }

        const activityDropdown = document.querySelector('[data-activity-dropdown]');
        const activityTrigger = document.querySelector('[data-activity-trigger]');
        if (activityDropdown && activityTrigger) {
            activityTrigger.addEventListener('click', (e) => {
                e.stopPropagation();
                activityDropdown.classList.toggle('open');
            });
            document.addEventListener('click', (e) => {
                if (!activityDropdown.contains(e.target)) {
                    activityDropdown.classList.remove('open');
                }
            });
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    activityDropdown.classList.remove('open');
                }
            });
        }

        const resetModalUploadForm = () => {
            const modal = document.getElementById('modal-upload');
            if (!modal) return;
            const form = modal.querySelector('form#lms-modal-upload-form') || modal.querySelector('form.lms-upload-form');
            if (!form) return;
            form.querySelectorAll('input[type="text"], input[type="url"], textarea').forEach((el) => {
                el.value = '';
            });
            form.querySelectorAll('input[type="file"]').forEach((el) => {
                el.value = '';
            });
            const fileRadio = form.querySelector('input[name="type"][value="file"]');
            if (fileRadio) {
                fileRadio.checked = true;
                fileRadio.dispatchEvent(new Event('change', { bubbles: true }));
            }
            const progress = form.querySelector('.lms-upload-progress-container');
            if (progress) progress.style.display = 'none';
        };

        const closeAllOpenModals = () => {
            document.querySelectorAll('.lms-modal.open').forEach((modalEl) => {
                modalEl.classList.remove('open');
            });
        };

        // FAB behavior (Open Modals) - handle any element with data-fab-target
        const fabTriggers = document.querySelectorAll('[data-fab-target]');
        fabTriggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                if (activityDropdown) activityDropdown.classList.remove('open');
                const targetId = trigger.getAttribute('data-fab-target');
                const targetModal = document.getElementById(targetId);
                if (targetModal) {
                    if (targetId === 'modal-upload') {
                        resetModalUploadForm();
                        const ls = document.getElementById('lms-upload-lesson-select');
                        if (ls) {
                            const lid = trigger.getAttribute('data-upload-lesson-id');
                            ls.value = (lid !== null && lid !== '') ? lid : '';
                        }
                    }
                    targetModal.classList.add('open');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                }
            });
        });

        const uploadActionTriggers = document.querySelectorAll('[data-upload-action-target]');
        uploadActionTriggers.forEach((trigger) => {
            trigger.addEventListener('click', () => {
                const targetId = trigger.getAttribute('data-upload-action-target');
                const targetModal = document.getElementById(targetId);
                if (!targetModal) return;

                const uploadLessonSelect = document.getElementById('lms-upload-lesson-select');
                const selectedLessonId = uploadLessonSelect ? uploadLessonSelect.value : '';

                closeAllOpenModals();
                targetModal.classList.add('open');
                document.body.style.overflow = 'hidden';

                if (targetId === 'modal-create-quiz') {
                    const quizLessonSelect = document.getElementById('lms-quiz-lesson-select');
                    if (quizLessonSelect) {
                        quizLessonSelect.value = selectedLessonId || '';
                    }
                }
            });
        });

        const editModuleModal = document.getElementById('modal-edit-module');
        const editModuleForm = document.getElementById('form-edit-module');
        const editModuleTitleInput = document.getElementById('edit-module-title');
        const editModuleDescInput = document.getElementById('edit-module-description');
        const editModuleContentLink = document.getElementById('edit-module-content-link');
        const editModuleContentDoc = document.getElementById('edit-module-content-doc');
        const editModuleLinkWrap = document.getElementById('edit-module-field-link');
        const editModuleDocWrap = document.getElementById('edit-module-field-doc');
        const editModuleFileWrap = document.getElementById('edit-module-field-file');
        const editModuleFileInput = document.getElementById('edit-module-file');

        const setEditModuleFieldsEnabled = (type) => {
            if (editModuleContentLink) {
                editModuleContentLink.disabled = type !== 'link';
            }
            if (editModuleContentDoc) {
                editModuleContentDoc.disabled = type !== 'doc';
            }
            if (editModuleFileInput) {
                editModuleFileInput.disabled = type !== 'file';
            }
        };

        const manageSectionModalEl = document.getElementById('modal-manage-section');

        const openEditModuleFromButton = (btn) => {
            const { moduleId, moduleTitle, moduleDescription, moduleType, moduleContent } = btn.dataset;
            if (!editModuleForm || !editModuleModal) return;
            if (manageSectionModalEl) manageSectionModalEl.classList.remove('open');
            editModuleForm.action = `{{ url('/tenant/lms/modules') }}/${moduleId}`;
            if (editModuleTitleInput) editModuleTitleInput.value = moduleTitle || '';
            if (editModuleDescInput) editModuleDescInput.value = moduleDescription || '';
            if (editModuleContentLink) editModuleContentLink.value = '';
            if (editModuleContentDoc) editModuleContentDoc.value = '';
            if (editModuleFileInput) editModuleFileInput.value = '';
            if (editModuleLinkWrap) editModuleLinkWrap.style.display = 'none';
            if (editModuleDocWrap) editModuleDocWrap.style.display = 'none';
            if (editModuleFileWrap) editModuleFileWrap.style.display = 'none';
            const mt = moduleType || 'file';
            if (mt === 'link' && editModuleLinkWrap) {
                editModuleLinkWrap.style.display = 'block';
                if (editModuleContentLink) editModuleContentLink.value = moduleContent || '';
            } else if (mt === 'doc' && editModuleDocWrap) {
                editModuleDocWrap.style.display = 'block';
                if (editModuleContentDoc) editModuleContentDoc.value = moduleContent || '';
            } else if (mt === 'file' && editModuleFileWrap) {
                editModuleFileWrap.style.display = 'block';
            }
            setEditModuleFieldsEnabled(mt);
            if (activityDropdown) activityDropdown.classList.remove('open');
            editModuleModal.classList.add('open');
            document.body.style.overflow = 'hidden';
        };

        document.addEventListener('click', (e) => {
            const modBtn = e.target.closest('[data-edit-module]');
            if (modBtn) {
                e.preventDefault();
                openEditModuleFromButton(modBtn);
            }
        });

        const manageSectionBody = document.getElementById('manage-section-resources-body');
        const manageSectionEmpty = document.getElementById('manage-section-resources-empty');
        const editLessonForm = document.getElementById('form-edit-lesson');
        const editLessonTitleInput = document.getElementById('edit-lesson-title');
        const moduleRowsStore = document.getElementById('manage-section-module-rows-store');

        document.querySelectorAll('[data-manage-section]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const lessonId = btn.getAttribute('data-lesson-id');
                const lessonTitle = btn.getAttribute('data-lesson-title') || '';
                if (!manageSectionModalEl || !manageSectionBody || !editLessonForm || !moduleRowsStore) return;
                editLessonForm.action = `{{ url('/tenant/lms/lessons') }}/${lessonId}`;
                if (editLessonTitleInput) editLessonTitleInput.value = lessonTitle;
                manageSectionBody.innerHTML = '';
                const rows = moduleRowsStore.querySelectorAll(`.lms-manage-mod-row[data-lesson-id="${lessonId}"]`);
                rows.forEach((row) => {
                    manageSectionBody.appendChild(row.cloneNode(true));
                });
                if (manageSectionEmpty) {
                    manageSectionEmpty.style.display = rows.length === 0 ? 'block' : 'none';
                }
                if (activityDropdown) activityDropdown.classList.remove('open');
                manageSectionModalEl.classList.add('open');
                document.body.style.overflow = 'hidden';
            });
        });

        // Classroom meeting (Google Meet) - OAuth-based creation
        const classroomFabTrigger = document.querySelector('[data-fab-target="modal-create-classroom"]');
        const modalCreateClassroom = document.getElementById('modal-create-classroom');
        const classroomStepGoogle = document.getElementById('classroom-modal-step-google');
        const classroomProceedBtn = document.getElementById('classroom-proceed-btn');
        const classroomBackToGoogleBtn = document.getElementById('classroom-back-to-google-btn');
        const googleConnectSection = document.getElementById('google-connect-section');
        const googleConnectedSection = document.getElementById('google-connected-section');
        const classroomForm = document.getElementById('form-create-classroom-meeting');
        const classroomTitleInput = document.getElementById('classroom-title-input');
        const classroomDescInput = document.getElementById('classroom-desc-input');
        const classroomStartInput = document.getElementById('classroom-start-input');
        const classroomEndInput = document.getElementById('classroom-end-input');
        const classroomLateEntryInput = document.getElementById('classroom-late-entry-input');
        const classroomErrorMsg = document.getElementById('classroom-error-msg');
        const classroomSuccessMsg = document.getElementById('classroom-success-msg');
        const createClassroomBtn = document.getElementById('create-classroom-btn');
        const createClassroomBtnText = document.getElementById('create-classroom-btn-text');
        const classroomGoogleEndpoint = "{{ url('/tenant/lms/classes/' . $classGroup->id . '/' . $subject->id . '/classroom-meetings/google') }}";
        const googleCheckEndpoint = "{{ url('/tenant/lms/google/check') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        const setDefaultDateTimes = () => {
            const now = new Date();
            const start = new Date(now.getTime() + 60 * 60 * 1000);
            const end = new Date(start.getTime() + 60 * 60 * 1000);

            const toLocalIso = (d) => {
                const offset = d.getTimezoneOffset();
                const local = new Date(d.getTime() - offset * 60 * 1000);
                return local.toISOString().slice(0, 16);
            };

            if (classroomStartInput && !classroomStartInput.value) {
                classroomStartInput.value = toLocalIso(start);
            }
            if (classroomEndInput && !classroomEndInput.value) {
                classroomEndInput.value = toLocalIso(end);
            }
        };

        const checkGoogleConnection = async () => {
            try {
                const res = await fetch(googleCheckEndpoint, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();
                return {
                    connected: data.connected === true,
                    google_account_email: data.google_account_email || null,
                };
            } catch {
                return { connected: false, google_account_email: null };
            }
        };

        const resetClassroomCreationWizard = () => {
            if (classroomStepGoogle) classroomStepGoogle.style.display = 'block';
            if (classroomForm) classroomForm.style.display = 'none';
            if (classroomProceedBtn) classroomProceedBtn.style.display = 'none';
            if (classroomErrorMsg) classroomErrorMsg.style.display = 'none';
            if (classroomSuccessMsg) classroomSuccessMsg.style.display = 'none';
        };

        const showClassroomMeetingFormStep = () => {
            if (classroomStepGoogle) classroomStepGoogle.style.display = 'none';
            if (classroomForm) classroomForm.style.display = 'block';
        };

        const updateGoogleConnectionUI = (connected, googleAccountEmail) => {
            const emailEl = document.getElementById('google-connected-email');
            if (classroomProceedBtn) {
                classroomProceedBtn.style.display = 'none';
            }
            if (connected) {
                if (googleConnectSection) googleConnectSection.style.display = 'none';
                if (googleConnectedSection) googleConnectedSection.style.display = 'block';
                if (classroomProceedBtn) {
                    classroomProceedBtn.style.display = 'inline-flex';
                }
                if (emailEl) {
                    if (googleAccountEmail) {
                        emailEl.textContent = '';
                        emailEl.append(
                            document.createTextNode('Event owner on file: '),
                            Object.assign(document.createElement('strong'), { textContent: googleAccountEmail }),
                            document.createTextNode(' — open Meet with '),
                            Object.assign(document.createElement('strong'), { textContent: 'Switch account' }),
                            document.createTextNode(' and pick this address to be host.')
                        );
                    } else {
                        emailEl.textContent = 'We couldn’t read the organizer Gmail yet. Tap “Continue with Google” above, then open Meet again.';
                    }
                }
            } else {
                if (googleConnectSection) googleConnectSection.style.display = 'block';
                if (googleConnectedSection) googleConnectedSection.style.display = 'none';
                if (emailEl) emailEl.textContent = '';
            }
        };

        classroomFabTrigger?.addEventListener('click', async () => {
            resetClassroomCreationWizard();
            setDefaultDateTimes();
            const { connected, google_account_email: gEmail } = await checkGoogleConnection();
            updateGoogleConnectionUI(connected, gEmail);
        });

        classroomProceedBtn?.addEventListener('click', () => {
            showClassroomMeetingFormStep();
        });

        classroomBackToGoogleBtn?.addEventListener('click', async () => {
            resetClassroomCreationWizard();
            const { connected, google_account_email: gEmail } = await checkGoogleConnection();
            updateGoogleConnectionUI(connected, gEmail);
        });

        classroomForm?.addEventListener('submit', async (e) => {
            e.preventDefault();

            const title = classroomTitleInput?.value?.trim() || '';
            const description = classroomDescInput?.value?.trim() || '';
            const startTime = classroomStartInput?.value || '';
            const endTime = classroomEndInput?.value || '';

            if (!title || !startTime || !endTime) {
                if (classroomErrorMsg) {
                    classroomErrorMsg.textContent = 'Please fill in all required fields.';
                    classroomErrorMsg.style.display = 'block';
                }
                return;
            }

            if (classroomErrorMsg) classroomErrorMsg.style.display = 'none';
            if (classroomSuccessMsg) classroomSuccessMsg.style.display = 'none';

            if (createClassroomBtn) createClassroomBtn.disabled = true;
            if (createClassroomBtnText) createClassroomBtnText.textContent = 'Creating...';

            const lateRaw = classroomLateEntryInput?.value?.trim() ?? '';
            let lateEntryMinutes = null;
            if (lateRaw !== '') {
                const n = parseInt(lateRaw, 10);
                if (!Number.isNaN(n) && n >= 1) {
                    lateEntryMinutes = n;
                }
            }

            try {
                const res = await fetch(classroomGoogleEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        title,
                        description,
                        start_time: startTime,
                        end_time: endTime,
                        late_entry_minutes: lateEntryMinutes,
                    }),
                });

                const data = await res.json().catch(() => ({}));

                if (data.needs_auth && data.auth_url) {
                    window.location.href = data.auth_url;
                    return;
                }

                if (!res.ok || !data.ok) {
                    throw new Error(data?.message || 'Failed to create meeting.');
                }

                if (classroomSuccessMsg) {
                    const escAttr = (s) => String(s ?? '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
                    classroomSuccessMsg.innerHTML = 'Meeting created successfully! <a href="' + escAttr(data.meet_link) + '" target="_blank" rel="noopener" style="color:#15803d; font-weight:700;">Open Google Meet</a>';
                    classroomSuccessMsg.style.display = 'block';
                }

                setTimeout(() => window.location.reload(), 1500);
            } catch (e) {
                if (classroomErrorMsg) {
                    classroomErrorMsg.textContent = e?.message || 'Failed to create meeting.';
                    classroomErrorMsg.style.display = 'block';
                }
            } finally {
                if (createClassroomBtn) createClassroomBtn.disabled = false;
                if (createClassroomBtnText) createClassroomBtnText.textContent = 'Create Meeting';
            }
        });

        // Close Modals
        const closeModals = () => {
            pendingConfirmForm = null;
            const classroomWasOpen = modalCreateClassroom?.classList.contains('open');
            document.querySelectorAll('.lms-modal').forEach(modal => {
                modal.classList.remove('open');
            });
            document.body.style.overflow = '';
            if (classroomWasOpen) {
                resetClassroomCreationWizard();
            }
            // Clear PDF iframe source when closing to stop background loading
            const pdfFrame = document.getElementById('pdf-viewer-frame');
            if (pdfFrame) pdfFrame.src = '';
        };

        const confirmDeleteModal = document.getElementById('modal-confirm-delete');
        const confirmTitleEl = document.getElementById('lms-confirm-dialog-title');
        const confirmMessageEl = document.getElementById('lms-confirm-dialog-message');
        const confirmDoBtn = document.getElementById('lms-confirm-dialog-confirm');

        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (!form || !form.hasAttribute('data-confirm-delete')) return;
            e.preventDefault();
            pendingConfirmForm = form;
            const title = form.getAttribute('data-confirm-title') || 'Confirm';
            const message = form.getAttribute('data-confirm-message') || 'Are you sure?';
            if (confirmTitleEl) confirmTitleEl.textContent = title;
            if (confirmMessageEl) confirmMessageEl.textContent = message;
            if (confirmDeleteModal) {
                confirmDeleteModal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        }, true);

        confirmDoBtn?.addEventListener('click', () => {
            if (!pendingConfirmForm) return;
            const f = pendingConfirmForm;
            pendingConfirmForm = null;
            if (confirmDeleteModal) confirmDeleteModal.classList.remove('open');
            document.body.style.overflow = '';
            f.submit();
        });

        // PDF Viewer logic
        const pdfViewerModal = document.getElementById('modal-view-pdf');
        const pdfViewerFrame = document.getElementById('pdf-viewer-frame');
        document.querySelectorAll('[data-view-pdf]').forEach(btn => {
            btn.addEventListener('click', () => {
                const url = btn.getAttribute('data-view-pdf');
                if (pdfViewerFrame && pdfViewerModal) {
                    pdfViewerFrame.src = url + '#toolbar=0';
                    pdfViewerModal.classList.add('open');
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        // Doc Viewer logic
        const docViewerModal = document.getElementById('modal-view-doc');
        const docViewerTitle = document.getElementById('doc-viewer-title');
        const docViewerContent = document.getElementById('doc-viewer-content');
        document.querySelectorAll('[data-view-doc]').forEach(btn => {
            btn.addEventListener('click', () => {
                const title = btn.getAttribute('data-doc-title');
                const content = btn.getAttribute('data-doc-content');
                if (docViewerModal && docViewerTitle && docViewerContent) {
                    docViewerTitle.textContent = title;
                    // Simple nl2br equivalent
                    docViewerContent.innerHTML = content.replace(/\n/g, '<br>');
                    docViewerModal.classList.add('open');
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        document.querySelectorAll('[data-modal-close], .lms-modal-backdrop').forEach(el => {
            el.addEventListener('click', closeModals);
        });

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModals();
        });

        // Type selector logic
        const typeTriggers = document.querySelectorAll('[data-type-trigger]');
        typeTriggers.forEach(trigger => {
            trigger.addEventListener('change', () => {
                const selectedType = trigger.value;
                document.querySelectorAll('.lms-type-field').forEach(field => {
                    field.style.display = 'none';
                    // Disable inputs in hidden fields to prevent validation issues or unwanted data
                    field.querySelectorAll('input, textarea').forEach(input => {
                        input.required = false;
                    });
                });

                const targetField = document.getElementById(`type-field-${selectedType}`);
                if (targetField) {
                    targetField.style.display = 'block';
                    // Enable required for the visible field if it was file
                    if (selectedType === 'file') {
                        targetField.querySelector('input').required = true;
                    } else if (selectedType === 'link' || selectedType === 'doc') {
                        targetField.querySelector('input, textarea').required = true;
                    }
                }
            });
        });

        // Quiz Question Modal logic
        const addQModal = document.getElementById('modal-add-question');
        const addQForm = document.getElementById('form-add-question');
        const addQTitle = document.getElementById('add-q-quiz-title');
        const qTypeSelector = document.getElementById('q-type-selector');
        const mcOptionsContainer = document.getElementById('mc-options-container');

        document.querySelectorAll('[data-add-question]').forEach(btn => {
            btn.addEventListener('click', () => {
                const quizId = btn.getAttribute('data-add-question');
                const quizTitle = btn.getAttribute('data-quiz-title');
                if (addQModal && addQForm && addQTitle) {
                    addQForm.action = `/tenant/lms/quizzes/${quizId}/questions`;
                    addQTitle.textContent = quizTitle;
                    addQModal.classList.add('open');
                    document.body.style.overflow = 'hidden';
                }
            });
        });

        if (qTypeSelector && mcOptionsContainer) {
            const toggleMC = () => {
                const isMC = qTypeSelector.value === 'multiple_choice';
                mcOptionsContainer.style.display = isMC ? 'block' : 'none';
                mcOptionsContainer.querySelectorAll('input').forEach(input => {
                    input.disabled = !isMC;
                    // Only the text inputs should have required
                    if (isMC && input.type === 'text') {
                        input.required = true;
                    } else {
                        input.required = false;
                    }
                });
            };
            qTypeSelector.addEventListener('change', toggleMC);
            // Initial run in case of validation errors (though usually it resets)
            toggleMC();
        }

        // AJAX Form Submission with Progress
        document.querySelectorAll('.lms-upload-form').forEach(form => {
            // Only apply to forms that have a file input
            if (!form.querySelector('input[type="file"]')) return;

            form.addEventListener('submit', (e) => {
                const fileInput = form.querySelector('input[type="file"]');
                // Only use AJAX if a file is actually selected
                if (!fileInput || !fileInput.files.length) return;

                e.preventDefault();

                const formData = new FormData(form);
                const xhr = new XMLHttpRequest();
                const progressContainer = form.querySelector('.lms-upload-progress-container');
                const progressBar = form.querySelector('.lms-upload-progress-bar');
                const progressText = form.querySelector('.lms-upload-percentage');
                const submitBtn = form.querySelector('button[type="submit"]');
                const btnText = form.querySelector('.lms-btn-text');

                // If this is an inline form without the progress container, we might want to add it or just let it submit normally.
                // For now, let's focus on the main modal form which I've already updated.
                if (!progressContainer) {
                    form.submit();
                    return;
                }

                xhr.upload.addEventListener('progress', (event) => {
                    if (event.lengthComputable) {
                        const percent = Math.round((event.loaded / event.total) * 100);
                        progressBar.style.width = percent + '%';
                        progressText.textContent = percent + '%';
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        // Success - reload the page to show new resource
                        window.location.reload();
                    } else {
                        // Error handling
                        alert('Upload failed. Please try again.');
                        progressContainer.style.display = 'none';
                        submitBtn.disabled = false;
                        if (btnText) btnText.textContent = 'Add Resource';
                    }
                });

                xhr.addEventListener('error', () => {
                    alert('An error occurred during upload.');
                    progressContainer.style.display = 'none';
                    submitBtn.disabled = false;
                });

                xhr.open('POST', form.action);
                // Important for Laravel CSRF
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                
                // Show progress UI
                progressContainer.style.display = 'block';
                submitBtn.disabled = true;
                if (btnText) btnText.textContent = 'Uploading...';                xhr.send(formData);
            });
        });
    })();
</script>
@endpush