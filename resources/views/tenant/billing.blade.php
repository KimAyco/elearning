@extends('layouts.app')

@section('title', 'Billing - School Portal')

@section('content')
<div class="app-shell">
    @include('tenant.partials.sidebar', ['active' => 'billing'])

    <div class="main-content">
        <header class="topbar">
            <div style="display:flex; align-items:center; gap:12px;">
                <button class="hamburger" aria-label="Toggle menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <span class="topbar-title">Billing & Payments</span>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->full_name ?? 'U', 0, 1)) }}</div>
                    <span>{{ auth()->user()->full_name ?? 'User' }}</span>
                </div>
            </div>
        </header>

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

            {{-- ── Finance User: Billing Rules Management ─────────────────── --}}
            @if($isFinanceUser ?? false)

            <div class="billing-page">

                {{-- Header Bar --}}
                <div class="billing-header">
                    <div class="billing-title-block">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        <h1 class="billing-title">Billing List</h1>
                    </div>
                    <div class="billing-actions">
                        <button class="btn-billing-action secondary" onclick="openModal('modal-category')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Create Billing Category
                    </button>
                        <button class="btn-billing-action primary" onclick="openModal('modal-create')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Create Billing
                    </button>
                        <button class="btn-billing-action teal" onclick="openModal('modal-group')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            Group Billing
                    </button>
                    </div>
                </div>

                {{-- Search Bar --}}
                <div class="billing-search-bar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" id="billing-search" placeholder="Search Billing..." oninput="filterBillingTable(this.value)">
                        </div>

                {{-- Billing Rules Table --}}
                <div class="card" style="padding:0; overflow:hidden;">
                    <div class="table-wrap" style="margin:0;">
                        <table id="billing-table">
                                <thead>
                                    <tr>
                                    <th style="width:70px;">
                                        <span class="th-sort" onclick="sortTable(0)">ID <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
                                    </th>
                                    <th>
                                        <span class="th-sort" onclick="sortTable(1)">Name <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></span>
                                    </th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Scope</th>
                                    <th>Status</th>
                                    <th style="width:140px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($billingRules as $rule)
                                    <tr class="billing-row">
                                        <td><span class="badge" style="font-size:0.72rem;">{{ $rule->id }}</span></td>
                                        <td class="billing-name-cell">{{ $rule->description }}</td>
                                        <td>
                                            @if($rule->category)
                                                <span class="badge blue" style="font-size:0.72rem;">{{ $rule->category->name }}</span>
                                                @else
                                                <span class="badge" style="font-size:0.72rem;">Uncategorized</span>
                                                @endif
                                            </td>
                                        <td style="font-weight:600; color:var(--ink);">
                                            ₱ {{ number_format((float) $rule->amount, 2) }}
                                        </td>
                                        <td style="font-size:0.78rem; color:var(--muted);">
                                            @if($rule->scope_type === 'all')
                                                <span class="badge green" style="font-size:0.7rem;">All Students</span>
                                            @elseif($rule->scope_type === 'program')
                                                <span class="badge amber" style="font-size:0.7rem;">{{ $programNameMap[$rule->program_id] ?? 'Program #'.$rule->program_id }}</span>
                                            @elseif($rule->scope_type === 'department')
                                                <span class="badge" style="font-size:0.7rem;">Department</span>
                                            @else
                                                <span class="badge" style="font-size:0.7rem;">{{ ucfirst($rule->scope_type) }}</span>
                @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $rule->status === 'active' ? 'green' : '' }}" style="font-size:0.72rem;">
                                                {{ ucfirst($rule->status ?? 'active') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display:flex; gap:6px; align-items:center;">
                                                <button class="btn-edit-rule"
                                                    onclick="openEditModal({{ $rule->id }}, '{{ addslashes($rule->description) }}', '{{ $rule->charge_type }}', '{{ $rule->amount }}', '{{ $rule->status ?? 'active' }}')">
                                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                    </svg>
                                                    Edit
                                                </button>
                                                <form method="post" action="{{ url('/tenant/billing/rules/' . $rule->id . '/delete') }}" style="display:inline;" onsubmit="return confirm('Delete this billing rule? This cannot be undone.');">
                                                    @csrf
                                                    <button type="submit" class="btn-delete-rule" title="Delete">
                                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                            <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--border-2); margin-bottom:8px;">
                                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                                                </svg>
                                                <p>No billing rules yet. Click <strong>Create Billing</strong> to add one.</p>
                                            </div>
                                        </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                {{-- Summary row --}}
                <div class="billing-summary">
                    <span>{{ $billingRules->count() }} billing {{ Str::plural('rule', $billingRules->count()) }}</span>
                    @if($billingRules->count())
                        <span>Total configured: <strong>₱ {{ number_format($billingRules->sum('amount'), 2) }}</strong></span>
                    @endif
                </div>

                {{-- ── Enrollment Fee Settings ───────────────────────── --}}
                <div style="margin-top:28px;">
                    <div class="section-divider">
                        <span>Enrollment Fee Settings</span>
                    </div>
                    <div class="card">
                        <form method="post" action="{{ url('/tenant/billing/fee-settings/enrollment/general') }}">
                            @csrf
                            <div style="display:grid; grid-template-columns: 1fr 1fr auto; gap:12px; align-items:end;">
                                <div>
                                    <label class="form-label">Enrollment Fee (General)</label>
                                    <input name="enrollment_fee" type="number" step="0.01" min="0"
                                           value="{{ number_format((float)($generalEnrollmentFeeSetting->enrollment_fee ?? 0), 2, '.', '') }}"
                                           placeholder="0.00" required>
                                </div>
                                <div>
                                    <label class="form-label">Price Per Course Unit</label>
                                    <input name="price_per_course_unit" type="number" step="0.01" min="0"
                                           value="{{ $generalEnrollmentFeeSetting?->price_per_course_unit !== null ? number_format((float)$generalEnrollmentFeeSetting->price_per_course_unit, 2, '.', '') : '' }}"
                                           placeholder="0.00">
                                </div>
                                <button class="btn primary" type="submit" style="height:40px;">Save</button>
                            </div>
                            <p style="margin-top:8px; color:var(--muted); font-size:0.78rem;">School-wide setting. Formula: <strong>price per unit × course units</strong></p>
                        </form>
                    </div>
                </div>

            </div>{{-- /.billing-page --}}

            {{-- ══════════ MODALS ══════════ --}}

            {{-- Modal: Billing Categories (list + create) --}}
            <div class="modal-backdrop" id="modal-category" onclick="closeModalOnBackdrop(event, 'modal-category')">
                <div class="modal-box" style="max-width:720px;">
                    <div class="modal-header">
                        <h3>Billing Categories</h3>
                        <button class="modal-close" onclick="closeModal('modal-category')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                        </div>
                    <div class="modal-body">
                        <div class="card" style="padding:0; overflow:hidden; margin-bottom:14px;">
                            <div class="table-wrap" style="margin:0;">
                            <table>
                                <thead>
                                    <tr>
                                            <th style="width:70px;">ID</th>
                                            <th>Category</th>
                                            <th style="width:120px;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        @forelse(($billingCategories ?? collect()) as $cat)
                                            <tr>
                                                <td><span class="badge" style="font-size:0.72rem;">{{ $cat->id }}</span></td>
                                                <td style="font-weight:600;">{{ $cat->name }}</td>
                                                <td><span class="badge {{ $cat->status === 'active' ? 'green' : '' }}" style="font-size:0.72rem;">{{ ucfirst($cat->status) }}</span></td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3"><div class="empty-state"><p>No categories yet.</p></div></td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <form method="post" action="{{ url('/tenant/billing/categories') }}">
                            @csrf
                            <div class="form-row" style="grid-template-columns: 1fr auto;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Create Billing Category <span class="req">*</span></label>
                                    <input name="name" type="text" placeholder="e.g. Laboratory, Parking Fee, Electric Bill" required>
                                </div>
                                <div class="form-group" style="margin-bottom:0; align-self:end;">
                                    <button type="submit" class="btn primary" style="height:40px;">Add</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal: Create Billing --}}
            <div class="modal-backdrop" id="modal-create" onclick="closeModalOnBackdrop(event, 'modal-create')">
                <div class="modal-box">
                    <div class="modal-header">
                        <h3>Create Billing</h3>
                        <button class="modal-close" onclick="closeModal('modal-create')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ url('/tenant/billing/rules') }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Billing Name / Description <span class="req">*</span></label>
                                <input name="description" type="text" placeholder="e.g. Student Parking, Employee Parking" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Category <span class="req">*</span></label>
                                    <select name="billing_category_id" required>
                                        <option value="">Select Category...</option>
                                        @foreach(($billingCategories ?? collect()) as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Amount (₱) <span class="req">*</span></label>
                                    <input name="amount" type="number" step="0.01" min="0.01" placeholder="0.00" required>
                                </div>
                            </div>
                            <input type="hidden" name="charge_type" value="other">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Semester <span class="req">*</span></label>
                                    <select name="semester_id" required>
                                        <option value="">Select semester...</option>
                                        @foreach($billingSemesters as $sem)
                                            <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Scope <span class="req">*</span></label>
                                    <select name="scope_type" id="create-scope-type" onchange="toggleScopeField('create')" required>
                                        <option value="all">All Students</option>
                                        <option value="program">By Program</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="create-scope-id-group" style="display:none;">
                                <label class="form-label">Program <span class="req">*</span></label>
                                <select name="scope_id" id="create-scope-id">
                                    <option value="">Select program...</option>
                                    @foreach($billingPrograms as $prog)
                                        <option value="{{ $prog->id }}">{{ trim(($prog->code ?? '') . ' - ' . $prog->name, ' -') }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group" id="create-year-level-group" style="display:none;">
                                <label class="form-label">Year Level <span class="req">*</span></label>
                                <select name="year_level" id="create-year-level">
                                    <option value="">Select year...</option>
                                    <option value="1">1st Year</option>
                                    <option value="2">2nd Year</option>
                                    <option value="3">3rd Year</option>
                                    <option value="4">4th Year</option>
                                    <option value="5">5th Year</option>
                                </select>
                                <div style="margin-top:6px; font-size:0.78rem; color:var(--muted);">
                                    Optional filter. Example: bill only IT <strong>4th year</strong> students for this semester.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn ghost" onclick="closeModal('modal-create')">Cancel</button>
                                <button type="submit" class="btn primary">Create Billing</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal: Group Billing --}}
            <div class="modal-backdrop" id="modal-group" onclick="closeModalOnBackdrop(event, 'modal-group')">
                <div class="modal-box" style="max-width:760px;">
                    <div class="modal-header">
                        <h3>Group Billing</h3>
                        <button class="modal-close" onclick="closeModal('modal-group')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div style="display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:12px;">
                            <div style="color:var(--muted); font-size:0.82rem;">Group templates for billing (ex: IT 4th Year 2nd Sem).</div>
                            <button class="btn primary" type="button" onclick="openModal('modal-create-group')">Create Group</button>
                        </div>

                        <div class="card" style="padding:0; overflow:hidden;">
                            <div class="table-wrap" style="margin:0;">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Group name</th>
                                            <th>Course</th>
                                            <th>Year Level</th>
                                            <th style="width:120px;">Billings</th>
                                            <th style="width:170px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($billingGroups ?? collect()) as $g)
                                            <tr>
                                                <td style="font-weight:600;">{{ $g->name }}</td>
                                                <td>{{ $g->program?->name ?? '-' }}</td>
                                                <td>{{ $g->year_level ?? '-' }}</td>
                                                <td><span class="badge blue" style="font-size:0.72rem;">{{ $g->items->count() }}</span></td>
                                                <td>
                                                    <div style="display:flex; gap:6px; align-items:center; justify-content:flex-end;">
                                                        <button type="button" class="btn-edit-rule"
                                                                onclick="openEditGroupModal({{ $g->id }})">
                                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                                            </svg>
                                                            Edit
                                                        </button>
                                                        <form method="post" action="{{ url('/tenant/billing/groups/' . $g->id . '/delete') }}"
                                                              onsubmit="return confirm('Delete this billing group template? This cannot be undone.');">
                                                            @csrf
                                                            <button type="submit" class="btn-delete-rule">
                                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                                    <line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/>
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5"><div class="empty-state"><p>No group templates yet.</p></div></td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Modal: Create Group (select multiple billings) --}}
            <div class="modal-backdrop" id="modal-create-group" onclick="closeModalOnBackdrop(event, 'modal-create-group')">
                <div class="modal-box" style="max-width:760px;">
                    <div class="modal-header">
                        <h3>Create Group</h3>
                        <button class="modal-close" onclick="closeModal('modal-create-group')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ url('/tenant/billing/groups') }}">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Group Name <span class="req">*</span></label>
                                <input name="name" type="text" placeholder="e.g. IT 4th Year - 2nd Sem" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Course</label>
                                    <select name="program_id">
                                        <option value="">Select Course...</option>
                                        @foreach($billingPrograms as $prog)
                                            <option value="{{ $prog->id }}">{{ trim(($prog->code ?? '') . ' - ' . $prog->name, ' -') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Year Level</label>
                                    <select name="year_level">
                                        <option value="">Select...</option>
                                        <option value="Grade 1">Grade 1</option>
                                        <option value="Grade 2">Grade 2</option>
                                        <option value="Grade 3">Grade 3</option>
                                        <option value="Grade 4">Grade 4</option>
                                        <option value="Grade 5">Grade 5</option>
                                        <option value="Grade 6">Grade 6</option>
                                        <option value="1st Year">1st Year</option>
                                        <option value="2nd Year">2nd Year</option>
                                        <option value="3rd Year">3rd Year</option>
                                        <option value="4th Year">4th Year</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Select Billings (you can pick many) <span class="req">*</span></label>
                                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; max-height:260px; overflow:auto; padding:10px; border:1px solid var(--border); border-radius: var(--radius); background: var(--surface-2);">
                                    @foreach($billingRules as $rule)
                                        <label style="display:flex; gap:8px; align-items:flex-start; padding:6px 8px; border-radius:8px; background:#fff; border:1px solid var(--border);">
                                            <input type="checkbox" name="billing_rule_ids[]" value="{{ $rule->id }}" style="margin-top:3px;">
                                            <span style="display:flex; flex-direction:column; gap:2px;">
                                                <span style="font-weight:600; font-size:0.85rem;">{{ $rule->description }}</span>
                                                <span style="font-size:0.75rem; color:var(--muted);">
                                                    {{ $rule->category?->name ?? 'Uncategorized' }} • ₱ {{ number_format((float)$rule->amount, 2) }}
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn ghost" onclick="closeModal('modal-create-group')">Cancel</button>
                                <button type="submit" class="btn primary">Create Group</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal: Edit Group --}}
            <div class="modal-backdrop" id="modal-edit-group" onclick="closeModalOnBackdrop(event, 'modal-edit-group')">
                <div class="modal-box" style="max-width:760px;">
                    <div class="modal-header">
                        <h3>Edit Group</h3>
                        <button class="modal-close" onclick="closeModal('modal-edit-group')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="edit-group-form" action="">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Group Name <span class="req">*</span></label>
                                <input id="edit-group-name" name="name" type="text" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Course</label>
                                    <select id="edit-group-program" name="program_id">
                                        <option value="">Select Course...</option>
                                        @foreach($billingPrograms as $prog)
                                            <option value="{{ $prog->id }}">{{ trim(($prog->code ?? '') . ' - ' . $prog->name, ' -') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Year Level</label>
                                    <select id="edit-group-year" name="year_level">
                                        <option value="">Select...</option>
                                        <option value="Grade 1">Grade 1</option>
                                        <option value="Grade 2">Grade 2</option>
                                        <option value="Grade 3">Grade 3</option>
                                        <option value="Grade 4">Grade 4</option>
                                        <option value="Grade 5">Grade 5</option>
                                        <option value="Grade 6">Grade 6</option>
                                        <option value="1st Year">1st Year</option>
                                        <option value="2nd Year">2nd Year</option>
                                        <option value="3rd Year">3rd Year</option>
                                        <option value="4th Year">4th Year</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Select Billings (you can pick many) <span class="req">*</span></label>
                                <div id="edit-group-billings"
                                     style="display:grid; grid-template-columns: 1fr 1fr; gap:8px; max-height:260px; overflow:auto; padding:10px; border:1px solid var(--border); border-radius: var(--radius); background: var(--surface-2);">
                                    @foreach($billingRules as $rule)
                                        <label style="display:flex; gap:8px; align-items:flex-start; padding:6px 8px; border-radius:8px; background:#fff; border:1px solid var(--border);">
                                            <input class="edit-group-rule" type="checkbox" name="billing_rule_ids[]" value="{{ $rule->id }}" style="margin-top:3px;">
                                            <span style="display:flex; flex-direction:column; gap:2px;">
                                                <span style="font-weight:600; font-size:0.85rem;">{{ $rule->description }}</span>
                                                <span style="font-size:0.75rem; color:var(--muted);">
                                                    {{ $rule->category?->name ?? 'Uncategorized' }} • ₱ {{ number_format((float)$rule->amount, 2) }}
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn ghost" onclick="closeModal('modal-edit-group')">Cancel</button>
                                <button type="submit" class="btn primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Modal: Edit Billing Rule --}}
            <div class="modal-backdrop" id="modal-edit" onclick="closeModalOnBackdrop(event, 'modal-edit')">
                <div class="modal-box">
                    <div class="modal-header">
                        <h3>Edit Billing Rule</h3>
                        <button class="modal-close" onclick="closeModal('modal-edit')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="edit-rule-form" action="">
                            @csrf
                            <div class="form-group">
                                <label class="form-label">Billing Name / Description <span class="req">*</span></label>
                                <input id="edit-description" name="description" type="text" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Charge Type <span class="req">*</span></label>
                                    <select id="edit-charge-type" name="charge_type" required>
                                        <option value="tuition">Tuition Fee</option>
                                        <option value="misc_fee">Miscellaneous Fee</option>
                                        <option value="lab_fee">Laboratory Fee</option>
                                        <option value="penalty">Penalty</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Amount (₱) <span class="req">*</span></label>
                                    <input id="edit-amount" name="amount" type="number" step="0.01" min="0.01" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select id="edit-status" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn ghost" onclick="closeModal('modal-edit')">Cancel</button>
                                <button type="submit" class="btn primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            @else
            {{-- ══════════ STUDENT VIEW ══════════ --}}
            <div class="billing-page">
                <div class="billing-header">
                    <div class="billing-title-block">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                        <h1 class="billing-title">My Billing</h1>
                    </div>
                    <div style="color:var(--muted); font-size:0.82rem;">{{ ($myBilling ?? collect())->count() }} {{ Str::plural('record', ($myBilling ?? collect())->count()) }}</div>
                </div>

                <div class="billing-search-bar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" placeholder="Search billing..." oninput="filterBillingTable(this.value)">
                </div>

                <div class="card" style="padding:0; overflow:hidden;">
                    <div class="table-wrap" style="margin:0;">
                        <table id="billing-table">
                            <thead>
                                <tr>
                                    <th>Billing #</th>
                                    <th>Description</th>
                                    <th>Amount Due</th>
                                    <th>Amount Paid</th>
                                    <th>Status</th>
                                    <th>Submit Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($myBilling ?? collect()) as $row)
                                    @php $remaining = max((float) $row->amount_due - (float) $row->amount_paid, 0); @endphp
                                    <tr class="billing-row">
                                        <td><span class="badge">#{{ $row->id }}</span></td>
                                        <td class="billing-name-cell">{{ $row->description ?? '-' }}</td>
                                        <td style="font-weight:600;">₱ {{ number_format((float) $row->amount_due, 2) }}</td>
                                        <td>₱ {{ number_format((float) $row->amount_paid, 2) }}</td>
                                        <td>
                                            <span class="badge {{ in_array($row->payment_status, ['verified','paid_unverified','waived'], true) ? 'green' : '' }}"
                                                  style="font-size:0.72rem;">
                                                {{ $row->payment_status }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(in_array((string) $row->payment_status, ['verified', 'waived', 'void'], true) || $remaining <= 0)
                                                <span style="color:var(--muted); font-size:0.8rem;">Settled</span>
                                                @else
                                                <form method="post" action="{{ url('/tenant/billing/' . $row->id . '/payments') }}">
                                                    @csrf
                                                    <div style="display:flex; gap:6px; align-items:center;">
                                                        <input name="amount" type="number" step="0.01" min="1"
                                                               max="{{ number_format($remaining, 2, '.', '') }}"
                                                               value="{{ number_format($remaining, 2, '.', '') }}"
                                                               style="width:100px; padding:5px 8px; font-size:0.8rem;" required>
                                                        <input name="reference_no" placeholder="Reference no."
                                                               style="width:130px; padding:5px 8px; font-size:0.8rem;" required>
                                                        <button class="btn primary sm" type="submit">Submit</button>
                                                    </div>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                        <td colspan="6">
                                            <div class="empty-state">
                                                <p>No billing records found.</p>
                                            </div>
                                        </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

        </main>
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* ── Billing Page Layout ──────────────────────────────────── */
    .billing-page { display:flex; flex-direction:column; gap:16px; }

    .billing-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .billing-title-block {
        display: flex;
        align-items: center;
        gap: 10px;
        color: var(--ink);
    }

    .billing-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--ink);
        margin: 0;
    }

    .billing-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-billing-action {
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

    .btn-billing-action.primary {
        background: var(--accent);
        color: #fff;
        border-color: var(--accent);
    }
    .btn-billing-action.primary:hover { background: var(--accent-h); border-color: var(--accent-h); }

    .btn-billing-action.secondary {
        background: var(--surface);
        color: var(--ink-2);
        border-color: var(--border-2);
    }
    .btn-billing-action.secondary:hover { background: var(--bg-2); color: var(--ink); }

    .btn-billing-action.teal {
        background: #0d9488;
        color: #fff;
        border-color: #0d9488;
    }
    .btn-billing-action.teal:hover { background: #0f766e; border-color: #0f766e; }

    /* ── Search Bar ───────────────────────────────────────────── */
    .billing-search-bar {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        padding: 8px 12px;
        max-width: 380px;
        color: var(--muted);
        transition: border-color .15s;
    }
    .billing-search-bar:focus-within { border-color: var(--accent); color: var(--ink); }
    .billing-search-bar input {
        border: none;
        outline: none;
        background: none;
        font-size: 0.875rem;
        color: var(--ink);
        width: 100%;
    }
    .billing-search-bar input::placeholder { color: var(--muted); }

    /* ── Table tweaks ─────────────────────────────────────────── */
    .th-sort {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        user-select: none;
    }
    .th-sort:hover { color: var(--accent); }

    .billing-name-cell { font-weight: 500; color: var(--ink); }

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

    .btn-delete-rule {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        background: var(--red-l);
        color: var(--red);
        border: 1px solid var(--red-l2);
        border-radius: var(--radius-sm);
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
    }
    .btn-delete-rule:hover { background: var(--red-l2); }

    /* ── Summary / Divider ────────────────────────────────────── */
    .billing-summary {
        display: flex;
        gap: 16px;
        color: var(--muted);
        font-size: 0.8rem;
        padding: 4px 0;
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

@php
    $billingGroupsPayload = ($billingGroups ?? collect())
        ->map(function ($g) {
            return [
                'id' => (int) $g->id,
                'name' => (string) $g->name,
                'program_id' => $g->program_id ? (int) $g->program_id : null,
                'year_level' => $g->year_level,
                'billing_rule_ids' => $g->items->pluck('billing_rule_id')->map(fn ($id) => (int) $id)->all(),
            ];
        })
        ->values()
        ->all();
@endphp
<script>
    window.__billingGroups = {!! json_encode($billingGroupsPayload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!};

    // ── Modal helpers ─────────────────────────────────────────
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

    // ── Edit modal ────────────────────────────────────────────
    function openEditModal(id, description, chargeType, amount, status) {
        document.getElementById('edit-rule-form').action = '/tenant/billing/rules/' + id + '/update';
        document.getElementById('edit-description').value = description;
        document.getElementById('edit-amount').value = amount;
        document.getElementById('edit-status').value = status;
        const ct = document.getElementById('edit-charge-type');
        if (ct) ct.value = chargeType;
        openModal('modal-edit');
    }

    function openEditGroupModal(groupId) {
        const groups = window.__billingGroups || [];
        const g = groups.find(x => x.id === groupId);
        if (!g) return;

        document.getElementById('edit-group-form').action = '/tenant/billing/groups/' + groupId + '/update';
        document.getElementById('edit-group-name').value = g.name || '';

        const programSel = document.getElementById('edit-group-program');
        if (programSel) programSel.value = g.program_id ? String(g.program_id) : '';

        const yearSel = document.getElementById('edit-group-year');
        if (yearSel) yearSel.value = g.year_level ?? '';

        // Reset then apply checkbox selections
        document.querySelectorAll('.edit-group-rule').forEach(cb => { cb.checked = false; });
        (g.billing_rule_ids || []).forEach(id => {
            const el = document.querySelector('.edit-group-rule[value=\"' + id + '\"]');
            if (el) el.checked = true;
        });

        openModal('modal-edit-group');
    }

    // ── Scope toggle ──────────────────────────────────────────
    function toggleScopeField(prefix) {
        const type = document.getElementById(prefix + '-scope-type')?.value;
        const group = document.getElementById(prefix + '-scope-id-group');
        if (group) group.style.display = (type && type !== 'all') ? 'flex' : 'none';

        // Only show year-level filter when filtering by program (your requested use case)
        const yearGroup = document.getElementById(prefix + '-year-level-group');
        if (yearGroup) yearGroup.style.display = (type === 'program') ? 'flex' : 'none';
    }

    // ── Search / filter ───────────────────────────────────────
    function filterBillingTable(query) {
        const q = query.toLowerCase();
        document.querySelectorAll('#billing-table .billing-row').forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });
    }

    // ── Sort ──────────────────────────────────────────────────
    let sortDir = [1, 1];
    function sortTable(col) {
        const tbody = document.querySelector('#billing-table tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr.billing-row'));
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
</script>
@endpush
