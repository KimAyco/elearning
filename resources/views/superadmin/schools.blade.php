@extends('layouts.app')

@section('title', 'Super Admin — School Management')

@section('content')
<div class="app-shell">
    {{-- ── Sidebar ── --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                </svg>
            </div>
            <div>
                <div class="sidebar-brand-text">EduPlatform</div>
                <div class="sidebar-brand-sub">Super Admin</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Management</div>
            <a href="{{ url('/superadmin/schools') }}" class="sidebar-link active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Schools
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="post" action="{{ url('/superadmin/logout') }}">
                @csrf
                <button class="btn secondary full" type="submit" style="justify-content:flex-start; gap:8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>

    <div class="sidebar-overlay"></div>

    {{-- ── Main ── --}}
    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">School Management</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="avatar">SA</div>
                    <span>Super Admin</span>
                </div>
            </div>
        </header>

        <main class="page-body">
            <div class="page-header">
                <div class="breadcrumb">
                    <span>Super Admin</span>
                    <span class="breadcrumb-sep">›</span>
                    <span>Schools</span>
                </div>
                <h1>School Management</h1>
                <p>Create and manage tenant schools, subscription states, and access control.</p>
            </div>

            @if (session('status'))
                <div class="alert success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- ── Stats Row ── --}}
            <div class="grid cols-4 mb-20">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                    </div>
                    <div class="stat-body">
                        <div class="stat-value">{{ $schools->count() }}</div>
                        <div class="stat-label">Total Schools</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <div class="stat-body">
                        <div class="stat-value">{{ $schools->where('status','active')->count() }}</div>
                        <div class="stat-label">Active</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                        </svg>
                    </div>
                    <div class="stat-body">
                        <div class="stat-value">{{ $schools->where('status','suspended')->count() }}</div>
                        <div class="stat-label">Suspended</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amber">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <div class="stat-body">
                        <div class="stat-value">{{ $schools->where('subscription_state','trial')->count() }}</div>
                        <div class="stat-label">On Trial</div>
                    </div>
                </div>
            </div>

            {{-- ── Create + Notes ── --}}

            <div class="card mb-20">
                <div class="card-header">
                    <h2>
                        <div class="card-icon purple">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 2v20"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                            </svg>
                        </div>
                        Subscription Pricing
                    </h2>
                </div>

                <div class="grid cols-2 mb-16">
                    <form method="post" action="{{ url('/superadmin/pricing/platform') }}" class="card" style="padding:14px;">
                        @csrf
                        <h3 style="margin-bottom:8px;">Default Monthly Price</h3>
                        <div class="form-group">
                            <label>Price Per Month (PHP)</label>
                            <input type="number" min="0" step="0.01" name="price_per_month" value="{{ old('price_per_month', number_format((float) ($platformSettings->price_per_month ?? 1000), 2, '.', '')) }}" required>
                        </div>
                        <button class="btn primary sm" type="submit">Save Default Price</button>
                    </form>

                    <form method="post" action="{{ url('/superadmin/pricing/plans') }}" class="card" style="padding:14px;">
                        @csrf
                        <h3 style="margin-bottom:8px;">Add Subscription Plan</h3>
                        <div class="grid cols-2">
                            <div class="form-group">
                                <label>Plan Name</label>
                                <input name="name" placeholder="Plan 12 months" required>
                            </div>
                            <div class="form-group">
                                <label>Months</label>
                                <input type="number" min="1" max="120" name="months" required>
                            </div>
                            <div class="form-group">
                                <label>Price / Month</label>
                                <input type="number" min="0" step="0.01" name="price_per_month" required>
                            </div>
                            <div class="form-group">
                                <label>Total Price (optional)</label>
                                <input type="number" min="0" step="0.01" name="total_price">
                            </div>
                        </div>
                        <button class="btn success sm" type="submit">Add Plan</button>
                    </form>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Months</th>
                                <th>Price/Month</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($paymentPlans ?? collect()) as $plan)
                                <tr>
                                    <td style="font-weight:600; color:var(--ink);">{{ $plan->name }}</td>
                                    <td>{{ (int) $plan->months }}</td>
                                    <td>PHP {{ number_format((float) $plan->price_per_month, 2) }}</td>
                                    <td>PHP {{ number_format((float) ($plan->total_price ?? ((float) $plan->price_per_month * (int) $plan->months)), 2) }}</td>
                                    <td>
                                        <details>
                                            <summary style="cursor:pointer; color:var(--accent); font-weight:600;">Edit</summary>
                                            <form method="post" action="{{ url('/superadmin/pricing/plans') }}" class="inline mt-8">
                                                @csrf
                                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                <input name="name" value="{{ $plan->name }}" required>
                                                <input type="number" min="1" max="120" name="months" value="{{ (int) $plan->months }}" required>
                                                <input type="number" min="0" step="0.01" name="price_per_month" value="{{ number_format((float) $plan->price_per_month, 2, '.', '') }}" required>
                                                <input type="number" min="0" step="0.01" name="total_price" value="{{ number_format((float) ($plan->total_price ?? ((float) $plan->price_per_month * (int) $plan->months)), 2, '.', '') }}">
                                                <button class="btn primary sm" type="submit">Update</button>
                                            </form>
                                        </details>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state" style="padding:24px 12px;">
                                            <p>No subscription plans configured yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-20">
                <div class="card-header">
                    <h2>
                        <div class="card-icon amber">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                        </div>
                        Pending School Approvals
                    </h2>
                    <span class="badge amber">{{ $pendingRegistrations->count() }} pending</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>School Name</th>
                                <th>Admin Email</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th>Paid At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingRegistrations as $registration)
                                <tr>
                                    <td style="color:var(--muted); font-size:0.8rem;">{{ $registration->id }}</td>
                                    <td style="font-weight:600; color:var(--ink);">{{ $registration->name }}</td>
                                    <td style="font-size:0.82rem;">{{ $registration->email }}</td>
                                    <td><span class="badge blue">{{ $registration->plan_months }} months</span></td>
                                    <td>
                                        <span class="badge {{ $registration->status === 'paid' ? 'green' : 'amber' }}">
                                            {{ strtoupper($registration->status) }}
                                        </span>
                                    </td>
                                    <td style="font-size:0.82rem; color:var(--muted);">
                                        {{ $registration->status === 'paid' ? optional($registration->updated_at)->format('M d, Y g:i A') : '—' }}
                                    </td>
                                    <td>
                                        @if ($registration->status === 'paid')
                                            <form method="post" action="{{ url('/superadmin/registrations/' . $registration->id . '/approve') }}">
                                                @csrf
                                                <button class="btn success sm" type="submit">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                                    </svg>
                                                    Approve
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge amber">Awaiting Payment</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state" style="padding:24px 12px;">
                                            <p>No pending registrations found.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- ── Schools Table ── --}}
            <div class="card">
                <div class="card-header">
                    <h2>
                        <div class="card-icon blue">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            </svg>
                        </div>
                        Registered Schools
                    </h2>
                    <span class="badge blue">{{ $schools->count() }} total</span>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>School</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Subscription</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($schools as $school)
                                <tr>
                                    <td style="color:var(--muted); font-size:0.8rem;">{{ $school->id }}</td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div style="width:36px; height:36px; background:linear-gradient(135deg,var(--accent),#7c3aed); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:0.85rem; flex-shrink:0;">
                                                {{ strtoupper(substr($school->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div style="font-weight:600; color:var(--ink); font-size:0.875rem;">{{ $school->name }}</div>
                                                <span class="badge" style="font-size:0.65rem; margin-top:2px;">{{ $school->school_code }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="max-width:220px;">
                                        <span style="font-size:0.82rem; color:var(--muted);">{{ $school->short_description }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $school->status === 'active' ? 'green' : 'red' }}">
                                            {{ $school->status === 'active' ? '● Active' : '● Suspended' }}
                                        </span>
                                        @if ($school->suspended_at)
                                            <div style="font-size:0.72rem; color:var(--muted); margin-top:3px;">
                                                since {{ \Carbon\Carbon::parse($school->suspended_at)->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $subColors = ['trial'=>'amber','active'=>'green','past_due'=>'red','expired'=>'red','cancelled'=>'red'];
                                            $subColor = $subColors[$school->subscription_state] ?? '';
                                        @endphp
                                        <span class="badge {{ $subColor }}">{{ ucfirst(str_replace('_',' ',$school->subscription_state)) }}</span>
                                    </td>
                                    <td>
                                        <form method="post" action="{{ url('/superadmin/schools/' . $school->id . '/status') }}">
                                            @csrf
                                            @method('PATCH')
                                            @if ($school->status === 'active')
                                                <input type="hidden" name="status" value="suspended">
                                                <button class="btn warning sm" type="submit">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                                    </svg>
                                                    Suspend
                                                </button>
                                            @else
                                                <input type="hidden" name="status" value="active">
                                                <button class="btn success sm" type="submit">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
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
                                        <div class="empty-state">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                            </svg>
                                            <p>No schools registered yet. Create one using the form above.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

