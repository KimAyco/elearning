<div class="card mb-20">
    <div class="card-header">
        <h2>
            <div class="card-icon green">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
            </div>
            Finance Verified Students (Registrar)
        </h2>
        @php
            $verifiedTotal = method_exists($financeVerifiedForRegistrar, 'total')
                ? (int) $financeVerifiedForRegistrar->total()
                : (int) collect($financeVerifiedForRegistrar)->count();
        @endphp
        <span class="badge green">{{ $verifiedTotal }} verified</span>
    </div>

    <div class="enroll-verified-search-row">
        <form id="registrar-verified-search-form" method="get" action="{{ url('/tenant/enrollments') }}" autocomplete="off">
            <input
                type="text"
                id="registrar-verified-search"
                name="registrar_verified_q"
                value="{{ $registrarVerifiedSearch ?? '' }}"
                class="enroll-verified-search-input"
                placeholder="Search by ID or student name"
            >
            <button type="submit" class="enroll-verified-search-btn">Search</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($financeVerifiedForRegistrar as $row)
                    <tr>
                        <td style="color:var(--muted); font-size:0.8rem;">{{ $row->id }}</td>
                        <td style="font-weight:600; color:var(--ink);">{{ $row->student->full_name ?? ('#' . $row->student_user_id) }}</td>
                        <td>
                            <div style="color:var(--ink-2);">{{ $row->student->email ?? 'N/A' }}</div>
                            <div style="margin-top:4px;">
                                <span class="badge {{ (($row->student->status ?? '') === 'active') ? 'green' : 'amber' }}">
                                    account {{ (($row->student->status ?? '') === 'active') ? 'active' : 'inactive' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            @if($row->offering?->subject)
                                <span class="badge blue">{{ $row->offering->subject->code }}</span>
                                {{ $row->offering->subject->title ?? '' }}
                            @else
                                <span class="text-muted text-sm">-</span>
                            @endif
                        </td>
                        <td><span class="badge green">{{ $row->status }}</span></td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <form method="post" action="{{ url('/tenant/enrollments/' . $row->id . '/confirm') }}">
                                    @csrf
                                    <button class="btn success sm" type="submit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                        Confirm Enrollment
                                    </button>
                                </form>
                                @if(($row->student->status ?? '') !== 'active')
                                    <form method="post" action="{{ url('/tenant/enrollments/students/' . $row->student_user_id . '/activate') }}">
                                        @csrf
                                        <button class="btn primary sm" type="submit">
                                            Activate Account
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state" style="padding:24px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                                <p>No finance-verified students available for registrar confirmation.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($financeVerifiedForRegistrar, 'hasPages') && $financeVerifiedForRegistrar->hasPages())
        <div class="cashier-pagination">
            {{ $financeVerifiedForRegistrar->onEachSide(2)->appends(request()->except('partial'))->links('vendor.pagination.cashier') }}
        </div>
    @endif
</div>
