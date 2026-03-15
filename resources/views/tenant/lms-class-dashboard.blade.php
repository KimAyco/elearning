@extends('layouts.app')

@section('title', 'Class Dashboard - LMS')

@section('content')
<div class="app-shell lms-dashboard-shell">
    @include('tenant.partials.sidebar', ['active' => 'lms'])

    <div class="main-content">
        <header class="lms-topbar">
            <div class="lms-topbar-left">
                <button class="lms-topbar-menu" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <div class="lms-topbar-brand">
                    <span class="lms-topbar-brand-icon">E</span>
                    <span class="lms-topbar-brand-text"><span class="brand-edu">Edu</span><span class="brand-admin">Admin</span></span>
                </div>
            </div>
            
            <div class="lms-topbar-right">
                <div class="lms-topbar-user">
                    <div class="lms-topbar-avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
                    <span class="lms-topbar-username">{{ auth()->user()->full_name ?? 'User' }}</span>
                    <svg class="lms-topbar-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
            </div>
        </header>

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
                </div>
                <div class="mdl-course-head-actions">
                    @php
                        $lessonResourceCount = collect($lessons ?? [])->sum(fn($ls) => collect($ls->modules ?? [])->count());
                        $totalResources = ($modules ?? collect())->count() + $lessonResourceCount;
                    @endphp
                    <span class="mdl-pill">{{ $totalResources }} resources</span>
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
                            <a class="mdl-nav-item active" href="#section-general">General</a>
                            <a class="mdl-nav-item" href="#section-resources">Resources</a>
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
                        <button type="button" class="mdl-nav-item" data-fab-target="modal-view-students" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 20px; text-decoration: none; color: #1e293b; transition: all 0.2s; border: 1px solid #e8f0fe; border-radius: 12px; background: #fff; cursor: pointer; width: 100%;">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(145deg, #6366f1 0%, #8b5cf6 100%); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </div>
                                <div style="flex: 1; text-align: left;">
                                    <div style="font-size: 0.9rem; font-weight: 700; color: #0f172a;">View All Students</div>
                                    <div style="font-size: 0.75rem; color: #94a3b8;">{{ ($enrolledStudents ?? collect())->count() }} enrolled</div>
                                </div>
                            </div>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
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
                    <div class="mdl-section" id="section-general">
                        <button class="mdl-section-head" type="button" data-accordion-btn>
                            <span>General</span>
                            <svg class="mdl-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="mdl-section-body" data-accordion-panel>
                            <div class="mdl-info">
                                <div class="mdl-info-title">Teacher tools</div>
                                <div class="mdl-info-text">Upload files under “Add resource”. Students will see them in their course page.</div>
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
                                                            <img src="{{ Storage::url($m->file_path) }}" alt="{{ $m->title }}" loading="lazy">
                                                        </div>
                                                    @elseif(str_contains($m->mime_type ?? '', 'video'))
                                                        <div class="lms-resource-preview video">
                                                            <video controls preload="metadata">
                                                                <source src="{{ Storage::url($m->file_path) }}" type="{{ $m->mime_type }}">
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
                                                @else
                                                    <a href="{{ url('/tenant/lms/modules/' . $m->id . '/download') }}" class="btn sm ghost">Download</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    @foreach (($lessons ?? collect()) as $lesson)
                    <div class="mdl-section" id="lesson-{{ $lesson->id }}">
                        <button class="mdl-section-head" type="button" data-accordion-btn>
                            <span>{{ $lesson->title }}</span>
                            <svg class="mdl-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                        <div class="mdl-section-body" data-accordion-panel>
                            @if (($lesson->modules ?? collect())->isEmpty())
                                <div class="lms-dash-empty" style="padding:18px;">
                                    <p style="margin:0;">No resources in this lesson yet.</p>
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
                                                            <img src="{{ Storage::url($m->file_path) }}" alt="{{ $m->title }}" loading="lazy">
                                                        </div>
                                                    @elseif(str_contains($m->mime_type ?? '', 'video'))
                                                        <div class="lms-resource-preview video">
                                                            <video controls preload="metadata">
                                                                <source src="{{ Storage::url($m->file_path) }}" type="{{ $m->mime_type }}">
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

                            <div style="margin-top:12px;">
                                <form method="post" action="{{ url('/tenant/lms/classes/' . $classGroup->id . '/' . $subject->id . '/modules') }}" enctype="multipart/form-data" class="lms-upload-form">
                                    @csrf
                                    <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                                    <div class="lms-form-group">
                                        <label>Add resource to {{ $lesson->title }}</label>
                                        <input name="title" type="text" value="" required maxlength="140" placeholder="e.g. Slides or Handout">
                                    </div>
                                    <div class="lms-form-group">
                                        <label>Description <span class="lms-optional">(optional)</span></label>
                                        <textarea name="description" rows="2" maxlength="2000" placeholder="Short notes for students..."></textarea>
                                    </div>
                                    <div class="lms-form-group">
                                        <label>File</label>
                                        <input name="file" type="file" required class="lms-file-input">
                                    </div>
                                    <button class="lms-btn-primary" type="submit">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                                        <span class="lms-btn-text">Upload to lesson</span>
                                    </button>

                                    <div class="lms-upload-progress-container" style="display:none; margin-top:16px;">
                                        <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.75rem; font-weight:700; color:#4C97FF;">
                                            <span>Uploading...</span>
                                            <span class="lms-upload-percentage">0%</span>
                                        </div>
                                        <div class="lms-progress-bg">
                                            <div class="lms-upload-progress-bar lms-progress-fill"></div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach

                </section>
            </div>

            <div class="lms-fab-container">
                <button type="button" class="lms-fab lms-fab-lesson" title="Add lesson" data-fab-target="modal-create-lesson">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span>Add lesson</span>
                </button>
                <button type="button" class="lms-fab lms-fab-resource" title="Add resource" data-fab-target="modal-upload">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                    <span>Add resource</span>
                </button>
                <button type="button" class="lms-fab lms-fab-quiz" title="Add quiz" data-fab-target="modal-create-quiz">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    <span>Add quiz</span>
                </button>
            </div>

            <!-- Modals -->
            <div id="modal-create-lesson" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Create New Lesson</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <form method="post" action="{{ url('/tenant/lms/classes/' . $classGroup->id . '/' . $subject->id . '/lessons') }}" class="lms-upload-form">
                            @csrf
                            <div class="lms-form-group">
                                <label>Lesson title</label>
                                <input name="title" type="text" value="" required maxlength="140" placeholder="e.g. Lesson 1 - Introduction">
                            </div>
                            <button class="lms-btn-primary" type="submit">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                                Create lesson
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="modal-upload" class="lms-modal">
                <div class="lms-modal-backdrop"></div>
                <div class="lms-modal-content">
                    <div class="lms-modal-header">
                        <h2 class="lms-modal-title">Add Resource</h2>
                        <button type="button" class="lms-modal-close" data-modal-close>&times;</button>
                    </div>
                    <div class="lms-modal-body">
                        <p class="lms-dash-upload-hint">Add files, links, or create documents for students.</p>
                        <form method="post" action="{{ url('/tenant/lms/classes/' . $classGroup->id . '/' . $subject->id . '/modules') }}" enctype="multipart/form-data" class="lms-upload-form">
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
                                <label>Lesson <span class="lms-optional">(optional)</span></label>
                                <select name="lesson_id" class="lms-input">
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
                                    <textarea name="content" rows="10" placeholder="Write your document content here..." class="lms-input"></textarea>
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

            <div id="modal-create-quiz" class="lms-modal">
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
                                <label>Lesson <span class="lms-optional">(optional)</span></label>
                                <select name="lesson_id" class="lms-input">
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
                            <button class="lms-btn-primary" type="submit" style="width:100%; margin-top:12px; background:linear-gradient(135deg, #10B981 0%, #059669 100%);">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                                Create Quiz
                            </button>
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
        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
/* LMS Dashboard: soft blues, oranges, subtle gradients (EduAdmin-style) */
.lms-dashboard-shell .main-content {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 50%, #eef4fc 100%);
}
.lms-dashboard-shell .page-body { padding: 20px 24px 32px; }

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
    border: 1px solid #e2e8f0; border-radius: 10px;
    font-size: 0.875rem; color: #0f172a;
    background: #fff;
}
.lms-form-group input[type="text"]:focus,
.lms-form-group textarea:focus {
    outline: none; border-color: #4C97FF; box-shadow: 0 0 0 3px rgba(76,151,255,0.15);
}
.lms-optional { font-weight: 400; color: #94a3b8; }
.lms-file-input { font-size: 0.8rem; }
.lms-btn-primary {
    display: inline-flex; align-items: center; justify-content: center; gap: 8px;
    padding: 12px 20px;
    font-size: 0.875rem; font-weight: 600; color: #fff;
    background: linear-gradient(145deg, #4C97FF 0%, #3b82f6 50%, #2563eb 100%);
    border: none; border-radius: 10px;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(76,151,255,0.35);
    transition: box-shadow 0.15s, transform 0.15s;
}
.lms-btn-primary:hover {
    box-shadow: 0 4px 16px rgba(76,151,255,0.45);
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
.mdl-layout { display: grid; grid-template-columns: 280px 1fr; gap: 16px; align-items: start; }
@media (max-width: 980px) { .mdl-layout { grid-template-columns: 1fr; } }
.mdl-side { display: flex; flex-direction: column; gap: 12px; }
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

.mdl-main { display: flex; flex-direction: column; gap: 12px; }
.mdl-section {
    background: #fff;
    border: 1px solid rgba(76,151,255,0.12);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(15,23,42,0.04);
}
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
.mdl-resource-actions { flex-shrink: 0; }

/* Floating Action Buttons */
.lms-fab-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    z-index: 1000;
}
.lms-fab {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border: none;
    border-radius: 30px;
    font-weight: 700;
    font-size: 0.9rem;
    color: #fff;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15), 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.2s;
}
.lms-fab:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}
.lms-fab-lesson {
    background: linear-gradient(135deg, #FF9966 0%, #FF5E62 100%);
}
.lms-fab-resource {
    background: linear-gradient(135deg, #4C97FF 0%, #3b82f6 100%);
}
.lms-fab-quiz {
    background: linear-gradient(135deg, #6EE7B7 0%, #10B981 100%);
}
.lms-fab span {
    white-space: nowrap;
}
@media (max-width: 600px) {
    .lms-fab span { display: none; }
    .lms-fab { padding: 14px; border-radius: 50%; }
}

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

.lms-input {
    width: 100%; padding: 10px 12px;
    border: 1px solid #e2e8f0; border-radius: 10px;
    font-size: 0.875rem; color: #0f172a;
    background: #fff;
    outline: none;
}
.lms-input:focus {
    border-color: #4C97FF; box-shadow: 0 0 0 3px rgba(76,151,255,0.15);
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
        const sections = document.querySelectorAll('.mdl-section');
        sections.forEach((s, i) => {
            if (i === 0) s.classList.add('open');
            const btn = s.querySelector('[data-accordion-btn]');
            if (!btn) return;
            btn.addEventListener('click', () => {
                s.classList.toggle('open');
            });
        });

        // FAB behavior (Open Modals) - handle any element with data-fab-target
        const fabTriggers = document.querySelectorAll('[data-fab-target]');
        fabTriggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                const targetId = trigger.getAttribute('data-fab-target');
                const targetModal = document.getElementById(targetId);
                if (targetModal) {
                    targetModal.classList.add('open');
                    document.body.style.overflow = 'hidden'; // Prevent scrolling
                }
            });
        });

        // Close Modals
        const closeModals = () => {
            document.querySelectorAll('.lms-modal').forEach(modal => {
                modal.classList.remove('open');
            });
            document.body.style.overflow = '';
            // Clear PDF iframe source when closing to stop background loading
            const pdfFrame = document.getElementById('pdf-viewer-frame');
            if (pdfFrame) pdfFrame.src = '';
        };

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