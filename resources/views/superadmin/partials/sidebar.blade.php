@php
    $nav = $activeNav ?? 'dashboard';
    $pendingN = isset($pendingRegistrations) ? $pendingRegistrations->count() : 0;
@endphp
<aside class="sidebar sa-sidebar">
    <div class="sa-sidebar-brand">
        <div class="sa-sidebar-brand-row">
            <div class="sa-sidebar-logo-disc">
                <img src="/images/logoon.png" alt="" class="sa-sidebar-logo-img" width="40" height="40" decoding="async">
            </div>
            <span class="sa-sidebar-wordmark">
                <span class="sa-sidebar-wordmark__line">Nehemiah</span>
                <span class="sa-sidebar-wordmark__line sa-sidebar-wordmark__line--sub">Solutions</span>
            </span>
        </div>
        <span class="sa-sidebar-badge">Super Admin</span>
    </div>

    <nav class="sa-sidebar-nav">
        <div class="sa-sidebar-section">Platform</div>
        <a href="{{ route('superadmin.dashboard') }}" class="sa-nav-link {{ $nav === 'dashboard' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/>
            </svg>
            Dashboard
        </a>
        <a href="{{ route('superadmin.approvals') }}" class="sa-nav-link {{ $nav === 'approvals' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            Approvals
            @if ($pendingN > 0)
                <span class="sa-nav-badge">{{ $pendingN }}</span>
            @endif
        </a>
        <a href="{{ route('superadmin.pricing') }}" class="sa-nav-link {{ $nav === 'pricing' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
            Subscription pricing
        </a>
        <a href="{{ route('superadmin.schools') }}" class="sa-nav-link {{ $nav === 'schools' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Schools
        </a>
    </nav>

    <div class="sa-sidebar-footer">
        <form method="post" action="{{ url('/superadmin/logout') }}">
            @csrf
            <button type="submit" class="sa-signout">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
                Sign out
            </button>
        </form>
    </div>
</aside>

@push('styles')
<style>
    :root {
        --sa-sidebar-w: 260px;
    }

    .sa-sidebar.sidebar {
        width: var(--sa-sidebar-w);
        background: linear-gradient(180deg, #12101c 0%, #0a0812 100%);
        border-right: 1px solid rgba(255,255,255,0.08);
        box-shadow: 4px 0 24px rgba(0,0,0,0.12);
    }

    .sa-sidebar .sidebar-brand { display: none; }

    .sa-sidebar-brand {
        padding: 20px 16px 18px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .sa-sidebar-brand-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sa-sidebar-logo-disc {
        flex-shrink: 0;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #fff;
        border: 1px solid rgba(255, 255, 255, 0.35);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.35);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        box-sizing: border-box;
    }

    .sa-sidebar-logo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .sa-sidebar-wordmark {
        display: flex;
        flex-direction: column;
        gap: 1px;
        min-width: 0;
        line-height: 1.12;
    }

    .sa-sidebar-wordmark__line {
        font-size: 1rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #f8fafc;
    }

    .sa-sidebar-wordmark__line--sub {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(232, 160, 92, 0.95);
    }

    .sa-sidebar-badge {
        display: block;
        margin-top: 10px;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(232, 160, 92, 0.9);
    }

    .sa-sidebar-nav {
        flex: 1;
        min-height: 0;
        padding: 16px 10px;
        overflow-y: auto;
    }

    .sa-sidebar-section {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: rgba(148, 163, 184, 0.65);
        padding: 8px 12px 10px;
    }

    .sa-nav-link {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        margin-bottom: 4px;
        border-radius: 10px;
        color: rgba(226, 232, 240, 0.88);
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 600;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .sa-nav-link svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
        opacity: 0.9;
    }

    .sa-nav-link:hover {
        background: rgba(255,255,255,0.06);
        color: #fff;
        text-decoration: none;
    }

    .sa-nav-link:focus-visible {
        text-decoration: none;
    }

    .sa-nav-link.active {
        background: linear-gradient(90deg, rgba(197, 90, 46, 0.35), rgba(124, 58, 237, 0.2));
        color: #fff;
        box-shadow: inset 3px 0 0 0 #e8a05c;
    }

    .sa-nav-badge {
        margin-left: auto;
        font-size: 0.65rem;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 999px;
        background: rgba(245, 158, 11, 0.25);
        color: #fcd34d;
        border: 1px solid rgba(251, 191, 36, 0.35);
    }

    .sa-sidebar-footer {
        padding: 12px 12px 18px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    .sa-signout {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid rgba(255,255,255,0.12);
        background: rgba(255,255,255,0.04);
        color: rgba(226, 232, 240, 0.9);
        font-size: 0.86rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .sa-signout:hover {
        background: rgba(239, 68, 68, 0.15);
        border-color: rgba(239, 68, 68, 0.35);
        color: #fecaca;
    }

    @media (max-width: 768px) {
        .sa-sidebar.sidebar {
            transform: translateX(-100%);
        }
        .sa-sidebar.sidebar.open {
            transform: translateX(0);
        }
        .sa-main { margin-left: 0; }
    }
</style>
@endpush
