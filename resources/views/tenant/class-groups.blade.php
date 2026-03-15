@extends('layouts.app')

@section('title', 'Class Groups - Admin')

@section('content')
<div class="app-shell">
    @include('tenant.partials.sidebar', ['active' => 'admin'])

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">Class Groups</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
                    <span>{{ auth()->user()->full_name ?? 'User' }}</span>
                </div>
            </div>
        </header>

        <main class="page-body class-groups-page">
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <a href="{{ url('/tenant/admin?tab=classes') }}">Admin · Classes</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <span>Class Groups</span>
                </div>
                <h1>Class Groups</h1>
                <p>Manage capacity and enrollment status for {{ $currentTermLabel ?? 'current semester' }}.</p>
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

            <div class="class-groups-section">
                <div class="class-groups-section-head">
                    <h2 class="class-groups-section-title">Current Semester Class Groups</h2>
                    <span class="badge blue">{{ $classGroupsCurrentTerm->count() }} groups</span>
                </div>
                <div class="class-groups-sort">
                    <span class="class-groups-sort-label">Sort by</span>
                    @php
                        $baseUrl = url('/tenant/admin/class-groups');
                        $term = request('term_code') ? '&term_code=' . request('term_code') : '';
                    @endphp
                    <a href="{{ $baseUrl }}?sort=name&dir={{ ($sortBy ?? '') === 'name' && ($sortDir ?? 'asc') === 'asc' ? 'desc' : 'asc' }}{{ $term }}" class="class-groups-sort-btn class-groups-sort-btn--name {{ ($sortBy ?? '') === 'name' ? 'active' : '' }}">
                        Name @if(($sortBy ?? '') === 'name') {{ ($sortDir ?? 'asc') === 'asc' ? '↑' : '↓' }} @endif
                    </a>
                    <a href="{{ $baseUrl }}?sort=program&dir={{ ($sortBy ?? '') === 'program' && ($sortDir ?? 'asc') === 'asc' ? 'desc' : 'asc' }}{{ $term }}" class="class-groups-sort-btn class-groups-sort-btn--program {{ ($sortBy ?? '') === 'program' ? 'active' : '' }}">
                        Program @if(($sortBy ?? '') === 'program') {{ ($sortDir ?? 'asc') === 'asc' ? '↑' : '↓' }} @endif
                    </a>
                    <a href="{{ $baseUrl }}?sort=year&dir={{ ($sortBy ?? '') === 'year' && ($sortDir ?? 'asc') === 'asc' ? 'desc' : 'asc' }}{{ $term }}" class="class-groups-sort-btn class-groups-sort-btn--year {{ ($sortBy ?? '') === 'year' ? 'active' : '' }}">
                        Year @if(($sortBy ?? '') === 'year') {{ ($sortDir ?? 'asc') === 'asc' ? '↑' : '↓' }} @endif
                    </a>
                    <a href="{{ $baseUrl }}?sort=sessions&dir={{ ($sortBy ?? '') === 'sessions' && ($sortDir ?? 'asc') === 'asc' ? 'desc' : 'asc' }}{{ $term }}" class="class-groups-sort-btn class-groups-sort-btn--sessions {{ ($sortBy ?? '') === 'sessions' ? 'active' : '' }}">
                        Sessions @if(($sortBy ?? '') === 'sessions') {{ ($sortDir ?? 'asc') === 'asc' ? '↑' : '↓' }} @endif
                    </a>
                </div>
                <div class="class-groups-grid">
                    @forelse($classGroupsCurrentTerm as $g)
                        <div class="class-group-card">
                            <div class="class-group-card-accent"></div>
                            <div class="class-group-card-header">
                                <span class="class-group-card-letter" aria-hidden="true">{{ strtoupper(substr($g->name, 0, 1)) }}</span>
                                <div class="class-group-card-header-text">
                                    <h3 class="class-group-card-title">{{ $g->name }}</h3>
                                    <span class="class-group-card-meta">Y{{ $g->year_level }} · {{ (int) ($g->sessions_count ?? 0) }} sessions</span>
                                </div>
                            </div>
                            <p class="class-group-card-program">{{ $g->program->code ?? '' }} — {{ \Illuminate\Support\Str::limit($g->program->name ?? '', 50) }}</p>
                            <form method="post" action="{{ url('/tenant/admin/classes/groups/' . $g->id . '/settings') }}" class="class-group-card-form">
                                @csrf
                                <div class="class-group-card-fields">
                                    <label class="class-group-card-label">Capacity</label>
                                    <input type="number" name="student_capacity" min="1" max="500" value="{{ (int) ($g->student_capacity ?? 10) }}" required class="class-group-card-input" title="Capacity">
                                </div>
                                <div class="class-group-card-fields">
                                    <label class="class-group-card-label">Enrollment</label>
                                    <select name="is_enrollment_open" required class="class-group-card-select class-group-card-select--{{ (bool) ($g->is_enrollment_open ?? false) ? 'open' : 'closed' }}">
                                        <option value="1" {{ (bool) ($g->is_enrollment_open ?? false) ? 'selected' : '' }}>Open</option>
                                        <option value="0" {{ !(bool) ($g->is_enrollment_open ?? false) ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>
                                <button class="btn sm primary class-group-card-submit" type="submit">Save</button>
                            </form>
                        </div>
                    @empty
                        <div class="class-groups-empty">No class groups for {{ $currentTermLabel ?? 'current semester' }}.</div>
                    @endforelse
                </div>
            </div>

            <p class="class-groups-back">
                <a href="{{ url('/tenant/admin?tab=classes') }}" class="btn ghost">← Back to Classes</a>
            </p>
        </main>
    </div>
</div>

@push('styles')
<style>
/* Class groups page — theme */
.class-groups-page {
    --cg-accent: #2563eb;
    --cg-accent-soft: #dbeafe;
    --cg-emerald: #059669;
    --cg-emerald-soft: #d1fae5;
    --cg-amber: #d97706;
    --cg-amber-soft: #fef3c7;
    --cg-violet: #7c3aed;
    --cg-violet-soft: #ede9fe;
    --cg-rose: #e11d48;
    --cg-rose-soft: #ffe4e6;
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100%;
    padding-bottom: 24px;
}
.class-groups-page .page-header {
    margin-bottom: 24px;
    padding: 20px 24px;
    background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.class-groups-page .page-header h1 {
    color: #1e293b;
    font-weight: 800;
    letter-spacing: -0.02em;
}
.class-groups-page .page-header p { color: #64748b; }
.class-groups-page .breadcrumb a { color: var(--cg-accent); font-weight: 500; }
.class-groups-back { margin-top: 24px; }
.class-groups-back .btn { border-radius: 10px; }

.class-groups-section {
    margin-bottom: 28px;
    padding: 20px 24px;
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.class-groups-section-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    padding-bottom: 14px;
    border-bottom: 2px solid var(--cg-accent-soft);
}
.class-groups-section-title {
    margin: 0;
    font-size: 1.15rem;
    font-weight: 700;
    color: #1e293b;
}
.class-groups-section-head .badge.blue {
    background: linear-gradient(135deg, var(--cg-accent) 0%, #1d4ed8 100%);
    color: #fff;
    padding: 6px 12px;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.8rem;
}
.class-groups-sort {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
    padding: 14px 16px;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}
.class-groups-sort-label {
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .05em;
    color: #64748b;
    margin-right: 6px;
}
.class-groups-sort-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 8px 14px;
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    text-decoration: none;
    transition: all .2s ease;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
}
.class-groups-sort-btn:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
    color: #1e293b;
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.class-groups-sort-btn--name.active { background: var(--cg-violet-soft); border-color: var(--cg-violet); color: #5b21b6; }
.class-groups-sort-btn--program.active { background: var(--cg-accent-soft); border-color: var(--cg-accent); color: #1d4ed8; }
.class-groups-sort-btn--year.active { background: var(--cg-amber-soft); border-color: var(--cg-amber); color: #b45309; }
.class-groups-sort-btn--sessions.active { background: var(--cg-emerald-soft); border-color: var(--cg-emerald); color: #047857; }
.class-groups-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 18px;
}
.class-group-card {
    position: relative;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    display: flex;
    flex-direction: column;
    gap: 12px;
    transition: box-shadow .2s, transform .15s;
    overflow: hidden;
}
.class-group-card:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
}
.class-group-card-accent {
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, var(--cg-accent) 0%, #3b82f6 100%);
    border-radius: 4px 0 0 4px;
}
.class-group-card:nth-child(4n+2) .class-group-card-accent { background: linear-gradient(180deg, var(--cg-emerald) 0%, #10b981 100%); }
.class-group-card:nth-child(4n+3) .class-group-card-accent { background: linear-gradient(180deg, var(--cg-violet) 0%, #8b5cf6 100%); }
.class-group-card:nth-child(4n+4) .class-group-card-accent { background: linear-gradient(180deg, var(--cg-amber) 0%, #f59e0b 100%); }
.class-group-card-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 2px;
}
.class-group-card-letter {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    font-weight: 800;
    color: #fff;
    background: linear-gradient(135deg, var(--cg-accent) 0%, #1d4ed8 100%);
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
}
.class-group-card:nth-child(4n+2) .class-group-card-letter { background: linear-gradient(135deg, var(--cg-emerald) 0%, #047857 100%); box-shadow: 0 2px 6px rgba(5, 150, 105, 0.35); }
.class-group-card:nth-child(4n+3) .class-group-card-letter { background: linear-gradient(135deg, var(--cg-violet) 0%, #5b21b6 100%); box-shadow: 0 2px 6px rgba(124, 58, 237, 0.35); }
.class-group-card:nth-child(4n+4) .class-group-card-letter { background: linear-gradient(135deg, var(--cg-amber) 0%, #b45309 100%); box-shadow: 0 2px 6px rgba(217, 119, 6, 0.35); }
.class-group-card-header-text { min-width: 0; }
.class-group-card-title {
    margin: 0 0 4px;
    font-size: 1.05rem;
    font-weight: 700;
    color: #1e293b;
}
.class-group-card-meta {
    font-size: 0.8rem;
    color: #64748b;
    font-weight: 500;
}
.class-group-card-program {
    margin: 0;
    font-size: 0.85rem;
    color: #64748b;
    line-height: 1.4;
    padding-left: 52px;
}
.class-group-card-form {
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 10px 14px;
}
.class-group-card-fields { display: flex; flex-direction: column; gap: 4px; }
.class-group-card-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
    color: #64748b;
}
.class-group-card-input {
    width: 72px;
    padding: 8px 10px;
    font-size: 0.9rem;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #f8fafc;
}
.class-group-card-input:focus {
    outline: none;
    border-color: var(--cg-accent);
    box-shadow: 0 0 0 3px var(--cg-accent-soft);
}
.class-group-card-select {
    min-width: 108px;
    padding: 8px 12px;
    font-size: 0.9rem;
    border-radius: 10px;
    font-weight: 500;
    border: 1px solid #e2e8f0;
    background: #f8fafc;
}
.class-group-card-select--open { border-color: #86efac; background: var(--cg-emerald-soft); color: #047857; }
.class-group-card-select--closed { border-color: #fecaca; background: var(--cg-rose-soft); color: #b91c1c; }
.class-group-card-submit {
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--cg-accent) 0%, #1d4ed8 100%) !important;
    border: none !important;
    border-radius: 10px;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
}
.class-group-card-submit:hover { box-shadow: 0 4px 10px rgba(37, 99, 235, 0.4); }
.class-groups-empty {
    grid-column: 1 / -1;
    padding: 32px;
    text-align: center;
    color: #64748b;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 14px;
    border: 2px dashed #cbd5e1;
    font-weight: 500;
}
@media (max-width: 640px) {
    .class-groups-grid { grid-template-columns: 1fr; }
    .class-group-card-program { padding-left: 0; }
}
</style>
@endpush
@endsection
