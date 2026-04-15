<div style="margin-bottom:18px;">
    <div class="section-divider" style="margin-bottom:12px;">
        <span>Pending Payment Verifications</span>
        <span class="badge amber">{{ ($pendingPayments ?? collect())->total() }}</span>
    </div>
    <form id="pending-toolbar" method="get" action="{{ url('/tenant/cashier') }}" class="cashier-toolbar" style="margin-top:-4px;">
        <div class="form-group">
            <label for="pending-search">Search</label>
            <input
                id="pending-search"
                name="pending_q"
                type="search"
                placeholder="Search billing ID, student, or reference…"
                value="{{ $pendingSearch ?? '' }}"
            >
        </div>
        <div class="form-group">
            <label for="pending-type">Type</label>
            <select id="pending-type" name="pending_type">
                <option value="all" {{ ($pendingTypeFilter ?? 'all') === 'all' ? 'selected' : '' }}>All types</option>
                <option value="tuition" {{ ($pendingTypeFilter ?? 'all') === 'tuition' ? 'selected' : '' }}>Tuition</option>
                <option value="enrollment" {{ ($pendingTypeFilter ?? 'all') === 'enrollment' ? 'selected' : '' }}>Enrollment</option>
                <option value="other" {{ ($pendingTypeFilter ?? 'all') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="pending-sort">Sort</label>
            <select id="pending-sort" name="pending_sort">
                <option value="latest" {{ ($pendingSort ?? 'latest') === 'latest' ? 'selected' : '' }}>Latest first</option>
                <option value="oldest" {{ ($pendingSort ?? 'latest') === 'oldest' ? 'selected' : '' }}>Oldest first</option>
                <option value="amount_high" {{ ($pendingSort ?? 'latest') === 'amount_high' ? 'selected' : '' }}>Amount: high to low</option>
                <option value="amount_low" {{ ($pendingSort ?? 'latest') === 'amount_low' ? 'selected' : '' }}>Amount: low to high</option>
            </select>
        </div>
        <div class="form-group cashier-toolbar-actions">
            <button type="submit" class="btn secondary sm cashier-search-btn" aria-label="Search pending payments">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                Search
            </button>
        </div>
    </form>
    <div class="card" style="padding:0; overflow:hidden;">
        <div class="table-wrap" style="margin:0;">
            <table>
                <thead>
                    <tr>
                            <th>#</th>
                            <th>Billing ID</th>
                            <th>Type</th>
                            <th>Applicant / Student</th>
                            <th>Amount</th>
                            <th>Reference</th>
                            <th>Decision</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($pendingPayments ?? collect()) as $row)
                            <tr>
                                <td style="color:var(--muted); font-size:0.8rem;">{{ $row->id }}</td>
                                <td><span class="badge">#{{ $row->billing_id }}</span></td>
                                <td>
                                    <span class="badge {{ $row->billing->charge_type === 'misc_fee' ? 'blue' : 'purple' }}" style="font-size:0.7rem; text-transform:uppercase;">
                                        {{ str_replace('_', ' ', $row->billing->charge_type === 'misc_fee' ? 'Enrollment' : $row->billing->charge_type) }}
                                    </span>
                                </td>
                                <td style="color:var(--ink-2); font-weight:600;">{{ $row->student->full_name ?? $row->student_user_id }}</td>
                                <td style="font-weight:600;">₱ {{ number_format($row->amount, 2) }}</td>
                                <td style="font-size:0.82rem; color:var(--muted);">{{ $row->reference_no ?? '-' }}</td>
                            <td>
                                <form method="post" action="{{ url('/tenant/payments/' . $row->id . '/verify') }}">
                                    @csrf
                                    <div style="display:flex; gap:6px; align-items:center;">
                                        <select name="status" style="width:120px; padding:5px 8px; font-size:0.8rem;" required>
                                            <option value="verified">Approve</option>
                                            <option value="rejected">Reject</option>
                                        </select>
                                        <input name="remarks" placeholder="Remarks" style="flex:1; padding:5px 8px; font-size:0.8rem; min-width:100px;">
                                        <button class="btn success sm" type="submit">Apply</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6"><div class="empty-state"><p>No pending payments.</p></div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(($pendingPayments ?? collect()) instanceof \Illuminate\Contracts\Pagination\Paginator)
            <div class="cashier-pagination">
                {{ $pendingPayments->onEachSide(2)->appends(request()->except('partial'))->links('vendor.pagination.cashier') }}
            </div>
        @endif
    </div>
</div>
