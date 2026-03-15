<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EduPlatform')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&family=plus-jakarta-sans:600,700" rel="stylesheet">
    <style>
        /* ─── Design Tokens ─────────────────────────────────────────── */
        :root {
            --bg:          #f4f7fe;
            --bg-2:        #edf1fb;
            --surface:     #ffffff;
            --surface-2:   #f8faff;
            --border:      #e2e8f8;
            --border-2:    #c8d5f0;
            --ink:         #0f1f3d;
            --ink-2:       #3a4f72;
            --muted:       #6b7fa8;
            --accent:      #2563eb;
            --accent-h:    #1d4ed8;
            --accent-l:    #eff6ff;
            --accent-l2:   #dbeafe;
            --green:       #16a34a;
            --green-l:     #f0fdf4;
            --green-l2:    #bbf7d0;
            --red:         #dc2626;
            --red-l:       #fef2f2;
            --red-l2:      #fecaca;
            --amber:       #d97706;
            --amber-l:     #fffbeb;
            --amber-l2:    #fde68a;
            --purple:      #7c3aed;
            --purple-l:    #f5f3ff;
            --purple-l2:   #ddd6fe;
            --shadow-sm:   0 1px 3px rgba(15,31,61,.06), 0 1px 2px rgba(15,31,61,.04);
            --shadow:      0 4px 16px rgba(15,31,61,.08), 0 1px 4px rgba(15,31,61,.04);
            --shadow-lg:   0 12px 40px rgba(15,31,61,.12), 0 4px 12px rgba(15,31,61,.06);
            --radius-sm:   6px;
            --radius:      10px;
            --radius-lg:   16px;
            --radius-xl:   20px;
            --sidebar-w:   240px;
            --topbar-h:    60px;
        }

        /* ─── Reset & Base ──────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--ink);
            background: var(--bg);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Scrollbar ─────────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--muted); }

        /* ─── Typography ────────────────────────────────────────────── */
        h1, h2, h3, h4 {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            color: var(--ink);
            line-height: 1.3;
        }
        h1 { font-size: 1.5rem; font-weight: 700; }
        h2 { font-size: 1.1rem; font-weight: 700; }
        h3 { font-size: 0.95rem; font-weight: 600; }
        p  { color: var(--ink-2); }

        a { color: var(--accent); text-decoration: none; }
        a:hover { text-decoration: underline; }

        /* ─── Layout Shell ──────────────────────────────────────────── */
        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ─── Sidebar ───────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            box-shadow: var(--shadow-sm);
            transition: transform .25s ease;
        }

        .sidebar-brand {
            padding: 20px 18px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-brand-icon {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-brand-icon svg { color: #fff; }

        .sidebar-brand-text {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.2;
        }

        .sidebar-brand-sub {
            font-size: 0.7rem;
            color: var(--muted);
            font-weight: 400;
        }

        .sidebar-brand-role {
            margin-top: 6px;
        }

        .sidebar-brand-role-pill {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.6rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 700;
            background: var(--accent-l2);
            color: var(--accent);
            border: 1px solid #bfdbfe;
        }

        .sidebar-nav {
            flex: 1;
            padding: 12px 10px;
            overflow-y: auto;
        }

        .sidebar-section-label {
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            padding: 8px 8px 4px;
            margin-top: 4px;
        }

        .sidebar-link {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: var(--radius);
            color: var(--ink-2);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
        }

        .sidebar-link svg {
            width: 18px; height: 18px;
            flex-shrink: 0;
            opacity: .7;
            transition: opacity .15s;
        }

        .sidebar-link:hover {
            background: var(--accent-l);
            color: var(--accent);
            text-decoration: none;
        }

        .sidebar-link:hover svg { opacity: 1; }

        .sidebar-link.active {
            background: var(--accent-l2);
            color: var(--accent);
            font-weight: 600;
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 6px;
            bottom: 6px;
            width: 3px;
            border-radius: 999px;
            background: var(--accent);
        }

        .sidebar-link.active svg { opacity: 1; }

        /* ─── Finance Submenu ───────────────────────────────────────── */
        .sidebar-submenu-toggle {
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            border-radius: var(--radius);
            color: var(--ink-2);
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            width: 100%;
            background: none;
            border: none;
            text-align: left;
            transition: background .15s, color .15s;
            margin-bottom: 2px;
            user-select: none;
        }

        .sidebar-submenu-toggle svg.icon-main {
            width: 18px; height: 18px;
            flex-shrink: 0;
            opacity: .7;
            transition: opacity .15s;
        }

        .sidebar-submenu-toggle:hover {
            background: var(--accent-l);
            color: var(--accent);
        }

        .sidebar-submenu-toggle:hover svg { opacity: 1; }

        .sidebar-submenu-toggle.open,
        .sidebar-submenu-toggle.has-active {
            color: var(--accent);
        }

        .sidebar-submenu-toggle.has-active svg.icon-main { opacity: 1; }

        .sidebar-submenu-chevron {
            width: 14px; height: 14px;
            margin-left: auto;
            flex-shrink: 0;
            opacity: .5;
            transition: transform .2s, opacity .15s;
        }

        .sidebar-submenu-toggle.open .sidebar-submenu-chevron {
            transform: rotate(180deg);
            opacity: 1;
        }

        .sidebar-submenu {
            overflow: hidden;
            max-height: 0;
            transition: max-height .25s ease;
        }

        .sidebar-submenu.open {
            max-height: 400px;
        }

        .sidebar-submenu-inner {
            padding: 2px 0 4px 28px;
            display: flex;
            flex-direction: column;
            gap: 1px;
        }

        .sidebar-sublink {
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-radius: var(--radius);
            color: var(--muted);
            font-size: 0.825rem;
            font-weight: 500;
            text-decoration: none;
            transition: background .15s, color .15s;
        }

        .sidebar-sublink::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
            background: currentColor;
            opacity: .4;
            flex-shrink: 0;
            transition: opacity .15s;
        }

        .sidebar-sublink:hover {
            background: var(--accent-l);
            color: var(--accent);
            text-decoration: none;
        }

        .sidebar-sublink:hover::before { opacity: 1; }

        .sidebar-sublink.active {
            background: var(--accent-l2);
            color: var(--accent);
            font-weight: 600;
        }

        .sidebar-sublink.active::before {
            opacity: 1;
            background: var(--accent);
        }

        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid var(--border);
        }

        /* ─── Main Content ──────────────────────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
            max-width: 100%;
        }

        /* ─── Topbar ────────────────────────────────────────────────── */
        .topbar {
            height: var(--topbar-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: var(--shadow-sm);
            overflow-x: hidden;
            max-width: 100%;
            box-sizing: border-box;
        }

        .topbar-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--ink);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            color: var(--ink-2);
        }

        .avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* ─── Page Body ─────────────────────────────────────────────── */
        .page-body {
            padding: 24px;
            flex: 1;
            overflow-x: hidden;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* ─── Page Header ───────────────────────────────────────────── */
        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 1.4rem;
        }

        .page-header p {
            margin-top: 4px;
            font-size: 0.875rem;
            color: var(--muted);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .breadcrumb a { color: var(--muted); }
        .breadcrumb a:hover { color: var(--accent); text-decoration: none; }
        .breadcrumb-sep { opacity: .4; }

        /* ─── Full-width page (no sidebar) ──────────────────────────── */
        .page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 20px 40px;
        }

        /* ─── Cards ─────────────────────────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            transition: box-shadow .2s;
        }

        .card:hover { box-shadow: var(--shadow); }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--border);
        }

        .card-header h2, .card-header h3 {
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-icon {
            width: 32px; height: 32px;
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .card-icon.blue   { background: var(--accent-l2); color: var(--accent); }
        .card-icon.green  { background: var(--green-l2);  color: var(--green); }
        .card-icon.red    { background: var(--red-l2);    color: var(--red); }
        .card-icon.amber  { background: var(--amber-l2);  color: var(--amber); }
        .card-icon.purple { background: var(--purple-l2); color: var(--purple); }

        /* ─── Stat Cards ─────────────────────────────────────────────── */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
            box-shadow: var(--shadow-sm);
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon.blue   { background: var(--accent-l2); color: var(--accent); }
        .stat-icon.green  { background: var(--green-l2);  color: var(--green); }
        .stat-icon.amber  { background: var(--amber-l2);  color: var(--amber); }
        .stat-icon.purple { background: var(--purple-l2); color: var(--purple); }

        .stat-body { flex: 1; }
        .stat-value { font-size: 1.6rem; font-weight: 700; color: var(--ink); line-height: 1; }
        .stat-label { font-size: 0.8rem; color: var(--muted); margin-top: 4px; }

        /* ─── Grid ───────────────────────────────────────────────────── */
        .grid { display: grid; gap: 16px; }
        .grid.cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid.cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid.cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }

        /* ─── Stack ──────────────────────────────────────────────────── */
        .stack { display: flex; flex-direction: column; gap: 12px; }

        /* ─── Inline ─────────────────────────────────────────────────── */
        .inline { display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end; }
        .inline > * { flex: 1 1 160px; }

        /* ─── Toolbar ────────────────────────────────────────────────── */
        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        /* ─── Alerts ─────────────────────────────────────────────────── */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            border-radius: var(--radius);
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .alert svg { width: 18px; height: 18px; flex-shrink: 0; margin-top: 1px; }

        .alert.success {
            background: var(--green-l);
            border: 1px solid var(--green-l2);
            color: #14532d;
        }

        .alert.error {
            background: var(--red-l);
            border: 1px solid var(--red-l2);
            color: #7f1d1d;
        }

        .alert.warning {
            background: var(--amber-l);
            border: 1px solid var(--amber-l2);
            color: #78350f;
        }

        .alert.info {
            background: var(--accent-l);
            border: 1px solid var(--accent-l2);
            color: #1e3a8a;
        }

        /* ─── Forms ──────────────────────────────────────────────────── */
        .form-group { display: flex; flex-direction: column; gap: 4px; }

        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--ink-2);
            letter-spacing: .01em;
        }

        input, select, textarea {
            width: 100%;
            border: 1.5px solid var(--border-2);
            border-radius: var(--radius);
            padding: 9px 12px;
            font: inherit;
            font-size: 0.875rem;
            color: var(--ink);
            background: var(--surface);
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }

        input::placeholder, textarea::placeholder { color: var(--muted); opacity: .7; }

        textarea { min-height: 80px; resize: vertical; }

        .input-hint {
            font-size: 0.75rem;
            color: var(--muted);
        }

        /* ─── Buttons ────────────────────────────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: none;
            border-radius: var(--radius);
            padding: 9px 16px;
            font: inherit;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s, box-shadow .15s, transform .1s, opacity .15s;
            white-space: nowrap;
            line-height: 1;
        }

        .btn:hover { text-decoration: none; transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; }

        .btn svg { width: 16px; height: 16px; flex-shrink: 0; }

        .btn.primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 2px 8px rgba(37,99,235,.3);
        }
        .btn.primary:hover { background: var(--accent-h); box-shadow: 0 4px 14px rgba(37,99,235,.4); }

        .btn.secondary {
            background: var(--surface);
            color: var(--ink-2);
            border: 1.5px solid var(--border-2);
            box-shadow: var(--shadow-sm);
        }
        .btn.secondary:hover { background: var(--bg-2); border-color: var(--accent); color: var(--accent); }

        .btn.success {
            background: var(--green);
            color: #fff;
            box-shadow: 0 2px 8px rgba(22,163,74,.3);
        }
        .btn.success:hover { background: #15803d; box-shadow: 0 4px 14px rgba(22,163,74,.4); }

        .btn.danger {
            background: var(--red);
            color: #fff;
            box-shadow: 0 2px 8px rgba(220,38,38,.3);
        }
        .btn.danger:hover { background: #b91c1c; box-shadow: 0 4px 14px rgba(220,38,38,.4); }

        .btn.warning {
            background: var(--amber);
            color: #fff;
            box-shadow: 0 2px 8px rgba(217,119,6,.3);
        }
        .btn.warning:hover { background: #b45309; }

        .btn.ghost {
            background: transparent;
            color: var(--ink-2);
            border: 1.5px solid transparent;
        }
        .btn.ghost:hover { background: var(--bg-2); border-color: var(--border); }

        .btn.sm { padding: 6px 12px; font-size: 0.8rem; }
        .btn.lg { padding: 12px 22px; font-size: 0.95rem; }
        .btn.full { width: 100%; }

        /* ─── Tables ─────────────────────────────────────────────────── */
        .table-wrap {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        thead { background: var(--surface-2); }

        th {
            text-align: left;
            padding: 10px 14px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            color: var(--ink-2);
        }

        tbody tr:last-child td { border-bottom: none; }

        tbody tr {
            transition: background .12s;
        }

        tbody tr:hover { background: var(--surface-2); }

        .table-empty {
            text-align: center;
            padding: 32px 16px;
            color: var(--muted);
        }

        .table-empty svg {
            width: 40px; height: 40px;
            margin: 0 auto 10px;
            opacity: .3;
            display: block;
        }

        /* ─── Badges ─────────────────────────────────────────────────── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: 3px 9px;
            background: var(--bg-2);
            color: var(--ink-2);
            border: 1px solid var(--border);
        }

        .badge.blue   { background: var(--accent-l2); color: var(--accent);  border-color: #bfdbfe; }
        .badge.green  { background: var(--green-l2);  color: var(--green);   border-color: #86efac; }
        .badge.red    { background: var(--red-l2);    color: var(--red);     border-color: #fca5a5; }
        .badge.amber  { background: var(--amber-l2);  color: var(--amber);   border-color: #fcd34d; }
        .badge.purple { background: var(--purple-l2); color: var(--purple);  border-color: #c4b5fd; }

        /* ─── Auth Layout ────────────────────────────────────────────── */
        .auth-page {
            min-height: 100vh;
            display: flex;
            background: var(--bg);
        }

        .auth-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 36px 32px;
            box-shadow: var(--shadow-lg);
        }

        .auth-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }

        .auth-brand-icon {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            border-radius: var(--radius);
            display: flex; align-items: center; justify-content: center;
        }

        .auth-brand-icon svg { color: #fff; }

        .auth-brand-name {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--ink);
        }

        .auth-heading { margin-bottom: 6px; }
        .auth-sub { font-size: 0.875rem; color: var(--muted); margin-bottom: 24px; }

        .auth-illustration {
            flex: 1;
            background: linear-gradient(135deg, #1e3a8a 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }

        .auth-illustration::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .auth-illustration-content {
            position: relative;
            text-align: center;
            color: #fff;
        }

        .auth-illustration-content h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 12px;
        }

        .auth-illustration-content p {
            color: rgba(255,255,255,.75);
            font-size: 1rem;
            max-width: 320px;
        }

        /* ─── Password Toggle ────────────────────────────────────────── */
        .input-wrap {
            position: relative;
        }

        .input-wrap input { padding-right: 40px; }

        .input-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--muted);
            padding: 4px;
            display: flex;
            align-items: center;
            transition: color .15s;
        }

        .input-toggle:hover { color: var(--ink); }
        .input-toggle svg { width: 18px; height: 18px; }

        /* ─── Divider ────────────────────────────────────────────────── */
        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--muted);
            font-size: 0.8rem;
            margin: 8px 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ─── Section Heading ────────────────────────────────────────── */
        .section-heading {
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }

        /* ─── Tabs ───────────────────────────────────────────────────── */
        .tabs {
            display: flex;
            gap: 2px;
            border-bottom: 2px solid var(--border);
            margin-bottom: 20px;
        }

        .tab-btn {
            padding: 10px 16px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--muted);
            background: none;
            border: none;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: color .15s, border-color .15s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tab-btn:hover { color: var(--ink); }

        .tab-btn.active {
            color: var(--accent);
            border-bottom-color: var(--accent);
        }

        .tab-panel { display: none; }
        .tab-panel.active { display: block; }

        /* ─── Pipeline Steps ─────────────────────────────────────────── */
        .pipeline {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 24px;
            overflow-x: auto;
            padding-bottom: 4px;
        }

        .pipeline-step {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .pipeline-dot {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            background: var(--bg-2);
            color: var(--muted);
            border: 2px solid var(--border);
            flex-shrink: 0;
        }

        .pipeline-dot.done  { background: var(--green-l2);  color: var(--green);  border-color: var(--green); }
        .pipeline-dot.active{ background: var(--accent-l2); color: var(--accent); border-color: var(--accent); }

        .pipeline-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--muted);
        }

        .pipeline-label.active { color: var(--accent); }
        .pipeline-label.done   { color: var(--green); }

        .pipeline-arrow {
            width: 32px;
            height: 2px;
            background: var(--border);
            flex-shrink: 0;
            margin: 0 4px;
        }

        .pipeline-arrow.done { background: var(--green); }

        /* ─── Empty State ────────────────────────────────────────────── */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--muted);
        }

        .empty-state svg {
            width: 48px; height: 48px;
            margin: 0 auto 12px;
            opacity: .25;
            display: block;
        }

        .empty-state p { font-size: 0.875rem; }

        /* ─── Responsive ─────────────────────────────────────────────── */
        @media (max-width: 1024px) {
            .grid.cols-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .grid.cols-2,
            .grid.cols-3,
            .grid.cols-4 { grid-template-columns: 1fr; }
            .auth-illustration { display: none; }
            .page-body { padding: 16px; }
            .auth-card { padding: 24px 20px; }
        }

        /* ─── Utility ────────────────────────────────────────────────── */
        .text-muted  { color: var(--muted); }
        .text-sm     { font-size: 0.8rem; }
        .text-xs     { font-size: 0.72rem; }
        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .fw-600      { font-weight: 600; }
        .fw-700      { font-weight: 700; }
        .mt-0  { margin-top: 0; }
        .mt-4  { margin-top: 4px; }
        .mt-8  { margin-top: 8px; }
        .mt-12 { margin-top: 12px; }
        .mt-16 { margin-top: 16px; }
        .mt-20 { margin-top: 20px; }
        .mb-0  { margin-bottom: 0; }
        .mb-4  { margin-bottom: 4px; }
        .mb-8  { margin-bottom: 8px; }
        .mb-12 { margin-bottom: 12px; }
        .mb-16 { margin-bottom: 16px; }
        .mb-20 { margin-bottom: 20px; }
        .gap-8  { gap: 8px; }
        .gap-12 { gap: 12px; }
        .flex   { display: flex; }
        .flex-wrap { flex-wrap: wrap; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .w-full { width: 100%; }
        .hidden { display: none; }

        /* ─── Mobile overlay ─────────────────────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.4);
            z-index: 99;
        }

        .sidebar-overlay.open { display: block; }

        /* ─── Hamburger ──────────────────────────────────────────────── */
        .hamburger {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            color: var(--ink-2);
        }

        @media (max-width: 768px) {
            .hamburger { display: flex; align-items: center; }
        }
    </style>
    @stack('styles')
    @stack('head')
</head>
<body>
@yield('content')

<script>
    // Password visibility toggle
    document.querySelectorAll('.input-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.input-wrap').querySelector('input');
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            btn.querySelector('.eye-open').style.display  = isText ? 'block' : 'none';
            btn.querySelector('.eye-close').style.display = isText ? 'none'  : 'block';
        });
    });

    // Tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const group = btn.closest('[data-tabs]');
            const target = btn.dataset.tab;
            group.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            group.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            group.querySelector('[data-panel="' + target + '"]').classList.add('active');
        });
    });

    // Mobile sidebar
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    document.querySelectorAll('.hamburger').forEach(btn => {
        btn.addEventListener('click', () => {
            sidebar?.classList.toggle('open');
            overlay?.classList.toggle('open');
        });
    });
    overlay?.addEventListener('click', () => {
        sidebar?.classList.remove('open');
        overlay?.classList.remove('open');
    });

    // Finance submenu toggle
    document.querySelectorAll('.sidebar-submenu-toggle').forEach(toggle => {
        toggle.addEventListener('click', () => {
            const submenu = toggle.nextElementSibling;
            const isOpen = toggle.classList.toggle('open');
            submenu.classList.toggle('open', isOpen);
        });
    });

    // Auto-dismiss alerts after 6s
    setTimeout(() => {
        document.querySelectorAll('.alert.success').forEach(el => {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 6000);
</script>
@stack('scripts')
</body>
</html>
