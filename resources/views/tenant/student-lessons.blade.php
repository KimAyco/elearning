@extends('layouts.app')

@section('title', 'Lessons - School Portal')

@section('content')
<div class="app-shell">
    @include('tenant.partials.sidebar', ['active' => 'class'])

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">Lessons</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
                    <span>{{ auth()->user()->full_name ?? 'User' }}</span>
                </div>
            </div>
        </header>

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
                    <div class="mdl-course-kicker">{{ $subject->code ?? 'SUBJ' }}</div>
                    <h1 class="mdl-course-title">{{ $subject->title ?? 'Lessons' }}</h1>
                    <div class="mdl-course-subtitle">{{ $classGroup->name ?? 'Class' }}</div>
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
                            <a class="mdl-nav-item active" href="#section-general">General</a>
                            @foreach ($lessonsList as $lesson)
                                <a class="mdl-nav-item" href="#lesson-{{$lesson->id}}">{{ $lesson->title }}</a>
                            @endforeach
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
                            <div class="mdl-info">
                                <div class="mdl-info-title">Welcome</div>
                                <div class="mdl-info-text">All course materials from your teacher will appear under Resources.</div>
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
        background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px 16px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 14px;
    }
    .mdl-course-kicker { font-size: 0.78rem; font-weight: 700; color: #1d4ed8; text-transform: uppercase; letter-spacing: 0.04em; }
    .mdl-course-title { margin: 3px 0 0; font-size: 1.15rem; font-weight: 800; color: var(--ink); }
    .mdl-course-subtitle { margin-top: 4px; color: var(--muted); font-size: 0.9rem; }
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

    .mdl-layout { display: grid; grid-template-columns: 280px 1fr; gap: 14px; align-items: start; }
    @media (max-width: 980px) { .mdl-layout { grid-template-columns: 1fr; } }

    .mdl-side { display: flex; flex-direction: column; gap: 12px; }
    .mdl-block {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 12px;
    }
    .mdl-block-title { font-weight: 800; color: var(--ink); font-size: 0.95rem; margin-bottom: 10px; }
    .mdl-nav { display: flex; flex-direction: column; gap: 6px; }
    .mdl-nav-item {
        display: flex; align-items: center;
        padding: 8px 10px;
        border-radius: 10px;
        border: 1px solid transparent;
        color: #0f172a;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .mdl-nav-item:hover { background: #f8fafc; border-color: #e2e8f0; }
    .mdl-nav-item.active { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; font-weight: 700; }
    .mdl-side-meta { display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem; }
    .mdl-side-label { color: var(--muted); font-weight: 600; }
    .mdl-side-value { color: var(--ink); font-weight: 700; }

    .mdl-main { display: flex; flex-direction: column; gap: 12px; }
    .mdl-section {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
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
        font-weight: 800;
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
        border-radius: 12px;
        padding: 12px;
    }
    .mdl-info-title { font-weight: 800; color: #0f172a; margin-bottom: 4px; }
    .mdl-info-text { color: #475569; font-size: 0.92rem; }

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
