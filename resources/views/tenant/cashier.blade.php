@extends('layouts.app')

@section('title', 'Cashier - School Portal')

@section('content')
<div class="app-shell">
    @include('tenant.partials.sidebar', ['active' => 'cashier'])

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">Cashier</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
                    <span>{{ auth()->user()->full_name ?? 'User' }}</span>
                </div>
            </div>
        </header>

        <main class="page-body">

            @if(session('status'))
                <div class="alert success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Auto Approve Payment --}}
            <div class="card" style="padding:14px 16px; margin-bottom:14px;">
                <form method="post" action="{{ url('/tenant/cashier/settings/auto-approve') }}" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                    @csrf
                    <div>
                        <div style="font-weight:700; color:var(--ink);">Auto Approve Payment</div>
                        <div style="color:var(--muted); font-size:0.85rem; margin-top:2px;">
                            When ON, student-submitted payments will be automatically verified.
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span class="badge {{ ($autoApprovePaymentsEnabled ?? false) ? 'green' : 'amber' }}">
                            {{ ($autoApprovePaymentsEnabled ?? false) ? 'ON' : 'OFF' }}
                        </span>
                        <input type="hidden" name="enabled" value="{{ ($autoApprovePaymentsEnabled ?? false) ? '0' : '1' }}">
                        <button type="submit" class="btn {{ ($autoApprovePaymentsEnabled ?? false) ? 'ghost' : 'primary' }}">
                            {{ ($autoApprovePaymentsEnabled ?? false) ? 'Turn Off' : 'Turn On' }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Pending Payment Verifications --}}
            <div style="margin-bottom:18px;">
                <div class="section-divider" style="margin-bottom:12px;">
                    <span>Pending Payment Verifications</span>
                    <span class="badge amber">{{ ($pendingPayments ?? collect())->count() }}</span>
                </div>
                <div class="card" style="padding:0; overflow:hidden;">
                    <div class="table-wrap" style="margin:0;">
                        <table>
                            <thead>
                                <tr>
                                        <th>#</th>
                                        <th>Billing ID</th>
                                        <th>Type</th>
                                        <th>Applicant / Student</th>
                                        <th>Amount</th>
                                        <th>Reference</th>
                                        <th>Decision</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($pendingPayments ?? collect()) as $row)
                                        <tr>
                                            <td style="color:var(--muted); font-size:0.8rem;">{{ $row->id }}</td>
                                            <td><span class="badge">#{{ $row->billing_id }}</span></td>
                                            <td>
                                                <span class="badge {{ $row->billing->charge_type === 'misc_fee' ? 'blue' : 'purple' }}" style="font-size:0.7rem; text-transform:uppercase;">
                                                    {{ str_replace('_', ' ', $row->billing->charge_type === 'misc_fee' ? 'Enrollment' : $row->billing->charge_type) }}
                                                </span>
                                            </td>
                                            <td style="color:var(--ink-2); font-weight:600;">{{ $row->student->full_name ?? $row->student_user_id }}</td>
                                            <td style="font-weight:600;">₱ {{ number_format($row->amount, 2) }}</td>
                                            <td style="font-size:0.82rem; color:var(--muted);">{{ $row->reference_no ?? '-' }}</td>
                                        <td>
                                            <form method="post" action="{{ url('/tenant/payments/' . $row->id . '/verify') }}">
                                                @csrf
                                                <div style="display:flex; gap:6px; align-items:center;">
                                                    <select name="status" style="width:120px; padding:5px 8px; font-size:0.8rem;" required>
                                                        <option value="verified">Approve</option>
                                                        <option value="rejected">Reject</option>
                                                    </select>
                                                    <input name="remarks" placeholder="Remarks" style="flex:1; padding:5px 8px; font-size:0.8rem; min-width:100px;">
                                                    <button class="btn success sm" type="submit">Apply</button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6"><div class="empty-state"><p>No pending payments.</p></div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Ready for Clearance (NEW: Moved from Billing) --}}
            @if(($forClearance ?? collect())->count() > 0)
            <div style="margin-bottom:18px;">
                <div class="section-divider" style="margin-bottom:12px;">
                    <span>Ready for Clearance</span>
                    <span class="badge green">{{ $forClearance->count() }}</span>
                </div>
                <div class="card" style="padding:0; overflow:hidden;">
                    <div class="table-wrap" style="margin:0;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Billing #</th>
                                    <th>Applicant / Student</th>
                                    <th>Payment Status</th>
                                    <th>Clearance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forClearance as $row)
                                    <tr>
                                        <td><span class="badge">#{{ $row->id }}</span></td>
                                        <td style="font-weight:600; color:var(--ink);">{{ $row->student->full_name ?? $row->student_user_id }}</td>
                                        <td><span class="badge">{{ $row->payment_status }}</span></td>
                                        <td><span class="badge">{{ $row->clearance_status }}</span></td>
                                        <td>
                                            <form method="post" action="{{ url('/tenant/billing/' . $row->id . '/clearance') }}">
                                                @csrf
                                                <button class="btn success sm" type="submit">Issue Clearance</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Confirmed Payments --}}
            <div style="margin-bottom:18px;">
                <div class="section-divider" style="margin-bottom:12px;">
                    <span>Confirmed Payments</span>
                    <span class="badge green">{{ ($confirmedPayments ?? collect())->count() }}</span>
                </div>
                <div class="card" style="padding:0; overflow:hidden;">
                    <div class="table-wrap" style="margin:0;">
                        <table>
                            <thead>
                                <tr>
                                        <th>#</th>
                                        <th>Billing ID</th>
                                        <th>Type</th>
                                        <th>Applicant / Student</th>
                                        <th>Amount</th>
                                        <th>Reference</th>
                                        <th>Verified At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($confirmedPayments ?? collect()) as $row)
                                        <tr>
                                            <td style="color:var(--muted); font-size:0.8rem;">{{ $row->id }}</td>
                                            <td><span class="badge">#{{ $row->billing_id }}</span></td>
                                            <td>
                                                <span class="badge {{ $row->billing->charge_type === 'misc_fee' ? 'blue' : 'purple' }}" style="font-size:0.7rem; text-transform:uppercase;">
                                                    {{ str_replace('_', ' ', $row->billing->charge_type === 'misc_fee' ? 'Enrollment' : $row->billing->charge_type) }}
                                                </span>
                                            </td>
                                            <td style="color:var(--ink-2); font-weight:600;">{{ $row->student->full_name ?? $row->student_user_id }}</td>
                                            <td style="font-weight:600;">₱ {{ number_format($row->amount, 2) }}</td>
                                            <td style="font-size:0.82rem; color:var(--muted);">{{ $row->reference_no ?? '-' }}</td>
                                        <td style="font-size:0.82rem; color:var(--muted);">{{ optional($row->verified_at)->format('M d, Y') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6"><div class="empty-state"><p>No confirmed payments.</p></div></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .section-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--muted);
    }
    .section-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }
</style>
<script>
    // (intentionally empty for now)
</script>
@endpush
