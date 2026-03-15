@extends('layouts.app')

@section('title', 'Enrollment Complete!')

@section('content')
<div style="min-height:100vh; background: linear-gradient(160deg, #eff6ff 0%, #f4f7fe 50%, #f5f3ff 100%);">

    {{-- â”€â”€ Top Nav â”€â”€ --}}
    <nav style="background: rgba(255,255,255,.85); backdrop-filter: blur(12px); border-bottom: 1px solid var(--border); position: sticky; top:0; z-index:50;">
        <div style="max-width:700px; margin:0 auto; padding:0 24px; height:60px; display:flex; align-items:center; justify-content:space-between;">
            <a href="/" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                <div style="width:34px; height:34px; background:linear-gradient(135deg,#2563eb,#7c3aed); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <span style="font-family:'Plus Jakarta Sans',sans-serif; font-weight:700; font-size:1rem; color:var(--ink);">EduPlatform</span>
            </a>
        </div>
    </nav>

    <div style="max-width:600px; margin:0 auto; padding:64px 24px;">

        {{-- â”€â”€ Success Card â”€â”€ --}}
        <div style="background:white; border:1px solid var(--border); border-radius:24px; box-shadow:var(--shadow-lg); overflow:hidden; text-align:center;">
            <div style="padding:48px 32px;">
                {{-- Success Icon --}}
                <div style="width:80px; height:80px; background:linear-gradient(135deg,#16a34a,#22c55e); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; box-shadow:0 8px 24px rgba(22,163,74,.3);">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>

                <h1 style="margin:0 0 12px; font-size:1.6rem; color:var(--ink);">Enrollment Fee Submitted!</h1>
                <p style="margin:0 0 32px; font-size:1rem; color:var(--muted); line-height:1.7;">
                    Your account application has been submitted to <strong>{{ $school->name }}</strong>.
                    You can log in after finance verifies payment and registrar activates your account.
                </p>

                @php($mockPayment = session('mock_payment'))
                @if (is_array($mockPayment))
                    <div style="text-align:left; background:var(--accent-l2); border:1px solid rgba(37,99,235,.2); border-radius:12px; padding:14px 16px; margin-bottom:22px;">
                        <h4 style="margin:0 0 8px; font-size:0.9rem; color:var(--accent);">Mock Payment Result</h4>
                        <div style="font-size:0.84rem; color:var(--ink-2); line-height:1.6;">
                            <div><strong>Status:</strong> {{ $mockPayment['status'] ?? 'Mock Paid' }}</div>
                            <div><strong>Method:</strong> {{ $mockPayment['method'] ?? 'N/A' }}</div>
                            <div><strong>Transaction ID:</strong> {{ $mockPayment['transaction_id'] ?? 'N/A' }}</div>
                            <div><strong>Paid At:</strong> {{ $mockPayment['paid_at'] ?? 'N/A' }}</div>
                            <div><strong>Amount:</strong> PHP {{ number_format((float) ($mockPayment['amount'] ?? 0), 2) }}</div>
                        </div>
                    </div>
                @endif

                {{-- Next Steps --}}
                <div style="text-align:left; background:var(--surface-2); border-radius:16px; padding:24px; margin-bottom:32px;">
                    <h3 style="margin:0 0 16px; font-size:0.95rem; color:var(--ink);">What happens next?</h3>
                    <div style="display:flex; flex-direction:column; gap:12px;">
                        @foreach([
                            ['step' => '1', 'title' => 'Enrollment Fee Submitted', 'desc' => 'Your enrollment fee payment is now queued for finance verification.'],
                            ['step' => '2', 'title' => 'Finance Verification', 'desc' => 'Finance reviews and verifies your payment record.'],
                            ['step' => '3', 'title' => 'Registrar Activation', 'desc' => 'Registrar activates your student account after finance verification.'],
                            ['step' => '4', 'title' => 'Student Access', 'desc' => 'After activation, log in to select program, subjects, schedules, then pay tuition.'],
                        ] as $item)
                        <div style="display:flex; gap:12px; align-items:flex-start;">
                            <div style="width:24px; height:24px; background:var(--accent); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; flex-shrink:0;">
                                {{ $item['step'] }}
                            </div>
                            <div>
                                <span style="font-weight:600; color:var(--ink); font-size:0.88rem;">{{ $item['title'] }}</span>
                                <p style="margin:2px 0 0; font-size:0.82rem; color:var(--muted);">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Login Info --}}
                <div style="background:var(--accent-l2); border:1px solid rgba(37,99,235,.2); border-radius:12px; padding:16px 20px; margin-bottom:24px;">
                    <p style="margin:0; font-size:0.88rem; color:var(--accent); line-height:1.6;">
                        <strong>Your account has been created!</strong><br>
                        Sign in is available only after registrar activation is completed.
                    </p>
                </div>

                <div style="display:flex; gap:12px; justify-content:center;">
                    <a href="{{ route('school.login', ['school_code' => $school->school_code]) }}"
                       class="btn primary lg"
                       style="text-decoration:none;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        Sign In to Portal
                    </a>
                    <a href="/"
                       style="display:inline-flex; align-items:center; gap:6px; padding:12px 20px; background:white; color:var(--ink-2); border:1.5px solid var(--border); border-radius:12px; font-size:0.9rem; font-weight:600; text-decoration:none;">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>

        {{-- â”€â”€ Contact Info â”€â”€ --}}
        <div style="text-align:center; margin-top:32px; padding:20px; background:white; border:1px solid var(--border); border-radius:16px;">
            <p style="margin:0; font-size:0.85rem; color:var(--muted);">
                Need help? Contact <strong>{{ $school->name }}</strong> registrar office for assistance with your enrollment.
            </p>
        </div>
    </div>

    {{-- â”€â”€ Footer â”€â”€ --}}
    <footer style="text-align:center; padding:32px 24px; color:var(--muted); font-size:0.8rem; border-top:1px solid var(--border); background:white; margin-top:40px;">
        <p>(c) {{ date('Y') }} EduPlatform. University SaaS E-Learning System.</p>
    </footer>
</div>
@endsection

