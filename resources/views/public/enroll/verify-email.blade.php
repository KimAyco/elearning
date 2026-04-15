@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
@php
    $enrollThemeKey = strtolower((string) ($school->theme ?? 'blue'));
    $enrollThemeMap = [
        'blue' => ['accent' => '#2563eb', 'accent_h' => '#1d4ed8', 'accent_l' => '#eff6ff', 'accent_l2' => '#dbeafe', 'bg_1' => '#eff6ff', 'bg_2' => '#f4f7fe', 'bg_3' => '#eef6ff'],
        'green' => ['accent' => '#15803d', 'accent_h' => '#166534', 'accent_l' => '#ecfdf5', 'accent_l2' => '#dcfce7', 'bg_1' => '#ecfdf5', 'bg_2' => '#f0fdf4', 'bg_3' => '#ecfdf5'],
        'indigo' => ['accent' => '#4f46e5', 'accent_h' => '#4338ca', 'accent_l' => '#eef2ff', 'accent_l2' => '#e0e7ff', 'bg_1' => '#eef2ff', 'bg_2' => '#f5f3ff', 'bg_3' => '#eef2ff'],
        'slate' => ['accent' => '#475569', 'accent_h' => '#334155', 'accent_l' => '#f1f5f9', 'accent_l2' => '#e2e8f0', 'bg_1' => '#f1f5f9', 'bg_2' => '#f8fafc', 'bg_3' => '#f1f5f9'],
        'teal' => ['accent' => '#0f766e', 'accent_h' => '#0f766e', 'accent_l' => '#f0fdfa', 'accent_l2' => '#ccfbf1', 'bg_1' => '#f0fdfa', 'bg_2' => '#f7fffd', 'bg_3' => '#ecfeff'],
        'amber' => ['accent' => '#d97706', 'accent_h' => '#b45309', 'accent_l' => '#fffbeb', 'accent_l2' => '#fde68a', 'bg_1' => '#fffbeb', 'bg_2' => '#fffbf1', 'bg_3' => '#fef3c7'],
        'rose' => ['accent' => '#e11d48', 'accent_h' => '#be123c', 'accent_l' => '#fff1f2', 'accent_l2' => '#fecdd3', 'bg_1' => '#fff1f2', 'bg_2' => '#fff5f7', 'bg_3' => '#ffe4e6'],
        'purple' => ['accent' => '#7c3aed', 'accent_h' => '#6d28d9', 'accent_l' => '#f5f3ff', 'accent_l2' => '#ddd6fe', 'bg_1' => '#f5f3ff', 'bg_2' => '#faf5ff', 'bg_3' => '#f3e8ff'],
        'emerald' => ['accent' => '#059669', 'accent_h' => '#047857', 'accent_l' => '#ecfdf5', 'accent_l2' => '#a7f3d0', 'bg_1' => '#ecfdf5', 'bg_2' => '#f0fdf4', 'bg_3' => '#d1fae5'],
        'sky' => ['accent' => '#0284c7', 'accent_h' => '#0369a1', 'accent_l' => '#e0f2fe', 'accent_l2' => '#bae6fd', 'bg_1' => '#e0f2fe', 'bg_2' => '#f0f9ff', 'bg_3' => '#e0f2fe'],
    ];
    $enrollTheme = $enrollThemeMap[$enrollThemeKey] ?? $enrollThemeMap['blue'];
    $maskedEmail = (function (?string $email): string {
        $email = trim((string) $email);
        if ($email === '' || !str_contains($email, '@')) {
            return $email;
        }
        [$local, $domain] = explode('@', $email, 2);
        if (strlen($local) <= 2) {
            $localMasked = substr($local, 0, 1) . '*';
        } else {
            $localMasked = substr($local, 0, 2) . str_repeat('*', max(strlen($local) - 2, 2));
        }
        return $localMasked . '@' . $domain;
    })($sent_to);
@endphp
<div style="min-height:100vh; --accent:{{ $enrollTheme['accent'] }}; --accent-h:{{ $enrollTheme['accent_h'] }}; --accent-l:{{ $enrollTheme['accent_l'] }}; --accent-l2:{{ $enrollTheme['accent_l2'] }}; background: linear-gradient(160deg, {{ $enrollTheme['bg_1'] }} 0%, {{ $enrollTheme['bg_2'] }} 50%, {{ $enrollTheme['bg_3'] }} 100%);">
    <nav style="background: rgba(255,255,255,.85); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); position: sticky; top:0; z-index:50;">
        <div style="max-width:900px; margin:0 auto; padding:0 24px; height:60px; display:flex; align-items:center; justify-content:space-between;">
            <a href="{{ route('school.enroll', ['school_code' => $school->school_code]) }}" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                <div style="width:34px; height:34px; background:linear-gradient(135deg,var(--accent),var(--accent-h)); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <span style="font-weight:700; font-size:1rem; color:var(--ink);">{{ $school->name }}</span>
            </a>
            <span class="badge blue">Email Verification</span>
        </div>
    </nav>

    <div style="max-width:720px; margin:0 auto; padding:48px 24px 64px;">
        <div style="background:white; border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow-lg); overflow:hidden;">
            <div style="padding:28px 32px; border-bottom:1px solid var(--border); background:linear-gradient(180deg, var(--accent-l) 0%, #ffffff 75%);">
                <h1 style="margin:0 0 6px; font-size:1.3rem;">Verify your email address</h1>
                <p style="margin:0; font-size:0.9rem; color:var(--muted);">Enter the 6-digit code sent to <strong style="color:var(--ink);">{{ $maskedEmail }}</strong>.</p>
                <p style="margin:8px 0 0; font-size:0.78rem; color:var(--muted);">
                    @if($expires_at)
                        This code expires at {{ $expires_at->format('g:i A') }}.
                    @else
                        Codes expire after 10 minutes for security.
                    @endif
                </p>
            </div>
        @if(session('status'))
            <div class="alert success" style="margin:20px 32px 0;"><span>{{ session('status') }}</span></div>
        @endif
        @if($errors->any())
            <div class="alert error" style="margin:20px 32px 0;"><span>{{ $errors->first() }}</span></div>
        @endif

        <form method="post" action="{{ route('enroll.verify.submit', ['school_code' => $school->school_code]) }}" style="padding:28px 32px;" autocomplete="one-time-code">
            @csrf
            <div class="form-group" style="max-width:360px;">
                <label for="code">Verification Code</label>
                <input id="code" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required placeholder="Enter 6-digit code" value="{{ old('code') }}" style="font-size:1.15rem; letter-spacing:0.3em; text-align:center; font-weight:700;">
                <span class="input-hint">Tip: You can paste the code directly from your email.</span>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end; align-items:center; margin-top:14px; flex-wrap:wrap;">
                <button class="btn primary" type="submit" style="min-width:140px;">Verify email</button>
            </div>
        </form>
        <form method="post" action="{{ route('enroll.verify.resend', ['school_code' => $school->school_code]) }}" style="padding:0 32px 0;">
            @csrf
            <button class="btn ghost" type="submit">Resend code</button>
        </form>
        <div style="padding:0 32px 28px;">
            <p style="margin:0; font-size:0.78rem; color:var(--muted); line-height:1.5;">
                Didn’t receive the email? Check your spam/promotions folder, then click <strong>Resend code</strong>.
            </p>
        </div>
    </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const codeInput = document.getElementById('code');
    if (!codeInput) return;
    codeInput.focus();
    codeInput.addEventListener('input', function () {
        codeInput.value = (codeInput.value || '').replace(/\D/g, '').slice(0, 6);
    });
});
</script>
@endsection
