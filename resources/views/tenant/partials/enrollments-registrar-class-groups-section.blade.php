<div class="card mb-20">
    <div class="card-header">
        <h2>
            <div class="card-icon blue">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 7h18"/><path d="M3 12h18"/><path d="M3 17h18"/>
                </svg>
            </div>
            Class Group Capacity (Registrar)
        </h2>
        @php
            $groupsTotal = method_exists($classGroupCapacityForRegistrar, 'total')
                ? (int) $classGroupCapacityForRegistrar->total()
                : (int) collect($classGroupCapacityForRegistrar)->count();
        @endphp
        <span class="badge blue">{{ $groupsTotal }} groups</span>
    </div>

    <div class="enroll-class-group-search-row">
        <form id="registrar-class-group-search-form" method="get" action="{{ url('/tenant/enrollments') }}" autocomplete="off">
            <input
                type="text"
                id="registrar-class-group-search"
                name="registrar_class_group_q"
                value="{{ $registrarClassGroupSearch ?? '' }}"
                class="enroll-class-group-search-input"
                placeholder="Search by class group or program"
            >
            <button type="submit" class="enroll-class-group-search-btn">Search</button>
        </form>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Class Group</th>
                    <th>Program</th>
                    <th>Semester</th>
                    <th>Students Inside</th>
                    <th>Capacity</th>
                    <th>Available Slots</th>
                    <th>Enrollment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classGroupCapacityForRegistrar as $group)
                    @php
                        $insideCount = (int) ($group->students_inside_count ?? 0);
                        $capacity = (int) ($group->student_capacity ?? 0);
                        $available = max($capacity - $insideCount, 0);
                        $isFull = $capacity > 0 && $insideCount >= $capacity;
                    @endphp
                    <tr>
                        <td style="color:var(--muted); font-size:0.8rem;">{{ $group->id }}</td>
                        <td style="font-weight:600; color:var(--ink);">{{ $group->name }} | Y{{ (int) $group->year_level }}</td>
                        <td>
                            <div style="color:var(--ink); font-weight:600;">{{ $group->program->code ?? 'N/A' }}</div>
                            <div style="color:var(--ink-2); font-size:0.85rem;">{{ $group->program->name ?? 'N/A' }}</div>
                        </td>
                        <td>{{ $group->semester->name ?? 'N/A' }}</td>
                        <td><span class="badge blue">{{ $insideCount }}</span></td>
                        <td><span class="badge">{{ $capacity }}</span></td>
                        <td><span class="badge {{ $isFull ? 'red' : 'green' }}">{{ $available }}</span></td>
                        <td>
                            <span class="badge {{ (bool) ($group->is_enrollment_open ?? false) ? 'green' : 'amber' }}">
                                {{ (bool) ($group->is_enrollment_open ?? false) ? 'open' : 'closed' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state" style="padding:24px;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/>
                                </svg>
                                <p>No class groups found.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($classGroupCapacityForRegistrar, 'hasPages') && $classGroupCapacityForRegistrar->hasPages())
        <div class="cashier-pagination">
            {{ $classGroupCapacityForRegistrar->onEachSide(2)->appends(request()->except('partial'))->links('vendor.pagination.cashier') }}
        </div>
    @endif
</div>
