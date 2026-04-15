<div class="card mb-20">
    <div class="card-header">
        <h2>
            <div class="card-icon amber">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                </svg>
            </div>
            Inactive Student Accounts (Finance Verified)
        </h2>
        @php
            $inactiveTotal = method_exists($inactiveStudentAccountsForRegistrar, 'total')
                ? (int) $inactiveStudentAccountsForRegistrar->total()
                : (int) collect($inactiveStudentAccountsForRegistrar)->count();
        @endphp
        <span class="badge amber">{{ $inactiveTotal }} ready</span>
    </div>

    <div class="enroll-inactive-search-row">
        <form id="registrar-inactive-search-form" method="get" action="{{ url('/tenant/enrollments') }}" autocomplete="off">
            <input
                type="text"
                id="registrar-inactive-search"
                name="registrar_inactive_q"
                value="{{ $registrarInactiveSearch ?? '' }}"
                class="enroll-inactive-search-input"
                placeholder="Search by ID or student name"
            >
            <button type="submit" class="enroll-inactive-search-btn">Search</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inactiveStudentAccountsForRegistrar as $student)
                    <tr>
                        <td style="color:var(--muted); font-size:0.8rem;">{{ $student->id }}</td>
                        <td style="font-weight:600; color:var(--ink);">{{ $student->full_name }}</td>
                        <td style="color:var(--ink-2);">{{ $student->email }}</td>
                        <td style="color:var(--ink-2);">{{ $student->phone ?? 'N/A' }}</td>
                        <td><span class="badge amber">inactive</span></td>
                        <td>
                            <form method="post" action="{{ url('/tenant/enrollments/students/' . $student->id . '/activate') }}">
                                @csrf
                                <button class="btn primary sm" type="submit">
                                    Activate Account
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state" style="padding:24px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                </svg>
                                <p>No finance-verified inactive student accounts found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($inactiveStudentAccountsForRegistrar, 'hasPages') && $inactiveStudentAccountsForRegistrar->hasPages())
        <div class="cashier-pagination">
            {{ $inactiveStudentAccountsForRegistrar->onEachSide(2)->appends(request()->except('partial'))->links('vendor.pagination.cashier') }}
        </div>
    @endif
</div>
