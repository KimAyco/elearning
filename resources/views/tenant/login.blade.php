@extends('layouts.app')

@section('title', isset($schoolName) ? $schoolName . ' — Sign In' : 'School Portal — Sign In')

@section('content')
<div class="login-page">
    <main class="login-main">
        <div class="login-card">
            <div class="login-form-wrap">
                @php
                    $loginBackUrl = isset($prefillSchoolCode) && $prefillSchoolCode !== ''
                        ? url('/schools/' . rawurlencode((string) $prefillSchoolCode) . '/enroll')
                        : url('/');
                @endphp
                <a href="{{ $loginBackUrl }}" class="login-back-icon" aria-label="Back to public page" title="Back">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                </a>
                <header class="login-header">
                    <h1 class="login-title">{{ isset($schoolName) ? 'Sign in to ' . $schoolName : 'Sign in to your school' }}</h1>
                    <p class="login-subtitle">{{ isset($prefillSchoolCode) ? 'Enter your credentials to access the portal.' : 'Enter your school code and credentials to access the portal.' }}</p>
                </header>

                @if ($errors->any())
                    <div class="login-alert login-alert--error" role="alert">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form method="post" action="{{ url('/login') }}" class="stack">
                        @csrf

                        <div class="form-group">
                            <label for="school_code">School Code</label>
                            <div style="position:relative;">
                                <input id="school_code" name="school_code" type="text"
                                       value="{{ old('school_code', $prefillSchoolCode ?? '') }}"
                                       placeholder="e.g. UNIV-001"
                                       autocomplete="organization"
                                       style="padding-left:38px;{{ isset($prefillSchoolCode) ? ' background:var(--surface-2); color:var(--ink-2);' : '' }}"
                                       {{ isset($prefillSchoolCode) ? 'readonly' : '' }}
                                       required>
                                <span style="position:absolute; left:11px; top:50%; transform:translateY(-50%); color:var(--muted);">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    </svg>
                                </span>
                            </div>
                            <span class="input-hint">{{ isset($prefillSchoolCode) ? 'School pre-selected from portal' : 'Your institution\'s unique identifier' }}</span>
                        </div>

                        <div class="form-group">
                            <label for="email">Email address</label>
                            <div style="position:relative;">
                                <input id="email" name="email" type="email"
                                       value="{{ old('email') }}"
                                       placeholder="you@school.edu"
                                       autocomplete="email"
                                       style="padding-left:38px;"
                                       required>
                                <span style="position:absolute; left:11px; top:50%; transform:translateY(-50%); color:var(--muted);">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                                    </svg>
                                </span>
                            </div>
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
                        <p class="login-forgot"><a href="{{ url('/forgot-password') }}">Forgot Password?</a></p>

                        <button class="auth-login-submit" type="submit">
                            <span>Sign in to portal</span>
                        </button>
                    </form>

                    <div class="login-social" aria-hidden="true">
                        <svg class="login-social-google" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" focusable="false">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>
                </div>
                {{-- Split panel uses CSS green gradient only: matches hero, inputs, and CTA. School theme is not applied here to avoid clashing with EduPlatform login chrome. --}}
                <div class="auth-split-image" aria-hidden="true">
                    <div class="auth-split-image-bg"></div>
                    <div class="auth-split-image-overlay"></div>
                    <div class="auth-split-image-frame">
                        <div class="auth-split-image-frame-inner">
                            @php
                                /** @var string|null $schoolLogoUrl */
                                $schoolLogoUrl = $schoolLogoUrl ?? null;
                            @endphp
                            @if ($schoolLogoUrl)
                                <img
                                    src="{{ $schoolLogoUrl }}"
                                    alt="{{ $schoolName ?? 'School logo' }}"
                                    class="auth-split-img"
                                >
                            @else
                                @php
                                    $initial = strtoupper(mb_substr((string)($schoolName ?? 'S'), 0, 1));
                                @endphp
                                <div class="auth-split-initial" aria-label="School initial">{{ $initial }}</div>
                            @endif
                        </div>
                    </div>
                    <p class="auth-welcome">WELCOME BACK!</p>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
.login-page {
    --login-bg: #e5e7eb;
    --login-surface: #fff;
    --login-border: rgba(15,23,42,0.14);
    --login-ink: #0f172a;
    --login-muted: #475569;
    --login-radius: 12px;
    --login-radius-lg: 24px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background:
        linear-gradient(120deg, rgba(10,40,10,0.20), rgba(50,146,0,0.30)),
        url("https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=1600&q=80");
    background-size: cover;
    background-position: center;
}

.login-nav {
    border-bottom: 1px solid rgba(255,255,255,0.28);
    background: rgba(255,255,255,0.86);
    backdrop-filter: blur(12px);
}

.login-nav-inner {
    max-width: 1000px;
    margin: 0 auto;
    padding: 8px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.login-brand {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    color: inherit;
}

.login-brand:hover { opacity: 0.88; }

.login-brand-logo {
    display: block;
    height: 28px;
    width: auto;
    max-width: min(130px, 30vw);
    object-fit: contain;
    object-position: left center;
}

.login-brand-name {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--login-ink);
    letter-spacing: 0.01em;
}

.login-main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px 16px;
}

.login-card {
    display: flex;
    width: 100%;
    max-width: 640px;
    min-height: 0;
    background: rgba(255,255,255,0.76);
    border-radius: var(--login-radius-lg);
    border: 1px solid rgba(255,255,255,0.45);
    box-shadow: 0 18px 44px rgba(2, 6, 23, 0.24);
    overflow: hidden;
    backdrop-filter: blur(2px);
}

.login-card:hover { box-shadow: 0 22px 54px rgba(2,6,23,0.26); }

.login-form-wrap {
    flex: 1;
    min-width: 0;
    padding: 22px 28px 18px;
    background: linear-gradient(180deg, rgba(255,255,255,0.92) 0%, rgba(255,255,255,0.80) 100%);
    position: relative;
}

.login-back-icon {
    position: absolute;
    top: 14px;
    left: 14px;
    width: 34px;
    height: 34px;
    border-radius: 999px;
    background: rgba(255,255,255,0.85);
    border: 1px solid rgba(15,23,42,0.12);
    color: #0f172a;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 6px 16px rgba(15,23,42,0.10);
    transition: transform 0.12s ease, background 0.15s ease, border-color 0.15s ease;
}
.login-back-icon:hover {
    transform: translateY(-1px);
    background: #ffffff;
    border-color: rgba(15,23,42,0.18);
    text-decoration: none;
}
.login-back-icon:active { transform: translateY(0); }
.login-back-icon:focus-visible {
    outline: 3px solid rgba(37, 99, 235, 0.35);
    outline-offset: 2px;
}

.login-header { margin-bottom: 10px; }

.login-title {
    margin: 0 0 4px;
    font-size: 1.1rem;
    font-weight: 800;
    color: var(--login-ink);
    line-height: 1.3;
    text-align: center;
}

.login-subtitle {
    margin: 0;
    font-size: 0.78rem;
    color: var(--login-muted);
    line-height: 1.5;
    text-align: center;
}

.login-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 14px;
    border-radius: var(--login-radius);
    margin-bottom: 20px;
    font-size: 0.875rem;
}

.login-alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 2px; }

.login-alert--error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #b91c1c;
}

.auth-split-image {
    position: relative;
    width: 34%;
    min-width: 200px;
    min-height: 320px;
    background: linear-gradient(165deg, #1f7f2d 0%, #155f25 100%);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding: 20px 12px;
    border-top-left-radius: 28px;
    border-bottom-left-radius: 28px;
}

.auth-split-image-frame {
    position: relative;
    width: 100%;
    max-width: 160px;
    padding: 0;
    z-index: 1;
    flex-shrink: 0;
}

.auth-split-image-frame-inner {
    position: relative;
    border-radius: 0;
    padding: 0;
    background: transparent;
}

.auth-split-img {
    width: 100%;
    max-height: 220px;
    height: auto;
    object-fit: contain;
    object-position: center;
    display: block;
}

.auth-split-initial {
    width: 96px;
    height: 96px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: 800;
    color: #ffffff;
    background: rgba(255, 255, 255, 0.18);
    box-shadow: 0 10px 24px rgba(0,0,0,0.25);
    margin: 12px auto;
}

.auth-split-image-bg {
    position: absolute;
    inset: 0;
    background-image:
        url("data:image/svg+xml,%3Csvg width='32' height='32' viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.02' fill-rule='evenodd'%3E%3Cpath d='M16 14v-2h-2v2h-2v2h2v2h2v-2h2v-2h-2zm0-12V0h-2v4H8v2h6V4h4V2h-2zM6 14v-2H4v2H2v2h2v2h2v-2h2v-2H6zM6 2V0H4v2H2v2h2v2h2V4h2V2H6z'/%3E%3C/g%3E%3C/svg%3E"),
        radial-gradient(ellipse 80% 50% at 50% 120%, rgba(255,255,255,0.04) 0%, transparent 60%);
    pointer-events: none;
}

.auth-split-image-overlay {
    position: absolute;
    inset: 0;
    background:
        linear-gradient(180deg, rgba(0,0,0,0.12) 0%, transparent 35%, transparent 65%, rgba(0,0,0,0.10) 100%),
        rgba(5,50,16,0.22);
    pointer-events: none;
}
.auth-welcome {
    position: relative;
    margin: 0;
    padding: 0;
    max-width: 100%;
    text-align: center;
    font-size: 1rem;
    letter-spacing: 0.03em;
    font-weight: 800;
    color: rgba(245, 253, 245, 0.92);
    z-index: 2;
    flex-shrink: 0;
}

/* Form — scoped to .login-form-wrap */
.login-form-wrap .form-group { margin-bottom: 2px; }

.login-form-wrap .stack { gap: 10px; }

.login-form-wrap label {
    font-size: 0.88rem;
    font-weight: 700;
    color: #22303b;
}

.login-form-wrap input[type="text"],
.login-form-wrap input[type="email"],
.login-form-wrap input[type="password"] {
    padding: 9px 10px 9px 36px;
    border-radius: 8px;
    border: 2px solid rgba(31,94,56,0.55);
    font-size: 0.9rem;
    background: rgba(248,252,248,0.82);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.75);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.login-form-wrap input:focus {
    border-color: #329200;
    box-shadow: 0 0 0 3px rgba(50,146,0,0.18);
}

.login-form-wrap .input-wrap {
    border-radius: 8px;
    border: 2px solid rgba(31,94,56,0.55);
    background: rgba(248,252,248,0.82);
}

.login-form-wrap .input-wrap input { border: none; box-shadow: none; border-radius: 999px; }

.login-form-wrap .input-wrap:focus-within {
    border-color: #329200;
    box-shadow: 0 0 0 3px rgba(50,146,0,0.18);
}

.auth-login-submit {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 6px;
    padding: 10px 16px;
    border: none;
    border-radius: 999px;
    background: linear-gradient(135deg, #2c7a33 0%, #256a2d 100%);
    color: #ffffff;
    font-size: 0.88rem;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
}

.auth-login-submit:hover {
    box-shadow: 0 12px 22px rgba(37,106,45,0.40);
    transform: translateY(-1px);
}

.auth-login-submit svg {
    width: 16px;
    height: 16px;
}

.login-form-wrap .input-hint {
    font-size: 0.75rem;
    color: var(--login-muted);
    margin-top: 4px;
}

.login-form-wrap .auth-login-back,
.login-form-footer {
    margin: 10px 0 0;
    padding-top: 10px;
    border-top: 2px solid rgba(31,94,56,0.24);
    text-align: center;
}

.login-form-wrap .auth-login-back a,
.login-back-link {
    font-size: 0.88rem;
    color: #3f5e4a;
    text-decoration: none;
    transition: color 0.15s;
    font-weight:600;
}

.login-form-wrap .auth-login-back a:hover,
.login-back-link:hover { color: #256a2d; }

.login-forgot{
    margin: -2px 0 4px;
}
.login-forgot a{
    color:#294f3a;
    font-weight:700;
    text-decoration: underline;
}

.login-social {
    margin-top: 8px;
    text-align: center;
    line-height: 0;
}

.login-social-google {
    width: 28px;
    height: 28px;
    display: inline-block;
    vertical-align: middle;
}

@media (max-width: 900px) {
    .auth-split-image { display: none; }
    .login-form-wrap { padding: 20px 18px; }
    .login-title{ font-size:1.15rem; text-align:left; }
    .login-subtitle{ text-align:left; font-size:0.82rem; }
    .login-card{ min-height: 0; max-width: 560px; }
}

@media (max-width: 640px) {
    .login-nav-inner { padding: 7px 14px; }
    .login-brand-logo { height: 24px; max-width: 110px; }
    .login-brand-name { font-size: 0.88rem; }
    .login-main { padding: 16px 14px; }
    .login-card { min-height: 0; max-width: 100%; }
    .login-form-wrap { padding: 18px 16px; }
}
</style>
@endsection
