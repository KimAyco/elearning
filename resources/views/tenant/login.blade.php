@extends('layouts.app')

@section('title', isset($schoolName) ? $schoolName . ' — Sign In' : 'School Portal — Sign In')

@section('content')
<div class="login-page">
    <header class="login-nav">
        <div class="login-nav-inner">
            <a href="/" class="login-brand">
                <span class="login-brand-icon">E</span>
                <span class="login-brand-name">EduPlatform</span>
            </a>
        </div>
    </header>

    <main class="login-main">
        <div class="login-card">
            <div class="login-form-wrap">
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

                        <button class="auth-login-submit" type="submit">
                            <span>Sign in to portal</span>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                            </svg>
                        </button>
                    </form>

                    <p class="auth-login-back">
                        <a href="/">← Back to public portal</a>
                    </p>
                </div>
                    @php
                        /** @var string|null $schoolTheme */
                        $schoolTheme = $schoolTheme ?? null;
                        $authBg = null;
                        if ($schoolTheme === 'green') {
                            $authBg = 'linear-gradient(165deg, #16a34a 0%, #15803d 100%)';
                        } elseif ($schoolTheme === 'indigo') {
                            $authBg = 'linear-gradient(165deg, #4f46e5 0%, #3730a3 100%)';
                        } elseif ($schoolTheme === 'slate') {
                            $authBg = 'linear-gradient(165deg, #475569 0%, #1e293b 100%)';
                        } elseif ($schoolTheme === 'blue') {
                            $authBg = 'linear-gradient(165deg, #2563eb 0%, #1d4ed8 100%)';
                        } elseif ($schoolTheme === 'teal') {
                            $authBg = 'linear-gradient(165deg, #14b8a6 0%, #0f766e 100%)';
                        } elseif ($schoolTheme === 'amber') {
                            $authBg = 'linear-gradient(165deg, #f59e0b 0%, #b45309 100%)';
                        } elseif ($schoolTheme === 'rose') {
                            $authBg = 'linear-gradient(165deg, #fb7185 0%, #e11d48 100%)';
                        } elseif ($schoolTheme === 'purple') {
                            $authBg = 'linear-gradient(165deg, #8b5cf6 0%, #6d28d9 100%)';
                        } elseif ($schoolTheme === 'emerald') {
                            $authBg = 'linear-gradient(165deg, #10b981 0%, #047857 100%)';
                        } elseif ($schoolTheme === 'sky') {
                            $authBg = 'linear-gradient(165deg, #38bdf8 0%, #0284c7 100%)';
                        }
                    @endphp
                <div class="auth-split-image" aria-hidden="true" @if($authBg) style="background: {{ $authBg }};" @endif>
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
                </div>
            </div>
        </div>
    </main>

    <footer class="schools-footer">
        <p>© {{ date('Y') }} EduPlatform</p>
    </footer>
</div>

<style>
/* Login page — variables & layout */
.login-page {
    --login-bg: #f3f4f6;
    --login-surface: #fff;
    --login-border: #e5e7eb;
    --login-ink: #0f172a;
    --login-muted: #6b7280;
    --login-radius: 12px;
    --login-radius-lg: 20px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: var(--login-bg);
}

.login-nav {
    border-bottom: 1px solid var(--login-border);
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(12px);
}

.login-nav-inner {
    max-width: 1000px;
    margin: 0 auto;
    padding: 14px 20px;
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

.login-brand:hover { opacity: 0.85; }

.login-brand-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #374151, #1f2937);
}

.login-brand-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--login-ink);
}

.login-main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 120px);
    padding: 24px 20px;
}

.login-card {
    display: flex;
    width: 100%;
    max-width: 880px;
    min-height: 480px;
    background: var(--login-surface);
    border-radius: var(--login-radius-lg);
    border: 1px solid var(--login-border);
    box-shadow: 0 4px 24px rgba(15,23,42,0.06);
    overflow: hidden;
}

.login-card:hover { box-shadow: 0 12px 32px rgba(15,23,42,0.08); }

.login-form-wrap {
    flex: 1;
    min-width: 0;
    padding: 36px 32px 32px;
}

.login-header { margin-bottom: 24px; }

.login-title {
    margin: 0 0 4px;
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--login-ink);
    line-height: 1.3;
}

.login-subtitle {
    margin: 0;
    font-size: 0.9rem;
    color: var(--login-muted);
    line-height: 1.5;
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
    width: 38%;
    min-width: 240px;
    min-height: 400px;
    background: linear-gradient(165deg, #374151 0%, #1f2937 100%);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px;
}

.auth-split-image-frame {
    position: relative;
    width: 100%;
    max-width: 240px;
    padding: 16px;
    z-index: 1;
}

.auth-split-image-frame::before {
    content: "";
    position: absolute;
    inset: 0;
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-radius: 20px;
    pointer-events: none;
    box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.2) inset;
}

.auth-split-image-frame-inner {
    position: relative;
    border-radius: 12px;
    padding: 24px;
    background: rgba(255, 255, 255, 0.06);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.auth-split-img {
    width: 100%;
    max-height: 260px;
    height: auto;
    object-fit: contain;
    object-position: center;
    display: block;
}

.auth-split-initial {
    width: 140px;
    height: 140px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 800;
    color: #ffffff;
    background: rgba(255, 255, 255, 0.18);
    box-shadow: 0 10px 24px rgba(0,0,0,0.25), inset 0 0 0 1px rgba(255,255,255,0.35);
    margin: 20px auto;
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
        linear-gradient(180deg, rgba(0,0,0,0.15) 0%, transparent 35%, transparent 65%, rgba(0,0,0,0.12) 100%),
        rgba(15,23,42,0.3);
    pointer-events: none;
}

.login-footer-bar,
.schools-footer {
    border-top: 1px solid #e5e7eb;
    background: #ffffff;
    padding: 18px 20px 22px;
    text-align: center;
    font-size: 0.8rem;
    color: #9ca3af;
}

/* Form — scoped to .login-form-wrap */
.login-form-wrap .form-group { margin-bottom: 2px; }

.login-form-wrap .stack { gap: 14px; }

.login-form-wrap label {
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
}

.login-form-wrap input[type="text"],
.login-form-wrap input[type="email"],
.login-form-wrap input[type="password"] {
    padding: 10px 12px 10px 40px;
    border-radius: 999px;
    border: 1px solid var(--login-border);
    font-size: 0.9rem;
    background: var(--login-surface);
    box-shadow: 0 1px 2px rgba(15,23,42,0.04);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.login-form-wrap input:focus {
    border-color: #374151;
    box-shadow: 0 0 0 2px rgba(55,65,81,0.15);
}

.login-form-wrap .input-wrap {
    border-radius: 999px;
    border: 1px solid var(--login-border);
    background: var(--login-surface);
    box-shadow: 0 1px 2px rgba(15,23,42,0.04);
}

.login-form-wrap .input-wrap input { border: none; box-shadow: none; border-radius: 999px; }

.login-form-wrap .input-wrap:focus-within {
    border-color: #374151;
    box-shadow: 0 0 0 2px rgba(55,65,81,0.15);
}

.auth-login-submit {
    width: 100%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
    padding: 10px 16px;
    border: none;
    border-radius: 999px;
    background: #1f2937;
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
}

.auth-login-submit:hover {
    background: #374151;
    box-shadow: 0 10px 22px rgba(31, 41, 55, 0.35);
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
    margin: 22px 0 0;
    padding-top: 18px;
    border-top: 1px solid var(--login-border);
    text-align: center;
}

.login-form-wrap .auth-login-back a,
.login-back-link {
    font-size: 0.82rem;
    color: var(--login-muted);
    text-decoration: none;
    transition: color 0.15s;
}

.login-form-wrap .auth-login-back a:hover,
.login-back-link:hover { color: #1f2937; }

@media (max-width: 900px) {
    .auth-split-image { display: none; }
    .login-form-wrap { padding: 32px 28px; }
}

@media (max-width: 640px) {
    .login-nav-inner { padding-inline: 16px; }
    .login-main { padding: 20px 16px; }
    .login-card { min-height: 0; }
    .login-form-wrap { padding: 24px 20px; }
}
</style>
@endsection
