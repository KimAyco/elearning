@extends('layouts.app')

@section('title', 'Enrollment Complete!')

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
    $mockPayment = session('mock_payment');
    $receiptTransactionId = (string) ($mockPayment['transaction_id'] ?? 'N/A');
    $receiptPaidAt = (string) ($mockPayment['paid_at'] ?? now()->format('Y-m-d H:i:s'));
    $receiptAmount = (float) ($mockPayment['amount'] ?? 0);
    $receiptStatus = (string) ($mockPayment['status'] ?? 'Submitted');
    $receiptMethod = (string) ($mockPayment['method'] ?? 'N/A');
    $receiptNo = 'OR-' . now()->format('Ymd') . '-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $receiptTransactionId), 0, 8) ?: 'ENROLL');
@endphp
<style>
.enroll-success-page{min-height:100vh;--accent:{{ $enrollTheme['accent'] }};--accent-h:{{ $enrollTheme['accent_h'] }};--accent-l:{{ $enrollTheme['accent_l'] }};--accent-l2:{{ $enrollTheme['accent_l2'] }};background:linear-gradient(160deg, {{ $enrollTheme['bg_1'] }} 0%, {{ $enrollTheme['bg_2'] }} 50%, {{ $enrollTheme['bg_3'] }} 100%);}
.enroll-success-topbar{background:rgba(255,255,255,.9);backdrop-filter:blur(12px);border-bottom:1px solid var(--border);position:sticky;top:0;z-index:50;}
.enroll-success-topbar-inner{max-width:980px;margin:0 auto;padding:0 24px;height:62px;display:flex;align-items:center;justify-content:space-between;}
.enroll-success-shell{max-width:980px;margin:0 auto;padding:44px 24px 64px;display:grid;grid-template-columns:minmax(0,1.25fr) minmax(0,.95fr);gap:18px;}
.enroll-success-card{background:#fff;border:1px solid var(--border);border-radius:20px;box-shadow:var(--shadow-lg);overflow:hidden;}
.enroll-success-main{padding:30px;}
.enroll-success-check{width:78px;height:78px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:18px;background:linear-gradient(135deg,#16a34a,#22c55e);box-shadow:0 8px 24px rgba(22,163,74,.28);}
.enroll-success-note{background:var(--accent-l);border:1px solid var(--accent-l2);border-radius:12px;padding:12px 14px;margin:16px 0;}
.enroll-success-steps{display:grid;gap:10px;margin-top:14px;}
.enroll-success-step{display:flex;gap:10px;align-items:flex-start;}
.enroll-success-step-no{width:22px;height:22px;border-radius:999px;background:var(--accent);color:#fff;font-size:.72rem;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;}
.receipt-card{background:#fff;border:1px solid var(--border);border-radius:20px;box-shadow:var(--shadow-lg);padding:18px;position:sticky;top:80px;height:fit-content;}
.receipt-actions{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px;}
.receipt-print{display:none;}
@media (max-width:900px){.enroll-success-shell{grid-template-columns:1fr;}.receipt-card{position:static;}}
@media print{
    body *{visibility:hidden !important;}
    .receipt-print,.receipt-print *{visibility:visible !important;}
    .receipt-print{display:block !important;position:absolute;left:0;top:0;width:100%;background:#fff;padding:0;}
}
</style>
<div class="enroll-success-page">
    <nav class="enroll-success-topbar">
        <div class="enroll-success-topbar-inner">
            <a href="/" style="display:flex;align-items:center;gap:10px;text-decoration:none;">
                @if($school->logo_url)
                    <img src="{{ $school->logo_url }}" alt="{{ $school->name }} logo" width="34" height="34" style="width:34px;height:34px;border-radius:8px;object-fit:cover;border:1px solid var(--border);background:#fff;">
                @else
                    <div style="width:34px;height:34px;background:linear-gradient(135deg,var(--accent),var(--accent-h));border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;">
                        {{ strtoupper(mb_substr($school->name, 0, 1)) }}
                    </div>
                @endif
                <span style="font-weight:700;font-size:1rem;color:var(--ink);">{{ $school->name }}</span>
            </a>
            <span class="badge blue">Payment Complete</span>
        </div>
    </nav>

    <div class="enroll-success-shell">
        <section class="enroll-success-card">
            <div class="enroll-success-main">
                <div class="enroll-success-check" aria-hidden="true">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h1 style="margin:0 0 10px;font-size:1.62rem;color:var(--ink);">Enrollment fee submitted successfully</h1>
                <p style="margin:0;color:var(--muted);font-size:.98rem;line-height:1.7;">
                    Your application has been submitted to <strong>{{ $school->name }}</strong>. Finance will verify your payment, then registrar will activate your portal access.
                </p>

                <div class="enroll-success-note">
                    <p style="margin:0;font-size:.86rem;color:var(--ink-2);line-height:1.6;">
                        <strong style="color:var(--accent);">Reference:</strong> {{ $receiptTransactionId }}<br>
                        <strong style="color:var(--accent);">Amount Paid:</strong> PHP {{ number_format($receiptAmount, 2) }}<br>
                        <strong style="color:var(--accent);">Date/Time:</strong> {{ $receiptPaidAt }}
                    </p>
                </div>

                <h3 style="margin:18px 0 10px;font-size:.95rem;color:var(--ink);">What happens next</h3>
                <div class="enroll-success-steps">
                    @foreach([
                        ['title' => 'Finance Verification', 'desc' => 'Your payment record is validated by the finance office.'],
                        ['title' => 'Registrar Confirmation', 'desc' => 'Registrar confirms your application details and account status.'],
                        ['title' => 'Portal Activation', 'desc' => 'You can sign in to complete your subjects and tuition payment.'],
                    ] as $idx => $item)
                        <div class="enroll-success-step">
                            <div class="enroll-success-step-no">{{ $idx + 1 }}</div>
                            <div>
                                <div style="font-weight:700;font-size:.86rem;color:var(--ink);">{{ $item['title'] }}</div>
                                <div style="font-size:.82rem;color:var(--muted);line-height:1.55;">{{ $item['desc'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:22px;">
                    <a href="{{ route('school.login', ['school_code' => $school->school_code]) }}" class="btn primary lg" style="text-decoration:none;">Sign In to Portal</a>
                    <a href="/" class="btn secondary lg" style="text-decoration:none;">Back to Home</a>
                </div>
            </div>
        </section>

        <aside class="receipt-card">
            <h3 style="margin:0 0 6px;font-size:1rem;">Official Receipt</h3>
            <p style="margin:0;color:var(--muted);font-size:.82rem;">Keep a copy for your records.</p>
            <div style="margin-top:12px;padding:12px;border:1px solid var(--border);border-radius:12px;background:#fff;">
                <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:8px;"><span style="color:var(--muted);font-size:.78rem;">Receipt No.</span><strong style="font-size:.82rem;">{{ $receiptNo }}</strong></div>
                <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:8px;"><span style="color:var(--muted);font-size:.78rem;">Transaction ID</span><strong style="font-size:.82rem;">{{ $receiptTransactionId }}</strong></div>
                <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:8px;"><span style="color:var(--muted);font-size:.78rem;">Payment Method</span><strong style="font-size:.82rem;">{{ $receiptMethod }}</strong></div>
                <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:8px;"><span style="color:var(--muted);font-size:.78rem;">Status</span><strong style="font-size:.82rem;">{{ $receiptStatus }}</strong></div>
                <div style="display:flex;justify-content:space-between;gap:8px;margin-bottom:8px;"><span style="color:var(--muted);font-size:.78rem;">Date Paid</span><strong style="font-size:.82rem;">{{ $receiptPaidAt }}</strong></div>
                <div style="display:flex;justify-content:space-between;gap:8px;padding-top:8px;border-top:1px dashed var(--border);"><span style="color:var(--ink);font-weight:700;">Total</span><strong style="font-size:1rem;color:var(--accent);">PHP {{ number_format($receiptAmount, 2) }}</strong></div>
            </div>
            <div class="receipt-actions">
                <button type="button" class="btn primary" onclick="window.print()">Print Receipt</button>
            </div>
        </aside>
    </div>

    <section class="receipt-print" id="print-receipt">
        <div style="max-width:760px;margin:22px auto;border:1px solid #d1d5db;border-radius:12px;overflow:hidden;font-family:Inter,Segoe UI,Arial,sans-serif;color:#0f172a;">
            <div style="padding:18px 20px;border-bottom:2px solid {{ $enrollTheme['accent'] }};">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        @if($school->logo_url)
                            <img src="{{ $school->logo_url }}" alt="{{ $school->name }} logo" width="58" height="58" style="width:58px;height:58px;border-radius:8px;object-fit:cover;border:1px solid #e5e7eb;">
                        @endif
                        <div>
                            <div style="font-size:1.02rem;font-weight:800;">{{ $school->name }}</div>
                            <div style="font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">Official Enrollment Receipt</div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:.74rem;color:#64748b;">Receipt No.</div>
                        <div style="font-weight:800;">{{ $receiptNo }}</div>
                    </div>
                </div>
            </div>
            <div style="padding:18px 20px;">
                <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;font-size:.86rem;">
                    <tr><td style="padding:7px 0;color:#64748b;">Transaction ID</td><td style="padding:7px 0;text-align:right;font-weight:600;">{{ $receiptTransactionId }}</td></tr>
                    <tr><td style="padding:7px 0;color:#64748b;">Date Paid</td><td style="padding:7px 0;text-align:right;font-weight:600;">{{ $receiptPaidAt }}</td></tr>
                    <tr><td style="padding:7px 0;color:#64748b;">Payment Method</td><td style="padding:7px 0;text-align:right;font-weight:600;">{{ $receiptMethod }}</td></tr>
                    <tr><td style="padding:7px 0;color:#64748b;">Payment Status</td><td style="padding:7px 0;text-align:right;font-weight:600;">{{ $receiptStatus }}</td></tr>
                    <tr><td style="padding:10px 0;border-top:1px dashed #cbd5e1;font-weight:700;">Enrollment Fee</td><td style="padding:10px 0;border-top:1px dashed #cbd5e1;text-align:right;font-weight:800;">PHP {{ number_format($receiptAmount, 2) }}</td></tr>
                </table>
                <p style="margin:16px 0 0;font-size:.78rem;color:#64748b;line-height:1.5;">
                    This document serves as a system-generated receipt of enrollment fee payment and is valid without signature.
                </p>
            </div>
        </div>
    </section>
</div>
@endsection

