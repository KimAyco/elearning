@extends('superadmin.layout')

@section('title', 'Approvals — Nehemiah Control')

@section('sa_heading', 'Pending approvals')

@php
    $queueTotal = $pendingRegistrations->count();
    $awaitingPayment = $pendingRegistrations->where('status', 'pending')->count();
    $readyToApprove = $pendingRegistrations->where('status', 'paid')->count();
@endphp

@section('sa_content')
    <div class="sa-dash sa-moodle">
        <section class="sa-moodle-region sa-moodle-region--hero">
            <header class="sa-moodle-region-hd">
                <h1 class="sa-moodle-title">Pending school approvals</h1>
                <p class="sa-moodle-summary">
                    Registrations appear here after checkout. When payment is confirmed, you can approve to provision the tenant school and primary admin account.
                </p>
            </header>
            <nav class="sa-breadcrumb sa-moodle-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('superadmin.dashboard') }}">Super Admin</a>
                <span class="breadcrumb-sep">›</span>
                <span>Approvals</span>
            </nav>
        </section>

        <section class="sa-moodle-region" aria-labelledby="sa-approval-kpi-heading">
            <header class="sa-moodle-region-hd sa-moodle-region-hd--compact">
                <h2 id="sa-approval-kpi-heading" class="sa-moodle-region-title">Queue snapshot</h2>
                <p class="sa-moodle-region-desc">Counts include both unpaid and paid rows still in this pipeline.</p>
            </header>
            <div class="sa-moodle-kpi sa-moodle-kpi--3">
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $queueTotal }}</span>
                    <span class="sa-moodle-kpi-label">Total in queue</span>
                </div>
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $awaitingPayment }}</span>
                    <span class="sa-moodle-kpi-label">Awaiting payment</span>
                </div>
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $readyToApprove }}</span>
                    <span class="sa-moodle-kpi-label">Ready to approve</span>
                </div>
            </div>
        </section>

        <section class="sa-moodle-region sa-moodle-region--approvals" aria-labelledby="sa-approval-table-heading">
            <header class="sa-moodle-region-hd sa-moodle-region-hd--row">
                <div>
                    <h2 id="sa-approval-table-heading" class="sa-moodle-region-title">Registration queue</h2>
                    <p class="sa-moodle-region-desc">Each row is a school signup. Actions unlock once status is paid.</p>
                </div>
                <div class="sa-moodle-pill-strip" aria-live="polite">
                    @if ($queueTotal > 0)
                        <span class="sa-moodle-pill sa-moodle-pill--amber">{{ $queueTotal }} total</span>
                        @if ($readyToApprove > 0)
                            <span class="sa-moodle-pill sa-moodle-pill--green">{{ $readyToApprove }} can approve</span>
                        @endif
                    @else
                        <span class="sa-moodle-pill sa-moodle-pill--slate">Queue clear</span>
                    @endif
                </div>
            </header>

            <div class="sa-approval-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">School name</th>
                            <th scope="col">Admin email</th>
                            <th scope="col">Plan</th>
                            <th scope="col">Status</th>
                            <th scope="col">Paid at</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingRegistrations as $registration)
                            <tr>
                                <td>
                                    <span class="sa-approval-id">{{ $registration->id }}</span>
                                </td>
                                <td>
                                    <span class="sa-approval-school">{{ $registration->name }}</span>
                                </td>
                                <td>
                                    <span class="sa-approval-email">{{ $registration->email }}</span>
                                </td>
                                <td>
                                    <span class="badge blue">{{ $registration->plan_months }} months</span>
                                </td>
                                <td>
                                    <span class="badge {{ $registration->status === 'paid' ? 'green' : 'amber' }}">
                                        {{ strtoupper($registration->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($registration->status === 'paid')
                                        <span class="sa-approval-email">{{ optional($registration->updated_at)->format('M d, Y g:i A') }}</span>
                                    @else
                                        <span class="sa-approval-id">—</span>
                                    @endif
                                </td>
                                <td class="sa-approval-actions">
                                    @if ($registration->status === 'paid')
                                        <form method="post" action="{{ url('/superadmin/registrations/' . $registration->id . '/approve') }}">
                                            @csrf
                                            <button class="btn success sm" type="submit">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                                </svg>
                                                Approve
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge amber">Awaiting payment</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="sa-approval-empty" role="status">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        <p>No registrations in the approval queue right now.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="sa-moodle-region sa-moodle-region--footer" aria-labelledby="sa-approval-foot-heading">
            <header class="sa-moodle-region-hd">
                <h2 id="sa-approval-foot-heading" class="sa-moodle-region-title">How approval works</h2>
            </header>
            <p class="sa-moodle-footnote">
                <strong>Pending</strong> means checkout is not complete or payment has not posted yet.
                <strong>Paid</strong> unlocks the approve button; use it once you are satisfied the order is legitimate.
                After approval, the school receives its code and the admin can sign in on the tenant domain.
            </p>
        </section>
    </div>
@endsection
