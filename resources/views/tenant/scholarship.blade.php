@extends('layouts.app')

@section('title', 'Scholarships - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'scholarship', 'sidebarClass' => 'sidebar--edu-mock'])

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
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                        </svg>
                        <h1 class="finance-title">Scholarship List</h1>
                    </div>
                    <div class="finance-actions">
                        <button class="btn-finance-action primary" onclick="openModal('modal-create')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Create Scholarship
                        </button>
                    </div>
                </div>

                {{-- Search --}}
                <div class="scholarship-search-row">
                    <div class="finance-search-bar" style="max-width:380px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="search" id="search-input" placeholder="Search scholarships..." autocomplete="off">
                    </div>
                    <button type="button" class="btn secondary sm scholarship-search-btn" id="scholarship-search-btn" aria-label="Search scholarships">
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
                        <table id="scholarships-table">
                            <thead>
                                <tr>
                                    <th style="width:70px;">
                                        <span class="th-sort" onclick="sortTable(0)">ID <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
                                    </th>
                                    <th>
                                        <span class="th-sort" onclick="sortTable(1)">Name <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
                                    </th>
                                    <th>Type</th>
                                    <th>Amount/Percentage</th>
                                    <th>Placement</th>
                                    <th style="width:90px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($scholarships as $scholarship)
                                    <tr class="finance-row">
                                        <td><span class="badge" style="font-size:0.72rem;">{{ $scholarship->id }}</span></td>
                                        <td class="finance-name-cell">{{ $scholarship->name }}</td>
                                        <td>
                                            <span class="badge blue" style="font-size:0.72rem;">{{ ucfirst($scholarship->type) }}</span>
                                        </td>
                                        <td style="font-weight:600; color:var(--ink);">
                                            @if($scholarship->type === 'amount')
                                                ₱ {{ number_format((float)$scholarship->amount, 2) }}
                                            @else
                                                {{ number_format((float)$scholarship->percentage, 2) }}%
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $scholarship->placement === 'admission' ? 'amber' : ($scholarship->placement === 'all' ? 'green' : '') }}" style="font-size:0.72rem;">
                                                {{ ucfirst($scholarship->placement) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn-edit-rule"
                                                onclick="openEditModal({{ $scholarship->id }}, '{{ addslashes($scholarship->name) }}', '{{ $scholarship->type }}', '{{ $scholarship->amount }}', '{{ $scholarship->percentage }}', '{{ $scholarship->placement }}', '{{ $scholarship->status ?? 'active' }}')">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                </svg>
                                                Edit
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">
                                            <div class="empty-state">
                                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--border-2); margin-bottom:8px;">
                                                    <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                                                </svg>
                                                <p>No scholarships created yet. Click <strong>Create Scholarship</strong> to add one.</p>
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
                    <span>{{ $scholarships->count() }} {{ Str::plural('scholarship', $scholarships->count()) }}</span>
                </div>

            </div>

            {{-- Modal: Create Scholarship --}}
            <div class="modal-backdrop" id="modal-create" onclick="closeModalOnBackdrop(event, 'modal-create')">
                <div class="modal-box">
                    <div class="modal-header">
                        <h3>Create Scholarship</h3>
                        <button class="modal-close" onclick="closeModal('modal-create')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ url('/tenant/scholarship') }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Scholarship Name <span class="req">*</span></label>
                                <input name="name" type="text" placeholder="e.g. Merit Scholarship Program" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Type <span class="req">*</span></label>
                                    <select name="type" id="create-type" onchange="toggleAmountField('create')" required>
                                        <option value="amount">Fixed Amount</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>
                                <div class="form-group" id="create-amount-group">
                                    <label class="form-label">Amount (₱) <span class="req">*</span></label>
                                    <input name="amount" id="create-amount" type="number" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="form-group" id="create-percentage-group" style="display:none;">
                                    <label class="form-label">Percentage (%) <span class="req">*</span></label>
                                    <input name="percentage" id="create-percentage" type="number" step="0.01" min="0" max="100" placeholder="0.00">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Placement <span class="req">*</span></label>
                                <select name="placement" required>
                                    <option value="regular">Regular</option>
                                    <option value="admission">Admission</option>
                                    <option value="all">All</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn ghost" onclick="closeModal('modal-create')">Cancel</button>
                                <button type="submit" class="btn primary">Create Scholarship</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal: Edit Scholarship --}}
            <div class="modal-backdrop" id="modal-edit" onclick="closeModalOnBackdrop(event, 'modal-edit')">
                <div class="modal-box">
                    <div class="modal-header">
                        <h3>Edit Scholarship</h3>
                        <button class="modal-close" onclick="closeModal('modal-edit')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="edit-form" action="">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Scholarship Name <span class="req">*</span></label>
                                <input id="edit-name" name="name" type="text" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Type <span class="req">*</span></label>
                                    <select name="type" id="edit-type" onchange="toggleAmountField('edit')" required>
                                        <option value="amount">Fixed Amount</option>
                                        <option value="percentage">Percentage</option>
                                    </select>
                                </div>
                                <div class="form-group" id="edit-amount-group">
                                    <label class="form-label">Amount (₱)</label>
                                    <input name="amount" id="edit-amount" type="number" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <div class="form-group" id="edit-percentage-group" style="display:none;">
                                    <label class="form-label">Percentage (%)</label>
                                    <input name="percentage" id="edit-percentage" type="number" step="0.01" min="0" max="100" placeholder="0.00">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Placement <span class="req">*</span></label>
                                    <select id="edit-placement" name="placement" required>
                                        <option value="regular">Regular</option>
                                        <option value="admission">Admission</option>
                                        <option value="all">All</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Status</label>
                                    <select id="edit-status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn ghost" onclick="closeModal('modal-edit')">Cancel</button>
                                <button type="submit" class="btn primary">Save Changes</button>
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
    .scholarship-search-row {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
        gap: 10px;
    }
    .scholarship-search-row .finance-search-bar { flex: 1 1 220px; min-height: 44px; }
    .scholarship-search-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: auto;
        min-height: 44px;
        padding: 9px 14px;
        white-space: nowrap;
    }
    .scholarship-search-btn svg { flex-shrink: 0; }
</style>
<script>
    function filterTable() {
        const search = (document.getElementById('search-input').value || '').toLowerCase();
        document.querySelectorAll('#scholarships-table .finance-row').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
        });
    }

    let sortDir = [1, 1];
    function sortTable(col) {
        const tbody = document.querySelector('#scholarships-table tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr.finance-row'));
        sortDir[col] *= -1;
        rows.sort((a, b) => {
            const av = a.cells[col]?.textContent.trim() ?? '';
            const bv = b.cells[col]?.textContent.trim() ?? '';
            const an = parseFloat(av.replace(/[^\d.]/g, ''));
            const bn = parseFloat(bv.replace(/[^\d.]/g, ''));
            if (!isNaN(an) && !isNaN(bn)) return (an - bn) * sortDir[col];
            return av.localeCompare(bv) * sortDir[col];
        });
        rows.forEach(r => tbody.appendChild(r));
    }

    (function () {
        const input = document.getElementById('search-input');
        const btn = document.getElementById('scholarship-search-btn');
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

    function toggleAmountField(prefix) {
        const type = document.getElementById(prefix + '-type').value;
        document.getElementById(prefix + '-amount-group').style.display = type === 'amount' ? '' : 'none';
        document.getElementById(prefix + '-percentage-group').style.display = type === 'percentage' ? '' : 'none';
    }

    function openEditModal(id, name, type, amount, percentage, placement, status) {
        document.getElementById('edit-form').action = '/tenant/scholarship/' + id + '/update';
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-type').value = type;
        document.getElementById('edit-amount').value = amount || '';
        document.getElementById('edit-percentage').value = percentage || '';
        document.getElementById('edit-placement').value = placement;
        document.getElementById('edit-status').value = status;
        toggleAmountField('edit');
        openModal('modal-edit');
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


