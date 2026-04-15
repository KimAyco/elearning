@extends('layouts.app')

@section('content')
<div class="sa-shell">
    @include('superadmin.partials.sidebar')

    <div class="sa-main">
        <header class="sa-topbar">
            <div class="sa-topbar-left">
                <button type="button" class="sa-hamburger hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <div class="sa-topbar-titles">
                    <span class="sa-topbar-kicker">Nehemiah · Platform control</span>
                    <span class="sa-topbar-title">@yield('sa_heading', 'Dashboard')</span>
                </div>
            </div>
            <div class="sa-topbar-right">
                <div class="sa-user-pill">
                    <span class="sa-user-avatar" aria-hidden="true">SA</span>
                    <span class="sa-user-label">Super Admin</span>
                </div>
            </div>
        </header>

        <main class="sa-body">
            @if (session('status'))
                <div class="alert success sa-flash">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert error sa-flash">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @yield('sa_content')
        </main>
    </div>
</div>
<div class="sidebar-overlay"></div>
@endsection

@push('styles')
<style>
    .sa-shell {
        display: flex;
        min-height: 100vh;
        background: var(--bg);
    }

    .sa-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-width: 0;
        margin-left: var(--sa-sidebar-w, 260px);
    }

    .sa-topbar {
        height: var(--topbar-h);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 22px 0 16px;
        background: var(--surface);
        border-bottom: 1px solid var(--border);
        box-shadow: var(--shadow-sm);
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .sa-topbar-left {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sa-hamburger {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 6px;
        color: var(--ink-2);
        border-radius: 8px;
    }

    .sa-hamburger:hover {
        background: var(--bg-2);
    }

    .sa-topbar-kicker {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }

    .sa-topbar-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--ink);
    }

    .sa-user-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 12px 6px 6px;
        border-radius: 999px;
        background: var(--bg-2);
        border: 1px solid var(--border);
    }

    .sa-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, #c55a2e, #7c3aed);
    }

    .sa-user-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--ink-2);
    }

    .sa-body {
        flex: 1;
        padding: 24px 28px 40px;
    }

    .sa-flash {
        margin-bottom: 20px;
    }

    .sa-page-head {
        margin-bottom: 22px;
    }

    .sa-breadcrumb {
        font-size: 0.78rem;
        color: var(--muted);
        margin-bottom: 8px;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        align-items: center;
    }

    .sa-breadcrumb a {
        color: var(--muted);
        text-decoration: none;
    }

    .sa-breadcrumb a:hover {
        color: var(--accent);
        text-decoration: none;
    }

    .sa-page-head h1 {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--ink);
        margin: 0 0 6px;
    }

    .sa-page-head p {
        margin: 0;
        font-size: 0.92rem;
        color: var(--ink-2);
        max-width: 640px;
    }

    .sa-quick-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 1024px) {
        .sa-quick-grid { grid-template-columns: 1fr; }
    }

    .sa-quick-card {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 18px 18px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        background: var(--surface);
        box-shadow: var(--shadow-sm);
        text-decoration: none;
        color: inherit;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .sa-quick-card:hover {
        border-color: var(--accent-l2);
        box-shadow: var(--shadow);
    }

    .sa-quick-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
    }

    .sa-quick-icon.amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .sa-quick-icon.blue { background: linear-gradient(135deg, #3b82f6, #2563eb); }
    .sa-quick-icon.purple { background: linear-gradient(135deg, #a855f7, #7c3aed); }

    .sa-quick-card h3 {
        margin: 0 0 4px;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--ink);
    }

    .sa-quick-card p {
        margin: 0;
        font-size: 0.82rem;
        color: var(--muted);
        line-height: 1.45;
    }

    .sa-quick-meta {
        margin-top: 8px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--accent);
    }

    @media (max-width: 768px) {
        .sa-main { margin-left: 0; }
        .sa-hamburger { display: flex; align-items: center; justify-content: center; }
    }

@include('superadmin.partials.moodle-css')

    /* Override layouts.app global `a:hover { text-decoration: underline }` */
    .sa-shell a,
    .sa-shell a:hover,
    .sa-shell a:focus,
    .sa-shell a:active {
        text-decoration: none;
    }
</style>
@endpush
