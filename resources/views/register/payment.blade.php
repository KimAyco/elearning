@extends('layouts.app')

@section('title', 'School Registration Payment')

@section('content')
<main class="page-body" style="max-width: 920px; margin: 0 auto;">
    <div class="page-header" style="margin-bottom: 18px;">
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
            <div class="card-icon green" style="width:34px; height:34px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="5" width="20" height="14" rx="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line>
                </svg>
            </div>
            <h1 style="margin:0;">School Registration Payment</h1>
        </div>
        <p>Pay with <strong>GCash</strong> or <strong>Maya</strong> via PayMongo. After payment, super admin will activate your school and admin account.</p>
    </div>

    @if($errors->any())
        <div class="alert error" style="margin-bottom:16px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <div class="card mb-16">
        <div class="card-header">
            <h2>
                <div class="card-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 21h16"></path><path d="M6 21V7l6-4 6 4v14"></path><path d="M10 11h4"></path><path d="M10 15h4"></path>
                    </svg>
                </div>
                Registration Summary
            </h2>
            <span class="badge {{ $registration->status === 'paid' ? 'green' : 'amber' }}">
                {{ strtoupper($registration->status) }}
            </span>
        </div>

        <div class="grid cols-2">
            <div>
                <div class="text-muted text-xs">School Name</div>
                <div class="fw-700">{{ $registration->name }}</div>
            </div>
            <div>
                <div class="text-muted text-xs">Owner Admin Email</div>
                <div class="fw-700">{{ $registration->email }}</div>
            </div>
            <div>
                <div class="text-muted text-xs">Plan</div>
                <div class="fw-700">{{ $plan->name }} ({{ $plan->months }} months)</div>
            </div>
            <div>
                <div class="text-muted text-xs">Amount to Pay</div>
                <div class="fw-700">PHP {{ number_format($plan->total_price, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>
                <div class="card-icon amber">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                </div>
                Choose payment
            </h2>
        </div>

        <p class="text-muted" style="margin-bottom:14px;">Only <strong>GCash</strong> and <strong>Maya</strong> are available. You will be redirected to authorize the payment.</p>

        <div class="stack" style="display:flex; flex-direction:column; gap:12px;">
            <form method="POST" action="{{ route('register.school.payment.confirm', ['registration' => $registration->id, 'token' => request('token')]) }}" style="margin:0;">
                @csrf
                <input type="hidden" name="payment_method" value="gcash">
                <input type="hidden" name="mock_confirm" value="1">
                <button type="submit" class="btn success" style="width:100%; display:flex; align-items:center; justify-content:center; gap:10px;">
                    <span style="font-size:1.25rem;">💙</span> Pay with GCash
                </button>
            </form>
            <form method="POST" action="{{ route('register.school.payment.confirm', ['registration' => $registration->id, 'token' => request('token')]) }}" style="margin:0;">
                @csrf
                <input type="hidden" name="payment_method" value="paymaya">
                <input type="hidden" name="mock_confirm" value="1">
                <button type="submit" class="btn success" style="width:100%; display:flex; align-items:center; justify-content:center; gap:10px; background:#00a651; border-color:#00a651;">
                    <span style="font-size:1.25rem;">💚</span> Pay with Maya
                </button>
            </form>
        </div>

        <div style="margin-top:20px;">
            <a href="{{ route('register.school.form') }}" class="btn secondary">Back</a>
        </div>
    </div>
</main>
@endsection
