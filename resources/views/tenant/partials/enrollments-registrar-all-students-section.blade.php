<div class="card mb-20">
    <div class="card-header">
        <h2>
            <div class="card-icon blue">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                </svg>
            </div>
            All Student Records (Registrar)
        </h2>
        @php
            $studentsTotal = method_exists($allStudentsForRegistrar, 'total')
                ? (int) $allStudentsForRegistrar->total()
                : (int) collect($allStudentsForRegistrar)->count();
        @endphp
        <span class="badge blue">{{ $studentsTotal }} students</span>
    </div>

    <div class="enroll-all-students-search-row">
        <form id="registrar-all-students-search-form" method="get" action="{{ url('/tenant/enrollments') }}" autocomplete="off">
            <input
                type="text"
                id="registrar-all-students-search"
                name="registrar_all_students_q"
                value="{{ $registrarAllStudentsSearch ?? '' }}"
                class="enroll-all-students-search-input"
                placeholder="Search by ID or student name"
            >
            <button type="submit" class="enroll-all-students-search-btn">Search</button>
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
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allStudentsForRegistrar as $student)
                    <tr>
                        <td style="color:var(--muted); font-size:0.8rem;">{{ $student->id }}</td>
                        <td style="font-weight:600; color:var(--ink);">{{ $student->full_name }}</td>
                        <td style="color:var(--ink-2);">{{ $student->email }}</td>
                        <td style="color:var(--ink-2);">{{ $student->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ ($student->status ?? '') === 'active' ? 'green' : 'amber' }}">
                                {{ ($student->status ?? '') === 'active' ? 'active' : 'inactive' }}
                            </span>
                        </td>
                        <td>
                            @if(($student->status ?? '') !== 'active')
                                <form method="post" action="{{ url('/tenant/enrollments/students/' . $student->id . '/activate') }}">
                                    @csrf
                                    <button class="btn primary sm" type="submit">Activate Account</button>
                                </form>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state" style="padding:24px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                </svg>
                                <p>No students found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($allStudentsForRegistrar, 'hasPages') && $allStudentsForRegistrar->hasPages())
        <div class="cashier-pagination">
            {{ $allStudentsForRegistrar->onEachSide(2)->appends(request()->except('partial'))->links('vendor.pagination.cashier') }}
        </div>
    @endif
</div>
