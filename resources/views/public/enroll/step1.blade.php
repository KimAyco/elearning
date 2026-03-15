@extends('layouts.app')

@section('title', 'Enrollment — Step 1: Personal Information')

@section('content')
<div style="min-height:100vh; background: linear-gradient(160deg, #eff6ff 0%, #f4f7fe 50%, #f5f3ff 100%);">

    {{-- ── Top Nav ── --}}
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
            <span class="badge blue">Step 1 of 4</span>
        </div>
    </nav>

    <div style="max-width:700px; margin:0 auto; padding:48px 24px 64px;">

        {{-- ── Progress Steps ── --}}
        <div style="display:flex; align-items:center; justify-content:center; gap:8px; margin-bottom:36px;">
            @foreach(['Personal Info', 'Select Program', 'Review', 'Payment'] as $i => $step)
            @php $stepNum = $i + 1; @endphp
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; {{ $stepNum === 1 ? 'background:linear-gradient(135deg,#2563eb,#7c3aed); color:#fff;' : 'background:var(--surface-2); color:var(--muted); border:1px solid var(--border);' }}">
                    {{ $stepNum }}
                </div>
                <span style="font-size:0.78rem; font-weight:600; {{ $stepNum === 1 ? 'color:var(--ink);' : 'color:var(--muted);' }}">{{ $step }}</span>
                @if ($stepNum < 4)
                <div style="width:24px; height:2px; background:var(--border); margin-left:4px;"></div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- ── Form Card ── --}}
        <div style="background:white; border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow-lg); overflow:hidden;">
            <div style="padding:28px 32px; border-bottom:1px solid var(--border);">
                <h1 style="margin:0 0 6px; font-size:1.3rem;">Personal Information</h1>
                <p style="margin:0; font-size:0.88rem; color:var(--muted);">Fill in your details to create your student account.</p>
            </div>

            @if ($errors->any())
                <div class="alert error" style="margin:20px 32px 0;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="post" action="{{ route('enroll.step1', ['school_code' => $school->school_code]) }}" style="padding:28px 32px;">
                @csrf

                {{-- Full Name --}}
                <div class="form-group">
                    <label for="full_name">Full Name <span style="color:var(--red);">*</span></label>
                    <input id="full_name" name="full_name" type="text"
                           value="{{ old('full_name') }}"
                           placeholder="Juan Dela Cruz"
                           required>
                </div>

                {{-- Birth Date --}}
                <div class="form-group">
                    <label for="birth_date">Date of Birth <span style="color:var(--red);">*</span></label>
                    <input id="birth_date" name="birth_date" type="date"
                           value="{{ old('birth_date') }}"
                           required>
                    <span class="input-hint">Your age will be calculated automatically</span>
                </div>

                {{-- Gender --}}
                <div class="form-group">
                    <label for="gender">Gender <span style="color:var(--red);">*</span></label>
                    <select id="gender" name="gender" required>
                        <option value="">Select gender</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email Address <span style="color:var(--red);">*</span></label>
                    <input id="email" name="email" type="email"
                           value="{{ old('email') }}"
                           placeholder="you@example.com"
                           required>
                    <span class="input-hint">This will be your login username</span>
                </div>

                {{-- Phone --}}
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input id="phone" name="phone" type="tel"
                           value="{{ old('phone') }}"
                           placeholder="+63 912 345 6789">
                </div>

                {{-- Address --}}
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="2"
                              placeholder="Street, City, Province, Zip Code">{{ old('address') }}</textarea>
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="password">Password <span style="color:var(--red);">*</span></label>
                    <input id="password" name="password" type="password"
                           placeholder="Minimum 8 characters"
                           required>
                </div>

                {{-- Confirm Password --}}
                <div class="form-group">
                    <label for="password_confirmation">Confirm Password <span style="color:var(--red);">*</span></label>
                    <input id="password_confirmation" name="password_confirmation" type="password"
                           placeholder="Re-enter your password"
                           required>
                </div>

                <div style="display:flex; gap:12px; margin-top:8px;">
                    <a href="{{ route('school.enroll', ['school_code' => $school->school_code]) }}"
                       style="display:inline-flex; align-items:center; gap:6px; padding:12px 20px; background:white; color:var(--ink-2); border:1.5px solid var(--border); border-radius:12px; font-size:0.9rem; font-weight:600; text-decoration:none;">
                        ← Back
                    </a>
                    <button class="btn primary lg" type="submit" style="flex:1;">
                        proceed to payment
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Footer ── --}}
    <footer style="text-align:center; padding:32px 24px; color:var(--muted); font-size:0.8rem; border-top:1px solid var(--border); background:white;">
        <p>© {{ date('Y') }} EduPlatform. University SaaS E-Learning System.</p>
    </footer>
</div>
@endsection
