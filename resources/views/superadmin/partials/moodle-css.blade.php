    .sa-dash.sa-moodle {
        max-width: 1180px;
        margin: 0 auto;
    }

    .sa-moodle-region {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        position: relative;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .sa-moodle-region::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #c55a2e, #7c3aed);
        opacity: 0.85;
    }

    .sa-moodle-region--hero {
        padding: 20px 22px 18px 30px;
        margin-bottom: 18px;
    }

    .sa-moodle-region--hero::before {
        background: linear-gradient(180deg, #2563eb, #7c3aed);
    }

    .sa-moodle-region--approvals::before {
        background: linear-gradient(180deg, #f59e0b, #c55a2e);
    }

    .sa-moodle-region--footer::before {
        background: linear-gradient(180deg, #64748b, #94a3b8);
    }

    .sa-moodle-region-hd {
        padding: 0 0 10px;
    }

    .sa-moodle-region-hd--row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding: 18px 22px 12px 26px;
    }

    .sa-moodle-region-hd--row .sa-moodle-region-title {
        margin-bottom: 2px;
    }

    .sa-moodle-region-hd--compact {
        padding-bottom: 6px;
    }

    .sa-moodle-title {
        margin: 0 0 8px;
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--ink);
    }

    .sa-moodle-summary {
        margin: 0;
        font-size: 0.92rem;
        color: var(--ink-2);
        line-height: 1.55;
        max-width: 720px;
    }

    .sa-moodle-breadcrumb {
        margin-top: 0;
        margin-bottom: 0;
        padding-top: 12px;
    }

    .sa-moodle-region-title {
        margin: 0 0 4px;
        font-size: 1rem;
        font-weight: 700;
        color: var(--ink);
        text-decoration: none;
    }

    .sa-moodle-region-desc {
        margin: 0;
        font-size: 0.84rem;
        color: var(--ink-2);
        line-height: 1.5;
        text-decoration: none;
    }

    .sa-moodle-region-hd .sa-moodle-region-title + .sa-moodle-region-desc {
        margin-top: 6px;
    }

    .sa-moodle-pill-strip {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .sa-moodle-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        border: 1px solid var(--border);
        background: var(--bg-2);
        color: var(--ink-2);
    }

    .sa-moodle-pill--amber {
        background: rgba(245, 158, 11, 0.12);
        border-color: rgba(245, 158, 11, 0.35);
        color: #b45309;
    }

    .sa-moodle-pill--green {
        background: rgba(34, 197, 94, 0.12);
        border-color: rgba(34, 197, 94, 0.35);
        color: #15803d;
    }

    .sa-moodle-pill--slate {
        background: rgba(100, 116, 139, 0.1);
        border-color: rgba(100, 116, 139, 0.25);
        color: #475569;
    }

    .sa-moodle-pill--blue {
        background: rgba(37, 99, 235, 0.1);
        border-color: rgba(37, 99, 235, 0.35);
        color: #1d4ed8;
    }

    .sa-moodle-tiles {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    @media (max-width: 960px) {
        .sa-moodle-tiles { grid-template-columns: 1fr; }
    }

    .sa-moodle-tile {
        display: flex;
        gap: 20px;
        padding: 16px 18px 16px 20px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        background: var(--surface);
        box-shadow: var(--shadow-sm);
        text-decoration: none;
        color: inherit;
        transition: box-shadow 0.15s ease, border-color 0.15s ease, transform 0.12s ease;
        position: relative;
        overflow: hidden;
    }

    .sa-moodle-tile:hover,
    .sa-moodle-tile:focus,
    .sa-moodle-tile:active {
        text-decoration: none;
    }

    .sa-moodle-tile .sa-moodle-tile-name,
    .sa-moodle-tile .sa-moodle-tile-text,
    .sa-moodle-tile .sa-moodle-tile-meta {
        text-decoration: none;
    }

    .sa-moodle-tile::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
    }

    .sa-moodle-tile--amber::before { background: linear-gradient(180deg, #f59e0b, #d97706); }
    .sa-moodle-tile--purple::before { background: linear-gradient(180deg, #a855f7, #7c3aed); }
    .sa-moodle-tile--blue::before { background: linear-gradient(180deg, #3b82f6, #2563eb); }

    .sa-moodle-tile:hover {
        border-color: var(--accent-l2);
        box-shadow: var(--shadow);
        transform: translateY(-1px);
    }

    .sa-moodle-tile-ic {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: #fff;
    }

    .sa-moodle-tile--amber .sa-moodle-tile-ic { background: linear-gradient(145deg, #f59e0b, #b45309); }
    .sa-moodle-tile--purple .sa-moodle-tile-ic { background: linear-gradient(145deg, #a855f7, #6d28d9); }
    .sa-moodle-tile--blue .sa-moodle-tile-ic { background: linear-gradient(145deg, #3b82f6, #1d4ed8); }

    .sa-moodle-tile-body {
        min-width: 0;
        padding-left: 2px;
    }

    .sa-moodle-tile-name {
        margin: 0 0 4px;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--ink);
        text-decoration: none;
    }

    .sa-moodle-tile-text {
        margin: 0 0 8px;
        font-size: 0.82rem;
        color: var(--ink-2);
        line-height: 1.45;
        text-decoration: none;
    }

    .sa-moodle-tile-meta {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: var(--accent);
    }

    .sa-moodle-tile-meta--muted {
        color: var(--muted);
        font-weight: 600;
    }

    .sa-moodle-kpi {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0;
        padding: 0 22px 18px 30px;
    }

    .sa-moodle-kpi--3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    @media (max-width: 768px) {
        .sa-moodle-kpi { grid-template-columns: repeat(2, 1fr); }
        .sa-moodle-kpi--3 { grid-template-columns: 1fr; }
        .sa-moodle-kpi--3 .sa-moodle-kpi-item {
            border-right: none;
            border-bottom: 1px solid var(--border);
        }
        .sa-moodle-kpi--3 .sa-moodle-kpi-item:last-child {
            border-bottom: none;
        }
    }

    .sa-moodle-kpi-item {
        padding: 14px 16px;
        border-right: 1px solid var(--border);
        text-align: center;
    }

    .sa-moodle-kpi-item:last-child {
        border-right: none;
    }

    @media (max-width: 768px) {
        .sa-moodle-kpi .sa-moodle-kpi-item:nth-child(2) { border-right: none; }
        .sa-moodle-kpi .sa-moodle-kpi-item:nth-child(1),
        .sa-moodle-kpi .sa-moodle-kpi-item:nth-child(2) { border-bottom: 1px solid var(--border); }
    }

    .sa-moodle-kpi-value {
        display: block;
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--ink);
        letter-spacing: -0.02em;
    }

    .sa-moodle-kpi-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .sa-moodle-charts {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
        align-items: stretch;
    }

    @media (max-width: 960px) {
        .sa-moodle-charts { grid-template-columns: 1fr; }
    }

    .sa-moodle-chart-card {
        padding: 18px 22px 18px 30px;
        margin-bottom: 0;
        display: flex;
        flex-direction: column;
    }

    .sa-moodle-chart-card .sa-moodle-region-hd {
        padding-right: 0;
    }

    .sa-chart-canvas-wrap {
        position: relative;
        flex: 1;
        min-height: 260px;
        padding: 8px 0 4px;
    }

    .sa-moodle-region--footer {
        padding: 16px 22px 18px 30px;
        margin-bottom: 0;
    }

    .sa-moodle-footnote {
        margin: 0;
        font-size: 0.88rem;
        color: var(--ink-2);
        line-height: 1.6;
    }

    .sa-approval-table-wrap {
        padding: 0 0 18px;
        overflow-x: auto;
    }

    .sa-approval-table-wrap table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .sa-approval-table-wrap thead th {
        text-align: left;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
        padding: 12px 16px;
        background: linear-gradient(180deg, #f8fafc, #f1f5f9);
        border-bottom: 1px solid var(--border);
    }

    .sa-approval-table-wrap thead th:first-child {
        padding-left: 30px;
        border-radius: 0;
    }

    .sa-approval-table-wrap tbody td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--border);
        font-size: 0.88rem;
    }

    .sa-approval-table-wrap tbody td:first-child {
        padding-left: 30px;
    }

    .sa-approval-table-wrap tbody tr:last-child td {
        border-bottom: none;
    }

    .sa-approval-table-wrap tbody tr:hover td {
        background: rgba(37, 99, 235, 0.04);
    }

    .sa-approval-school {
        font-weight: 700;
        color: var(--ink);
    }

    .sa-approval-email {
        font-size: 0.84rem;
        color: var(--ink-2);
    }

    .sa-approval-id {
        font-size: 0.78rem;
        color: var(--muted);
        font-variant-numeric: tabular-nums;
    }

    .sa-approval-actions .btn {
        white-space: nowrap;
    }

    .sa-approval-empty {
        padding: 36px 30px 40px;
        text-align: center;
    }

    .sa-approval-empty svg {
        width: 48px;
        height: 48px;
        margin: 0 auto 12px;
        color: var(--border-2);
        opacity: 0.9;
    }

    .sa-approval-empty p {
        margin: 0;
        color: var(--muted);
        font-size: 0.92rem;
    }

    .sa-pricing-forms-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 20px;
        align-items: stretch;
    }

    @media (max-width: 960px) {
        .sa-pricing-forms-grid { grid-template-columns: 1fr; }
    }

    .sa-moodle-region--pricing-rate::before {
        background: linear-gradient(180deg, #7c3aed, #a855f7);
    }

    .sa-moodle-region--pricing-add::before {
        background: linear-gradient(180deg, #059669, #10b981);
    }

    .sa-moodle-region--pricing-table::before {
        background: linear-gradient(180deg, #4f46e5, #6366f1);
    }

    .sa-moodle-region--pricing-rate .sa-moodle-region-hd,
    .sa-moodle-region--pricing-add .sa-moodle-region-hd {
        padding: 18px 22px 8px 30px;
    }

    .sa-pricing-form-wrap {
        padding: 0 22px 22px 30px;
    }

    .sa-pricing-form .form-group {
        margin-bottom: 14px;
    }

    .sa-pricing-form .form-group:last-of-type {
        margin-bottom: 16px;
    }

    .sa-pricing-form label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--muted);
        margin-bottom: 6px;
    }

    .sa-pricing-form input[type="text"],
    .sa-pricing-form input[type="number"] {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid var(--border);
        background: var(--surface);
        font-size: 0.9rem;
        color: var(--ink);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .sa-pricing-form input:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .sa-pricing-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px 14px;
    }

    @media (max-width: 520px) {
        .sa-pricing-form-grid { grid-template-columns: 1fr; }
    }

    .sa-pricing-form .btn {
        border-radius: 10px;
        font-weight: 600;
    }

    .sa-pricing-details {
        position: relative;
    }

    .sa-pricing-details summary {
        list-style: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--accent);
        padding: 4px 0;
    }

    .sa-pricing-details summary::-webkit-details-marker {
        display: none;
    }

    .sa-pricing-details summary:hover {
        color: #1d4ed8;
        text-decoration: none;
    }

    .sa-pricing-details summary svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
        opacity: 0.85;
    }

    .sa-pricing-plan-edit {
        margin-top: 12px;
        padding: 14px 16px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        background: linear-gradient(180deg, #f8fafc, #fff);
    }

    .sa-pricing-edit-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 12px;
        margin-bottom: 12px;
    }

    @media (max-width: 640px) {
        .sa-pricing-edit-grid { grid-template-columns: 1fr; }
    }

    .sa-pricing-edit-grid input {
        width: 100%;
        box-sizing: border-box;
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid var(--border);
        font-size: 0.84rem;
    }

    .sa-pricing-plan-name {
        font-weight: 700;
        color: var(--ink);
    }

    .sa-pricing-mono {
        font-variant-numeric: tabular-nums;
        font-size: 0.88rem;
        color: var(--ink-2);
    }

    .sa-moodle-region--schools-table::before {
        background: linear-gradient(180deg, #2563eb, #6366f1);
    }

    .sa-school-identity {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .sa-school-avatar {
        width: 36px;
        height: 36px;
        flex-shrink: 0;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.85rem;
        color: #fff;
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
    }

    .sa-school-identity-text {
        min-width: 0;
    }

    .sa-school-name {
        font-weight: 700;
        font-size: 0.875rem;
        color: var(--ink);
        line-height: 1.3;
    }

    .sa-school-code-pill {
        display: inline-block;
        margin-top: 4px;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 3px 8px;
        border-radius: 999px;
        background: var(--bg-2);
        border: 1px solid var(--border);
        color: var(--muted);
    }

    .sa-school-desc {
        display: block;
        font-size: 0.82rem;
        color: var(--muted);
        line-height: 1.45;
        max-width: 280px;
    }

    .sa-school-suspended-note {
        font-size: 0.72rem;
        color: var(--muted);
        margin-top: 4px;
    }

    .sa-school-actions .btn {
        white-space: nowrap;
    }
