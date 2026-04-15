<div class="card mb-20">
    <div class="card-header">
        <h2>
            <div class="card-icon purple">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            School Staff Records (Registrar)
        </h2>
        @php
            $staffTotal = method_exists($schoolStaffForRegistrar, 'total')
                ? (int) $schoolStaffForRegistrar->total()
                : (int) collect($schoolStaffForRegistrar)->count();
        @endphp
        <span class="badge purple">{{ $staffTotal }} staffs</span>
    </div>

    <div class="enroll-registrar-staff-search-row">
        <form id="registrar-staff-search-form" method="get" action="{{ url('/tenant/enrollments') }}" autocomplete="off">
            <input
                type="text"
                id="registrar-staff-search"
                name="registrar_staff_q"
                value="{{ $registrarStaffSearch ?? '' }}"
                class="enroll-registrar-staff-search-input"
                placeholder="Search by ID or name"
            >
            <button type="submit" class="enroll-registrar-staff-search-btn">Search</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Roles</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schoolStaffForRegistrar as $staff)
                    <tr>
                        <td style="color:var(--muted); font-size:0.8rem;">{{ $staff['id'] }}</td>
                        <td style="font-weight:600; color:var(--ink);">{{ $staff['full_name'] }}</td>
                        <td style="color:var(--ink-2);">{{ $staff['email'] }}</td>
                        <td style="color:var(--ink-2);">{{ $staff['phone'] !== '' ? $staff['phone'] : 'N/A' }}</td>
                        <td>
                            @foreach(($staff['roles'] ?? []) as $roleCode)
                                <span class="badge blue" style="margin-right:6px;">{{ strtoupper((string) $roleCode) }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge {{ ($staff['status'] ?? '') === 'active' ? 'green' : 'amber' }}">
                                {{ ($staff['status'] ?? '') === 'active' ? 'active' : 'inactive' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state" style="padding:24px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                </svg>
                                <p>No school staffs found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($schoolStaffForRegistrar, 'hasPages') && $schoolStaffForRegistrar->hasPages())
        <div class="cashier-pagination">
            {{ $schoolStaffForRegistrar->onEachSide(2)->appends(request()->except('partial'))->links('vendor.pagination.cashier') }}
        </div>
    @endif
</div>
