@extends('layouts.app')

@section('title', 'Register Your School')

@section('content')
<main class="page-body" style="max-width: 920px; margin: 0 auto;">
    <div class="page-header" style="margin-bottom: 16px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
            <div class="card-icon purple" style="width:34px; height:34px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 21h16"/><path d="M6 21V7l6-4 6 4v14"/><path d="M10 11h4"/><path d="M10 15h4"/>
                </svg>
            </div>
            <h1 style="margin:0;">Register Your School</h1>
        </div>
        <p>Complete your school profile and choose a subscription plan to continue to payment.</p>
    </div>

    @if($errors->any())
        <div class="alert error" style="margin-bottom:16px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('register.school.submit') }}" class="stack">
        @csrf

        <div class="card">
            <div class="card-header">
                <h2>
                    <div class="card-icon blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 21h16"/><path d="M6 21V7l6-4 6 4v14"/><path d="M10 11h4"/><path d="M10 15h4"/>
                        </svg>
                    </div>
                    School Information
                </h2>
            </div>

            <div class="grid cols-2">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>School Name</label>
                    <input name="name" required value="{{ old('name') }}" placeholder="Enter school name">
                    @error('name') <small style="color:var(--red);">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>School Admin Email Address</label>
                    <input name="email" type="email" required value="{{ old('email') }}" placeholder="admin@school.edu">
                    @error('email') <small style="color:var(--red);">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input name="phone" value="{{ old('phone') }}" placeholder="+63 900 000 0000">
                    @error('phone') <small style="color:var(--red);">{{ $message }}</small> @enderror
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Address</label>
                    <textarea name="address" rows="3" placeholder="Enter school address">{{ old('address') }}</textarea>
                    @error('address') <small style="color:var(--red);">{{ $message }}</small> @enderror
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Subdomain <span style="font-weight:400; color:var(--muted);">(optional)</span></label>
                    <div style="display:flex;">
                        <span style="display:inline-flex; align-items:center; padding:0 12px; border:1.5px solid var(--border); border-right:none; border-radius:var(--radius) 0 0 var(--radius); background:var(--surface-2); color:var(--muted);">
                            yourschool.
                        </span>
                        <input name="subdomain" value="{{ old('subdomain') }}" placeholder="domain" style="border-radius:0 var(--radius) var(--radius) 0;">
                    </div>
                    @error('subdomain') <small style="color:var(--red);">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>School Admin Password</label>
                    <input name="password" type="password" required minlength="8" placeholder="Minimum 8 characters">
                    @error('password') <small style="color:var(--red);">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <input name="password_confirmation" type="password" required minlength="8" placeholder="Re-enter password">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>
                    <div class="card-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 6h18"/><path d="M3 12h18"/><path d="M3 18h18"/><path d="M17 4v16"/>
                        </svg>
                    </div>
                    Choose Your Plan
                </h2>
            </div>

            <div class="form-group">
                <label for="plan_months">Subscription Plan</label>
                @php
                    $defaultPlanMonths = $plans->firstWhere('months', 12)->months ?? ($plans->first()->months ?? null);
                    $selectedPlanMonths = old('plan_months', $defaultPlanMonths);
                @endphp
                <select id="plan_months" name="plan_months" required>
                    @forelse($plans as $plan)
                        <option value="{{ $plan->months }}" {{ (string) $selectedPlanMonths === (string) $plan->months ? 'selected' : '' }}>
                            {{ $plan->name }} - {{ $plan->months }} months - PHP {{ number_format($plan->total_price ?? ($plan->price_per_month * $plan->months), 2) }}
                        </option>
                    @empty
                        <option value="">No payment plans configured yet</option>
                    @endforelse
                </select>
            </div>
        </div>

        <div style="display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:4px;">
            <a href="{{ url('/') }}" class="btn secondary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Back to Home
            </a>

            <button type="submit" class="btn primary">
                Proceed to Payment
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                </svg>
            </button>
        </div>
    </form>

    <p style="text-align:center; color:var(--muted); margin-top:18px;">
        Already have an account?
        <a href="{{ route('login') }}" style="font-weight:600;">Sign in here</a>
    </p>
</main>
@endsection
