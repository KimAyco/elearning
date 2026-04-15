@once
@push('styles')
@php
    $adminAppearance = session('admin_appearance', []);
    $adminPrimary = $adminAppearance['primary'] ?? '#334155';
    $adminSidebar = $adminAppearance['sidebar'] ?? '#f8fafc';
    $adminAccent  = $adminAppearance['accent']  ?? '#475569';

    // Detect whether the sidebar is a light color so we can flip text to dark.
    // Parse the hex brightness: if R+G+B > 382 (out of 765) it's a light sidebar.
    $sidebarIsLight = false;
    $sidebarHex = ltrim($adminSidebar, '#');
    if (strlen($sidebarHex) === 6) {
        $r = hexdec(substr($sidebarHex, 0, 2));
        $g = hexdec(substr($sidebarHex, 2, 2));
        $b = hexdec(substr($sidebarHex, 4, 2));
        $sidebarIsLight = ($r + $g + $b) > 382;
    }
@endphp
<style>
/* ─── CSS variable roots: all theme colors derive from these three ─── */
.tenant-ui-mock {
    --admin-primary:    {{ $adminPrimary }};
    --admin-sidebar:    {{ $adminSidebar }};
    --admin-accent:     {{ $adminAccent }};

    /* Sidebar text tones: dark when sidebar is light, white when dark */
    --admin-sidebar-text:        {{ $sidebarIsLight ? '#1e293b' : 'rgba(255,255,255,0.88)' }};
    --admin-sidebar-muted:       {{ $sidebarIsLight ? '#64748b' : 'rgba(255,255,255,0.50)' }};
    --admin-sidebar-border:      {{ $sidebarIsLight ? 'rgba(15,23,42,0.08)' : 'rgba(255,255,255,0.10)' }};

    /* Hover: light sidebar → soft gray bg + keep dark text;
              dark sidebar → translucent white bg + white text */
    --admin-sidebar-hover-bg:    {{ $sidebarIsLight ? '#f1f5f9' : 'rgba(255,255,255,0.10)' }};
    --admin-sidebar-hover-txt:   {{ $sidebarIsLight ? '#0f172a' : '#ffffff' }};
    --admin-sidebar-hover-icon:  {{ $sidebarIsLight ? $adminPrimary : '#ffffff' }};

    /* Active: light sidebar → tinted primary bg + primary text;
               dark sidebar → translucent white bg + white text */
    --admin-sidebar-active-bg:   {{ $sidebarIsLight ? "color-mix(in srgb, {$adminPrimary} 12%, #ffffff)" : 'rgba(255,255,255,0.18)' }};
    --admin-sidebar-active-txt:  {{ $sidebarIsLight ? $adminPrimary : '#ffffff' }};
    --admin-sidebar-active-icon: {{ $sidebarIsLight ? $adminPrimary : '#ffffff' }};

    /* Submenu parent with active child */
    --admin-sidebar-parent-bg:   {{ $sidebarIsLight ? "color-mix(in srgb, {$adminPrimary} 8%, #ffffff)" : 'rgba(255,255,255,0.14)' }};
    --admin-sidebar-parent-txt:  {{ $sidebarIsLight ? $adminPrimary : '#ffffff' }};

    /* Content-area derived tints */
    --admin-primary-bg: color-mix(in srgb, var(--admin-primary) 12%, #ffffff);
    --admin-primary-l2: color-mix(in srgb, var(--admin-primary) 22%, #ffffff);
    --admin-accent-bg:  color-mix(in srgb, var(--admin-accent)  10%, #ffffff);
    --admin-focus-ring: color-mix(in srgb, var(--admin-primary) 22%, transparent);

    /* ── Override the global :root accent tokens so every component that
       uses --accent, --accent-l, --accent-l2, --accent-h automatically
       picks up the saved admin theme without needing per-component rules. ── */
    --accent:    var(--admin-primary);
    --accent-h:  color-mix(in srgb, var(--admin-primary) 82%, #000000);
    --accent-l:  color-mix(in srgb, var(--admin-primary) 10%, #ffffff);
    --accent-l2: color-mix(in srgb, var(--admin-primary) 20%, #ffffff);
}

/* ─── Shell ───────────────────────────────────────────────────────── */
.tenant-ui-mock.app-shell {
    width: 100%;
    max-width: 100%;
    min-width: 0;
    box-sizing: border-box;
}

.tenant-ui-mock .main-content {
    flex: 1 1 0%;
    min-width: 0;
    width: 100%;
    max-width: none;
}

/* ─── Sidebar shell — uses --admin-sidebar directly ──────────────── */
.tenant-ui-mock .sidebar--edu-mock {
    background: var(--admin-sidebar);
    border-right: {{ $sidebarIsLight ? '1px solid rgba(15,23,42,0.10)' : 'none' }};
    box-shadow: {{ $sidebarIsLight ? '2px 0 8px rgba(15,23,42,0.06)' : '4px 0 28px rgba(0,0,0,0.18)' }};
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-brand {
    align-items: flex-start;
    border-bottom: 1px solid var(--admin-sidebar-border);
    padding: 16px 14px 14px;
    gap: 12px;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-brand-text-block--edu-mock {
    padding-top: 2px;
}

/* ─── Brand mark ──────────────────────────────────────────────────── */
.tenant-ui-mock .sidebar--edu-mock .sidebar-brand-mock-mark {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-brand-mock-mark--has-logo {
    background: transparent;
    border: none;
    box-shadow: none;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-brand-mock-mark--has-logo img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: contain;
    background: rgba(255,255,255,0.12);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-brand-mock-mark--fallback {
    background: {{ $sidebarIsLight ? "color-mix(in srgb, {$adminPrimary} 14%, #ffffff)" : 'rgba(255,255,255,0.14)' }};
    border: none;
    box-shadow: none;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-brand-mock-fallback {
    color: {{ $sidebarIsLight ? $adminPrimary : '#ffffff' }};
    font-size: 1.2rem;
    font-weight: 800;
    line-height: 1;
    font-family: var(--font-sans, 'Inter', system-ui, -apple-system, sans-serif);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-brand-primary--edu-mock {
    font-family: var(--font-sans, 'Inter', system-ui, -apple-system, sans-serif);
    font-size: 0.72rem;
    font-weight: 800;
    line-height: 1.35;
    color: var(--admin-sidebar-text);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    word-wrap: break-word;
    hyphens: auto;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-brand-role {
    margin-top: 8px;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-brand-role-pill--edu-mock {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.62rem;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    background: {{ $sidebarIsLight ? "color-mix(in srgb, {$adminPrimary} 10%, #ffffff)" : 'rgba(255,255,255,0.15)' }};
    color: {{ $sidebarIsLight ? $adminPrimary : '#ffffff' }};
    border: 1px solid {{ $sidebarIsLight ? "color-mix(in srgb, {$adminPrimary} 24%, #ffffff)" : 'rgba(255,255,255,0.22)' }};
}

/* ─── Sidebar section labels ──────────────────────────────────────── */
.tenant-ui-mock .sidebar--edu-mock .sidebar-section-label {
    color: var(--admin-sidebar-muted);
    font-size: 0.62rem;
}

/* ─── Sidebar nav items ───────────────────────────────────────────── */
.tenant-ui-mock .sidebar--edu-mock .sidebar-link {
    border-radius: 12px;
    color: var(--admin-sidebar-text);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-link.active {
    background: var(--admin-sidebar-active-bg);
    color: var(--admin-sidebar-active-txt);
    font-weight: 600;
    box-shadow: none;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-link.active::before {
    display: none;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-link.active svg {
    opacity: 1;
    stroke: var(--admin-sidebar-active-icon);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-link:hover:not(.active) {
    background: var(--admin-sidebar-hover-bg);
    color: var(--admin-sidebar-hover-txt);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-link:hover:not(.active) svg {
    stroke: var(--admin-sidebar-hover-icon);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-link svg {
    stroke: var(--admin-sidebar-text);
}

/* ─── Submenu toggles ─────────────────────────────────────────────── */
.tenant-ui-mock .sidebar--edu-mock .sidebar-submenu-toggle {
    color: var(--admin-sidebar-text);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-submenu-toggle:hover {
    background: var(--admin-sidebar-hover-bg);
    color: var(--admin-sidebar-hover-txt);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-submenu-toggle:hover svg {
    stroke: var(--admin-sidebar-hover-icon);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-submenu-toggle.has-active {
    background: var(--admin-sidebar-parent-bg);
    color: var(--admin-sidebar-parent-txt);
    box-shadow: none;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-submenu-toggle--finance.open:not(.has-active) {
    background: var(--admin-sidebar-hover-bg);
    color: var(--admin-sidebar-hover-txt);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-submenu-toggle svg { stroke: var(--admin-sidebar-text); }
.tenant-ui-mock .sidebar--edu-mock .sidebar-submenu-toggle.has-active svg.icon-main { opacity: 1; stroke: var(--admin-sidebar-parent-txt); }
.tenant-ui-mock .sidebar--edu-mock .sidebar-submenu-toggle.has-active .sidebar-submenu-chevron { opacity: 1; }

/* ─── Sub-links ───────────────────────────────────────────────────── */
.tenant-ui-mock .sidebar--edu-mock .sidebar-sublink {
    color: var(--admin-sidebar-text);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-sublink::before {
    background: var(--admin-sidebar-muted);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-sublink.active {
    background: var(--admin-sidebar-active-bg);
    color: var(--admin-sidebar-active-txt);
    font-weight: 600;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-sublink.active::before {
    background: var(--admin-sidebar-active-icon);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-sublink:hover {
    background: var(--admin-sidebar-hover-bg);
    color: var(--admin-sidebar-hover-txt);
}

/* ─── Sidebar footer ──────────────────────────────────────────────── */
.tenant-ui-mock .sidebar--edu-mock .sidebar-footer {
    padding: 16px 12px 20px;
    border-top: 1px solid var(--admin-sidebar-border);
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-footer button {
    width: 100%;
    justify-content: center;
    border-radius: 999px;
    background: {{ $sidebarIsLight ? "color-mix(in srgb, {$adminPrimary} 10%, #ffffff)" : 'rgba(255,255,255,0.14)' }} !important;
    color: {{ $sidebarIsLight ? $adminPrimary : '#ffffff' }} !important;
    border: 1px solid {{ $sidebarIsLight ? "color-mix(in srgb, {$adminPrimary} 28%, #ffffff)" : 'rgba(255,255,255,0.22)' }} !important;
    font-weight: 700;
    padding: 11px 18px;
    box-shadow: none;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-footer button:hover {
    background: {{ $sidebarIsLight ? "color-mix(in srgb, {$adminPrimary} 18%, #ffffff)" : 'rgba(255,255,255,0.22)' }} !important;
}

.tenant-ui-mock .sidebar--edu-mock .sidebar-footer button svg {
    stroke: {{ $sidebarIsLight ? $adminPrimary : '#ffffff' }};
    opacity: 1;
}

/* ─── sidebar-logout-form sublink (Sign Out inside submenu) ──────── */
.tenant-ui-mock .sidebar--edu-mock .sidebar-logout-form .sidebar-sublink {
    color: var(--admin-sidebar-text);
}
.tenant-ui-mock .sidebar--edu-mock .sidebar-logout-form .sidebar-sublink:hover {
    background: {{ $sidebarIsLight ? 'rgba(220,38,38,0.08)' : 'rgba(255,80,80,0.18)' }};
    color: {{ $sidebarIsLight ? '#dc2626' : '#fca5a5' }};
}

/* ─── Topbar ──────────────────────────────────────────────────────── */
.tenant-ui-mock .topbar.topbar--tenant-mock {
    display: flex !important;
    flex-wrap: nowrap;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    min-height: 0;
    height: auto !important;
    max-width: 100%;
    overflow: visible;
    padding: 6px 12px 6px 8px;
    background: #ffffff;
    border-bottom: 1px solid color-mix(in srgb, var(--admin-primary) 12%, #e2e8f0);
    box-shadow: none;
}

.topbar-mock-left {
    display: flex;
    align-items: center;
    flex-shrink: 0;
}

.topbar-mock-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    margin-left: auto;
}

.topbar-mock-icon-btn {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #475569;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    position: relative;
}

.topbar-mock-icon-btn:hover {
    background: #f8fafc;
    color: var(--admin-primary);
}

.topbar-mock-icon-btn--badge .topbar-mock-badge {
    position: absolute;
    top: -2px;
    right: -2px;
    min-width: 15px;
    height: 15px;
    border-radius: 999px;
    background: #dc2626;
    color: #ffffff;
    font-size: 0.62rem;
    font-weight: 700;
    line-height: 15px;
    text-align: center;
    padding: 0 3px;
    border: 1px solid #ffffff;
}

.topbar-mock-notify {
    position: relative;
}

.topbar-notification-panel {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: min(360px, 84vw);
    max-height: 360px;
    overflow: auto;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16);
    z-index: 80;
    opacity: 0;
    transform: translateY(-6px) scale(0.98);
    transform-origin: top right;
    pointer-events: none;
    transition: opacity 0.16s ease, transform 0.16s ease;
}

.topbar-notification-panel.is-open {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}

.topbar-notification-panel__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
}
.topbar-notification-panel__head h4 {
    font-size: 0.86rem;
    color: #0f172a;
    font-weight: 700;
    margin: 0;
}
.topbar-notification-panel__head span {
    font-size: 0.72rem;
    font-weight: 700;
    color: var(--admin-primary);
    background: var(--admin-primary-bg);
    border: 1px solid color-mix(in srgb, var(--admin-primary) 25%, transparent);
    border-radius: 999px;
    padding: 2px 8px;
}

.topbar-notification-empty {
    padding: 26px 14px;
    text-align: center;
    color: #64748b;
}
.topbar-notification-empty svg {
    color: #94a3b8;
    margin-bottom: 6px;
}
.topbar-notification-empty p {
    margin: 0;
    font-size: 0.82rem;
}

.topbar-notification-list {
    padding: 6px 0;
}
.topbar-notification-item {
    padding: 10px 14px;
    border-bottom: 1px solid #f8fafc;
}
.topbar-notification-item:last-child { border-bottom: none; }
.topbar-notification-title {
    margin: 0 0 2px;
    color: #0f172a;
    font-size: 0.82rem;
    font-weight: 600;
}
.topbar-notification-text {
    margin: 0;
    color: #64748b;
    font-size: 0.76rem;
    line-height: 1.4;
}
.topbar-notification-time {
    margin-top: 4px;
    display: inline-block;
    color: #94a3b8;
    font-size: 0.7rem;
}

.topbar-mock-avatar-wrap {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    position: relative;
}

.topbar-mock-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--admin-primary);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.8rem;
    flex-shrink: 0;
    box-shadow: 0 1px 6px color-mix(in srgb, var(--admin-primary) 30%, transparent);
}
.topbar-mock-avatar img {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    display: block;
}
.topbar-mock-avatar > span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
}

.topbar-mock-chevron {
    width: 18px;
    height: 18px;
    border: none;
    background: transparent;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    border-radius: 999px;
}
.topbar-mock-chevron:hover {
    color: var(--admin-primary);
    background: #f8fafc;
}

.topbar-user-menu {
    position: absolute;
    top: calc(100% + 8px);
    right: 0;
    width: 230px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16);
    z-index: 90;
    overflow: hidden;
    opacity: 0;
    transform: translateY(-6px) scale(0.98);
    transform-origin: top right;
    pointer-events: none;
    transition: opacity 0.15s ease, transform 0.15s ease;
}
.topbar-user-menu.is-open {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}
.topbar-user-menu__item {
    width: 100%;
    display: flex;
    align-items: center;
    text-decoration: none;
    border: 0;
    background: #ffffff;
    color: #0f172a;
    font-size: 0.95rem;
    font-weight: 500;
    padding: 12px 14px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    text-align: left;
}
.topbar-user-menu__item:hover {
    background: #f8fafc;
    color: var(--admin-primary);
}
.topbar-user-menu__logout-form {
    margin: 0;
}
.topbar-user-menu__item--danger {
    color: #b91c1c;
    border-bottom: 0;
}
.topbar-user-menu__item--danger:hover {
    background: #fef2f2;
    color: #b91c1c;
}

/* Student profile modal */
.student-profile-modal[hidden] { display: none; }
.student-profile-modal {
    position: fixed;
    inset: 0;
    z-index: 120;
    background: rgba(2, 6, 23, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 18px;
    opacity: 0;
    transition: opacity 0.16s ease;
}
.student-profile-modal.is-open { opacity: 1; }
.student-profile-modal__dialog {
    width: min(640px, 95vw);
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    box-shadow: 0 20px 48px rgba(15, 23, 42, 0.22);
    transform: translateY(10px) scale(0.98);
    transition: transform 0.16s ease;
}
.student-profile-modal.is-open .student-profile-modal__dialog {
    transform: translateY(0) scale(1);
}
.student-profile-modal__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
}
.student-profile-modal__head h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #0f172a;
}
.student-profile-modal__close {
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 999px;
    background: #f8fafc;
    color: #475569;
    font-size: 1.2rem;
    line-height: 1;
    cursor: pointer;
}
.student-profile-modal__close:hover {
    background: #eef2ff;
    color: #1e293b;
}
.student-profile-modal__body {
    padding: 16px;
}
.student-profile-avatar-block {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 14px;
}
.student-profile-avatar-picker {
    position: relative;
    width: 84px;
    height: 84px;
    border-radius: 999px;
    border: 2px solid #dbeafe;
    overflow: visible;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    background: #ffffff;
}
.student-profile-avatar-picker img {
    width: 84px;
    height: 84px;
    border-radius: 999px;
    object-fit: cover;
}
.student-profile-avatar-fallback {
    width: 84px;
    height: 84px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--admin-primary);
    color: #ffffff;
    font-weight: 800;
    font-size: 1.5rem;
}
.student-profile-avatar-edit {
    position: absolute;
    right: -2px;
    bottom: -2px;
    width: 22px;
    height: 22px;
    border-radius: 999px;
    background: #2563eb;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    border: 2px solid #ffffff;
}
.student-profile-avatar-hint {
    margin: 8px 0 0;
    font-size: 0.73rem;
    color: #64748b;
}
.student-profile-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px 12px;
}
.student-profile-field {
    display: flex;
    flex-direction: column;
    gap: 5px;
}
.student-profile-field label {
    font-size: 0.75rem;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}
.student-profile-value {
    margin: 0;
    min-height: 38px;
    border: 1px solid #dbe3ee;
    border-radius: 10px;
    padding: 8px 10px;
    font-size: 0.92rem;
    color: #0f172a;
    background: #f8fafc;
    display: flex;
    align-items: center;
    line-height: 1.35;
}
.student-profile-field--span {
    grid-column: 1 / -1;
}
.student-profile-modal__foot {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    padding: 12px 16px 16px;
    border-top: 1px solid #f1f5f9;
}
.student-profile-modal__foot .btn.primary[disabled] {
    opacity: 0.55;
    cursor: not-allowed;
}
@media (max-width: 640px) {
    .student-profile-grid { grid-template-columns: 1fr; }
    .student-profile-field--span { grid-column: auto; }
}

/* ─── Page body / content area ────────────────────────────────────── */
.tenant-ui-mock .page-body {
    --dash-bg:        #f8fafc;
    --dash-surface:   #ffffff;
    --dash-border:    color-mix(in srgb, var(--admin-primary) 16%, #e2e8f0);
    --dash-ink:       #0f172a;
    --dash-ink-2:     #334155;
    --dash-muted:     #64748b;
    --dash-accent:    var(--admin-primary);
    --dash-radius:    10px;
    --dash-radius-lg: 16px;
    --dash-shadow:    none;
    --dash-shadow-md: 0 6px 20px color-mix(in srgb, var(--admin-primary) 10%, transparent);

    background: color-mix(in srgb, var(--admin-primary) 4%, #f8fafc);
    padding: 22px 20px 44px;
    width: 100%;
    max-width: none;
    margin: 0;
    box-sizing: border-box;
    overflow-x: hidden;
}

/* ─── Buttons inside tenant shell ─────────────────────────────────── */
.tenant-ui-mock .btn.primary {
    background: var(--admin-primary);
    color: #ffffff;
    box-shadow: 0 2px 8px color-mix(in srgb, var(--admin-primary) 30%, transparent);
}

.tenant-ui-mock .btn.primary:hover {
    background: color-mix(in srgb, var(--admin-primary) 85%, #000000);
    box-shadow: 0 4px 14px color-mix(in srgb, var(--admin-primary) 38%, transparent);
}

.tenant-ui-mock .btn.secondary:hover {
    border-color: var(--admin-primary);
    color: var(--admin-primary);
}

/* ─── Input focus inside tenant shell ─────────────────────────────── */
.tenant-ui-mock input:focus,
.tenant-ui-mock select:focus,
.tenant-ui-mock textarea:focus {
    border-color: var(--admin-primary);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary) 18%, transparent);
    outline: none;
}

/* ─── Links inside tenant shell ───────────────────────────────────── */
.tenant-ui-mock a {
    color: var(--admin-primary);
}

/* ─── Badge / pill overrides ──────────────────────────────────────── */
.tenant-ui-mock .badge.green {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
    border-color: color-mix(in srgb, var(--admin-primary) 28%, transparent);
}

/* ─── Tabs / active highlights ────────────────────────────────────── */
.tenant-ui-mock .tab-btn.active {
    color: var(--admin-primary);
    border-bottom-color: var(--admin-primary);
}

/* ─── Card section accents ────────────────────────────────────────── */
.tenant-ui-mock .card-icon.blue,
.tenant-ui-mock .stat-icon.blue {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
}

/* ─── Pipeline dots ───────────────────────────────────────────────── */
.tenant-ui-mock .pipeline-dot.active {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
    border-color: var(--admin-primary);
}
.tenant-ui-mock .pipeline-label.active {
    color: var(--admin-primary);
}

/* ─── Section dividers / accents ──────────────────────────────────── */
.tenant-ui-mock .section-divider {
    color: var(--admin-primary);
    border-color: color-mix(in srgb, var(--admin-primary) 22%, #e2e8f0);
}

/* ─── Enrollments page: override all hardcoded colors ────────────────
   enrollments.blade.php has its own full CSS block with hardcoded green,
   blue, and amber hex values. Every theme-relevant rule is overridden
   here using the admin CSS variables so saving any theme color works.   */

/* KPI cards */
.tenant-ui-mock .enroll-kpi-card {
    border-color: color-mix(in srgb, var(--admin-primary) 22%, transparent);
}
.tenant-ui-mock .enroll-kpi-card:hover {
    border-color: color-mix(in srgb, var(--admin-primary) 40%, transparent);
    box-shadow: 0 4px 20px color-mix(in srgb, var(--admin-primary) 12%, transparent);
}
.tenant-ui-mock .enroll-kpi-icon {
    color: var(--admin-primary);
}
.tenant-ui-mock .enroll-kpi-num--green {
    color: var(--admin-primary);
}
.tenant-ui-mock .enroll-kpi-num--blue {
    color: var(--admin-primary);
}
.tenant-ui-mock .enroll-kpi-num--purple {
    color: var(--admin-primary);
}

/* Tab strip pill buttons */
.tenant-ui-mock .enroll-tab-btn {
    border-color: color-mix(in srgb, var(--admin-primary) 30%, transparent);
}
.tenant-ui-mock .enroll-tab-btn:hover {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
    border-color: color-mix(in srgb, var(--admin-primary) 55%, transparent);
}
.tenant-ui-mock .enroll-tab-btn.active {
    background: var(--admin-primary);
    color: #ffffff;
    border-color: transparent;
    box-shadow: 0 3px 10px color-mix(in srgb, var(--admin-primary) 30%, transparent);
}

/* Section cards */
.tenant-ui-mock .card {
    background: #ffffff;
    border-color: color-mix(in srgb, var(--admin-primary) 18%, transparent);
}
.tenant-ui-mock .card-header {
    border-bottom-color: color-mix(in srgb, var(--admin-primary) 14%, transparent);
}
.tenant-ui-mock .card-icon.green {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
}
.tenant-ui-mock .card-icon.blue {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
}

/* Table thead border */
.tenant-ui-mock .table-wrap table thead th {
    border-bottom-color: color-mix(in srgb, var(--admin-primary) 18%, transparent);
}
/* Table row hover */
.tenant-ui-mock .table-wrap table tbody tr:hover td {
    background: color-mix(in srgb, var(--admin-primary) 4%, #ffffff);
}

/* Search inputs — all variants */
.tenant-ui-mock .enroll-verified-search-input,
.tenant-ui-mock .enroll-class-group-search-input,
.tenant-ui-mock .enroll-all-students-search-input,
.tenant-ui-mock .enroll-inactive-search-input,
.tenant-ui-mock .enroll-registrar-staff-search-input {
    border-color: color-mix(in srgb, var(--admin-primary) 28%, transparent);
}
.tenant-ui-mock .enroll-verified-search-input:focus,
.tenant-ui-mock .enroll-class-group-search-input:focus,
.tenant-ui-mock .enroll-all-students-search-input:focus,
.tenant-ui-mock .enroll-inactive-search-input:focus,
.tenant-ui-mock .enroll-registrar-staff-search-input:focus {
    border-color: color-mix(in srgb, var(--admin-primary) 65%, transparent);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary) 14%, transparent);
}

/* Search buttons — all variants */
.tenant-ui-mock .enroll-verified-search-btn,
.tenant-ui-mock .enroll-class-group-search-btn,
.tenant-ui-mock .enroll-all-students-search-btn,
.tenant-ui-mock .enroll-inactive-search-btn,
.tenant-ui-mock .enroll-registrar-staff-search-btn {
    background: var(--admin-primary);
    border-color: var(--admin-primary);
    color: #ffffff;
}
.tenant-ui-mock .enroll-verified-search-btn:hover,
.tenant-ui-mock .enroll-class-group-search-btn:hover,
.tenant-ui-mock .enroll-all-students-search-btn:hover,
.tenant-ui-mock .enroll-inactive-search-btn:hover,
.tenant-ui-mock .enroll-registrar-staff-search-btn:hover {
    background: color-mix(in srgb, var(--admin-primary) 82%, #000000);
    border-color: color-mix(in srgb, var(--admin-primary) 82%, #000000);
}

/* Badge green inside enrollment tables */
.tenant-ui-mock main.page-body .badge.green {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
    border-color: color-mix(in srgb, var(--admin-primary) 28%, transparent);
}

/* ─── Dashboard: override all hardcoded green borders/shadows ───────
   dashboard.blade.php defines its own --dash-* tokens inside
   .dashboard-body. We re-point them to the admin theme here so that
   every card, welcome banner, and hover state follows the saved colors.   */
.tenant-ui-mock .dashboard-body {
    --dash-border:    color-mix(in srgb, var(--admin-primary) 28%, transparent);
    --dash-accent:    var(--admin-primary);
    --dash-shadow-md: 0 6px 20px color-mix(in srgb, var(--admin-primary) 10%, transparent);
}

/* Dashboard card borders & hover */
.tenant-ui-mock .dashboard-card {
    border-color: color-mix(in srgb, var(--admin-primary) 22%, transparent);
}
.tenant-ui-mock .dashboard-card:hover {
    border-color: color-mix(in srgb, var(--admin-primary) 40%, transparent);
    box-shadow: 0 6px 20px color-mix(in srgb, var(--admin-primary) 10%, transparent);
}
.tenant-ui-mock .dashboard-card--highlight {
    border-color: color-mix(in srgb, var(--admin-primary) 40%, transparent);
}

/* Welcome banner border & shadow */
.tenant-ui-mock .dashboard-welcome--mock {
    border-color: color-mix(in srgb, var(--admin-primary) 30%, transparent);
    box-shadow: 0 4px 24px color-mix(in srgb, var(--admin-primary) 8%, transparent);
}

/* Welcome banner avatar & name accent */
.tenant-ui-mock .dashboard-welcome-avatar {
    background: var(--admin-primary);
    box-shadow: 0 4px 16px color-mix(in srgb, var(--admin-primary) 32%, transparent);
}
.tenant-ui-mock .dashboard-welcome-title strong {
    color: color-mix(in srgb, var(--admin-primary) 85%, #000000);
}

/* Dashboard card-icon variants that use the green slot */
.tenant-ui-mock .dashboard-card-icon--blue,
.tenant-ui-mock .dashboard-card-icon--green {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
}

/* Dashboard "0 sessions" badge pill */
.tenant-ui-mock .dashboard-badge--sessions,
.tenant-ui-mock [class*="dashboard-badge"] {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
    border-color: color-mix(in srgb, var(--admin-primary) 28%, transparent);
}

/* ─── Topbar breadcrumb link ──────────────────────────────────────── */
.tenant-ui-mock .topbar-meta strong,
.tenant-ui-mock .topbar-mock-meta strong {
    color: var(--admin-primary);
}
.tenant-ui-mock .breadcrumb a,
.tenant-ui-mock .breadcrumb a:hover {
    color: var(--admin-primary);
}

/* ─── Card icons: green variant follows admin theme ──────────────── */
.tenant-ui-mock .card-icon.green,
.tenant-ui-mock .stat-icon.green {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
}

/* ─── Stat values / big numbers ──────────────────────────────────── */
.tenant-ui-mock .stat-value {
    color: var(--admin-primary);
}

/* ─── Alert success: tinted with admin primary ───────────────────── */
.tenant-ui-mock .alert.success {
    background: color-mix(in srgb, var(--admin-primary) 7%, #ffffff);
    border-color: color-mix(in srgb, var(--admin-primary) 22%, #ffffff);
    color: color-mix(in srgb, var(--admin-primary) 90%, #000000);
}
.tenant-ui-mock .alert.success svg {
    stroke: var(--admin-primary);
}

/* ─── Badge: green variant follows admin theme ───────────────────── */
.tenant-ui-mock .badge.green {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
    border-color: color-mix(in srgb, var(--admin-primary) 28%, #ffffff);
}

/* ─── Filter / tab pill buttons (active state) ───────────────────── */
.tenant-ui-mock .tab-btn.active,
.tenant-ui-mock .enroll-tab-btn.active {
    color: var(--admin-primary);
    border-bottom-color: var(--admin-primary);
}

/* ─── Enrollment pill/chip tabs: border, hover, active ───────────── */
.tenant-ui-mock .enroll-tab-btn {
    border-color: color-mix(in srgb, var(--admin-primary) 30%, transparent);
}
.tenant-ui-mock .enroll-tab-btn:hover {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
    border-color: color-mix(in srgb, var(--admin-primary) 55%, transparent);
}
.tenant-ui-mock .enroll-tab-btn.active {
    background: var(--admin-primary);
    color: #ffffff;
    border-color: transparent;
    box-shadow: 0 3px 10px color-mix(in srgb, var(--admin-primary) 30%, transparent);
}

/* ─── Filled pill / filter chip active (e.g. Pending Clearances btn) */
.tenant-ui-mock .filter-btn.active,
.tenant-ui-mock [data-filter].active,
.tenant-ui-mock .enrollments-tab-btn.active,
.tenant-ui-mock .section-tab.active {
    background: var(--admin-primary);
    color: #ffffff;
    border-color: var(--admin-primary);
}

/* ─── Pipeline arrows active ─────────────────────────────────────── */
.tenant-ui-mock .pipeline-arrow.done {
    background: var(--admin-primary);
}
.tenant-ui-mock .pipeline-dot.done {
    background: var(--admin-primary-bg);
    color: var(--admin-primary);
    border-color: var(--admin-primary);
}

/* ─── Checkbox / radio accents ───────────────────────────────────── */
.tenant-ui-mock input[type="checkbox"]:checked,
.tenant-ui-mock input[type="radio"]:checked {
    accent-color: var(--admin-primary);
}

/* ─── Table row hover ────────────────────────────────────────────── */
.tenant-ui-mock table tbody tr:hover td {
    background: color-mix(in srgb, var(--admin-primary) 4%, #ffffff);
}

/* ─── Section header / card-header accent border ─────────────────── */
.tenant-ui-mock .card-header {
    border-bottom: 1px solid color-mix(in srgb, var(--admin-primary) 10%, #e2e8f0);
}

/* ─── Topbar user-info name ──────────────────────────────────────── */
.tenant-ui-mock .topbar-user-name,
.tenant-ui-mock .topbar-user strong {
    color: var(--admin-primary);
}

@media (max-width: 900px) {
    .tenant-ui-mock .page-body {
        padding: 20px 16px 32px;
    }
}
</style>
@endpush
@endonce
