<div class="card mb-20">
    <div class="card-header">
        <h2>
            <div class="card-icon amber">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>
            </div>
            Pending Clearance Approvals
        </h2>
        @php
            $pendingTotal = method_exists($pendingClearancesForRegistrar, 'total')
                ? (int) $pendingClearancesForRegistrar->total()
                : (int) collect($pendingClearancesForRegistrar)->count();
        @endphp
        <span class="badge amber">{{ $pendingTotal }} pending</span>
    </div>

    <div class="enroll-clearance-search-row">
        <form id="registrar-clearance-search-form" method="get" action="{{ url('/tenant/enrollments') }}" autocomplete="off">
            <input
                type="text"
                id="registrar-clearance-search"
                name="registrar_clearance_q"
                value="{{ $registrarClearanceSearch ?? '' }}"
                class="enroll-clearance-search-input"
                placeholder="Search by ID or student name"
            >
            <button type="submit" class="enroll-clearance-search-btn">Search</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Cleared By</th>
                    <th>Cleared At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $groupedByStudent = collect($pendingClearancesForRegistrar)->groupBy('student_user_id');
                @endphp
                @forelse($pendingClearancesForRegistrar as $billing)
                    @php
                        $studentBillings = $groupedByStudent[$billing->student_user_id] ?? collect();
                        $isFirstForStudent = $studentBillings->first()?->id === $billing->id;
                        $studentBillingCount = $studentBillings->count();
                    @endphp
                    <tr>
                        <td style="color:var(--muted); font-size:0.8rem;">{{ $billing->id }}</td>
                        <td>
                            <div style="font-weight:600;">{{ $billing->student?->full_name ?? 'Student #' . $billing->student_user_id }}</div>
                            <span class="badge" style="font-size:0.7rem;">ID: {{ $billing->student_user_id }}</span>
                            @if($studentBillingCount > 1)
                                <span class="badge amber" style="font-size:0.7rem; margin-left:4px;">{{ $studentBillingCount }} pending</span>
                            @endif
                        </td>
                        <td>{{ $billing->description ?? '-' }}</td>
                        <td style="font-weight:600;">₱ {{ number_format((float) $billing->amount_due, 2) }}</td>
                        <td style="font-size:0.85rem; color:var(--muted);">
                            {{ $billing->clearedByFinance?->full_name ?? 'Finance #' . $billing->cleared_by_finance_user_id }}
                        </td>
                        <td style="font-size:0.85rem; color:var(--muted);">
                            {{ optional($billing->cleared_at)->format('M d, Y H:i') ?? '-' }}
                        </td>
                        <td>
                            <div style="display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
                                <form method="post" action="{{ url('/tenant/billing/' . $billing->id . '/clearance/approve') }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn success sm">Approve</button>
                                </form>
                                @if($isFirstForStudent && $studentBillingCount > 1)
                                    <form method="post" action="{{ url('/tenant/billing/clearance/approve-all-for-student') }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="student_user_id" value="{{ $billing->student_user_id }}">
                                        <input type="hidden" name="semester_id" value="{{ $billing->semester_id }}">
                                        <button type="submit" class="btn primary sm" title="Approve all {{ $studentBillingCount }} clearances for this student" style="font-weight:600;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;">
                                                <path d="M20 6L9 17l-5-5"/>
                                            </svg>
                                            Approve All ({{ $studentBillingCount }})
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state" style="padding:24px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                </svg>
                                <p>No pending clearances awaiting approval.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($pendingClearancesForRegistrar, 'hasPages') && $pendingClearancesForRegistrar->hasPages())
        <div class="cashier-pagination">
            {{ $pendingClearancesForRegistrar->onEachSide(2)->appends(request()->except('partial'))->links('vendor.pagination.cashier') }}
        </div>
    @endif
</div>
