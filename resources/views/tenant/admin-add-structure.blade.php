@extends('layouts.app')

@section('title', 'Add Department & Program - Admin')

@section('content')
<div class="app-shell">
    @include('tenant.partials.sidebar', ['active' => 'admin'])

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <span class="topbar-title">Add Department & Program</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
                    <span>{{ auth()->user()->full_name ?? 'User' }}</span>
                </div>
            </div>
        </header>

        <main class="page-body add-structure-page">
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <a href="{{ url('/tenant/admin?tab=structure') }}">Admin · Structure</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <span>Add Department & Program</span>
                </div>
                <h1>Add Department & Program</h1>
                <p>Create new departments and programs for your academic structure.</p>
            </div>

            @if (session('status'))
                <div class="alert success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="admin-structure-panels">
                <section class="admin-structure-panel">
                    <h3 class="admin-structure-panel-title">Add Department</h3>
                    <form method="post" action="{{ url('/tenant/admin/departments') }}" class="admin-structure-form">
                        @csrf
                        @if($colleges->isNotEmpty())
                            <input type="hidden" name="college_id" value="{{ $colleges->first()->id }}">
                        @endif
                        <div class="admin-structure-form-grid">
                            <div class="admin-structure-field">
                                <label class="admin-structure-label">Code</label>
                                <input name="code" placeholder="e.g. CE" required class="admin-structure-input">
                            </div>
                            <div class="admin-structure-field admin-structure-field--grow">
                                <label class="admin-structure-label">Name</label>
                                <input name="name" placeholder="Civil Engineering" required class="admin-structure-input">
                            </div>
                            <div class="admin-structure-field admin-structure-field--action">
                                @if($colleges->isNotEmpty())
                                    <span class="admin-structure-hint">{{ $colleges->first()->name }}</span>
                                @endif
                                <button class="btn primary sm" type="submit">Add Department</button>
                            </div>
                        </div>
                    </form>
                </section>
                <section class="admin-structure-panel">
                    <h3 class="admin-structure-panel-title">Add Program</h3>
                    <form method="post" action="{{ url('/tenant/admin/programs') }}" class="admin-structure-form">
                        @csrf
                        <div class="admin-structure-form-grid admin-structure-form-grid--program">
                            <div class="admin-structure-field">
                                <label class="admin-structure-label">Department</label>
                                <select name="department_id" required class="admin-structure-input">
                                    <option value="">Select department</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="admin-structure-field">
                                <label class="admin-structure-label">Code</label>
                                <input name="code" placeholder="e.g. BSCE" required class="admin-structure-input">
                            </div>
                            <div class="admin-structure-field admin-structure-field--grow">
                                <label class="admin-structure-label">Name</label>
                                <input name="name" placeholder="Bachelor of Science in Civil Engineering" required class="admin-structure-input">
                            </div>
                            <div class="admin-structure-field">
                                <label class="admin-structure-label">Degree</label>
                                <select name="degree_level" required class="admin-structure-input">
                                    <option value="bachelor">Bachelor</option>
                                    <option value="master">Master</option>
                                    <option value="doctorate">Doctorate</option>
                                    <option value="diploma">Diploma</option>
                                    <option value="certificate">Certificate</option>
                                </select>
                            </div>
                            <div class="admin-structure-field admin-structure-field--action">
                                <button class="btn primary sm" type="submit">Add Program</button>
                            </div>
                        </div>
                    </form>
                </section>
            </div>

            <p class="add-structure-back">
                <a href="{{ url('/tenant/admin?tab=structure') }}" class="btn ghost">← Back to Structure</a>
            </p>
        </main>
    </div>
</div>

@push('styles')
<style>
.add-structure-page .page-header { margin-bottom: 24px; }
.add-structure-page .breadcrumb a { color: var(--accent); }
.add-structure-back { margin-top: 24px; }

.admin-structure-panels {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}
.admin-structure-panel {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.admin-structure-panel-title {
    margin: 0 0 16px;
    font-size: 0.95rem;
    font-weight: 700;
    color: #1e293b;
}
.admin-structure-form-grid {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-end;
    gap: 14px 18px;
}
.admin-structure-form-grid--program { gap: 14px 18px; }
.admin-structure-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
    min-width: 0;
}
.admin-structure-field--grow { flex: 1 1 180px; }
.admin-structure-field--action {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    margin-left: auto;
}
.admin-structure-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: #64748b;
}
.admin-structure-input {
    padding: 8px 12px;
    font-size: 0.9rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    min-width: 100px;
}
.admin-structure-input:focus {
    outline: none;
    border-color: #94a3b8;
    box-shadow: 0 0 0 2px rgba(148, 163, 184, 0.2);
}
.admin-structure-field--grow .admin-structure-input { min-width: 140px; }
.admin-structure-hint { font-size: 0.8rem; color: #94a3b8; }
@media (max-width: 900px) {
    .admin-structure-panels { grid-template-columns: 1fr; }
    .admin-structure-form-grid { align-items: stretch; }
    .admin-structure-field--action { margin-left: 0; margin-top: 4px; }
}
</style>
@endpush
@endsection
