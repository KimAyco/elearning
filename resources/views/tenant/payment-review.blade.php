@extends('layouts.app')

@section('title', ($isTuitionBreakdown ?? true) ? 'Review Tuition & Pay - School Portal' : 'Review Bill & Pay - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'payments', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

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
                    <h2>{{ ($isTuitionBreakdown ?? true) ? 'Tuition Breakdown' : 'Bill details' }}</h2>
                    <span class="badge">Billing #{{ (int) $billing->id }}</span>
                </div>
                <div class="table-wrap" style="margin:0;">
                    <table>
                        <thead>
                            <tr>
                                @if($isTuitionBreakdown ?? true)
                                    <th>Subject</th>
                                    <th>Title</th>
                                    <th>Units</th>
                                    <th>Amount</th>
                                @else
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th style="text-align:right;">Amount</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($breakdown ?? collect()) as $row)
                                <tr>
                                    @if($isTuitionBreakdown ?? true)
                                        <td><span class="badge blue">{{ $row['code'] ?? 'N/A' }}</span></td>
                                        <td>{{ $row['title'] ?? '-' }}</td>
                                        <td style="text-align:right;">{{ number_format((float) ($row['units'] ?? 0), 0) }}</td>
                                        <td style="text-align:right; font-weight:600;">₱ {{ number_format((float) ($row['amount'] ?? 0), 2) }}</td>
                                    @else
                                        <td><span class="badge blue">{{ $row['code'] ?? '—' }}</span></td>
                                        <td>{{ $row['title'] ?? '-' }}</td>
                                        <td style="text-align:right; font-weight:600;">₱ {{ number_format((float) ($row['amount'] ?? 0), 2) }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr><td colspan="{{ ($isTuitionBreakdown ?? true) ? 4 : 3 }}"><div class="empty-state"><p>{{ ($isTuitionBreakdown ?? true) ? 'No subjects found for this semester.' : 'No line items for this bill.' }}</p></div></td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                @if($isTuitionBreakdown ?? true)
                                    <td colspan="3" style="text-align:right; font-weight:600;">Total Due</td>
                                @else
                                    <td colspan="2" style="text-align:right; font-weight:600;">Total Due</td>
                                @endif
                                <td style="text-align:right; font-weight:700;">₱ {{ number_format((float) $totalDue, 2) }}</td>
                            </tr>
                            <tr>
                                @if($isTuitionBreakdown ?? true)
                                    <td colspan="3" style="text-align:right;">Amount Paid</td>
                                @else
                                    <td colspan="2" style="text-align:right;">Amount Paid</td>
                                @endif
                                <td style="text-align:right;">₱ {{ number_format((float) $amountPaid, 2) }}</td>
                            </tr>
                            <tr>
                                @if($isTuitionBreakdown ?? true)
                                    <td colspan="3" style="text-align:right;">Remaining</td>
                                @else
                                    <td colspan="2" style="text-align:right;">Remaining</td>
                                @endif
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
                            <button class="btn gcash" type="submit" {{ $remaining <= 0 ? 'disabled' : '' }} style="width:100%; display:flex; align-items:center; justify-content:center; gap:10px;">
                                <img src="{{ asset('images/payments/gcash.webp') }}" alt="" width="24" height="24" loading="lazy" decoding="async" style="height:22px;width:auto;max-height:22px;object-fit:contain;flex-shrink:0;display:block;" aria-hidden="true">
                                <span>Pay with GCash — ₱ {{ number_format((float) $remaining, 2) }}</span>
                            </button>
                        </form>
                        <form method="POST" action="{{ url('/tenant/billing/' . (int) $billing->id . '/pay/paymongo') }}" style="margin:0;">
                            @csrf
                            <input type="hidden" name="wallet" value="paymaya">
                            <button class="btn success" type="submit" {{ $remaining <= 0 ? 'disabled' : '' }} style="width:100%; display:flex; align-items:center; justify-content:center; gap:10px; background:#00a651; border-color:#00a651;">
                                <img src="{{ asset('images/payments/maya.svg') }}" alt="" loading="lazy" decoding="async" style="height:18px;width:auto;max-width:72px;object-fit:contain;flex-shrink:0;display:block;filter:brightness(0) invert(1);" aria-hidden="true">
                                <span>Pay with Maya — ₱ {{ number_format((float) $remaining, 2) }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

