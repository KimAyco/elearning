@extends('layouts.app')

@section('title', 'Cashier - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'cashier', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body">

            @if(session('status'))
                <div
                    id="cashier-toast"
                    class="cashier-toast cashier-toast--success"
                    role="status"
                    aria-live="polite"
                    data-open="1"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                    <button type="button" class="cashier-toast-close" aria-label="Close notification">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Auto Approve Payment --}}
            <div class="card" style="padding:14px 16px; margin-bottom:14px;">
                <form id="auto-approve-form" method="post" action="{{ url('/tenant/cashier/settings/auto-approve') }}" style="display:flex; align-items:center; justify-content:space-between; gap:12px;">
                    @csrf
                    <div>
                        <div style="font-weight:700; color:var(--ink);">Auto Approve Payment</div>
                        <div style="color:var(--muted); font-size:0.85rem; margin-top:2px;">
                            When ON, student-submitted payments will be automatically verified.
                        </div>
                    </div>
                    <div class="cashier-toggle-wrap">
                        <input
                            id="auto-approve-toggle"
                            type="checkbox"
                            class="cashier-toggle-input"
                            {{ ($autoApprovePaymentsEnabled ?? false) ? 'checked' : '' }}
                            data-initial="{{ ($autoApprovePaymentsEnabled ?? false) ? '1' : '0' }}"
                            aria-label="Toggle auto approve payment"
                        >
                        <label for="auto-approve-toggle" class="cashier-toggle-ui" aria-hidden="true">
                            <span class="cashier-toggle-knob"></span>
                        </label>
                        <span id="auto-approve-state" class="badge {{ ($autoApprovePaymentsEnabled ?? false) ? 'green' : 'amber' }}">
                            {{ ($autoApprovePaymentsEnabled ?? false) ? 'ON' : 'OFF' }}
                        </span>
                        <input type="hidden" id="auto-approve-enabled" name="enabled" value="{{ ($autoApprovePaymentsEnabled ?? false) ? '1' : '0' }}">
                    </div>
                </form>
            </div>

            <div id="cashier-confirm-overlay" class="cashier-confirm-overlay" hidden>
                <div class="cashier-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="cashier-confirm-title" aria-describedby="cashier-confirm-text">
                    <h3 id="cashier-confirm-title">Confirm Action</h3>
                    <p id="cashier-confirm-text">Are you sure you want to turn on Auto Approve Payment?</p>
                    <div class="cashier-confirm-actions">
                        <button type="button" id="cashier-confirm-cancel" class="btn ghost">Cancel</button>
                        <button type="button" id="cashier-confirm-ok" class="btn primary">Turn On</button>
                    </div>
                </div>
            </div>

            {{-- Pending Payment Verifications (fragment updates via JS; keep id stable) --}}
            <div id="cashier-pending-root">
                @include('tenant.partials.cashier-pending-section')
            </div>

            {{-- Ready for Clearance (NEW: Moved from Billing) --}}
            @if(($forClearance ?? collect())->count() > 0)
            <div style="margin-bottom:18px;">
                <div class="section-divider" style="margin-bottom:12px;">
                    <span>Ready for Clearance</span>
                    <span class="badge green">{{ $forClearance->count() }}</span>
                </div>
                <div class="card" style="padding:0; overflow:hidden;">
                    <div class="table-wrap" style="margin:0;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Billing #</th>
                                    <th>Applicant / Student</th>
                                    <th>Payment Status</th>
                                    <th>Clearance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forClearance as $row)
                                    <tr>
                                        <td><span class="badge">#{{ $row->id }}</span></td>
                                        <td style="font-weight:600; color:var(--ink);">{{ $row->student->full_name ?? $row->student_user_id }}</td>
                                        <td><span class="badge">{{ $row->payment_status }}</span></td>
                                        <td><span class="badge">{{ $row->clearance_status }}</span></td>
                                        <td>
                                            <form method="post" action="{{ url('/tenant/billing/' . $row->id . '/clearance') }}">
                                                @csrf
                                                <button class="btn success sm" type="submit">Issue Clearance</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Confirmed Payments --}}
            <div id="cashier-confirmed-root">
                @include('tenant.partials.cashier-confirmed-section')
            </div>

        </main>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .cashier-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-end;
        margin-bottom: 16px;
    }

    .cashier-toolbar .form-group {
        min-width: 0;
        flex: 1 1 180px;
    }

    .cashier-toolbar-actions {
        flex: 0 0 auto;
    }

    .cashier-search-btn {
        min-width: 132px;
        justify-content: center;
        height: 40px;
        padding: 9px 14px;
        line-height: 1;
    }

    .cashier-search-btn svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    @media (max-width: 720px) {
        .cashier-toolbar-actions {
            width: auto;
            flex: 0 0 auto;
        }

        .cashier-search-btn {
            width: auto;
            min-width: 120px;
        }
    }

    .cashier-pagination {
        padding: 12px 16px 16px;
        border-top: 1px solid var(--border);
        background: #f9fafb;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Moodle-like numbered pager (no Tailwind dependency) */
    .cashier-pager {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .cashier-pager-inner {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 6px 10px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .cashier-pager-step {
        color: #64748b;
        text-decoration: none;
        padding: 4px 2px;
        border: none;
        background: none;
        cursor: pointer;
        font: inherit;
    }

    .cashier-pager-step:hover:not(.disabled) {
        color: #0f172a;
        text-decoration: underline;
    }

    .cashier-pager-step.disabled {
        color: #cbd5e1;
        cursor: default;
        text-decoration: none;
    }

    .cashier-pager-ellipsis {
        color: #94a3b8;
        padding: 0 4px;
        user-select: none;
    }

    .cashier-pager-page {
        min-width: 2rem;
        height: 2rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        border-radius: 6px;
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.15s, color 0.15s;
    }

    .cashier-pager-page:hover:not(.is-active) {
        background: rgba(14, 165, 233, 0.12);
        color: #0369a1;
    }

    .cashier-pager-page.is-active {
        background: #0ea5e9;
        color: #ffffff;
        box-shadow: 0 1px 3px rgba(14, 165, 233, 0.45);
        cursor: default;
    }

    .section-divider {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 12px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--muted);
    }
    .section-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }

    /* Cashier page: soften visual intensity */
    main.page-body > .card {
        border-radius: var(--radius-lg);
        background: rgba(255,255,255,0.92);
        border: 1px solid rgba(226, 232, 240, 0.95);
        box-shadow: var(--shadow-sm);
    }

    main.page-body table {
        border-collapse: separate;
        border-spacing: 0;
    }

    main.page-body table thead th {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        color: var(--muted);
        font-weight: 800;
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        border-bottom: 1px solid var(--border);
        padding-top: 12px;
        padding-bottom: 12px;
    }

    main.page-body table tbody tr:hover {
        background: rgba(79, 70, 229, 0.04);
    }

    /* Softer badge colors + keep consistent casing */
    main.page-body .badge {
        text-transform: none;
        font-weight: 900;
        border-color: rgba(226,232,240,0.95);
    }

    main.page-body .badge.green  { background: rgba(22,163,74,0.10); color: #15803d; }
    main.page-body .badge.amber  { background: rgba(180,83,9,0.10); color: #b45309; }
    main.page-body .badge.blue   { background: rgba(37,99,235,0.10); color: #2563eb; }
    main.page-body .badge.purple { background: rgba(109,40,217,0.10); color: #6d28d9; }

    .cashier-toggle-wrap {
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .cashier-toggle-input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .cashier-toggle-ui {
        position: relative;
        width: 52px;
        height: 30px;
        border-radius: 999px;
        background: #d1d5db;
        border: 1px solid #cbd5e1;
        cursor: pointer;
        transition: background .18s ease, border-color .18s ease;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.12);
    }

    .cashier-toggle-knob {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(15, 23, 42, 0.24);
        transition: transform .18s ease;
    }

    .cashier-toggle-input:checked + .cashier-toggle-ui {
        background: #22c55e;
        border-color: #16a34a;
    }

    .cashier-toggle-input:checked + .cashier-toggle-ui .cashier-toggle-knob {
        transform: translateX(22px);
    }

    .cashier-toast {
        position: fixed;
        top: 18px;
        left: 50%;
        transform: translate(-50%, -14px);
        min-width: 280px;
        max-width: min(92vw, 480px);
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 14px;
        border-radius: 12px;
        border: 1px solid rgba(34, 197, 94, 0.26);
        background: rgba(240, 253, 244, 0.98);
        color: #166534;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.14);
        z-index: 9999;
        opacity: 0;
        pointer-events: none;
        transition: opacity .24s ease, transform .24s ease;
    }

    .cashier-toast svg {
        width: 18px;
        height: 18px;
        flex: 0 0 auto;
    }

    .cashier-toast-close {
        margin-left: auto;
        border: 0;
        background: transparent;
        color: inherit;
        font-size: 1.1rem;
        line-height: 1;
        cursor: pointer;
        opacity: 0.78;
    }

    .cashier-toast[data-open="1"] {
        opacity: 1;
        pointer-events: auto;
        transform: translate(-50%, 0);
    }

    /* [hidden] must win over display:flex — otherwise the dialog stays visible on load */
    .cashier-confirm-overlay[hidden] {
        display: none !important;
    }

    .cashier-confirm-overlay:not([hidden]) {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cashier-confirm-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.42);
        z-index: 10000;
        padding: 16px;
    }

    .cashier-confirm-dialog {
        width: min(92vw, 430px);
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.3);
        padding: 18px 18px 14px;
    }

    .cashier-confirm-dialog h3 {
        margin: 0;
        font-size: 1.02rem;
        color: #0f172a;
    }

    .cashier-confirm-dialog p {
        margin: 10px 0 0;
        color: #475569;
        font-size: 0.92rem;
    }

    .cashier-confirm-actions {
        margin-top: 16px;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
</style>
<script>
    (function () {
        const INPUT_DEBOUNCE_MS = 480;

        const debounce = (fn, wait = 300) => {
            let t = null;
            return (...args) => {
                if (t) window.clearTimeout(t);
                t = window.setTimeout(() => fn(...args), wait);
            };
        };

        /**
         * Merge current URL with one toolbar's fields so the other block's filters stay intact.
         * @param {'pending'|'confirmed'} which
         */
        const mergeCashierParams = (form, which) => {
            const merged = new URLSearchParams(window.location.search);
            merged.delete('partial');
            const fd = new FormData(form);
            if (which === 'pending') {
                for (const [k, v] of fd.entries()) {
                    if (k.startsWith('pending_')) merged.set(k, String(v));
                }
                merged.delete('pending_page');
            } else {
                for (const [k, v] of fd.entries()) {
                    if (k.startsWith('confirmed_')) merged.set(k, String(v));
                }
                merged.delete('confirmed_page');
            }
            return merged;
        };

        const replaceHistoryWithoutPartial = (form, which) => {
            const q = mergeCashierParams(form, which);
            const path = form.action + (q.toString() ? '?' + q.toString() : '');
            window.history.replaceState(null, '', path);
        };

        let pendingAbort = null;
        let confirmedAbort = null;

        const captureSearchInputState = (inputId) => {
            const el = document.getElementById(inputId);
            if (!el || el.tagName !== 'INPUT') return null;
            const v = el.value;
            return {
                id: inputId,
                value: v,
                selectionStart: typeof el.selectionStart === 'number' ? el.selectionStart : v.length,
                selectionEnd: typeof el.selectionEnd === 'number' ? el.selectionEnd : v.length,
                keepFocus: document.activeElement === el,
            };
        };

        const applySearchInputState = (state) => {
            if (!state) return;
            const el = document.getElementById(state.id);
            if (!el || el.tagName !== 'INPUT') return;
            el.value = state.value;
            if (!state.keepFocus) return;
            el.focus();
            const len = state.value.length;
            const s = Math.min(state.selectionStart, len);
            const e = Math.min(state.selectionEnd, len);
            try {
                el.setSelectionRange(s, e);
            } catch (err) { /* ignore */ }
        };

        const fetchSection = async (which) => {
            const formId = which === 'pending' ? 'pending-toolbar' : 'confirmed-toolbar';
            const rootId = which === 'pending' ? 'cashier-pending-root' : 'cashier-confirmed-root';
            const inputId = which === 'pending' ? 'pending-search' : 'confirmed-search';
            const form = document.getElementById(formId);
            const root = document.getElementById(rootId);
            if (!form || !root) return;

            const params = mergeCashierParams(form, which);
            params.set('partial', which);

            const url = form.action + '?' + params.toString();
            const ac = new AbortController();
            if (which === 'pending') {
                if (pendingAbort) pendingAbort.abort();
                pendingAbort = ac;
            } else {
                if (confirmedAbort) confirmedAbort.abort();
                confirmedAbort = ac;
            }

            try {
                const res = await fetch(url, {
                    signal: ac.signal,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'text/html',
                    },
                    credentials: 'same-origin',
                });
                if (!res.ok) return;
                const html = await res.text();
                const searchState = captureSearchInputState(inputId);
                root.innerHTML = html;
                replaceHistoryWithoutPartial(document.getElementById(formId), which);
                applySearchInputState(searchState);
            } catch (err) {
                if (err && err.name === 'AbortError') return;
            }
        };

        const fetchPending = () => fetchSection('pending');
        const fetchConfirmed = () => fetchSection('confirmed');
        const fetchPendingDebounced = debounce(fetchPending, INPUT_DEBOUNCE_MS);
        const fetchConfirmedDebounced = debounce(fetchConfirmed, INPUT_DEBOUNCE_MS);

        document.body.addEventListener('input', (e) => {
            if (e.target && e.target.id === 'pending-search') fetchPendingDebounced();
            if (e.target && e.target.id === 'confirmed-search') fetchConfirmedDebounced();
        });

        document.body.addEventListener('search', (e) => {
            if (e.target && e.target.id === 'pending-search') fetchPending();
            if (e.target && e.target.id === 'confirmed-search') fetchConfirmed();
        });

        document.body.addEventListener('change', (e) => {
            const t = e.target;
            if (!t || !t.id) return;
            if (t.id === 'pending-type' || t.id === 'pending-sort') fetchPending();
            if (t.id === 'confirmed-type' || t.id === 'confirmed-sort') fetchConfirmed();
        });

        document.body.addEventListener('submit', (e) => {
            const f = e.target;
            if (!f || (f.id !== 'pending-toolbar' && f.id !== 'confirmed-toolbar')) return;
            e.preventDefault();
            if (f.id === 'pending-toolbar') fetchPending();
            else fetchConfirmed();
        });

        const autoApproveForm = document.getElementById('auto-approve-form');
        const autoApproveToggle = document.getElementById('auto-approve-toggle');
        const autoApproveHidden = document.getElementById('auto-approve-enabled');
        const autoApproveState = document.getElementById('auto-approve-state');
        const confirmOverlay = document.getElementById('cashier-confirm-overlay');
        const confirmTitle = document.getElementById('cashier-confirm-title');
        const confirmText = document.getElementById('cashier-confirm-text');
        const confirmCancelBtn = document.getElementById('cashier-confirm-cancel');
        const confirmOkBtn = document.getElementById('cashier-confirm-ok');
        const showConfirmDialog = ({ title, message, okLabel }) => {
            if (!confirmOverlay || !confirmCancelBtn || !confirmOkBtn) return Promise.resolve(true);
            return new Promise((resolve) => {
                const close = (accepted) => {
                    confirmOverlay.hidden = true;
                    confirmCancelBtn.removeEventListener('click', onCancel);
                    confirmOkBtn.removeEventListener('click', onOk);
                    confirmOverlay.removeEventListener('click', onBackdrop);
                    document.removeEventListener('keydown', onEsc);
                    resolve(accepted);
                };
                const onCancel = () => close(false);
                const onOk = () => close(true);
                const onBackdrop = (e) => {
                    if (e.target === confirmOverlay) close(false);
                };
                const onEsc = (e) => {
                    if (e.key === 'Escape') close(false);
                };

                if (confirmTitle) confirmTitle.textContent = title;
                if (confirmText) confirmText.textContent = message;
                confirmOkBtn.textContent = okLabel;
                confirmOverlay.hidden = false;
                confirmCancelBtn.addEventListener('click', onCancel);
                confirmOkBtn.addEventListener('click', onOk);
                confirmOverlay.addEventListener('click', onBackdrop);
                document.addEventListener('keydown', onEsc);
                confirmOkBtn.focus();
            });
        };
        if (autoApproveForm && autoApproveToggle && autoApproveHidden && autoApproveState) {
            autoApproveToggle.addEventListener('change', async () => {
                const wantsEnabled = autoApproveToggle.checked;
                const ok = await showConfirmDialog(
                    wantsEnabled
                        ? {
                            title: 'Turn On Auto Approve',
                            message: 'Are you sure you want to turn on Auto Approve Payment?',
                            okLabel: 'Turn On',
                        }
                        : {
                            title: 'Turn Off Auto Approve',
                            message: 'Are you sure you want to turn off Auto Approve Payment?',
                            okLabel: 'Turn Off',
                        }
                );
                if (!ok) {
                    autoApproveToggle.checked = !wantsEnabled;
                    return;
                }

                autoApproveHidden.value = wantsEnabled ? '1' : '0';
                autoApproveState.textContent = wantsEnabled ? 'ON' : 'OFF';
                autoApproveState.classList.toggle('green', wantsEnabled);
                autoApproveState.classList.toggle('amber', !wantsEnabled);
                autoApproveForm.submit();
            });
        }

        const toast = document.getElementById('cashier-toast');
        if (toast) {
            const closeBtn = toast.querySelector('.cashier-toast-close');
            const closeToast = () => {
                toast.dataset.open = '0';
            };
            if (closeBtn) closeBtn.addEventListener('click', closeToast);
            window.setTimeout(closeToast, 3600);
        }
    })();
</script>
@endpush
