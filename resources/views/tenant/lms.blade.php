@extends('layouts.app')

@section('title', 'LMS - School Portal')

@section('content')
<div class="app-shell">
    @include('tenant.partials.sidebar', ['active' => 'lms'])

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">LMS</span>
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
                $teachable = $teachableSubjects ?? collect();
                $sessions = $weeklySessions ?? collect();

                $teacherClasses = $sessions
                    ->map(fn ($s) => $s->classGroup)
                    ->filter()
                    ->unique('id')
                    ->values();

                $classSubjectRows = $sessions
                    ->filter(fn ($s) => optional($s->classGroup)->id !== null && optional($s->subject)->id !== null)
                    ->map(fn ($s) => [
                        'class' => $s->classGroup,
                        'subject' => $s->subject,
                    ])
                    ->unique(fn ($row) => ($row['class']->id ?? 0) . '-' . ($row['subject']->id ?? 0))
                    ->sortBy([
                        fn ($row) => strtoupper($row['class']->name ?? ''),
                        fn ($row) => strtoupper($row['subject']->code ?? ''),
                    ])
                    ->values();
            @endphp

            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <span>LMS</span>
                </div>
                <h1>Teacher LMS</h1>
                <p>Manage your subjects and classes from one page.</p>
            </div>

            @if (session('status'))
                <div class="alert success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="card">
                <div class="card-header" style="align-items:center; gap:12px;">
                    <div>
                        <h2>Your Classes</h2>
                        <p style="margin:2px 0 0; font-size:0.85rem; color:var(--muted);">
                            All class groups where you are assigned as teacher.
                        </p>
                    </div>
                    <div style="margin-left:auto; display:flex; align-items:center; gap:8px;">
                        <span class="badge blue">{{ $teacherClasses->count() }} classes</span>
                        <a href="{{ url('/tenant/grades') }}" class="btn primary sm">Open Grade Workflow</a>
                    </div>
                </div>

                @if ($classSubjectRows->isEmpty())
                    <div class="empty-state" style="padding:24px;">
                        <p class="text-muted" style="margin:0;">No classes assigned yet. Once schedules are created for you, they will appear here.</p>
                    </div>
                @else
                    <div class="mdl-course-grid">
                        @foreach ($classSubjectRows as $row)
                            @php $class = $row['class']; $subject = $row['subject']; @endphp
                            <a class="mdl-course-card" href="{{ url('/tenant/lms/classes/' . ($class->id ?? 0) . '/' . ($subject->id ?? 0)) }}">
                                <div class="mdl-course-card-top">
                                    <div class="mdl-course-badge">{{ $subject->code ?? 'SUBJ' }}</div>
                                    @if(optional($class->semester)->term_code)
                                        <div class="mdl-course-term">{{ $class->semester->term_code }}</div>
                                    @endif
                                </div>
                                <div class="mdl-course-card-title">{{ $subject->title ?? 'Course' }}</div>
                                <div class="mdl-course-card-sub">{{ $class->name ?? 'Class group' }}</div>
                                <div class="mdl-course-card-meta">
                                    @if(optional($class->program)->code)
                                        <span class="mdl-tag">{{ $class->program->code }}</span>
                                    @endif
                                    @if(optional($class->program)->name)
                                        <span class="mdl-tag">{{ Str::limit($class->program->name, 26) }}</span>
                                    @endif
                                </div>
                                <div class="mdl-course-card-foot">
                                    <span class="mdl-course-open">Open course</span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
    .mdl-course-grid {
        padding: 14px 16px 18px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 12px;
    }
    .mdl-course-card {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: 8px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
        padding: 14px;
        box-shadow: 0 1px 3px rgba(15,23,42,0.05);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        position: relative;
        overflow: hidden;
    }
    .mdl-course-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 26px rgba(15,23,42,0.12);
        border-color: #93c5fd;
    }
    .mdl-course-card:focus-visible { outline: 2px solid #3b82f6; outline-offset: 2px; }

    .mdl-course-card-top { display:flex; align-items:center; justify-content:space-between; gap:10px; }
    .mdl-course-badge {
        border-radius: 999px;
        border: 1px solid #bfdbfe;
        padding: 3px 10px;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 800;
        font-size: 0.78rem;
        letter-spacing: 0.03em;
    }
    .mdl-course-term {
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        padding: 3px 10px;
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.78rem;
    }
    .mdl-course-card-title { font-weight: 900; color: #0f172a; line-height: 1.25; }
    .mdl-course-card-sub { color: #64748b; font-size: 0.9rem; }
    .mdl-course-card-meta { display:flex; flex-wrap:wrap; gap:6px; margin-top: 2px; }
    .mdl-tag {
        border-radius: 999px;
        border: 1px solid var(--border);
        padding: 2px 8px;
        background: #f1f5f9;
        font-size: 0.75rem;
        color: #475569;
        font-weight: 700;
    }
    .mdl-course-card-foot {
        margin-top: 6px;
        display:flex; align-items:center; justify-content:space-between;
        color: #1d4ed8;
        font-weight: 800;
        font-size: 0.85rem;
    }
</style>
@endpush

