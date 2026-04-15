@extends('layouts.app')

@section('title', 'Account - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
@php
    $u = $accountUser ?? null;
@endphp
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => $active ?? 'settings-account', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body">
            <div class="page-header account-page__header">
                <div class="breadcrumb">
                    <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <span>Settings</span>
                </div>
                <h1>Account</h1>
                <p>Your profile in this school portal session.</p>
            </div>

            <div class="card mb-20 account-page__card">
                <div class="card-header">
                    <h2>
                        <div class="card-icon blue">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </div>
                        Profile
                    </h2>
                </div>
                <div class="account-page__card-body">
                    @if($u)
                        <dl class="account-page__dl">
                            <div>
                                <dt>Name</dt>
                                <dd>{{ $u->full_name ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt>Email</dt>
                                <dd>{{ $u->email ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt>Status</dt>
                                <dd><span class="badge {{ ($u->status ?? '') === 'active' ? 'green' : 'amber' }}">{{ $u->status ?? '—' }}</span></dd>
                            </div>
                        </dl>
                    @else
                        <p style="margin:0; color: var(--muted);">Could not load your user profile.</p>
                    @endif

                    @if(!empty($accountRoleCodes))
                        <div class="account-page__roles">
                            <div class="account-page__roles-label">Roles in this school</div>
                            <div class="account-page__roles-badges">
                                @foreach($accountRoleCodes as $code)
                                    <span class="badge blue">{{ strtoupper((string) $code) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="account-page__grid">
                        <div class="card account-page__panel">
                            <div class="card-header account-page__panel-header">
                                <h3 class="account-page__panel-title">Change email</h3>
                            </div>
                            <div class="account-page__panel-body">
                                <form method="post" action="{{ url('/tenant/settings/account/email') }}">
                                    @csrf
                                    <div class="form-group account-page__form-group">
                                        <label for="email">New email</label>
                                        <input id="email" name="email" type="email" class="input" value="{{ old('email', $u?->email) }}" required>
                                    </div>
                                    <div class="form-group account-page__form-group">
                                        <label for="current_password_email">Current password</label>
                                        <input id="current_password_email" name="current_password" type="password" class="input" required>
                                    </div>
                                    <div class="account-page__actions">
                                        <button type="submit" class="btn primary">Update email</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card account-page__panel">
                            <div class="card-header account-page__panel-header">
                                <h3 class="account-page__panel-title">Change password</h3>
                            </div>
                            <div class="account-page__panel-body">
                                <form method="post" action="{{ url('/tenant/settings/account/password') }}">
                                    @csrf
                                    <div class="form-group account-page__form-group">
                                        <label for="current_password_pw">Current password</label>
                                        <input id="current_password_pw" name="current_password" type="password" class="input" required>
                                    </div>
                                    <div class="form-group account-page__form-group">
                                        <label for="new_password">New password</label>
                                        <input id="new_password" name="new_password" type="password" class="input" minlength="8" required>
                                    </div>
                                    <div class="form-group account-page__form-group">
                                        <label for="new_password_confirmation">Confirm new password</label>
                                        <input id="new_password_confirmation" name="new_password_confirmation" type="password" class="input" minlength="8" required>
                                    </div>
                                    <div class="account-page__actions">
                                        <button type="submit" class="btn primary">Update password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>
            /* Moodle-inspired settings layout: clean, readable, theme-consistent */
            .tenant-ui-mock .page-body {
                /* Forward the admin theme accent into the global --accent var */
                --accent:   var(--admin-primary);
                --accent-h: color-mix(in srgb, var(--admin-primary) 82%, #000000);
            }

            .account-page__header { margin-bottom: 18px; }
            .account-page__card { border-radius: 16px; }
            .account-page__card-body { padding: 6px 16px 18px; }

            .account-page__dl {
                margin: 0;
                display: grid;
                gap: 12px;
                font-size: 0.92rem;
            }
            .account-page__dl dt {
                color: var(--muted);
                font-size: 0.78rem;
                margin-bottom: 4px;
            }
            .account-page__dl dd {
                margin: 0;
            }
            .account-page__dl dd:first-child { font-weight: 600; }

            .account-page__roles { margin-top: 16px; }
            .account-page__roles-label { color: var(--muted); font-size: 0.78rem; margin-bottom: 6px; }
            .account-page__roles-badges { display: flex; flex-wrap: wrap; gap: 6px; }

            .account-page__grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px;
                margin-top: 20px;
                align-items: start;
            }
            .account-page__panel {
                border: 1px solid var(--border);
                box-shadow: none;
                border-radius: 14px;
            }
            .account-page__panel-header { border-bottom: 1px solid var(--border); }
            .account-page__panel-title { margin: 0; font-size: 0.95rem; font-weight: 700; }
            .account-page__panel-body { padding: 14px 16px 18px; }

            .account-page__form-group { margin-bottom: 14px; }
            .account-page__form-group label { margin-bottom: 6px; }
            .account-page__actions { margin-top: 6px; }

            /* Focus states: use theme accent variable, no hard-coded color */
            .tenant-ui-mock .page-body input:focus,
            .tenant-ui-mock .page-body select:focus,
            .tenant-ui-mock .page-body textarea:focus {
                border-color: var(--admin-primary);
                box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary) 18%, transparent);
            }

            @media (max-width: 860px) {
                .account-page__grid { grid-template-columns: 1fr; }
            }
            </style>
        </main>
    </div>
</div>
@endsection
