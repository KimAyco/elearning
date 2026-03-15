@extends('layouts.app')

@section('title', 'Review Tuition & Pay - School Portal')

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
                <span class="topbar-title">Review Tuition & Pay</span>
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

            <div class="card" style="margin-bottom:16px;">
                <div class="card-header">
                    <h2>Tuition Breakdown</h2>
                    <span class="badge">Billing #{{ (int) $billing->id }}</span>
                </div>
                <div class="table-wrap" style="margin:0;">
                    <table>
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Title</th>
                                <th>Units</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($breakdown ?? collect()) as $row)
                                <tr>
                                    <td><span class="badge blue">{{ $row['code'] ?? 'N/A' }}</span></td>
                                    <td>{{ $row['title'] ?? '-' }}</td>
                                    <td style="text-align:right;">{{ number_format((float) ($row['units'] ?? 0), 0) }}</td>
                                    <td style="text-align:right; font-weight:600;">₱ {{ number_format((float) ($row['amount'] ?? 0), 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4"><div class="empty-state"><p>No subjects found for this semester.</p></div></td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align:right; font-weight:600;">Total Due</td>
                                <td style="text-align:right; font-weight:700;">₱ {{ number_format((float) $totalDue, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align:right;">Amount Paid</td>
                                <td style="text-align:right;">₱ {{ number_format((float) $amountPaid, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" style="text-align:right;">Remaining</td>
                                <td style="text-align:right;">₱ {{ number_format((float) $remaining, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Select Payment Method</h2>
                </div>
                <div style="padding:16px;">
                    <p class="text-muted" style="margin-bottom:14px;">Pay with <strong>GCash</strong> or <strong>Maya</strong> via PayMongo. You will be redirected to authorize the payment.</p>
                    <div style="display:flex; flex-direction:column; gap:12px; max-width:320px;">
                        <form method="POST" action="{{ url('/tenant/billing/' . (int) $billing->id . '/pay/paymongo') }}" style="margin:0;">
                            @csrf
                            <input type="hidden" name="wallet" value="gcash">
                            <button class="btn success" type="submit" {{ $remaining <= 0 ? 'disabled' : '' }} style="width:100%; display:flex; align-items:center; justify-content:center; gap:10px;">
                                <span style="font-size:1.25rem;">💙</span> Pay with GCash — ₱ {{ number_format((float) $remaining, 2) }}
                            </button>
                        </form>
                        <form method="POST" action="{{ url('/tenant/billing/' . (int) $billing->id . '/pay/paymongo') }}" style="margin:0;">
                            @csrf
                            <input type="hidden" name="wallet" value="paymaya">
                            <button class="btn success" type="submit" {{ $remaining <= 0 ? 'disabled' : '' }} style="width:100%; display:flex; align-items:center; justify-content:center; gap:10px; background:#00a651; border-color:#00a651;">
                                <span style="font-size:1.25rem;">💚</span> Pay with Maya — ₱ {{ number_format((float) $remaining, 2) }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

