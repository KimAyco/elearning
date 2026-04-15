@extends('layouts.app')

@section('title', 'Nehemiah — Super Admin Sign In')

@php
    $supportMailto = 'mailto:' . rawurlencode(config('mail.from.address', 'hello@example.com'));
@endphp

@section('content')
<div class="auth-page superadmin-auth-page">
    {{-- Left: brand + welcome (reference layout) --}}
    <div class="auth-illustration superadmin-illustration">
        <div class="superadmin-illustration-glow" aria-hidden="true"></div>
        <div class="auth-illustration-content superadmin-hero">
            <div class="superadmin-logo-plate">
                {{-- Root-relative URL: works with Hostinger TLS proxies; avoids mixed-content if scheme is wrong. --}}
                <img
                    src="/images/nehemiah-logo.png"
                    alt="Nehemiah Solutions"
                    class="superadmin-logo superadmin-logo--hero"
                    width="320"
                    height="96"
                    decoding="async"
                    onerror="this.onerror=null;this.src='/images/logoon.png';"
                >
            </div>
            <div class="superadmin-badge-pill">
                <span class="superadmin-badge-pill__icon" aria-hidden="true">🛡️</span>
                <span>Super Admin Access</span>
            </div>
            <h2 class="superadmin-hero-heading">
                <span class="superadmin-hero-heading__muted">Welcome to</span>
                <span class="superadmin-hero-heading__accent">Control Center</span>
            </h2>
            <p class="superadmin-hero-lead">
                Securely manage your entire learning management system with comprehensive administrative controls.
            </p>
        </div>
    </div>

    {{-- Right: sign-in card --}}
    <div class="auth-panel superadmin-panel">
        <div class="auth-card auth-card-superadmin">
            <header class="superadmin-card-brand">
                <div class="superadmin-lock-badge" aria-hidden="true">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <div class="superadmin-card-brand__text">
                    <span class="superadmin-card-brand__title">Super Admin</span>
                    <span class="superadmin-card-brand__sub">Sign in to continue</span>
                </div>
            </header>

            {{-- Mobile-only logo (hero hidden on small screens) --}}
            <div class="superadmin-logo-plate superadmin-logo-plate--compact">
                <img
                    src="/images/nehemiah-logo.png"
                    alt="Nehemiah Solutions"
                    class="superadmin-logo superadmin-logo--card"
                    width="200"
                    height="56"
                    decoding="async"
                    onerror="this.onerror=null;this.src='/images/logoon.png';"
                >
            </div>

            @if ($errors->any())
                <div class="alert error superadmin-alert" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="post" action="{{ url('/superadmin/login') }}" class="stack superadmin-form" novalidate>
                @csrf

                <div class="form-group superadmin-field">
                    <label for="email">Email address</label>
                    <div class="superadmin-input-shell">
                        <span class="superadmin-input-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                        </span>
                        <input id="email" name="email" type="email"
                               class="superadmin-input superadmin-input--icon"
                               value="{{ old('email') }}"
                               placeholder="admin@nehemiah.com"
                               autocomplete="email"
                               inputmode="email"
                               @if(! old('email')) autofocus @endif
                               required>
                    </div>
                </div>

                <div class="form-group superadmin-field">
                    <div class="superadmin-label-row">
                        <label for="password">Password</label>
                        <a href="{{ $supportMailto }}" class="superadmin-forgot" data-no-loader>Forgot password?</a>
                    </div>
                    <div class="superadmin-input-shell">
                        <span class="superadmin-input-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </span>
                        <input id="password" name="password" type="password"
                               class="superadmin-input superadmin-input--icon superadmin-input--pwd"
                               placeholder="Enter your password"
                               autocomplete="current-password"
                               @if(old('email')) autofocus @endif
                               required>
                        <button type="button" class="input-toggle superadmin-input-toggle" aria-label="Toggle password visibility">
                            <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="eye-close" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <label class="superadmin-remember">
                    <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    <span class="superadmin-remember__box" aria-hidden="true"></span>
                    <span>Keep me signed in for 30 days</span>
                </label>

                <button class="btn superadmin-btn-cta full lg" type="submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 9.9-1"/>
                    </svg>
                    Sign In Securely
                </button>
            </form>

            <div class="superadmin-info-notice" role="status">
                <span class="superadmin-info-notice__icon" aria-hidden="true">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="16" x2="12" y2="12"/>
                        <line x1="12" y1="8" x2="12.01" y2="8"/>
                    </svg>
                </span>
                <p>Super Admin access is logged and monitored. Ensure you're authorized to access this portal.</p>
            </div>

            <footer class="superadmin-card-foot">
                <div class="superadmin-help-row">
                    <span class="superadmin-help-q">Need help?</span>
                    <a href="{{ $supportMailto }}" class="superadmin-help-link" data-no-loader>Contact Support</a>
                </div>
                <a href="/" class="superadmin-back-link" data-no-loader>← Back to public portal</a>
            </footer>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .superadmin-auth-page {
        --nh-bg-0: #0a0812;
        --nh-bg-1: #120f1c;
        --nh-bg-2: #1a1628;
        --nh-purple-tint: #221a35;
        --nh-terracotta: #c55a2e;
        --nh-terracotta-h: #d9734a;
        --nh-terracotta-d: #a34720;
        --nh-amber: #e8a05c;
        --nh-text: #f1f4fb;
        --nh-muted: rgba(232, 237, 247, 0.68);
        --nh-line: rgba(255, 255, 255, 0.08);
        --nh-info-bg: rgba(30, 58, 95, 0.45);
        --nh-info-border: rgba(96, 165, 250, 0.28);
        --nh-info-text: rgba(191, 219, 254, 0.95);
    }

    .superadmin-auth-page.auth-page {
        background: var(--nh-bg-0);
    }

    .superadmin-illustration {
        position: relative;
        background:
            radial-gradient(ellipse 90% 70% at 15% 25%, rgba(88, 60, 140, 0.22) 0%, transparent 50%),
            radial-gradient(ellipse 80% 60% at 80% 80%, rgba(197, 90, 46, 0.08) 0%, transparent 45%),
            linear-gradient(165deg, var(--nh-bg-2) 0%, var(--nh-bg-0) 100%);
        border-right: 1px solid var(--nh-line);
    }

    .superadmin-illustration::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(197, 90, 46, 0.06) 0%, transparent 50%);
        pointer-events: none;
    }

    .superadmin-illustration-glow {
        position: absolute;
        width: min(440px, 75vw);
        height: min(440px, 75vw);
        left: 42%;
        top: 36%;
        transform: translate(-50%, -50%);
        background: radial-gradient(circle, rgba(197, 90, 46, 0.12) 0%, transparent 65%);
        pointer-events: none;
    }

    .superadmin-hero {
        max-width: 420px;
        margin: 0 auto;
        text-align: left;
    }

    .superadmin-logo-plate {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 18px 22px;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 4px 28px rgba(0, 0, 0, 0.4);
        margin-bottom: 24px;
    }

    .superadmin-logo {
        display: block;
        width: auto;
        object-fit: contain;
    }

    .superadmin-logo--hero {
        max-width: min(100%, 300px);
        height: auto;
        max-height: 72px;
    }

    .superadmin-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 999px;
        border: 1px solid rgba(197, 90, 46, 0.45);
        background: rgba(15, 10, 25, 0.5);
        color: rgba(255, 255, 255, 0.88);
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.04em;
        margin-bottom: 20px;
    }

    .superadmin-badge-pill__icon { font-size: 0.95rem; line-height: 1; }

    .superadmin-hero-heading {
        margin: 0 0 14px;
        display: flex;
        flex-direction: column;
        gap: 2px;
        line-height: 1.15;
    }

    .superadmin-hero-heading__muted {
        font-size: clamp(1.35rem, 2.4vw, 1.65rem);
        font-weight: 600;
        color: #fff;
    }

    .superadmin-hero-heading__accent {
        font-size: clamp(1.65rem, 3vw, 2.15rem);
        font-weight: 800;
        color: var(--nh-terracotta-h);
        letter-spacing: -0.02em;
    }

    .superadmin-hero-lead {
        margin: 0;
        color: var(--nh-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        max-width: 400px;
    }

    .superadmin-panel {
        background: linear-gradient(168deg, var(--nh-bg-1) 0%, var(--nh-bg-0) 100%);
    }

    .auth-card-superadmin {
        position: relative;
        max-width: 420px;
        border-radius: 20px;
        background: linear-gradient(180deg, rgba(22, 18, 34, 0.97) 0%, rgba(10, 8, 18, 0.99) 100%);
        border: 1px solid rgba(255, 255, 255, 0.07);
        box-shadow:
            0 28px 72px rgba(0, 0, 0, 0.55),
            inset 0 1px 0 rgba(255, 255, 255, 0.04);
        overflow: hidden;
        color: var(--nh-text);
        padding: 32px 28px 28px;
    }

    .auth-card-superadmin::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(180deg, var(--nh-terracotta-h), var(--nh-terracotta), var(--nh-amber));
        box-shadow: 0 0 28px rgba(197, 90, 46, 0.4);
        pointer-events: none;
    }

    .superadmin-card-brand {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 26px;
    }

    .superadmin-lock-badge {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: linear-gradient(145deg, var(--nh-terracotta-h), var(--nh-terracotta-d));
        color: #fff;
        box-shadow: 0 8px 24px rgba(197, 90, 46, 0.35);
    }

    .superadmin-card-brand__text { display: flex; flex-direction: column; gap: 2px; }

    .superadmin-card-brand__title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #fff;
    }

    .superadmin-card-brand__sub {
        font-size: 0.86rem;
        color: var(--nh-muted);
    }

    .superadmin-logo-plate--compact {
        display: none;
        padding: 12px 16px;
        margin-bottom: 20px;
        border-radius: 12px;
    }

    @media (max-width: 768px) {
        .superadmin-logo-plate--compact { display: inline-flex; }
    }

    .superadmin-logo--card {
        max-width: 200px;
        max-height: 48px;
    }

    .superadmin-field label {
        font-size: 0.82rem;
        font-weight: 600;
        color: rgba(232, 237, 247, 0.9);
    }

    .superadmin-label-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .superadmin-label-row label { margin-bottom: 0; }

    .superadmin-forgot {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--nh-terracotta-h) !important;
        text-decoration: none;
        white-space: nowrap;
    }

    .superadmin-forgot:hover {
        text-decoration: none;
        color: var(--nh-amber) !important;
    }

    .superadmin-input-shell {
        position: relative;
        display: flex;
        align-items: center;
    }

    .superadmin-input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(148, 163, 184, 0.85);
        pointer-events: none;
        z-index: 1;
        display: flex;
    }

    .superadmin-input {
        width: 100%;
        background: rgba(6, 5, 12, 0.75);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: var(--nh-text);
        padding: 12px 14px 12px 44px;
        font-size: 0.92rem;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .superadmin-input--pwd {
        padding-right: 44px;
    }

    .superadmin-input::placeholder {
        color: rgba(148, 163, 184, 0.65);
    }

    .superadmin-input:hover {
        border-color: rgba(255, 255, 255, 0.14);
    }

    .superadmin-input:focus {
        outline: none;
        border-color: rgba(197, 90, 46, 0.65);
        box-shadow: 0 0 0 3px rgba(197, 90, 46, 0.2);
    }

    /* Eye icon: global .input-toggle:hover uses var(--ink); nh-text is invisible on light/autofill fields */
    .superadmin-auth-page .superadmin-input-shell .input-toggle.superadmin-input-toggle {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: rgba(148, 163, 184, 0.95);
        padding: 6px;
        display: flex;
        z-index: 2;
        transition: color 0.15s ease;
    }

    .superadmin-auth-page .superadmin-input-shell .input-toggle.superadmin-input-toggle:hover,
    .superadmin-auth-page .superadmin-input-shell .input-toggle.superadmin-input-toggle:focus-visible {
        color: var(--nh-amber) !important;
    }

    .superadmin-auth-page .superadmin-input-shell .input-toggle.superadmin-input-toggle:active {
        color: var(--nh-terracotta-h) !important;
    }

    .superadmin-remember {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        cursor: pointer;
        font-size: 0.86rem;
        color: var(--nh-muted);
        margin: 4px 0 2px;
        user-select: none;
    }

    .superadmin-remember input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .superadmin-remember__box {
        flex-shrink: 0;
        width: 18px;
        height: 18px;
        margin-top: 2px;
        border-radius: 5px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(6, 5, 12, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: border-color 0.15s, background 0.15s;
    }

    .superadmin-remember input:focus-visible + .superadmin-remember__box {
        outline: 2px solid rgba(197, 90, 46, 0.7);
        outline-offset: 2px;
    }

    .superadmin-remember input:checked + .superadmin-remember__box {
        background: var(--nh-terracotta);
        border-color: var(--nh-terracotta-h);
    }

    .superadmin-remember input:checked + .superadmin-remember__box::after {
        content: '';
        width: 5px;
        height: 9px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg) translate(-0.5px, -1px);
    }

    .superadmin-btn-cta {
        margin-top: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-weight: 600;
        letter-spacing: 0.02em;
        border: none;
        cursor: pointer;
        background: linear-gradient(180deg, var(--nh-terracotta-h) 0%, var(--nh-terracotta) 48%, var(--nh-terracotta-d) 100%);
        color: #fff !important;
        box-shadow:
            0 6px 22px rgba(197, 90, 46, 0.38),
            inset 0 1px 0 rgba(255, 255, 255, 0.15);
        transition: transform 0.12s ease, box-shadow 0.15s ease, filter 0.15s ease;
    }

    .superadmin-btn-cta:hover {
        filter: brightness(1.06);
        box-shadow:
            0 8px 28px rgba(197, 90, 46, 0.48),
            inset 0 1px 0 rgba(255, 255, 255, 0.18);
    }

    .superadmin-btn-cta:focus {
        outline: none;
        box-shadow:
            0 0 0 3px rgba(197, 90, 46, 0.35),
            0 8px 28px rgba(197, 90, 46, 0.45);
    }

    .superadmin-btn-cta:active {
        transform: translateY(1px);
    }

    .superadmin-btn-cta svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
    }

    .superadmin-info-notice {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-top: 22px;
        padding: 14px 16px;
        border-radius: 12px;
        background: var(--nh-info-bg);
        border: 1px solid var(--nh-info-border);
        color: var(--nh-info-text);
        font-size: 0.82rem;
        line-height: 1.5;
    }

    .superadmin-info-notice__icon {
        flex-shrink: 0;
        color: rgba(147, 197, 253, 0.95);
        margin-top: 1px;
    }

    .superadmin-info-notice p {
        margin: 0;
    }

    .superadmin-card-foot {
        margin-top: 22px;
        padding-top: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.07);
        text-align: center;
    }

    .superadmin-help-row {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 14px;
        font-size: 0.84rem;
    }

    .superadmin-help-q {
        color: var(--nh-muted);
    }

    .superadmin-help-link {
        font-weight: 600;
        color: rgba(147, 197, 253, 0.95) !important;
        text-decoration: none;
    }

    .superadmin-help-link:hover {
        text-decoration: none;
        color: #bfdbfe !important;
    }

    .superadmin-back-link {
        font-size: 0.8rem;
        color: rgba(148, 163, 184, 0.9) !important;
        text-decoration: none;
    }

    .superadmin-back-link:hover {
        color: var(--nh-text) !important;
        text-decoration: none;
    }

    .superadmin-alert {
        margin-bottom: 18px;
    }

    .auth-card-superadmin .alert.error {
        border-radius: 12px;
        border: 1px solid rgba(248, 113, 113, 0.35);
        background: rgba(127, 29, 29, 0.38);
        color: #fecaca;
    }

    .superadmin-illustration::before {
        animation: superadminGridDrift 26s linear infinite;
        opacity: 0.5;
    }

    .auth-card-superadmin {
        animation: superadminCardIn 0.6s ease-out both;
    }

    .superadmin-panel {
        animation: superadminPanelFade 0.6s ease-out both;
    }

    @keyframes superadminGridDrift {
        from { transform: translate3d(0, 0, 0); }
        to { transform: translate3d(-40px, -20px, 0); }
    }

    @keyframes superadminCardIn {
        from {
            opacity: 0;
            transform: translate3d(0, 18px, 0) scale(0.99);
        }
        to {
            opacity: 1;
            transform: translate3d(0, 0, 0) scale(1);
        }
    }

    @keyframes superadminPanelFade {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (prefers-reduced-motion: reduce) {
        .superadmin-illustration::before,
        .auth-card-superadmin,
        .superadmin-panel {
            animation: none !important;
        }
    }
</style>
@endpush
