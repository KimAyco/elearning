@extends('layouts.app')

@section('title', 'Lessons - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'class', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body">
            @php
                $modulesList = ($modules ?? collect())->values();
                $lessonsList = ($lessons ?? collect())->values();
                $lessonResourceCount = $lessonsList->sum(fn($ls) => collect($ls->modules ?? [])->count());
                $totalResources = $modulesList->count() + $lessonResourceCount;
            @endphp

            <div class="mdl-breadcrumb">
                <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                <span class="mdl-bread-sep">›</span>
                <a href="{{ url('/tenant/class') }}">Classes</a>
                <span class="mdl-bread-sep">›</span>
                <span>{{ $classGroup->name ?? 'Class' }}</span>
                <span class="mdl-bread-sep">›</span>
                <span>{{ $subject->code ?? 'SUBJ' }}</span>
            </div>

            <div class="mdl-course-head">
                <div class="mdl-course-head-main">
                    <div class="mdl-course-head-badges">
                        <span class="mdl-course-kicker">{{ $subject->code ?? 'SUBJ' }}</span>
                        <span class="mdl-course-chip">{{ $classGroup->name ?? 'Class' }}</span>
                    </div>
                    <h1 class="mdl-course-title">{{ $subject->title ?? 'Lessons' }}</h1>
                    @if (($courseTeachers ?? collect())->isNotEmpty())
                        <div class="mdl-course-teacher">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            {{ ($courseTeachers->count() === 1) ? 'Teacher' : 'Teachers' }}:
                            <span class="mdl-course-teacher-names">{{ $courseTeachers->pluck('full_name')->filter()->join(', ') }}</span>
                        </div>
                    @endif
                </div>
                <div class="mdl-course-head-meta">
                    <span class="mdl-pill">{{ $totalResources }} resources</span>
                </div>
            </div>

            @if (session('status'))
                <div class="alert success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            <div class="mdl-layout">
                <aside class="mdl-side">
                    <div class="mdl-block">
                        <div class="mdl-block-title">Course index</div>
                        <div class="mdl-nav">
                            <div class="mdl-nav-group-label">General</div>
                            <a class="mdl-nav-item active" href="#section-general">General</a>
                            <div class="mdl-nav-group-label">Modules</div>
                            @forelse ($lessonsList as $lesson)
                                <a class="mdl-nav-item" href="#lesson-{{$lesson->id}}">{{ $lesson->title }}</a>
                            @empty
                                <span class="mdl-nav-empty">No modules yet</span>
                            @endforelse
                            @if ($modulesList->isNotEmpty())
                                <a class="mdl-nav-item" href="#section-resources">Resources</a>
                            @endif
                        </div>
                    </div>
                    <div class="mdl-block">
                        <div class="mdl-block-title">Course</div>
                        <div class="mdl-side-meta">
                            <div><span class="mdl-side-label">Class</span> <span class="mdl-side-value">{{ $classGroup->name ?? '—' }}</span></div>
                            <div><span class="mdl-side-label">Subject</span> <span class="mdl-side-value">{{ $subject->code ?? '—' }}</span></div>
                        </div>
                    </div>
                </aside>

                <section class="mdl-main">
                    <div class="mdl-section" id="section-general">
                        <button class="mdl-section-head" type="button" data-accordion-btn>
                            <span>General</span>
                            <svg class="mdl-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="mdl-section-body" data-accordion-panel>
                            <div class="mdl-info mdl-info--item">
                                <div class="mdl-info-icon" aria-hidden="true">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 10.5L12 3l9 7.5"/><path d="M5 9.5V20h14V9.5"/></svg>
                                </div>
                                <div>
                                <div class="mdl-info-title">Welcome</div>
                                <div class="mdl-info-text">All course materials from your teacher will appear under Resources.</div>
                                </div>
                            </div>

                            <div style="margin-top: 12px;">
                                @if (!empty($classroomMeeting?->meet_link))
                                    @php
                                        $meetTz = config('app.timezone');
                                        $meetJoinUrl = url("/tenant/classes/{$classGroup->id}/{$subject->id}/meet-join");
                                        $hasMeetSchedule = $classroomMeeting->scheduled_start && $classroomMeeting->scheduled_end;
                                        $meetJoinAllowed = $classroomMeeting->isOpenForStudentJoin();
                                        $studentJoinDeadline = $hasMeetSchedule ? $classroomMeeting->studentJoinDeadline() : null;
                                        $lateEntryClosed = $hasMeetSchedule
                                            && $classroomMeeting->restrictsLateEntry()
                                            && $studentJoinDeadline
                                            && now()->gt($studentJoinDeadline)
                                            && $classroomMeeting->scheduled_end
                                            && now()->lte($classroomMeeting->scheduled_end);
                                    @endphp
                                    <div class="mdl-info" style="background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%); border: 1px solid #c7d2fe; padding: 20px;">
                                        <div style="display: flex; align-items: flex-start; gap: 14px;">
                                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #4F46E5 0%, #7c3aed 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2">
                                                    <path d="M15 10l5 5-5 5"/>
                                                    <path d="M4 4v7a4 4 0 0 0 4 4h12"/>
                                                </svg>
                                            </div>
                                            <div style="flex: 1; min-width: 0;">
                                                <div style="font-weight: 800; font-size: 1rem; color: #1e293b;">{{ $classroomMeeting->title }}</div>
                                                @if ($classroomMeeting->description)
                                                    <div style="margin-top: 6px; font-size: 0.85rem; color: #4b5563; line-height: 1.5;">{{ Str::limit($classroomMeeting->description, 120) }}</div>
                                                @endif
                                                @if ($classroomMeeting->scheduled_start || $classroomMeeting->scheduled_end)
                                                    <div style="margin-top: 12px; padding: 12px 14px; background: rgba(255,255,255,0.85); border: 1px solid #a5b4fc; border-radius: 10px;">
                                                        <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; color: #4338ca; margin-bottom: 10px;">When the meet runs</div>
                                                        <div style="font-size: 0.68rem; color: #94a3b8; margin-bottom: 8px; line-height: 1.35;">Times use the school’s configured timezone: <strong style="color:#64748b;">{{ config('app.timezone') }}</strong></div>
                                                        @if ($classroomMeeting->scheduled_start)
                                                            <div style="font-size: 0.9rem; color: #0f172a; margin-bottom: 6px; line-height: 1.45;">
                                                                <span style="color:#64748b; font-weight: 700; display: inline-block; min-width: 3.25rem;">Opens</span>
                                                                {{ $classroomMeeting->scheduled_start->copy()->timezone($meetTz)->format('l, M j, Y \a\t g:i A') }}
                                                            </div>
                                                        @endif
                                                        @if ($classroomMeeting->scheduled_end)
                                                            <div style="font-size: 0.9rem; color: #0f172a; line-height: 1.45;">
                                                                <span style="color:#64748b; font-weight: 700; display: inline-block; min-width: 3.25rem;">Ends</span>
                                                                {{ $classroomMeeting->scheduled_end->copy()->timezone($meetTz)->format('l, M j, Y \a\t g:i A') }}
                                                            </div>
                                                        @endif
                                                        @if ($classroomMeeting->restrictsLateEntry() && $studentJoinDeadline)
                                                            <div style="font-size: 0.82rem; color: #b45309; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #fcd34d; line-height: 1.45;">
                                                                <span style="font-weight: 800;">Last time to join (students):</span>
                                                                {{ $studentJoinDeadline->copy()->timezone($meetTz)->format('l, M j, Y \a\t g:i A') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div style="margin-top: 12px; padding: 10px 12px; background: rgba(255,255,255,0.55); border: 1px dashed #c7d2fe; border-radius: 10px; font-size: 0.82rem; color: #64748b; line-height: 1.45;">
                                                        Your teacher must set both a start and end time before you can join. The Meet link is hidden until the scheduled window opens.
                                                    </div>
                                                @endif
                                                <div style="margin-top: 16px;">
                                                    @if ($meetJoinAllowed)
                                                        <a href="{{ $meetJoinUrl }}" target="_blank" rel="noopener noreferrer" style="background: linear-gradient(135deg, #4F46E5 0%, #7c3aed 100%); color: #fff; border: none; display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; font-weight: 700; text-decoration: none; border-radius: 10px; font-size: 0.9rem;">
                                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                                                            Join Google Meet
                                                        </a>
                                                        <div style="margin-top: 10px; font-size: 0.78rem; color: #64748b; line-height: 1.45; max-width: 36rem;">
                                                            If Google Meet shows <strong style="color:#475569;">Ask to join</strong>, wait on that screen—your teacher (the meeting host) can admit you from their Meet window.
                                                        </div>
                                                    @elseif ($hasMeetSchedule)
                                                        @if (now()->lt($classroomMeeting->scheduled_start))
                                                            <span style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; font-weight: 700; border-radius: 10px; font-size: 0.9rem; background: #e2e8f0; color: #475569; cursor: not-allowed; user-select: none;">
                                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                                                Join opens when the meeting starts
                                                            </span>
                                                            <div style="margin-top: 8px; font-size: 0.8rem; color: #64748b;">The Meet link is not available yet.</div>
                                                        @elseif ($classroomMeeting->hasScheduledMeetEnded())
                                                            <span style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; font-weight: 700; border-radius: 10px; font-size: 0.9rem; background: #e2e8f0; color: #64748b; cursor: not-allowed; user-select: none;">
                                                                Meeting ended
                                                            </span>
                                                            <div style="margin-top: 8px; font-size: 0.8rem; color: #64748b;">The join link is no longer available.</div>
                                                        @elseif ($lateEntryClosed)
                                                            <span style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; font-weight: 700; border-radius: 10px; font-size: 0.9rem; background: #e2e8f0; color: #92400e; cursor: not-allowed; user-select: none;">
                                                                Late entry closed
                                                            </span>
                                                            <div style="margin-top: 8px; font-size: 0.8rem; color: #64748b;">Your teacher only allowed joining within {{ (int) $classroomMeeting->late_entry_minutes }} minutes after the class started. Ask your teacher if you still need access.</div>
                                                        @else
                                                            <span style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; font-weight: 700; border-radius: 10px; font-size: 0.9rem; background: #e2e8f0; color: #64748b;">Join unavailable</span>
                                                        @endif
                                                    @else
                                                        <span style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 22px; font-weight: 700; border-radius: 10px; font-size: 0.9rem; background: #e2e8f0; color: #64748b; cursor: not-allowed; user-select: none;">
                                                            Join unavailable
                                                        </span>
                                                        <div style="margin-top: 8px; font-size: 0.8rem; color: #64748b;">Set both start and end times on this meeting to enable student access during that window.</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="mdl-info mdl-info--item" style="background:#f8fafc;">
                                        <div class="mdl-info-icon" aria-hidden="true">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M8 21h8"/><path d="M12 18v3"/></svg>
                                        </div>
                                        <div>
                                            <div class="mdl-info-title">Classroom meeting</div>
                                            <div class="mdl-info-text">Your teacher will post a Google Meet link for this class.</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @foreach ($lessonsList as $lesson)
                        <div class="mdl-section" id="lesson-{{$lesson->id}}">
                            <button class="mdl-section-head" type="button" data-accordion-btn>
                                <span>{{ $lesson->title }}</span>
                                <svg class="mdl-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </button>
                            <div class="mdl-section-body" data-accordion-panel>
                                @if (collect($lesson->modules)->isEmpty())
                                    <div class="empty-state" style="padding:18px;">
                                        <p class="text-muted" style="margin:0;">No resources in this lesson yet.</p>
                                    </div>
                                @else
                                    <div class="mdl-resources">
                                        @foreach ($lesson->modules as $m)
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
                                                            <div class="mdl-resource-preview image">
                                                                <img src="{{ Storage::url($m->file_path) }}" alt="{{ $m->title }}" loading="lazy">
                                                            </div>
                                                        @elseif(str_contains($m->mime_type ?? '', 'video'))
                                                        <div class="mdl-resource-preview video">
                                                            <video controls preload="metadata">
                                                                <source src="{{ Storage::url($m->file_path) }}" type="{{ $m->mime_type }}">
                                                                Your browser does not support the video tag.
                                                            </video>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="mdl-resource-actions">
                                                @if($m->type === 'link')
                                                    <a href="{{ $m->content }}" target="_blank" class="btn sm ghost">Visit Link</a>
                                                @elseif($m->type === 'doc')
                                                    <button type="button" class="btn sm ghost" data-view-doc="{{ $m->id }}" data-doc-title="{{ $m->title }}" data-doc-content="{{ $m->content }}">View Doc</button>
                                                @elseif(str_contains($m->mime_type ?? '', 'pdf'))
                                                    <button type="button" class="btn sm ghost" data-view-pdf="{{ Storage::url($m->file_path) }}">View Book</button>
                                                @elseif($m->type === 'file')
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
                                                        <span class="mdl-tag">{{ $quiz->questions_count }} Questions</span>
                                                        @if($quiz->time_limit_minutes)
                                                            <span class="mdl-tag">{{ $quiz->time_limit_minutes }} mins</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mdl-resource-actions">
                                                    <a href="{{ url('/tenant/lms/quizzes/'.$quiz->id) }}" class="btn sm primary" style="background: #10B981; border: none;">Start Quiz</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if ($modulesList->isNotEmpty() || (isset($ungroupedQuizzes) && $ungroupedQuizzes->isNotEmpty()))
                    <div class="mdl-section" id="section-resources">
                        <button class="mdl-section-head" type="button" data-accordion-btn>
                            <span>Resources</span>
                            <svg class="mdl-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="mdl-section-body" data-accordion-panel>
                            @if ($modulesList->isEmpty() && (!isset($ungroupedQuizzes) || $ungroupedQuizzes->isEmpty()))
                                <div class="empty-state" style="padding:18px;">
                                    <p class="text-muted" style="margin:0;">No resources yet. Check back later for lessons from your teacher.</p>
                                </div>
                            @else
                                @if($modulesList->isNotEmpty())
                                    <div class="mdl-resources">
                                        @foreach ($modulesList as $m)
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
                                                            <div class="mdl-resource-preview image">
                                                                <img src="{{ Storage::url($m->file_path) }}" alt="{{ $m->title }}" loading="lazy">
                                                            </div>
                                                        @elseif(str_contains($m->mime_type ?? '', 'video'))
                                                        <div class="mdl-resource-preview video">
                                                            <video controls preload="metadata">
                                                                <source src="{{ Storage::url($m->file_path) }}" type="{{ $m->mime_type }}">
                                                                Your browser does not support the video tag.
                                                            </video>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                            <div class="mdl-resource-actions">
                                                @if($m->type === 'link')
                                                    <a href="{{ $m->content }}" target="_blank" class="btn sm ghost">Visit Link</a>
                                                @elseif($m->type === 'doc')
                                                    <button type="button" class="btn sm ghost" data-view-doc="{{ $m->id }}" data-doc-title="{{ $m->title }}" data-doc-content="{{ $m->content }}">View Doc</button>
                                                @elseif(str_contains($m->mime_type ?? '', 'pdf'))
                                                    <button type="button" class="btn sm ghost" data-view-pdf="{{ Storage::url($m->file_path) }}">View Book</button>
                                                @elseif($m->type === 'file')
                                                    <a href="{{ url('/tenant/lms/modules/' . $m->id . '/download') }}" class="btn sm ghost">Download</a>
                                                @endif
                                            </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if(isset($ungroupedQuizzes) && $ungroupedQuizzes->isNotEmpty())
                                    <div class="lms-quiz-list" style="{{ $modulesList->isNotEmpty() ? 'margin-top: 12px; border-top: 1px solid #eef2f7; padding-top: 12px;' : '' }}">
                                        @foreach ($ungroupedQuizzes as $quiz)
                                            <div class="mdl-resource quiz-type" style="border-left: 4px solid #10B981;">
                                                <div class="mdl-resource-icon" style="background: #ecfdf5; color: #10B981;">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                                </div>
                                                <div class="mdl-resource-main">
                                                    <div class="mdl-resource-title">{{ $quiz->title }}</div>
                                                    <div class="mdl-resource-meta">
                                                        <span class="mdl-tag">{{ $quiz->questions_count }} Questions</span>
                                                        @if($quiz->time_limit_minutes)
                                                            <span class="mdl-tag">{{ $quiz->time_limit_minutes }} mins</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mdl-resource-actions">
                                                    <a href="{{ url('/tenant/lms/quizzes/'.$quiz->id) }}" class="btn sm primary" style="background: #10B981; border: none;">Start Quiz</a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    @endif
                </section>
            </div>

            <!-- Modals -->
            <div id="modal-view-pdf" class="mdl-modal">
                <div class="mdl-modal-backdrop"></div>
                <div class="mdl-modal-content" style="max-width: 900px; height: 90vh;">
                    <div class="mdl-modal-header">
                        <h2 class="mdl-modal-title">View Book</h2>
                        <button type="button" class="mdl-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="mdl-modal-body" style="height: calc(100% - 70px); padding: 0;">
                        <iframe id="pdf-viewer-frame" src="" width="100%" height="100%" style="border: none;"></iframe>
                    </div>
                </div>
            </div>

            <div id="modal-view-doc" class="mdl-modal">
                <div class="mdl-modal-backdrop"></div>
                <div class="mdl-modal-content">
                    <div class="mdl-modal-header">
                        <h2 class="mdl-modal-title" id="doc-viewer-title">Document View</h2>
                        <button type="button" class="mdl-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="mdl-modal-body" id="doc-viewer-content" style="max-height: 70vh; overflow-y: auto; line-height: 1.6; color: #334155; padding: 24px;">
                        <!-- Content injected via JS -->
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
    .mdl-breadcrumb {
        font-size: 0.82rem;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }
    .mdl-breadcrumb a { color: #2563eb; text-decoration: none; }
    .mdl-breadcrumb a:hover { text-decoration: underline; }
    .mdl-bread-sep { color: #cbd5e1; }

    .mdl-course-head {
        background: #ffffff;
        border: 1px solid color-mix(in srgb, var(--admin-primary, #334155) 12%, #e5e7eb);
        border-radius: 18px;
        padding: 20px 22px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        box-shadow: 0 6px 18px rgba(15,23,42,0.06);
    }
    .mdl-course-head-badges {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 4px;
    }
    .mdl-course-kicker { font-size: 0.78rem; font-weight: 700; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.04em; }
    .mdl-course-chip {
        font-size: 0.72rem;
        font-weight: 700;
        color: #475569;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 2px 8px;
    }
    .mdl-course-title { margin: 6px 0 0; font-size: 1.6rem; font-weight: 800; color: var(--ink); letter-spacing: -0.01em; }
    .mdl-course-subtitle { margin-top: 4px; color: var(--muted); font-size: 0.9rem; }
    .mdl-course-teacher { margin-top: 8px; font-size: 0.88rem; color: var(--muted); line-height: 1.4; display:flex; align-items:center; gap:6px; }
    .mdl-course-teacher-names { font-weight: 700; color: var(--ink); }
    .mdl-pill {
        display: inline-flex; align-items: center; justify-content: center;
        border-radius: 999px;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 700;
        font-size: 0.78rem;
        padding: 2px 10px;
        white-space: nowrap;
    }

    .mdl-layout { display: grid; grid-template-columns: 300px 1fr; gap: 18px; align-items: start; }
    @media (max-width: 980px) { .mdl-layout { grid-template-columns: 1fr; } }

    .mdl-side { display: flex; flex-direction: column; gap: 12px; }
    .mdl-block {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 14px;
        box-shadow: 0 4px 14px rgba(15,23,42,0.05);
    }
    .mdl-block-title { font-weight: 800; color: var(--ink); font-size: 0.95rem; margin-bottom: 10px; }
    .mdl-nav { display: flex; flex-direction: column; gap: 6px; }
    .mdl-nav-group-label {
        margin: 2px 0 2px;
        font-size: 0.66rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: #94a3b8;
        padding: 0 2px;
    }
    .mdl-nav-empty {
        font-size: 0.82rem;
        color: #94a3b8;
        padding: 4px 8px;
    }
    .mdl-nav-item {
        display: flex; align-items: center;
        padding: 10px 11px;
        border-radius: 10px;
        border: 1px solid transparent;
        color: #0f172a;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .mdl-nav-item:hover { background: #f8fafc; border-color: #e2e8f0; }
    .mdl-nav-item.active { background: color-mix(in srgb, var(--admin-primary, #334155) 12%, #ffffff); border-color: color-mix(in srgb, var(--admin-primary, #334155) 36%, #bfdbfe); color: var(--admin-primary, #334155); font-weight: 700; }
    .mdl-side-meta { display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem; }
    .mdl-side-label { color: var(--muted); font-weight: 600; }
    .mdl-side-value { color: var(--ink); font-weight: 700; }

    .mdl-main { display: flex; flex-direction: column; gap: 12px; }
    .mdl-section {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 5px 16px rgba(15,23,42,0.05);
    }
    .mdl-section-head {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border: none;
        cursor: pointer;
        font-weight: 800;
        font-size: 0.98rem;
        color: #0f172a;
    }
    .mdl-section-head:hover { background: #f8fafc; }
    .mdl-chevron { color: #64748b; transition: transform .15s ease; }
    .mdl-section.open .mdl-chevron { transform: rotate(180deg); }
    .mdl-section-body { padding: 14px 16px 16px; border-top: 1px solid #eef2f7; display: none; }
    .mdl-section.open .mdl-section-body { display: block; }

    .mdl-info {
        border: 1px solid #e2e8f0;
        background: #fbfdff;
        border-radius: 14px;
        padding: 14px;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }
    .mdl-info:hover {
        border-color: color-mix(in srgb, var(--admin-primary, #334155) 28%, #dbe3ee);
        box-shadow: 0 10px 20px rgba(15,23,42,0.07);
        transform: translateY(-1px);
    }
    .mdl-info--item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .mdl-info-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid #dbe3ee;
        background: #ffffff;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .mdl-info-title { font-weight: 800; color: #0f172a; margin-bottom: 5px; font-size: 0.95rem; }
    .mdl-info-text { color: #475569; font-size: 0.94rem; line-height: 1.55; }

    .mdl-resources { display: flex; flex-direction: column; gap: 10px; }
    .mdl-resource {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        padding: 12px;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease, transform .15s ease;
    }
    .mdl-resource:hover {
        border-color: color-mix(in srgb, var(--admin-primary, #334155) 28%, #dbe3ee);
        box-shadow: 0 8px 20px rgba(15,23,42,0.08);
        transform: translateY(-1px);
    }
    .mdl-resource-icon {
        width: 40px; height: 40px;
        border-radius: 10px;
        background: linear-gradient(145deg, #e0f2ff 0%, #dbeafe 100%);
        border: 1px solid rgba(59,130,246,0.25);
        color: #1d4ed8;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .mdl-resource-main { flex: 1; min-width: 0; }
    .mdl-resource-title { font-weight: 800; color: #0f172a; }
    .mdl-resource-desc { margin-top: 4px; color: var(--muted); font-size: 0.9rem; }
    .mdl-resource-meta { margin-top: 8px; display: flex; flex-wrap: wrap; gap: 6px; }
    .mdl-tag {
        font-size: 0.75rem;
        color: #475569;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 999px;
        padding: 2px 8px;
    }
    .mdl-resource-actions { flex-shrink: 0; }

    /* Resource Previews */
    .mdl-resource-preview {
        margin-top: 12px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    .mdl-resource-preview.image img {
        display: block;
        max-width: 100%;
        height: auto;
        max-height: 500px;
        margin: 0 auto;
    }
    .mdl-resource-preview.video video {
        display: block;
        width: 100%;
        max-height: 500px;
        background: #000;
    }
    .mdl-resource-preview.doc {
        padding: 16px;
        font-size: 0.95rem;
        line-height: 1.6;
        color: #334155;
        background: #fff;
        max-height: 400px;
        overflow-y: auto;
    }
    .mdl-resource-preview.pdf iframe {
        display: block;
        border: none;
    }

    /* Modal Styles */
    .mdl-modal {
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
    .mdl-modal.open {
        display: flex;
    }
    .mdl-modal-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(4px);
    }
    .mdl-modal-content {
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
    .mdl-modal-header {
        padding: 20px 24px;
        background: linear-gradient(180deg, #ffffff 0%, #fafbff 100%);
        border-bottom: 1px solid #eef2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .mdl-modal-title {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        color: #0f172a;
    }
    .mdl-modal-close {
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
    .mdl-modal-close:hover {
        background: #f1f5f9;
        color: #334155;
    }
    .mdl-modal-body {
        padding: 24px;
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        const sections = document.querySelectorAll('.mdl-section');
        sections.forEach((s, i) => {
            if (i === 0) s.classList.add('open');
            const btn = s.querySelector('[data-accordion-btn]');
            if (!btn) return;
            btn.addEventListener('click', () => {
                s.classList.toggle('open');
            });
        });        // Modal Close logic
        const closeModals = () => {
            document.querySelectorAll('.mdl-modal').forEach(modal => {
                modal.classList.remove('open');
            });
            document.body.style.overflow = '';
            // Clear PDF iframe source
            const pdfFrame = document.getElementById('pdf-viewer-frame');
            if (pdfFrame) pdfFrame.src = '';
        };        document.querySelectorAll('[data-modal-close], .mdl-modal-backdrop').forEach(el => {
            el.addEventListener('click', closeModals);
        });        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModals();
        });        // PDF Viewer logic
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
        });        // Doc Viewer logic
        const docViewerModal = document.getElementById('modal-view-doc');
        const docViewerTitle = document.getElementById('doc-viewer-title');
        const docViewerContent = document.getElementById('doc-viewer-content');
        document.querySelectorAll('[data-view-doc]').forEach(btn => {
            btn.addEventListener('click', () => {
                const title = btn.getAttribute('data-doc-title');
                const content = btn.getAttribute('data-doc-content');
                if (docViewerModal && docViewerTitle && docViewerContent) {
                    docViewerTitle.textContent = title;
                    docViewerContent.innerHTML = content.replace(/\n/g, '<br>');
                    docViewerModal.classList.add('open');
                    document.body.style.overflow = 'hidden';
                }
            });
        });
    })();
</script>
@endpush
