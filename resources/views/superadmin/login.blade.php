@extends('layouts.app')

@section('title', 'Super Admin — Sign In')

@section('content')
<div class="auth-page">
    {{-- ── Illustration Panel ── --}}
    <div class="auth-illustration superadmin-illustration">
        <div class="auth-illustration-content superadmin-hero">
            <div class="superadmin-badge-row">
                <div class="superadmin-icon-wrap">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
                <h2>Super Admin Portal</h2>
            </div>
        </div>
    </div>

    {{-- ── Form Panel ── --}}
    <div class="auth-panel superadmin-panel">
        <div class="auth-card auth-card-superadmin">
            <div class="auth-brand">
                <div class="auth-brand-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <span class="auth-brand-name">EduPlatform</span>
            </div>

            <h1 class="auth-heading">Super Admin sign in</h1>

            @if ($errors->any())
                <div class="alert error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="post" action="{{ url('/superadmin/login') }}" class="stack">
                @csrf

                <div class="form-group">
                    <label for="email">Email address</label>
                    <input id="email" name="email" type="email"
                           value="{{ old('email') }}"
                           placeholder="admin@example.com"
                           autocomplete="email"
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input id="password" name="password" type="password"
                               placeholder="••••••••"
                               autocomplete="current-password"
                               required>
                        <button type="button" class="input-toggle" aria-label="Toggle password visibility">
                            <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-close" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button class="btn primary full lg" type="submit" style="margin-top:4px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Sign In to Super Admin
                </button>
            </form>

            <div style="margin-top:24px; padding-top:20px; border-top:1px solid var(--border); text-align:center;">
                <a href="/" style="font-size:0.82rem; color:var(--muted);">
                    ← Back to public portal
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .superadmin-hero {
        max-width: 430px;
        margin: 0 auto;
        text-align: left;
    }

    .superadmin-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(15,23,42,0.35);
        color: rgba(255,255,255,0.9);
        font-size: 0.78rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .superadmin-badge-row {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 8px;
    }

    .superadmin-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        background: rgba(15,23,42,0.45);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(15,23,42,0.35);
    }

    .superadmin-hero h2 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
        color: #fff;
    }

    .superadmin-hero p {
        margin-top: 6px;
        margin-bottom: 18px;
        color: rgba(255,255,255,.78);
        font-size: 0.98rem;
        max-width: 360px;
    }

    .superadmin-illustration {
        background: radial-gradient(circle at top left, #1e293b 0%, #020617 55%, #0b1120 100%);
    }

    .superadmin-panel {
        background: radial-gradient(circle at top right, #020617 0%, #020617 55%, #020617 100%);
    }

    .auth-card-superadmin {
        position: relative;
        border-radius: 18px;
        background: radial-gradient(circle at top left, #020617, #020617 45%, #020617 100%);
        box-shadow: 0 16px 40px rgba(15,23,42,0.45);
        overflow: hidden;
        color: #e5e7eb;
    }

    .auth-card-superadmin::before {
        content: '';
        position: absolute;
        inset: 0;
        height: 4px;
        background: linear-gradient(90deg, #4f46e5, #0ea5e9, #22c55e);
    }

    .auth-card-superadmin > * {
        position: relative;
    }

    .auth-card-superadmin .auth-brand-name,
    .auth-card-superadmin .auth-heading,
    .auth-card-superadmin label {
        color: #e5e7eb;
    }

    .auth-card-superadmin .form-group input {
        background: #0f172a;
        border-color: #1e293b;
        color: #e5e7eb;
    }

    .auth-card-superadmin .form-group input::placeholder {
        color: #64748b;
    }

    /* ─── Super admin animations ─────────────────────────────────────── */

    .superadmin-illustration::before {
        animation: superadminGridDrift 26s linear infinite;
        opacity: 0.6;
    }

    .auth-card-superadmin {
        animation: superadminCardIn 0.7s ease-out both;
    }

    .superadmin-panel {
        animation: superadminPanelFade 0.7s ease-out both;
    }

    .superadmin-icon-wrap {
        animation: superadminIconFloat 4.2s ease-in-out infinite;
    }

    .auth-card-superadmin::before {
        background-size: 200% 100%;
        animation: superadminBorderShift 14s linear infinite;
    }

    @keyframes superadminGridDrift {
        from {
            transform: translate3d(0, 0, 0);
        }
        to {
            transform: translate3d(-40px, -20px, 0);
        }
    }

    @keyframes superadminCardIn {
        from {
            opacity: 0;
            transform: translate3d(0, 24px, 0) scale(0.97);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }
    }

    @keyframes superadminPanelFade {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes superadminIconFloat {
        0%   { transform: translateY(0); }
        50%  { transform: translateY(-6px); }
        100% { transform: translateY(0); }
    }

    @keyframes superadminBorderShift {
        0%   { background-position: 0% 0; }
        50%  { background-position: 100% 0; }
        100% { background-position: 0% 0; }
    }
</style>
@endpush
