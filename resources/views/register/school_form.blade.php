@extends('layouts.app')

@section('title', 'Register Your School')

@section('content')
@php
    $step1Err = $errors->has('name') || $errors->has('email') || $errors->has('phone') || $errors->has('address') || $errors->has('password') || $errors->has('password_confirmation');
    $showStep2 = ! $step1Err && (old('school_reg_step') === '2' || $errors->any());
@endphp
<main class="page-body school-reg-page school-reg-pro">
    <div class="school-reg-pro__ambient" aria-hidden="true"></div>

    <div class="school-reg-pro__wrap">
        <aside class="school-reg-pro__hero">
            <a href="{{ url('/') }}" class="school-reg-pro__brand">
                <span class="school-reg-pro__brand-mark">
                    <img src="{{ url('/images/icon.png') }}?v=20260410" alt="" width="40" height="40" class="school-reg-pro__brand-img" decoding="async">
                </span>
                <span class="school-reg-pro__brand-text">EduPlatform</span>
            </a>

            <p class="school-reg-pro__eyebrow">School onboarding</p>
            <h1 class="school-reg-pro__title">Bring your institution online</h1>
            <p class="school-reg-pro__lead">Enter your school details first, then choose a subscription plan. You’ll verify your email next, and complete payment after that.</p>

            <ul class="school-reg-pro__benefits">
                <li>
                    <span class="school-reg-pro__benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </span>
                    <span><strong>Two quick steps</strong> — school profile, then billing term.</span>
                </li>
                <li>
                    <span class="school-reg-pro__benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <span><strong>Secure by design</strong> — email verification before payment.</span>
                </li>
                <li>
                    <span class="school-reg-pro__benefit-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </span>
                    <span><strong>Flexible billing</strong> — pick the plan term that fits your budget.</span>
                </li>
            </ul>

            <div class="school-reg-pro__timeline" id="sr_timeline">
                <div class="school-reg-pro__timeline-item js-sr-tl" data-sr-step="1">
                    <span class="school-reg-pro__timeline-dot"></span>
                    <span class="school-reg-pro__timeline-label">School details</span>
                </div>
                <div class="school-reg-pro__timeline-item js-sr-tl" data-sr-step="2">
                    <span class="school-reg-pro__timeline-dot"></span>
                    <span class="school-reg-pro__timeline-label">Subscription</span>
                </div>
                <div class="school-reg-pro__timeline-item js-sr-tl" data-sr-step="3">
                    <span class="school-reg-pro__timeline-dot"></span>
                    <span class="school-reg-pro__timeline-label">Verify email</span>
                </div>
                <div class="school-reg-pro__timeline-item js-sr-tl" data-sr-step="4">
                    <span class="school-reg-pro__timeline-dot"></span>
                    <span class="school-reg-pro__timeline-label">Payment</span>
                </div>
            </div>
        </aside>

        <div class="school-reg-pro__panel">
            <form method="POST" action="{{ route('register.school.submit') }}" class="school-reg-pro__form" id="sr_form">
                @csrf
                <input type="hidden" name="school_reg_step" id="sr_school_reg_step" value="{{ $showStep2 ? '2' : '1' }}">

                <div class="school-reg-pro__sheet">
                    @if($errors->any())
                        <div class="school-reg-pro__alert" role="alert">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif
                    <header class="school-reg-pro__sheet-head">
                        <div class="school-reg-pro__step-pills" role="navigation" aria-label="Registration steps">
                            <span class="school-reg-pro__step-pill js-sr-pill {{ ! $showStep2 ? 'is-active' : '' }}" data-sr-pill="1">1 · School</span>
                            <span class="school-reg-pro__step-pill js-sr-pill {{ $showStep2 ? 'is-active' : '' }}" data-sr-pill="2">2 · Plan</span>
                        </div>
                        <h2 class="school-reg-pro__sheet-title" id="sr_sheet_title">{{ $showStep2 ? 'Choose your plan' : 'School & administrator' }}</h2>
                        <p class="school-reg-pro__sheet-lead" id="sr_sheet_lead">{{ $showStep2 ? 'Select a billing term. You’ll confirm your email on the next screen.' : 'School name, contact, and admin password — then continue to plans.' }}</p>
                    </header>

                    <div class="school-reg-pro__steps-viewport">
                        <div id="sr_step_1" class="school-reg-pro__step {{ $showStep2 ? 'is-hidden' : '' }}" data-sr-panel="1" {{ $showStep2 ? 'hidden' : '' }}>
                        <h3 class="school-reg-pro__subhead">School &amp; contact</h3>
                        <div class="school-reg-pro__fields school-reg-pro__fields--tight">
                            <div class="school-reg-pro__field school-reg-pro__field--full">
                                <label for="sr_name">School name</label>
                                <input id="sr_name" name="name" required value="{{ old('name') }}" placeholder="e.g. Riverside Academy" autocomplete="organization">
                                @error('name') <span class="school-reg-pro__err">{{ $message }}</span> @enderror
                            </div>

                            <div class="school-reg-pro__field">
                                <label for="sr_email">Admin email</label>
                                <input id="sr_email" name="email" type="email" required value="{{ old('email') }}" placeholder="admin@school.edu" autocomplete="email">
                                @error('email') <span class="school-reg-pro__err">{{ $message }}</span> @enderror
                            </div>

                            <div class="school-reg-pro__field">
                                <label for="sr_phone">Phone <span class="school-reg-pro__opt">opt.</span></label>
                                <input id="sr_phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="+63 900 000 0000" autocomplete="tel">
                                @error('phone') <span class="school-reg-pro__err">{{ $message }}</span> @enderror
                            </div>

                            <div class="school-reg-pro__field school-reg-pro__field--full">
                                <label for="sr_address">Address <span class="school-reg-pro__opt">opt.</span></label>
                                <textarea id="sr_address" name="address" rows="2" placeholder="Street, city, region">{{ old('address') }}</textarea>
                                @error('address') <span class="school-reg-pro__err">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <h3 class="school-reg-pro__subhead">Admin password</h3>
                        <div class="school-reg-pro__fields school-reg-pro__fields--tight">
                            <div class="school-reg-pro__field">
                                <label for="sr_pw">Password</label>
                                <input id="sr_pw" name="password" type="password" required minlength="8" placeholder="8+ characters" autocomplete="new-password">
                                @error('password') <span class="school-reg-pro__err">{{ $message }}</span> @enderror
                            </div>

                            <div class="school-reg-pro__field">
                                <label for="sr_pw2">Confirm</label>
                                <input id="sr_pw2" name="password_confirmation" type="password" required minlength="8" placeholder="Repeat" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="school-reg-pro__actions">
                            <a href="{{ url('/') }}" class="school-reg-pro__btn school-reg-pro__btn--ghost">Cancel</a>
                            <button type="button" class="school-reg-pro__btn school-reg-pro__btn--primary" id="sr_btn_next">
                                Next
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </button>
                        </div>
                        </div>

                        <div id="sr_step_2" class="school-reg-pro__step {{ $showStep2 ? '' : 'is-hidden' }}" data-sr-panel="2" {{ $showStep2 ? '' : 'hidden' }}>
                        <h3 class="school-reg-pro__subhead">Subscription plan</h3>

                        @php
                            $defaultPlanMonths = $plans->firstWhere('months', 12)->months ?? ($plans->first()->months ?? null);
                            $selectedPlanMonths = old('plan_months', $defaultPlanMonths);
                        @endphp

                        @if($plans->isEmpty())
                            <p class="school-reg-pro__empty">No plans are available yet. Please contact support.</p>
                        @else
                            <fieldset class="school-reg-pro__plans">
                                <legend class="school-reg-pro__sr-only">Choose a plan</legend>
                                @foreach($plans as $plan)
                                    @php
                                        $price = $plan->total_price ?? ($plan->price_per_month * $plan->months);
                                        $isSelected = (string) $selectedPlanMonths === (string) $plan->months;
                                    @endphp
                                    <label class="school-reg-pro__plan {{ $isSelected ? 'is-selected' : '' }}">
                                        <input type="radio" name="plan_months" value="{{ $plan->months }}" class="school-reg-pro__plan-input" {{ $isSelected ? 'checked' : '' }}>
                                        <span class="school-reg-pro__plan-body">
                                            <span class="school-reg-pro__plan-top">
                                                <span class="school-reg-pro__plan-name">{{ $plan->name }}</span>
                                                <span class="school-reg-pro__plan-price">PHP {{ number_format($price, 2) }}</span>
                                            </span>
                                            <span class="school-reg-pro__plan-meta">{{ $plan->months }} months · billed once</span>
                                        </span>
                                        <span class="school-reg-pro__plan-check" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        </span>
                                    </label>
                                @endforeach
                            </fieldset>
                            @error('plan_months') <span class="school-reg-pro__err school-reg-pro__err--block">{{ $message }}</span> @enderror
                        @endif

                        <div class="school-reg-pro__actions">
                            <button type="button" class="school-reg-pro__btn school-reg-pro__btn--ghost" id="sr_btn_back">Back</button>
                            <button type="submit" class="school-reg-pro__btn school-reg-pro__btn--primary" @if($plans->isEmpty()) disabled @endif>
                                Continue to verification
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </button>
                        </div>
                        </div>
                    </div>
                </div>
            </form>

            <p class="school-reg-pro__signin">
                Already registered? <a href="{{ route('login') }}">Sign in</a>
            </p>
        </div>
    </div>
</main>

<style>
    .school-reg-pro {
        --sr-accent: #16a34a;
        --sr-accent-soft: #22c55e;
        --sr-accent-h: #15803d;
        --sr-accent-l: rgba(34, 197, 94, 0.18);
        --sr-shadow: rgba(22, 163, 74, 0.32);
        --sr-shadow-hover: rgba(22, 163, 74, 0.38);
        position: relative;
        min-height: calc(100vh - 48px);
        padding: 0;
        overflow: hidden;
    }
    .school-reg-pro__ambient {
        position: absolute;
        inset: 0;
        background:
            radial-gradient(ellipse 90% 55% at 15% -5%, rgba(34, 197, 94, 0.12), transparent 52%),
            radial-gradient(ellipse 70% 50% at 92% 5%, rgba(86, 182, 31, 0.07), transparent 48%),
            linear-gradient(180deg, rgba(34, 197, 94, 0.06) 0%, rgba(34, 197, 94, 0) 55%, var(--bg) 100%);
        pointer-events: none;
    }
    .school-reg-pro__ambient::before {
        content: '';
        position: absolute;
        left: -200px;
        bottom: -320px;
        width: 560px;
        height: 560px;
        background: rgba(34, 197, 94, 0.14);
        border-radius: 55% 45% 40% 60% / 45% 55% 45% 55%;
        pointer-events: none;
    }
    .school-reg-pro__ambient::after {
        content: '';
        position: absolute;
        right: -120px;
        top: -160px;
        width: 420px;
        height: 400px;
        background: linear-gradient(135deg, rgba(108, 200, 64, 0.35) 0%, rgba(86, 182, 31, 0.22) 100%);
        border-radius: 58% 42% 43% 57% / 56% 43% 57% 44%;
        transform: rotate(6deg);
        pointer-events: none;
    }
    .school-reg-pro__wrap {
        position: relative;
        z-index: 1;
        max-width: 1200px;
        margin: 0 auto;
        padding: clamp(24px, 4vw, 48px) clamp(16px, 3vw, 32px) 48px;
        display: grid;
        grid-template-columns: minmax(280px, 1fr) minmax(0, 520px);
        gap: clamp(24px, 4vw, 56px);
        align-items: start;
    }
    @media (max-width: 960px) {
        .school-reg-pro__wrap {
            grid-template-columns: 1fr;
            max-width: 560px;
        }
    }

    /* Hero column */
    .school-reg-pro__hero {
        padding-top: 8px;
    }
    .school-reg-pro__brand {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: var(--ink);
        margin-bottom: 22px;
    }
    .school-reg-pro__brand-mark {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--surface);
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        flex-shrink: 0;
    }
    .school-reg-pro__brand-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        display: block;
    }
    .school-reg-pro__brand-text {
        font-weight: 800;
        font-size: 2.1rem;
        letter-spacing: -0.02em;
    }
    .school-reg-pro__eyebrow {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--sr-accent-soft);
        margin-bottom: 10px;
    }
    .school-reg-pro__title {
        font-size: clamp(1.75rem, 4vw, 2.35rem);
        font-weight: 800;
        letter-spacing: -0.035em;
        line-height: 1.12;
        color: var(--ink);
        margin: 0 0 14px;
    }
    .school-reg-pro__lead {
        font-size: 0.95rem;
        line-height: 1.55;
        color: var(--ink-2);
        margin: 0 0 20px;
        max-width: 38ch;
    }
    .school-reg-pro__benefits {
        list-style: none;
        margin: 0 0 22px;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .school-reg-pro__benefits li {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        font-size: 0.92rem;
        line-height: 1.5;
        color: var(--ink-2);
    }
    .school-reg-pro__benefits strong { color: var(--ink); font-weight: 700; }
    .school-reg-pro__benefit-icon {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: var(--surface);
        border: 1px solid var(--border);
        color: var(--sr-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-sm);
    }
    .school-reg-pro__benefit-icon svg { width: 16px; height: 16px; }

    .school-reg-pro__timeline {
        display: flex;
        flex-direction: column;
        gap: 0;
        padding-left: 4px;
        border-left: 2px solid var(--border);
    }
    .school-reg-pro__timeline-item {
        position: relative;
        padding: 0 0 12px 18px;
        margin-left: -7px;
    }
    .school-reg-pro__timeline-item:last-child { padding-bottom: 0; }
    .school-reg-pro__timeline-dot {
        position: absolute;
        left: -7px;
        top: 4px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--surface);
        border: 2px solid var(--border-2);
    }
    .school-reg-pro__timeline-item.is-active .school-reg-pro__timeline-dot {
        background: var(--sr-accent);
        border-color: var(--sr-accent);
        box-shadow: 0 0 0 4px var(--sr-accent-l);
    }
    .school-reg-pro__timeline-item.is-done .school-reg-pro__timeline-dot {
        background: var(--sr-accent);
        border-color: var(--sr-accent);
        opacity: 0.55;
    }
    .school-reg-pro__timeline-item.is-done .school-reg-pro__timeline-label {
        color: var(--ink-2);
        font-weight: 600;
    }
    .school-reg-pro__timeline-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--muted);
    }
    .school-reg-pro__timeline-item.is-active .school-reg-pro__timeline-label {
        color: var(--ink);
    }

    /* Panel / form — single compact sheet */
    .school-reg-pro__panel {
        background: transparent;
        border-radius: 0;
        border: none;
        box-shadow: none;
        padding: 0;
    }
    .school-reg-pro__sheet {
        background: var(--surface);
        border-radius: var(--radius-xl);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-lg);
        padding: 20px 20px 18px;
    }
    .school-reg-pro__sheet-head {
        margin-bottom: 12px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--border);
    }
    .school-reg-pro__sheet .school-reg-pro__alert + .school-reg-pro__sheet-head {
        margin-top: 0;
    }
    .school-reg-pro__sheet-title {
        margin: 0 0 4px;
        font-size: 1.2rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--ink);
    }
    .school-reg-pro__sheet-lead {
        margin: 0;
        font-size: 0.84rem;
        color: var(--muted);
        line-height: 1.45;
    }
    .school-reg-pro__step-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 12px;
    }
    .school-reg-pro__step-pill {
        display: inline-flex;
        align-items: center;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 6px 11px;
        border-radius: 999px;
        border: 1px solid var(--border);
        color: var(--muted);
        background: var(--surface-2);
        transition: border-color 0.2s, color 0.2s, background 0.2s;
    }
    .school-reg-pro__step-pill.is-active {
        border-color: var(--sr-accent);
        color: var(--sr-accent-h);
        background: var(--sr-accent-l);
    }
    .school-reg-pro__steps-viewport {
        position: relative;
        overflow: hidden;
    }
    .school-reg-pro__step.is-hidden {
        display: none !important;
    }
    .school-reg-pro__step.school-reg-pro__step--out {
        animation: sr-step-out 0.32s cubic-bezier(0.33, 1, 0.68, 1) forwards;
        pointer-events: none;
    }
    .school-reg-pro__step.school-reg-pro__step--in {
        animation: sr-step-in 0.38s cubic-bezier(0.33, 1, 0.68, 1) forwards;
    }
    .school-reg-pro__step.school-reg-pro__step--out-rev {
        animation: sr-step-out-rev 0.32s cubic-bezier(0.33, 1, 0.68, 1) forwards;
        pointer-events: none;
    }
    .school-reg-pro__step.school-reg-pro__step--in-rev {
        animation: sr-step-in-rev 0.38s cubic-bezier(0.33, 1, 0.68, 1) forwards;
    }
    @keyframes sr-step-out {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-6px);
        }
    }
    @keyframes sr-step-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @keyframes sr-step-out-rev {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(8px);
        }
    }
    @keyframes sr-step-in-rev {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    @media (prefers-reduced-motion: reduce) {
        .school-reg-pro__step.school-reg-pro__step--out,
        .school-reg-pro__step.school-reg-pro__step--in,
        .school-reg-pro__step.school-reg-pro__step--out-rev,
        .school-reg-pro__step.school-reg-pro__step--in-rev {
            animation: none;
        }
    }
    .school-reg-pro__subhead {
        margin: 14px 0 8px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .school-reg-pro__subhead:first-of-type {
        margin-top: 0;
    }
    .school-reg-pro__sep {
        border: none;
        height: 1px;
        margin: 14px 0 12px;
        background: linear-gradient(90deg, transparent, var(--border), transparent);
    }
    .school-reg-pro__alert {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: 10px 12px;
        border-radius: var(--radius);
        background: var(--red-l);
        border: 1px solid var(--red-l2);
        color: #991b1b;
        font-size: 0.86rem;
        margin: 0 0 14px;
        line-height: 1.45;
    }
    .school-reg-pro__alert svg { flex-shrink: 0; width: 18px; height: 18px; margin-top: 1px; }

    .school-reg-pro__form {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .school-reg-pro__fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px 14px;
    }
    .school-reg-pro__fields--tight {
        gap: 10px 12px;
    }
    .school-reg-pro__field--full { grid-column: 1 / -1; }
    .school-reg-pro__field label {
        display: block;
        font-size: 0.74rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--ink-2);
        margin-bottom: 5px;
    }
    .school-reg-pro__opt {
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0;
        color: var(--muted);
        font-size: 0.75rem;
    }
    .school-reg-pro__field input,
    .school-reg-pro__field textarea {
        width: 100%;
        min-height: 42px;
        padding: 9px 12px;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        background: var(--surface-2);
        color: var(--ink);
        font-size: 0.92rem;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
    }
    .school-reg-pro__field textarea {
        min-height: 68px;
        resize: vertical;
        line-height: 1.45;
    }
    .school-reg-pro__field input::placeholder,
    .school-reg-pro__field textarea::placeholder {
        color: #94a3b8;
    }
    .school-reg-pro__field input:hover,
    .school-reg-pro__field textarea:hover {
        border-color: var(--border-2);
        background: var(--surface);
    }
    .school-reg-pro__field input:focus,
    .school-reg-pro__field textarea:focus {
        outline: none;
        border-color: var(--sr-accent);
        background: var(--surface);
        box-shadow: 0 0 0 3px var(--sr-accent-l);
    }
    .school-reg-pro__err {
        display: block;
        margin-top: 5px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--red);
    }
    .school-reg-pro__err--block { margin-top: 10px; }

    .school-reg-pro__empty {
        margin: 0;
        padding: 14px 16px;
        border-radius: var(--radius);
        background: var(--amber-l);
        border: 1px solid var(--amber-l2);
        color: #92400e;
        font-size: 0.9rem;
    }

    .school-reg-pro__plans {
        border: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .school-reg-pro__sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        border: 0;
    }
    .school-reg-pro__plan {
        position: relative;
        display: block;
        cursor: pointer;
        border-radius: var(--radius);
        border: 2px solid var(--border);
        background: var(--surface-2);
        padding: 10px 12px 10px 42px;
        transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    }
    .school-reg-pro__plan:hover {
        border-color: var(--border-2);
        background: var(--surface);
    }
    .school-reg-pro__plan.is-selected {
        border-color: var(--sr-accent);
        background: var(--sr-accent-l);
        box-shadow: 0 0 0 1px rgba(22, 163, 74, 0.12);
    }
    .school-reg-pro__plan-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }
    .school-reg-pro__plan-body {
        display: block;
    }
    .school-reg-pro__plan-top {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 12px;
        margin-bottom: 4px;
    }
    .school-reg-pro__plan-name {
        font-weight: 800;
        font-size: 0.9rem;
        color: var(--ink);
    }
    .school-reg-pro__plan-price {
        font-weight: 800;
        font-size: 0.95rem;
        color: var(--sr-accent);
        white-space: nowrap;
    }
    .school-reg-pro__plan-meta {
        font-size: 0.75rem;
        color: var(--muted);
        font-weight: 500;
    }
    .school-reg-pro__plan-check {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid var(--border-2);
        background: var(--surface);
        display: flex;
        align-items: center;
        justify-content: center;
        color: transparent;
        transition: 0.2s;
    }
    .school-reg-pro__plan-check svg { width: 12px; height: 12px; }
    .school-reg-pro__plan.is-selected .school-reg-pro__plan-check {
        border-color: var(--sr-accent);
        background: var(--sr-accent);
        color: #fff;
    }

    .school-reg-pro__actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 14px;
        margin-top: 12px;
        border-top: 1px solid var(--border);
    }
    .school-reg-pro__btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 44px;
        padding: 0 18px;
        border-radius: var(--radius);
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
        transition: transform 0.15s, box-shadow 0.2s, background 0.2s, border-color 0.2s;
    }
    .school-reg-pro__btn svg { width: 18px; height: 18px; }
    .school-reg-pro__btn--ghost {
        background: transparent;
        border-color: var(--border);
        color: var(--ink-2);
    }
    .school-reg-pro__btn--ghost:hover {
        background: var(--surface-2);
        border-color: var(--border-2);
    }
    .school-reg-pro__btn--primary {
        background: linear-gradient(135deg, var(--sr-accent) 0%, var(--sr-accent-h) 100%);
        color: #fff;
        border-color: var(--sr-accent-h);
        box-shadow: 0 8px 24px var(--sr-shadow);
    }
    .school-reg-pro__btn--primary:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 12px 28px var(--sr-shadow-hover);
    }
    .school-reg-pro__btn--primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    .school-reg-pro__signin {
        text-align: center;
        margin-top: 14px;
        font-size: 0.88rem;
        color: var(--muted);
    }
    .school-reg-pro__signin a {
        color: var(--sr-accent);
        font-weight: 700;
        text-decoration: none;
    }
    .school-reg-pro__signin a:hover { text-decoration: underline; }

    @media (max-width: 640px) {
        .school-reg-pro__fields {
            grid-template-columns: 1fr;
        }
        .school-reg-pro__actions {
            flex-direction: column-reverse;
        }
        .school-reg-pro__btn {
            width: 100%;
        }
        .school-reg-pro__timeline { display: none; }
    }
</style>
<script>
    (function () {
        var form = document.getElementById('sr_form');
        var step1 = document.getElementById('sr_step_1');
        var step2 = document.getElementById('sr_step_2');
        var hiddenStep = document.getElementById('sr_school_reg_step');
        var btnNext = document.getElementById('sr_btn_next');
        var btnBack = document.getElementById('sr_btn_back');
        var titleEl = document.getElementById('sr_sheet_title');
        var leadEl = document.getElementById('sr_sheet_lead');
        var pills = document.querySelectorAll('.js-sr-pill');
        var timelineItems = document.querySelectorAll('.js-sr-tl');
        var prefersReducedMotion =
            window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        var titles = { 1: 'School & administrator', 2: 'Choose your plan' };
        var leads = {
            1: 'School name, contact, and admin password — then continue to plans.',
            2: 'Select a billing term. You’ll confirm your email on the next screen.'
        };

        function scrollToStep(step) {
            var el = step === 2 ? step2 : step1;
            if (!el) return;
            requestAnimationFrame(function () {
                el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        }

        function applyStepState(step, doScroll) {
            var is2 = step === 2;
            step1.classList.toggle('is-hidden', is2);
            step2.classList.toggle('is-hidden', !is2);
            if (is2) {
                step1.setAttribute('hidden', '');
                step2.removeAttribute('hidden');
            } else {
                step1.removeAttribute('hidden');
                step2.setAttribute('hidden', '');
            }
            if (hiddenStep) hiddenStep.value = String(step);

            pills.forEach(function (p) {
                var n = parseInt(p.getAttribute('data-sr-pill'), 10);
                p.classList.toggle('is-active', n === step);
            });

            if (titleEl) titleEl.textContent = titles[step];
            if (leadEl) leadEl.textContent = leads[step];

            timelineItems.forEach(function (el) {
                var n = parseInt(el.getAttribute('data-sr-step'), 10);
                el.classList.remove('is-active', 'is-done');
                if (step === 1) {
                    if (n === 1) el.classList.add('is-active');
                } else {
                    if (n === 1) el.classList.add('is-done');
                    if (n === 2) el.classList.add('is-active');
                }
            });

            if (doScroll) scrollToStep(step);
        }

        function animateForward() {
            if (prefersReducedMotion || !step1 || !step2) {
                applyStepState(2, true);
                return;
            }
            step1.classList.add('school-reg-pro__step--out');
            step1.addEventListener(
                'animationend',
                function onOut() {
                    step1.classList.remove('school-reg-pro__step--out');
                    applyStepState(2, false);
                    step2.classList.add('school-reg-pro__step--in');
                    step2.addEventListener(
                        'animationend',
                        function onIn() {
                            step2.classList.remove('school-reg-pro__step--in');
                            scrollToStep(2);
                        },
                        { once: true }
                    );
                },
                { once: true }
            );
        }

        function animateBack() {
            if (prefersReducedMotion || !step1 || !step2) {
                applyStepState(1, true);
                return;
            }
            step2.classList.add('school-reg-pro__step--out-rev');
            step2.addEventListener(
                'animationend',
                function onOut() {
                    step2.classList.remove('school-reg-pro__step--out-rev');
                    applyStepState(1, false);
                    step1.classList.add('school-reg-pro__step--in-rev');
                    step1.addEventListener(
                        'animationend',
                        function onIn() {
                            step1.classList.remove('school-reg-pro__step--in-rev');
                            scrollToStep(1);
                        },
                        { once: true }
                    );
                },
                { once: true }
            );
        }

        document.querySelectorAll('.school-reg-pro__plan').forEach(function (label) {
            var input = label.querySelector('.school-reg-pro__plan-input');
            if (!input) return;
            input.addEventListener('change', function () {
                document.querySelectorAll('.school-reg-pro__plan').forEach(function (el) {
                    var inp = el.querySelector('.school-reg-pro__plan-input');
                    el.classList.toggle('is-selected', inp && inp.checked);
                });
            });
        });

        if (btnNext) {
            btnNext.addEventListener('click', function () {
                if (!step1) return;
                var fields = step1.querySelectorAll('input, textarea');
                for (var i = 0; i < fields.length; i++) {
                    if (!fields[i].checkValidity()) {
                        fields[i].reportValidity();
                        return;
                    }
                }
                animateForward();
            });
        }

        if (btnBack) {
            btnBack.addEventListener('click', function () {
                animateBack();
            });
        }

        if (form && step2) {
            form.addEventListener('submit', function (e) {
                if (step2.classList.contains('is-hidden')) {
                    e.preventDefault();
                }
            });
        }

        var initialStep = hiddenStep && hiddenStep.value === '2' ? 2 : 1;
        applyStepState(initialStep, false);
    })();
</script>
@endsection
