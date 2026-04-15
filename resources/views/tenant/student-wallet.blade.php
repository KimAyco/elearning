@extends('layouts.app')

@section('title', 'Student Wallet - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'student-wallet', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body">

            @if(session('status'))
                <div class="alert success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
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

            <div class="finance-page">

                {{-- Header --}}
                <div class="finance-header">
                    <div class="finance-title-block">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="6" width="22" height="12" rx="2"/><path d="M1 10h22"/><circle cx="17" cy="12" r="1"/>
                        </svg>
                        <h1 class="finance-title">Students</h1>
                    </div>
                </div>

                {{-- Search --}}
                <div class="wallet-search-row">
                    <div class="finance-search-bar" style="max-width:380px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="search" id="search-input" placeholder="Search Student..." autocomplete="off">
                    </div>
                    <button type="button" class="btn secondary sm wallet-search-btn" id="wallet-search-btn" aria-label="Search students">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        Search
                    </button>
                </div>

                {{-- Table --}}
                <div class="card" style="padding:0; overflow:hidden;">
                    <div class="table-wrap" style="margin:0;">
                        <table id="wallets-table">
                            <thead>
                                <tr>
                                    <th>
                                        <span class="th-sort" onclick="sortTable(0)">Name <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
                                    </th>
                                    <th>
                                        <span class="th-sort" onclick="sortTable(1)">Amount <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
                                    </th>
                                    <th style="width:160px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    @php
                                        $wallet = $student->studentWallets->first();
                                        $balance = $wallet ? (float)$wallet->balance : 0;
                                    @endphp
                                    <tr class="finance-row">
                                        <td class="finance-name-cell">{{ $student->full_name }}</td>
                                        <td style="font-weight:600; color:var(--green);">
                                            ₱ {{ number_format($balance, 2) }}
                                        </td>
                                        <td>
                                            <button class="btn-wallet-add" onclick="openAddWalletModal({{ $student->id }}, '{{ addslashes($student->full_name ?? '') }}')">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <rect x="1" y="6" width="22" height="12" rx="2"/><path d="M1 10h22"/><line x1="12" y1="9" x2="12" y2="15"/><line x1="9" y1="12" x2="15" y2="12"/>
                                                </svg>
                                                Add Wallet
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="empty-state">
                                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--border-2); margin-bottom:8px;">
                                                    <rect x="1" y="6" width="22" height="12" rx="2"/><path d="M1 10h22"/>
                                                </svg>
                                                <p>No students found.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Summary --}}
                <div class="finance-summary">
                    <span>{{ $students->count() }} {{ Str::plural('student', $students->count()) }}</span>
                </div>

            </div>

            {{-- Modal: Add Wallet --}}
            <div class="modal-backdrop" id="modal-add-wallet" onclick="closeModalOnBackdrop(event, 'modal-add-wallet')">
                <div class="modal-box" style="max-width:400px;">
                    <div class="modal-header">
                        <h3>Add to Wallet</h3>
                        <button class="modal-close" onclick="closeModal('modal-add-wallet')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="add-wallet-form" action="">
                            @csrf
                            <p style="margin-bottom:16px; color:var(--muted); font-size:0.85rem;">
                                Adding wallet credit for: <strong id="wallet-student-name"></strong>
                            </p>
                            <div class="form-group">
                                <label class="form-label">Amount (₱) <span class="req">*</span></label>
                                <input name="amount" type="number" step="0.01" min="0.01" placeholder="0.00" required>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn ghost" onclick="closeModal('modal-add-wallet')">Cancel</button>
                                <button type="submit" class="btn primary">Add to Wallet</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
@endsection

@push('scripts')
@include('tenant.partials.finance-styles')
<style>
    .wallet-search-row {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        gap: 10px;
    }
    .wallet-search-row .finance-search-bar { flex: 1 1 220px; min-height: 44px; }
    .wallet-search-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: auto;
        min-height: 44px;
        padding: 9px 14px;
        white-space: nowrap;
    }
    .wallet-search-btn svg { flex-shrink: 0; }

    .btn-wallet-add {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        background: var(--green-l);
        color: var(--green);
        border: 1px solid var(--green-l2);
        border-radius: var(--radius-sm);
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }
    .btn-wallet-add:hover { background: var(--green-l2); }
</style>
<script>
    function filterTable() {
        const search = (document.getElementById('search-input').value || '').toLowerCase();
        document.querySelectorAll('#wallets-table .finance-row').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
        });
    }

    let sortDir = [1, 1];
    function sortTable(col) {
        const tbody = document.querySelector('#wallets-table tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr.finance-row'));
        sortDir[col] *= -1;
        rows.sort((a, b) => {
            const av = a.cells[col]?.textContent.trim() ?? '';
            const bv = b.cells[col]?.textContent.trim() ?? '';
            if (col === 1) {
                const an = parseFloat(av.replace(/[^\d.]/g, '')) || 0;
                const bn = parseFloat(bv.replace(/[^\d.]/g, '')) || 0;
                return (an - bn) * sortDir[col];
            }
            return av.localeCompare(bv) * sortDir[col];
        });
        rows.forEach(r => tbody.appendChild(r));
    }

    (function () {
        const input = document.getElementById('search-input');
        const btn = document.getElementById('wallet-search-btn');
        if (!input) return;
        let timer = null;
        const run = () => filterTable();
        input.addEventListener('input', () => {
            if (timer) window.clearTimeout(timer);
            timer = window.setTimeout(run, 250);
        });
        input.addEventListener('search', run);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (timer) window.clearTimeout(timer);
                run();
            }
        });
        if (btn) btn.addEventListener('click', () => {
            if (timer) window.clearTimeout(timer);
            run();
        });
    })();

    function openAddWalletModal(studentId, studentName) {
        document.getElementById('add-wallet-form').action = '/tenant/student-wallet/' + studentId + '/add';
        document.getElementById('wallet-student-name').textContent = studentName;
        openModal('modal-add-wallet');
    }

    function openModal(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }
    function closeModalOnBackdrop(e, id) {
        if (e.target === e.currentTarget) closeModal(id);
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-backdrop.open').forEach(m => {
                m.classList.remove('open');
                document.body.style.overflow = '';
            });
        }
    });
</script>
@endpush


