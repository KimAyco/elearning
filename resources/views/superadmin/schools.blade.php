@extends('superadmin.layout')

@section('title', 'Schools — Nehemiah Control')

@section('sa_heading', 'School management')

@php
    $totalSchools = $schools->count();
    $activeCount = $schools->where('status', 'active')->count();
    $suspendedCount = $schools->where('status', 'suspended')->count();
    $trialCount = $schools->where('subscription_state', 'trial')->count();
@endphp

@section('sa_content')
    <div class="sa-dash sa-moodle">
        <section class="sa-moodle-region sa-moodle-region--hero">
            <header class="sa-moodle-region-hd">
                <h1 class="sa-moodle-title">School directory</h1>
                <p class="sa-moodle-summary">
                    Monitor tenant status, subscription state, and suspend or resume access. Suspended schools cannot sign in until you resume them.
                </p>
            </header>
            <nav class="sa-breadcrumb sa-moodle-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('superadmin.dashboard') }}">Super Admin</a>
                <span class="breadcrumb-sep">›</span>
                <span>Schools</span>
            </nav>
        </section>

        <section class="sa-moodle-region" aria-labelledby="sa-schools-kpi-heading">
            <header class="sa-moodle-region-hd sa-moodle-region-hd--compact">
                <h2 id="sa-schools-kpi-heading" class="sa-moodle-region-title">Directory snapshot</h2>
                <p class="sa-moodle-region-desc">Roll-up of tenants on the platform right now.</p>
            </header>
            <div class="sa-moodle-kpi">
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $totalSchools }}</span>
                    <span class="sa-moodle-kpi-label">Total schools</span>
                </div>
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $activeCount }}</span>
                    <span class="sa-moodle-kpi-label">Active</span>
                </div>
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $suspendedCount }}</span>
                    <span class="sa-moodle-kpi-label">Suspended</span>
                </div>
                <div class="sa-moodle-kpi-item">
                    <span class="sa-moodle-kpi-value">{{ $trialCount }}</span>
                    <span class="sa-moodle-kpi-label">On trial</span>
                </div>
            </div>
        </section>

        <section class="sa-moodle-region sa-moodle-region--schools-table" aria-labelledby="sa-schools-table-heading">
            <header class="sa-moodle-region-hd sa-moodle-region-hd--row">
                <div>
                    <h2 id="sa-schools-table-heading" class="sa-moodle-region-title">Registered schools</h2>
                    <p class="sa-moodle-region-desc">Each row is a provisioned tenant. Use Suspend to block access without deleting data.</p>
                </div>
                <div class="sa-moodle-pill-strip" aria-live="polite">
                    @if ($totalSchools > 0)
                        <span class="sa-moodle-pill sa-moodle-pill--blue">{{ $totalSchools }} total</span>
                    @else
                        <span class="sa-moodle-pill sa-moodle-pill--slate">No tenants</span>
                    @endif
                </div>
            </header>

            <div class="sa-approval-table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">School</th>
                            <th scope="col">Description</th>
                            <th scope="col">Status</th>
                            <th scope="col">Subscription</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($schools as $school)
                            <tr>
                                <td>
                                    <span class="sa-approval-id">{{ $school->id }}</span>
                                </td>
                                <td>
                                    <div class="sa-school-identity">
                                        <div class="sa-school-avatar" aria-hidden="true">{{ strtoupper(substr($school->name, 0, 1)) }}</div>
                                        <div class="sa-school-identity-text">
                                            <div class="sa-school-name">{{ $school->name }}</div>
                                            <span class="sa-school-code-pill">{{ $school->school_code }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="sa-school-desc">{{ $school->short_description ?: '—' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $school->status === 'active' ? 'green' : 'red' }}">
                                        {{ $school->status === 'active' ? '● Active' : '● Suspended' }}
                                    </span>
                                    @if ($school->suspended_at)
                                        <div class="sa-school-suspended-note">
                                            since {{ \Carbon\Carbon::parse($school->suspended_at)->format('M d, Y') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $subColors = ['trial' => 'amber', 'active' => 'green', 'past_due' => 'red', 'expired' => 'red', 'cancelled' => 'red'];
                                        $subColor = $subColors[$school->subscription_state] ?? '';
                                    @endphp
                                    <span class="badge {{ $subColor }}">{{ ucfirst(str_replace('_', ' ', $school->subscription_state)) }}</span>
                                </td>
                                <td class="sa-school-actions">
                                    <form method="post" action="{{ url('/superadmin/schools/' . $school->id . '/status') }}">
                                        @csrf
                                        @method('PATCH')
                                        @if ($school->status === 'active')
                                            <input type="hidden" name="status" value="suspended">
                                            <button class="btn warning sm" type="submit">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                                </svg>
                                                Suspend
                                            </button>
                                        @else
                                            <input type="hidden" name="status" value="active">
                                            <button class="btn success sm" type="submit">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                                </svg>
                                                Resume
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="sa-approval-empty" role="status">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                        </svg>
                                        <p>No schools registered yet.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="sa-moodle-region sa-moodle-region--footer" aria-labelledby="sa-schools-foot-heading">
            <header class="sa-moodle-region-hd">
                <h2 id="sa-schools-foot-heading" class="sa-moodle-region-title">Suspend vs resume</h2>
            </header>
            <p class="sa-moodle-footnote">
                <strong>Suspend</strong> immediately blocks tenant logins while keeping data intact.
                <strong>Resume</strong> restores normal access. Subscription state is shown for billing visibility; it is separate from access control.
            </p>
        </section>
    </div>
@endsection
