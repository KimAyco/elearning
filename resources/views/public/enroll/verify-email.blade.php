@extends('layouts.app')

@section('title', 'Verify Email')

@section('content')
<div style="min-height:100vh; background: linear-gradient(160deg, #eff6ff 0%, #f4f7fe 50%, #f5f3ff 100%);">
    <nav style="background: rgba(255,255,255,.85); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); position: sticky; top:0; z-index:50;">
        <div style="max-width:900px; margin:0 auto; padding:0 24px; height:60px; display:flex; align-items:center; justify-content:space-between;">
            <a href="{{ route('school.enroll', ['school_code' => $school->school_code]) }}" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                <div style="width:34px; height:34px; background:linear-gradient(135deg,#2563eb,#7c3aed); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <span style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:1rem; color:var(--ink);">{{ $school->name }}</span>
            </a>
            <span class="badge blue">Email Verification</span>
        </div>
    </nav>

    <div style="max-width:700px; margin:0 auto; padding:48px 24px 64px;">
        <div style="background:white; border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow-lg); overflow:hidden;">
            <div style="padding:28px 32px; border-bottom:1px solid var(--border);">
                <h1 style="margin:0 0 6px; font-size:1.3rem;">Verify Your Email</h1>
                <p style="margin:0; font-size:0.88rem; color:var(--muted);">Enter the 6-digit code sent to {{ $sent_to }}</p>
            </div>
        @if(session('status'))
            <div class="alert success" style="margin:20px 32px 0;"><span>{{ session('status') }}</span></div>
        @endif
        @if($errors->any())
            <div class="alert error" style="margin:20px 32px 0;"><span>{{ $errors->first() }}</span></div>
        @endif

        <form method="post" action="{{ route('enroll.verify.submit', ['school_code' => $school->school_code]) }}" style="padding:28px 32px;">
            @csrf
            <div class="form-group" style="max-width:320px;">
                <label for="code">Verification Code</label>
                <input id="code" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" required placeholder="Enter 6-digit code">
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:8px;">
                <button class="btn primary" type="submit">Verify</button>
            </div>
        </form>

        <form method="post" action="{{ route('enroll.verify.resend', ['school_code' => $school->school_code]) }}" style="padding:0 32px 28px;">
            @csrf
            <button class="btn ghost" type="submit">Resend Code</button>
        </form>
    </div>
    </div>
</div>
@endsection
