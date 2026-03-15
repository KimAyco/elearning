<style>
    /* ── Finance Page Layout ──────────────────────────────────── */
    .finance-page { display:flex; flex-direction:column; gap:16px; }

    .finance-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .finance-title-block {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--ink);
    }

    .finance-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--ink);
        margin: 0;
    }

    .finance-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-finance-action {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: var(--radius);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        border: 1.5px solid transparent;
        transition: all .15s;
        white-space: nowrap;
    }

    .btn-finance-action.primary {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
    }
    .btn-finance-action.primary:hover { background: var(--accent-h); border-color: var(--accent-h); }

    .btn-finance-action.secondary {
        background: var(--surface);
        color: var(--ink-2);
        border-color: var(--border-2);
    }
    .btn-finance-action.secondary:hover { background: var(--bg-2); color: var(--ink); }

    .btn-finance-action.teal {
        background: #0d9488;
        color: #fff;
        border-color: #0d9488;
    }
    .btn-finance-action.teal:hover { background: #0f766e; border-color: #0f766e; }

    /* ── Tabs ─────────────────────────────────────────────────── */
    .finance-tabs {
        display: flex;
        gap: 4px;
        background: var(--bg-2);
        padding: 4px;
        border-radius: var(--radius);
        width: fit-content;
    }

    .finance-tab {
        padding: 8px 16px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--muted);
        background: none;
        border: none;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: all .15s;
        white-space: nowrap;
    }
    .finance-tab:hover { color: var(--ink); background: var(--surface); }
    .finance-tab.active {
        color: var(--accent);
        background: var(--surface);
        box-shadow: var(--shadow-sm);
    }

    /* ── Search Bar ───────────────────────────────────────────── */
    .finance-search-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        padding: 8px 12px;
        color: var(--muted);
        transition: border-color .15s;
    }
    .finance-search-bar:focus-within { border-color: var(--accent); color: var(--ink); }
    .finance-search-bar input {
        border: none;
        outline: none;
        background: none;
        font-size: 0.875rem;
        color: var(--ink);
        width: 100%;
    }
    .finance-search-bar input::placeholder { color: var(--muted); }

    /* ── Filters ──────────────────────────────────────────────── */
    .finance-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }
    .finance-filter-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .finance-filter-item label {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .finance-filter-item select,
    .finance-filter-item input[type="date"] {
        padding: 7px 10px;
        font-size: 0.82rem;
        border: 1.5px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--surface);
        min-width: 150px;
    }

    /* ── Table tweaks ─────────────────────────────────────────── */
    .th-sort {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        user-select: none;
    }
    .th-sort:hover { color: var(--accent); }

    .finance-name-cell { font-weight: 500; color: var(--ink); }

    .btn-edit-rule {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        background: var(--accent-l);
        color: var(--accent);
        border: 1px solid var(--accent-l2);
        border-radius: var(--radius-sm);
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }
    .btn-edit-rule:hover { background: var(--accent-l2); }

    /* ── Summary ──────────────────────────────────────────────── */
    .finance-summary {
        display: flex;
        gap: 16px;
        color: var(--muted);
        font-size: 0.8rem;
        padding: 4px 0;
    }

    /* ── Modals ───────────────────────────────────────────────── */
    .modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,31,61,.45);
        z-index: 200;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .modal-backdrop.open { display: flex; }

    .modal-box {
        background: var(--surface);
        border-radius: var(--radius-lg);
        width: 100%;
        max-width: 520px;
        box-shadow: var(--shadow-lg);
        overflow: hidden;
        animation: modal-in .18s ease;
    }
    @keyframes modal-in {
        from { opacity:0; transform:translateY(-12px) scale(.97); }
        to   { opacity:1; transform:none; }
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 20px 14px;
        border-bottom: 1px solid var(--border);
    }
    .modal-header h3 { font-size: 1rem; font-weight: 700; color: var(--ink); }
    .modal-close {
        background: none;
        border: none;
        color: var(--muted);
        cursor: pointer;
        padding: 4px;
        border-radius: var(--radius-sm);
        transition: color .15s, background .15s;
    }
    .modal-close:hover { background: var(--bg-2); color: var(--ink); }

    .modal-body { padding: 20px; }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 20px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }

    /* ── Form helpers ─────────────────────────────────────────── */
    .form-group { display: flex; flex-direction: column; gap: 5px; margin-bottom: 12px; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .form-label { font-size: 0.78rem; font-weight: 600; color: var(--ink-2); }
    .req { color: var(--red); }
</style>
