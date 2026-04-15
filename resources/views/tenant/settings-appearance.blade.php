@extends('layouts.app')

@section('title', 'Appearance - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => $active ?? 'settings-appearance', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body">
            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <span>Settings</span>
                </div>
                <h1>Appearance</h1>
                <p>Control the look and feel of this admin dashboard only. Public branding and school landing pages are managed separately.</p>
            </div>

            <div class="card mb-20 appearance-card">
                <div class="card-header appearance-card__header">
                    <h2>
                        <div class="card-icon purple">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" y1="21" x2="4" y2="14"/>
                                <line x1="4" y1="10" x2="4" y2="3"/>
                                <line x1="12" y1="21" x2="12" y2="12"/>
                                <line x1="12" y1="8" x2="12" y2="3"/>
                                <line x1="20" y1="21" x2="20" y2="16"/>
                                <line x1="20" y1="12" x2="20" y2="3"/>
                                <line x1="1" y1="14" x2="7" y2="14"/>
                                <line x1="9" y1="8" x2="15" y2="8"/>
                                <line x1="17" y1="16" x2="23" y2="16"/>
                            </svg>
                        </div>
                        Admin dashboard appearance
                    </h2>
                    <span class="badge purple">UI preview only</span>
                </div>

                <div class="appearance-card__body">
                    <div class="appearance-layout">
                        <section class="appearance-pane">
                            <h3 class="appearance-section-title">Theme preset</h3>
                            <p class="appearance-section-help">
                                Choose a starting point for the admin dashboard theme. This does not affect the public school page.
                            </p>

                            <form method="post" action="{{ url('/tenant/settings/appearance') }}" id="appearance-form">
                                @csrf
                                <div class="appearance-field-group">
                                    <label for="appearance-theme-preset" class="appearance-label">Preset</label>
                                    <select id="appearance-theme-preset" name="preset" class="input">
                                        @php $preset = $appearance['preset'] ?? 'default'; @endphp
                                        <option value="default" {{ $preset === 'default' ? 'selected' : '' }}>Default</option>
                                        <option value="green" {{ $preset === 'green' ? 'selected' : '' }}>Green</option>
                                        <option value="blue" {{ $preset === 'blue' ? 'selected' : '' }}>Blue</option>
                                        <option value="purple" {{ $preset === 'purple' ? 'selected' : '' }}>Purple</option>
                                        <option value="dark" {{ $preset === 'dark' ? 'selected' : '' }}>Dark</option>
                                    </select>
                                </div>
                                <p class="appearance-hint">
                                    Later you will be able to save presets per school. For now this is a visual preview only.
                                </p>

                            <hr class="appearance-divider">

                            <h3 class="appearance-section-title">Custom colors</h3>
                            <p class="appearance-section-help">
                                Experiment with primary, sidebar, and accent colors for the admin interface. These controls are decorative only for now.
                            </p>

                            <div class="appearance-color-grid">
                                <div class="appearance-color-row">
                                    <div class="appearance-color-labels">
                                        <span class="appearance-label">Primary color</span>
                                        <span class="appearance-hint">Used for main action buttons and highlights.</span>
                                    </div>
                                    <div class="appearance-color-controls">
                                        <input type="color" class="appearance-color-input" id="primary-color-input" value="{{ $appearance['primary'] ?? '#334155' }}">
                                        <input type="text" class="input appearance-hex-input" id="primary-color-hex" name="primary_color" value="{{ $appearance['primary'] ?? '#334155' }}">
                                        <span class="appearance-rgb" id="primary-color-rgb">RGB 21, 128, 61</span>
                                    </div>
                                </div>

                                <div class="appearance-color-row">
                                    <div class="appearance-color-labels">
                                        <span class="appearance-label">Sidebar color</span>
                                        <span class="appearance-hint">Background for the left navigation panel.</span>
                                    </div>
                                    <div class="appearance-color-controls">
                                        <input type="color" class="appearance-color-input" id="sidebar-color-input" value="{{ $appearance['sidebar'] ?? '#f8fafc' }}">
                                        <input type="text" class="input appearance-hex-input" id="sidebar-color-hex" name="sidebar_color" value="{{ $appearance['sidebar'] ?? '#f8fafc' }}">
                                        <span class="appearance-rgb" id="sidebar-color-rgb">RGB 6, 95, 70</span>
                                    </div>
                                </div>

                                <div class="appearance-color-row">
                                    <div class="appearance-color-labels">
                                        <span class="appearance-label">Accent color</span>
                                        <span class="appearance-hint">Links, badges, and subtle highlights.</span>
                                    </div>
                                    <div class="appearance-color-controls">
                                        <input type="color" class="appearance-color-input" id="accent-color-input" value="{{ $appearance['accent'] ?? '#475569' }}">
                                        <input type="text" class="input appearance-hex-input" id="accent-color-hex" name="accent_color" value="{{ $appearance['accent'] ?? '#475569' }}">
                                        <span class="appearance-rgb" id="accent-color-rgb">RGB 15, 118, 110</span>
                                    </div>
                                </div>
                            </div>

                            <div class="appearance-actions">
                                <button type="submit" class="btn primary">Save changes</button>
                                <button type="submit" name="reset_admin_appearance" value="1" class="btn secondary">Reset to default</button>
                            </div>
                            </form>
                        </section>

                        <aside class="appearance-preview">
                            <h3 class="appearance-section-title">Preview</h3>
                            <p class="appearance-section-help">
                                A compact preview of how the admin dashboard could look with these settings.
                            </p>

                            <div class="appearance-preview-card">
                                <div class="appearance-preview-shell">
                                    <div class="appearance-preview-sidebar">
                                        <div class="appearance-preview-logo"></div>
                                        <div class="appearance-preview-nav">
                                            <div class="appearance-preview-nav-item appearance-preview-nav-item--active">Dashboard</div>
                                            <div class="appearance-preview-nav-item">Registrar</div>
                                            <div class="appearance-preview-nav-item">Finance</div>
                                            <div class="appearance-preview-nav-item">Settings</div>
                                        </div>
                                    </div>
                                    <div class="appearance-preview-main">
                                        <div class="appearance-preview-topbar">
                                            <div class="appearance-preview-breadcrumb"></div>
                                            <div class="appearance-preview-avatar"></div>
                                        </div>
                                        <div class="appearance-preview-body">
                                            <div class="appearance-preview-card-row">
                                                <div class="appearance-preview-card-block"></div>
                                                <div class="appearance-preview-card-block appearance-preview-card-block--muted"></div>
                                            </div>
                                            <div class="appearance-preview-form">
                                                <div class="appearance-preview-input"></div>
                                                <div class="appearance-preview-input appearance-preview-input--focused"></div>
                                                <div class="appearance-preview-actions">
                                                    <div class="appearance-preview-btn appearance-preview-btn--primary"></div>
                                                    <div class="appearance-preview-btn appearance-preview-btn--ghost"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>

            <style>
            .appearance-card {
                border-radius: 16px;
                overflow: hidden;
            }
            .appearance-card__header {
                border-bottom: 1px solid var(--border);
            }
            .appearance-card__body {
                padding: 14px 16px 18px;
            }
            .appearance-layout {
                display: grid;
                grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.2fr);
                gap: 20px;
                align-items: flex-start;
            }
            .appearance-pane,
            .appearance-preview {
                min-width: 0;
            }
            .appearance-section-title {
                margin: 0 0 4px;
                font-size: 0.98rem;
                font-weight: 700;
                color: var(--ink);
            }
            .appearance-section-help {
                margin: 0 0 14px;
                font-size: 0.85rem;
                color: var(--muted);
            }
            .appearance-field-group {
                margin-bottom: 16px;
            }
            .appearance-label {
                display: block;
                font-size: 0.84rem;
                font-weight: 600;
                color: var(--ink-2);
                margin-bottom: 6px;
            }
            .appearance-hint {
                margin-top: 4px;
                font-size: 0.8rem;
                color: var(--muted);
            }
            .appearance-divider {
                border: 0;
                border-top: 1px solid var(--border);
                margin: 18px 0 16px;
            }
            .appearance-color-grid {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }
            .appearance-color-row {
                display: flex;
                flex-wrap: wrap;
                gap: 10px 14px;
                align-items: flex-start;
            }
            .appearance-color-labels {
                min-width: 160px;
                max-width: 220px;
            }
            .appearance-color-controls {
                flex: 1;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                align-items: center;
            }
            .appearance-color-input {
                width: 40px;
                height: 32px;
                padding: 0;
                border-radius: 8px;
                border: 1px solid var(--border-2);
                background: transparent;
                cursor: pointer;
            }
            .appearance-hex-input {
                max-width: 120px;
            }
            .appearance-rgb {
                font-size: 0.78rem;
                color: var(--muted);
            }
            .appearance-actions {
                margin-top: 20px;
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
            }

            .appearance-preview-card {
                border-radius: 14px;
                border: 1px solid var(--border);
                background: var(--dash-surface, #ffffff);
                box-shadow: 0 6px 20px rgba(15, 81, 50, 0.08);
                padding: 10px;
            }
            .appearance-preview-shell {
                display: grid;
                grid-template-columns: 90px minmax(0, 1fr);
                background: #f8fafc;
                border-radius: 10px;
                overflow: hidden;
                border: 1px solid #e2e8f0;
            }
            .appearance-preview-sidebar {
                background: var(--admin-sidebar, #f8fafc);
                border-right: 1px solid rgba(15,23,42,0.08);
                padding: 8px 6px;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .appearance-preview-logo {
                width: 30px;
                height: 30px;
                border-radius: 999px;
                background: rgba(71,85,105,0.14);
                margin-bottom: 4px;
            }
            .appearance-preview-nav {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }
            .appearance-preview-nav-item {
                border-radius: 999px;
                padding: 4px 8px;
                font-size: 0.68rem;
                /* JS overrides this based on sidebar brightness */
                color: #64748b;
                background: transparent;
            }
            .appearance-preview-nav-item--active {
                background: var(--admin-primary, #334155);
                color: #ffffff;
                font-weight: 600;
            }
            .appearance-preview-main {
                background: #f1f5f9;
                display: flex;
                flex-direction: column;
            }
            .appearance-preview-topbar {
                height: 32px;
                background: #ffffff;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 10px;
            }
            .appearance-preview-breadcrumb {
                width: 80px;
                height: 6px;
                border-radius: 999px;
                background: #cbd5e1;
            }
            .appearance-preview-avatar {
                width: 20px;
                height: 20px;
                border-radius: 999px;
                background: var(--admin-primary, #334155);
            }
            .appearance-preview-body {
                padding: 10px;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .appearance-preview-card-row {
                display: grid;
                grid-template-columns: 1.2fr 1fr;
                gap: 8px;
            }
            .appearance-preview-card-block {
                height: 40px;
                border-radius: 10px;
                background: #ffffff;
                border: 1px solid #e2e8f0;
            }
            .appearance-preview-card-block--muted {
                opacity: 0.78;
            }
            .appearance-preview-form {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }
            .appearance-preview-input {
                height: 10px;
                border-radius: 999px;
                background: #e5e7eb;
            }
            .appearance-preview-input--focused {
                background: #ffffff;
                box-shadow: 0 0 0 2px color-mix(in srgb, var(--admin-primary, #334155) 35%, transparent);
            }
            .appearance-preview-actions {
                margin-top: 4px;
                display: flex;
                gap: 6px;
            }
            .appearance-preview-btn {
                height: 16px;
                border-radius: 999px;
                flex: 0 0 72px;
            }
            .appearance-preview-btn--primary {
                background: var(--admin-primary, #334155);
            }
            .appearance-preview-btn--ghost {
                background: transparent;
                border: 1px solid #9ca3af;
            }

            @media (max-width: 960px) {
                .appearance-layout {
                    grid-template-columns: minmax(0, 1fr);
                }
                .appearance-preview-card {
                    margin-top: 4px;
                }
            }
            </style>
            @push('scripts')
            <script>
            (function () {
                const hexToRgb = (hex) => {
                    const clean = hex.replace('#', '');
                    if (clean.length !== 6) return null;
                    const num = parseInt(clean, 16);
                    return {
                        r: (num >> 16) & 255,
                        g: (num >> 8) & 255,
                        b: num & 255,
                    };
                };

                const syncPair = (colorInputId, hexInputId, rgbLabelId) => {
                    const colorEl = document.getElementById(colorInputId);
                    const hexEl = document.getElementById(hexInputId);
                    const rgbEl = document.getElementById(rgbLabelId);
                    if (!colorEl || !hexEl) return;

                    const updateRgb = (hex) => {
                        if (!rgbEl) return;
                        const rgb = hexToRgb(hex);
                        if (!rgb) return;
                        rgbEl.textContent = 'RGB ' + rgb.r + ', ' + rgb.g + ', ' + rgb.b;
                    };

                    const syncFromColor = () => {
                        hexEl.value = colorEl.value;
                        updateRgb(colorEl.value);
                        applyPreviewColors();
                    };

                    const syncFromHex = () => {
                        let val = hexEl.value.trim();
                        if (!val.startsWith('#')) val = '#' + val;
                        if (/^#[0-9A-Fa-f]{6}$/.test(val)) {
                            colorEl.value = val;
                            hexEl.value = val;
                            updateRgb(val);
                            applyPreviewColors();
                        }
                    };

                    colorEl.addEventListener('input', syncFromColor);
                    hexEl.addEventListener('blur', syncFromHex);

                    updateRgb(hexEl.value);
                };

                // Returns true when a hex color is perceptually light (brightness > 0.5)
                const hexIsLight = (hex) => {
                    const h = hex.replace('#', '');
                    if (h.length !== 6) return false;
                    const r = parseInt(h.slice(0,2),16);
                    const g = parseInt(h.slice(2,4),16);
                    const b = parseInt(h.slice(4,6),16);
                    // Weighted luminance
                    return (r * 0.299 + g * 0.587 + b * 0.114) > 160;
                };

                const applyPreviewColors = () => {
                    const primary = document.getElementById('primary-color-hex')?.value || '#334155';
                    const sidebar = document.getElementById('sidebar-color-hex')?.value || '#f8fafc';
                    const accent  = document.getElementById('accent-color-hex')?.value  || '#475569';
                    const lightSidebar = hexIsLight(sidebar);

                    // Push all three variables onto the real admin shell so that
                    // every CSS rule in tenant-mock-ui.blade.php picks them up automatically.
                    const root = document.querySelector('.tenant-ui-mock');
                    if (root) {
                        root.style.setProperty('--admin-primary',           primary);
                        root.style.setProperty('--admin-sidebar',           sidebar);
                        root.style.setProperty('--admin-accent',            accent);
                        // Mirror PHP-side sidebar contrast logic in JS
                        root.style.setProperty('--admin-sidebar-text',       lightSidebar ? '#1e293b' : 'rgba(255,255,255,0.88)');
                        root.style.setProperty('--admin-sidebar-muted',      lightSidebar ? '#64748b' : 'rgba(255,255,255,0.50)');
                        root.style.setProperty('--admin-sidebar-border',     lightSidebar ? 'rgba(15,23,42,0.08)' : 'rgba(255,255,255,0.10)');
                        root.style.setProperty('--admin-sidebar-hover-bg',   lightSidebar ? 'rgba(15,23,42,0.06)' : 'rgba(255,255,255,0.10)');
                        root.style.setProperty('--admin-sidebar-active-bg',  lightSidebar ? primary : 'rgba(255,255,255,0.18)');
                        root.style.setProperty('--admin-sidebar-active-txt', '#ffffff');
                    }
                    // Update real sidebar border/shadow for light vs dark
                    const realSidebar = document.querySelector('.tenant-ui-mock .sidebar--edu-mock');
                    if (realSidebar) {
                        realSidebar.style.borderRight = lightSidebar ? '1px solid rgba(15,23,42,0.10)' : 'none';
                        realSidebar.style.boxShadow   = lightSidebar ? '2px 0 8px rgba(15,23,42,0.06)' : '4px 0 28px rgba(0,0,0,0.18)';
                    }

                    // Preview mini-card
                    const previewSidebar = document.querySelector('.appearance-preview-sidebar');
                    if (previewSidebar) {
                        previewSidebar.style.backgroundColor = sidebar;
                        previewSidebar.style.borderRight = lightSidebar ? '1px solid rgba(15,23,42,0.10)' : 'none';
                    }

                    // Active nav item
                    const activeNav = document.querySelector('.appearance-preview-nav-item--active');
                    if (activeNav) {
                        if (lightSidebar) {
                            activeNav.style.background = primary;
                            activeNav.style.color = '#ffffff';
                        } else {
                            activeNav.style.background = 'rgba(255,255,255,0.20)';
                            activeNav.style.color = '#ffffff';
                        }
                    }
                    // Inactive nav items
                    document.querySelectorAll('.appearance-preview-nav-item:not(.appearance-preview-nav-item--active)')
                        .forEach(el => { el.style.color = lightSidebar ? '#475569' : 'rgba(255,255,255,0.72)'; });

                    // Primary button + avatar
                    const primaryBtn = document.querySelector('.appearance-preview-btn--primary');
                    if (primaryBtn) primaryBtn.style.backgroundColor = primary;

                    const previewAvatar = document.querySelector('.appearance-preview-avatar');
                    if (previewAvatar) previewAvatar.style.backgroundColor = primary;

                    const focusedInput = document.querySelector('.appearance-preview-input--focused');
                    if (focusedInput) focusedInput.style.boxShadow = '0 0 0 2px ' + accent + '55';
                };

                syncPair('primary-color-input', 'primary-color-hex', 'primary-color-rgb');
                syncPair('sidebar-color-input', 'sidebar-color-hex', 'sidebar-color-rgb');
                syncPair('accent-color-input', 'accent-color-hex', 'accent-color-rgb');

                // Apply on load
                applyPreviewColors();

                // Preset helper: when preset changes, update inputs but not save yet
                const presetSelect = document.getElementById('appearance-theme-preset');
                if (presetSelect) {
                    presetSelect.addEventListener('change', () => {
                        const preset = presetSelect.value;
                        const map = {
                            default: { primary: '#334155', sidebar: '#f8fafc', accent: '#475569' },
                            green:   { primary: '#16a34a', sidebar: '#065f46', accent: '#22c55e' },
                            blue:    { primary: '#2563eb', sidebar: '#1d4ed8', accent: '#0ea5e9' },
                            purple:  { primary: '#7c3aed', sidebar: '#4c1d95', accent: '#a855f7' },
                            dark:    { primary: '#22c55e', sidebar: '#020617', accent: '#38bdf8' },
                        };
                        const cfg = map[preset] || map.default;
                        const primaryHex = document.getElementById('primary-color-hex');
                        const sidebarHex = document.getElementById('sidebar-color-hex');
                        const accentHex = document.getElementById('accent-color-hex');
                        if (primaryHex) primaryHex.value = cfg.primary;
                        if (sidebarHex) sidebarHex.value = cfg.sidebar;
                        if (accentHex) accentHex.value = cfg.accent;

                        const primaryColor = document.getElementById('primary-color-input');
                        const sidebarColor = document.getElementById('sidebar-color-input');
                        const accentColor = document.getElementById('accent-color-input');
                        if (primaryColor) primaryColor.value = cfg.primary;
                        if (sidebarColor) sidebarColor.value = cfg.sidebar;
                        if (accentColor) accentColor.value = cfg.accent;

                        // Refresh RGB labels + preview
                        syncPair('primary-color-input', 'primary-color-hex', 'primary-color-rgb');
                        syncPair('sidebar-color-input', 'sidebar-color-hex', 'sidebar-color-rgb');
                        syncPair('accent-color-input', 'accent-color-hex', 'accent-color-rgb');
                        applyPreviewColors();
                    });
                }
            })();
            </script>
            @endpush
        </main>
    </div>
</div>
@endsection
