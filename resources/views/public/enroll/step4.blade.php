@extends('layouts.app')

@section('title', 'Enrollment - Step 2: Enrollment Fee Payment')

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
            <span class="badge blue">Step 2 of 2</span>
        </div>
    </nav>

    <div style="max-width:900px; margin:0 auto; padding:48px 24px 64px;">
        <div style="display:flex; align-items:center; justify-content:center; gap:8px; margin-bottom:36px;">
            @foreach(['Personal Info', 'Enrollment Fee Payment'] as $i => $step)
            @php $stepNum = $i + 1; @endphp
            <div style="display:flex; align-items:center; gap:8px;">
                <div style="width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:700; background:linear-gradient(135deg,#2563eb,#7c3aed); color:#fff;">
                    @if ($stepNum < 2)
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    @else
                        {{ $stepNum }}
                    @endif
                </div>
                <span style="font-size:0.78rem; font-weight:600; color:var(--ink);">{{ $step }}</span>
                @if ($stepNum < 2)
                <div style="width:24px; height:2px; background:var(--accent); margin-left:4px;"></div>
                @endif
            </div>
            @endforeach
        </div>

        <div style="display:grid; grid-template-columns:1fr 320px; gap:24px;">
            <div style="background:white; border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow-lg); overflow:hidden;">
                <div style="padding:24px 28px; border-bottom:1px solid var(--border);">
                    <h1 style="margin:0 0 4px; font-size:1.2rem;">Enrollment Fee Payment</h1>
                    <p style="margin:0; font-size:0.85rem; color:var(--muted);">
                        Initial payment includes enrollment fee only. Tuition will be charged after subject selection in the student portal.
                    </p>
                </div>

                <div style="padding:28px;">
                    @if ($errors->any())
                        <div class="alert error" style="margin-bottom:16px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <p style="margin:0 0 20px; font-size:0.9rem; color:var(--muted);">Pay with <strong>GCash</strong> or <strong>Maya</strong> via PayMongo. You will be redirected to authorize the payment.</p>

                    <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:24px;">
                        <form method="post" action="{{ route('enroll.paymongo.initiate', ['school_code' => $school->school_code]) }}" style="margin:0;">
                            @csrf
                            <input type="hidden" name="wallet" value="gcash">
                            <input type="hidden" name="terms_accepted" value="1">
                            <button type="submit" class="btn primary lg" style="width:100%; display:flex; align-items:center; justify-content:center; gap:10px;">
                                <span style="font-size:1.25rem;">💙</span> Pay with GCash — PHP {{ number_format($totalFee ?? $enrollmentFee, 2) }}
                            </button>
                        </form>
                        <form method="post" action="{{ route('enroll.paymongo.initiate', ['school_code' => $school->school_code]) }}" style="margin:0;">
                            @csrf
                            <input type="hidden" name="wallet" value="paymaya">
                            <input type="hidden" name="terms_accepted" value="1">
                            <button type="submit" class="btn primary lg" style="width:100%; display:flex; align-items:center; justify-content:center; gap:10px; background:linear-gradient(135deg,#00a651,#00d662); border:none;">
                                <span style="font-size:1.25rem;">💚</span> Pay with Maya — PHP {{ number_format($totalFee ?? $enrollmentFee, 2) }}
                            </button>
                        </form>
                    </div>

                    <div style="display:flex; gap:12px;">
                        <a href="{{ route('enroll.step1', ['school_code' => $school->school_code]) }}"
                           style="display:inline-flex; align-items:center; gap:6px; padding:12px 20px; background:white; color:var(--ink-2); border:1.5px solid var(--border); border-radius:12px; font-size:0.9rem; font-weight:600; text-decoration:none;">
                            <- Back
                        </a>
                    </div>
                </div>
            </div>

            <div style="background:white; border:1px solid var(--border); border-radius:20px; box-shadow:var(--shadow-lg); overflow:hidden; height:fit-content;">
                <div style="padding:20px 24px; border-bottom:1px solid var(--border); background:var(--surface-2);">
                    <h3 style="margin:0; font-size:0.95rem;">Fee Summary</h3>
                </div>
                <div style="padding:20px 24px;">
                    <div style="margin-bottom:16px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="font-size:0.85rem; color:var(--muted);">Enrollment Fee</span>
                            <span style="font-weight:600; color:var(--ink);">PHP {{ number_format($enrollmentFee, 2) }}</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="font-size:0.85rem; color:var(--muted);">Tuition Fee</span>
                            <span style="font-weight:600; color:var(--ink);">Billed after subject selection</span>
                        </div>
                        @if(!empty($tuitionSubjectItems ?? []))
                            <div style="margin:10px 0 12px; padding:10px; border:1px solid var(--border); border-radius:10px; background:var(--surface-2);">
                                @foreach(($tuitionSubjectItems ?? []) as $item)
                                    <div style="display:flex; justify-content:space-between; gap:10px; margin-bottom:6px; font-size:0.8rem;">
                                        <span style="color:var(--ink-2);">{{ $item['code'] ?? 'SUBJ' }} - {{ $item['title'] ?? '' }}</span>
                                        <span style="font-weight:600; color:var(--ink);">PHP {{ number_format((float) ($item['price'] ?? 0), 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="font-size:0.78rem; color:var(--muted);">Fee Basis</span>
                            <span style="font-size:0.78rem; font-weight:600; color:var(--ink-2);">{{ $feeSourceLabel }}</span>
                        </div>
                    </div>

                    <div style="border-top:1px dashed var(--border); padding-top:16px; margin-top:16px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-weight:700; color:var(--ink);">Total Amount</span>
                            <span style="font-size:1.3rem; font-weight:800; color:var(--accent);">PHP {{ number_format($totalFee, 2) }}</span>
                        </div>
                    </div>

                    <div style="margin-top:20px; padding:12px; background:var(--green-l); border-radius:10px;">
                        <p style="margin:0; font-size:0.8rem; color:var(--green); line-height:1.5;">
                            Payment is processed securely via <strong>PayMongo</strong> (GCash / Maya). Finance may verify before registrar activation.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer style="text-align:center; padding:32px 24px; color:var(--muted); font-size:0.8rem; border-top:1px solid var(--border); background:white;">
        <p>(c) {{ date('Y') }} EduPlatform. University SaaS E-Learning System.</p>
    </footer>
</div>
@endsection
