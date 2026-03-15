@extends('layouts.app')

@section('title', 'Payments - School Portal')

@section('content')
<div class="app-shell">
    @include('tenant.partials.sidebar', ['active' => 'payments'])

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">Payments</span>
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

            <div data-tabs>
                <div class="tabs">
                    <button class="tab-btn active" data-tab="bills">
                        Bills
                        <span class="badge blue" style="font-size:0.65rem; padding:2px 6px;">{{ ($myBilling ?? collect())->count() }}</span>
                    </button>
                    <button class="tab-btn" data-tab="transactions">
                        Transactions
                        <span class="badge" style="font-size:0.65rem; padding:2px 6px;">{{ ($transactions ?? collect())->count() }}</span>
                    </button>
                </div>

                <div class="tab-panel active" data-panel="bills">
                    <div class="card" style="padding:0; overflow:hidden;">
                        <div class="table-wrap" style="margin:0;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Billing #</th>
                                        <th>Description</th>
                                        <th>Amount Due</th>
                                        <th>Amount Paid</th>
                                        <th>Status</th>
                                        <th>Pay</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($myBilling ?? collect()) as $row)
                                        @php $remaining = max((float) $row->amount_due - (float) $row->amount_paid, 0); @endphp
                                        <tr>
                                            <td><span class="badge">#{{ $row->id }}</span></td>
                                            <td>{{ $row->description ?? '-' }}</td>
                                            <td style="font-weight:600;">₱ {{ number_format((float) $row->amount_due, 2) }}</td>
                                            <td>₱ {{ number_format((float) $row->amount_paid, 2) }}</td>
                                            <td><span class="badge {{ in_array($row->payment_status, ['verified','paid_unverified','waived'], true) ? 'green' : '' }}">{{ $row->payment_status }}</span></td>
                                            <td>
                                                @if(in_array((string) $row->payment_status, ['verified', 'waived', 'void'], true) || $remaining <= 0)
                                                    <span class="text-muted text-sm">Settled</span>
                                                @else
                                                    <button type="button" class="btn success sm" data-pay-billing-id="{{ (int) $row->id }}" data-pay-amount="{{ number_format($remaining, 2, '.', '') }}">Proceed to Payment</button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6"><div class="empty-state"><p>No billing records found.</p></div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-panel" data-panel="transactions">
                    <div class="card" style="padding:0; overflow:hidden;">
                        <div class="table-wrap" style="margin:0;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date</th>
                                        <th>Billing</th>
                                        <th>Amount</th>
                                        <th>Reference</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse(($transactions ?? collect()) as $t)
                                        <tr>
                                            <td style="color:var(--muted); font-size:0.8rem;">{{ $t->id }}</td>
                                            <td style="font-size:0.82rem;">{{ optional($t->created_at)->format('M d, Y') }}</td>
                                            <td><span class="badge">#{{ $t->billing_id }}</span></td>
                                            <td style="font-weight:600;">₱ {{ number_format((float) $t->amount, 2) }}</td>
                                            <td style="font-size:0.82rem; color:var(--muted);">{{ $t->reference_no ?? '-' }}</td>
                                            <td><span class="badge {{ $t->status === 'verified' ? 'green' : ($t->status === 'rejected' ? 'red' : '') }}">{{ $t->status }}</span></td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6"><div class="empty-state"><p>No transactions yet.</p></div></td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
<style>
    #payment-method-modal[hidden] { display: none !important; }
    #payment-method-modal { backdrop-filter: blur(2px); }
    .pm-dialog { width: 760px; max-width: 96vw; border-radius: 10px; box-shadow: 0 20px 40px rgba(0,0,0,0.25); overflow: hidden; }
    .pm-header { display:flex; align-items:center; justify-content:space-between; }
    .pm-amount { background: var(--mint-1, #e7f8ef); color: var(--green-9, #0f5132); padding:4px 8px; border-radius:20px; font-weight:600; }
    .pm-options { display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:12px; margin-top:6px; }
    .pm-option { display:flex; align-items:center; gap:10px; border:1px solid var(--border, #e5e7eb); padding:12px; border-radius:8px; background:#fff; transition: box-shadow .15s ease, border-color .15s ease; cursor:pointer; }
    .pm-option input { transform: scale(1.1); }
    .pm-option:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); border-color:#c7d2fe; }
    .pm-option.pm-disabled { opacity:.5; cursor:not-allowed; pointer-events:none; }
    .pm-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:16px; }
}</style>
<div id="payment-method-modal" hidden style="position:fixed; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.4); z-index:9999;">
    <div class="card pm-dialog">
        <div class="card-header pm-header">
            <h2 style="margin:0;">Proceed to Payment</h2>
            <span id="payment-modal-amount" class="pm-amount"></span>
        </div>
        <div style="padding:16px;">
            <p style="margin:0 0 12px; font-size:0.9rem; color:var(--muted);">Pay with <strong>GCash</strong> or <strong>Maya</strong> via PayMongo.</p>
            <div class="pm-actions">
                <button type="button" class="btn ghost" id="payment-modal-cancel">Cancel</button>
                <a href="#" class="btn success" id="payment-modal-continue">Continue to Pay (GCash / Maya)</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const modal = document.getElementById('payment-method-modal');
        const btnContinue = document.getElementById('payment-modal-continue');
        const btnCancel = document.getElementById('payment-modal-cancel');
        const amountBadge = document.getElementById('payment-modal-amount');
        let currentBillingId = null;

        if (modal) {
            modal.setAttribute('hidden', 'hidden');
        }

        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('[data-pay-billing-id]');
            if (trigger) {
                currentBillingId = trigger.getAttribute('data-pay-billing-id');
                const amt = trigger.getAttribute('data-pay-amount');
                amountBadge.textContent = 'Amount: ₱ ' + Number(amt).toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
                btnContinue.setAttribute('href', '/tenant/billing/' + currentBillingId + '/review');
                modal.removeAttribute('hidden');
            }
        });

        btnCancel.addEventListener('click', function() {
            modal.setAttribute('hidden', 'hidden');
            currentBillingId = null;
        });

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.setAttribute('hidden', 'hidden');
                currentBillingId = null;
            }
        });
    })();
</script>
@endpush
