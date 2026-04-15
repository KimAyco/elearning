{{-- Sidebar navigation shared across tenant pages --}}
{{-- Usage: @include('components.nav.sidebar', ['active' => 'dashboard']) --}}
@php $active = $active ?? ''; @endphp
@php
    $permissionCodes = (array) session('permission_codes', []);
    $roleCodes = collect((array) session('role_codes', []))
        ->map(fn ($role) => strtolower((string) $role))
        ->filter(fn ($role) => $role !== '')
        ->values();
    $hasAnyPermission = function (array $required) use ($permissionCodes): bool {
        foreach ($required as $code) {
            if (in_array($code, $permissionCodes, true)) {
                return true;
            }
        }
        return false;
    };
    $hasAnyRole = function (array $required) use ($roleCodes): bool {
        foreach ($required as $role) {
            if ($roleCodes->contains(strtolower((string) $role))) {
                return true;
            }
        }
        return false;
    };
@endphp
@php
    $isSchoolAdmin = $hasAnyRole(['school_admin']);
    $activeSchoolId = (int) session('active_school_id', 0);
    $actorUserId = (int) session('user_id', 0);
    $showClassMenu = false;

    if ($activeSchoolId > 0 && $actorUserId > 0 && $hasAnyRole(['student'])) {
        $activeStatuses = ['selected', 'validated', 'billing_pending', 'payment_verified', 'registrar_confirmed', 'enrolled'];

        $activeSemesterIds = \App\Models\Enrollment::query()
            ->where('school_id', $activeSchoolId)
            ->where('student_user_id', $actorUserId)
            ->whereIn('status', $activeStatuses)
            ->pluck('semester_id')
            ->filter(fn ($id) => (int) $id > 0)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($activeSemesterIds !== []) {
            $hasPaidTuitionForActiveSubjects = \App\Models\Billing::query()
                ->where('school_id', $activeSchoolId)
                ->where('student_user_id', $actorUserId)
                ->where('charge_type', 'tuition')
                ->whereIn('semester_id', $activeSemesterIds)
                ->where(function ($query): void {
                    $query->whereIn('payment_status', ['verified', 'paid_unverified', 'waived'])
                        ->orWhere(function ($paidInFull): void {
                            $paidInFull->whereColumn('amount_paid', '>=', 'amount_due')
                                ->where('amount_due', '>', 0);
                        });
                })
                ->exists();

            $showClassMenu = $hasPaidTuitionForActiveSubjects;
        }
    }

    // Pre-calc visibility for cleaner, grouped navigation
    $showEnrollmentsLink = $hasAnyPermission(['student.enrollment.create', 'registrar.enrollment.confirm']);
    $isFinanceRole = $hasAnyRole(['finance_staff', 'school_admin']);
    $showFinanceMenu = $isFinanceRole && $hasAnyPermission(['finance.billing.manage', 'finance.payment.verify', 'finance.clearance.issue']);
    $showStudentPayments = (! $isFinanceRole) && $hasAnyPermission(['student.records.view']);
    $showGradesLink = $hasAnyPermission(['teacher.grades.submit', 'dean.grades.review', 'registrar.grades.finalize', 'registrar.grades.release', 'student.grades.view']);
    $showLmsLink = $hasAnyRole(['teacher']);
    $showSchoolPageLink = $isSchoolAdmin;
    $showAdminLink = $hasAnyPermission(['school_admin.manage_staff', 'school_admin.assign_roles', 'school_admin.manage_curriculum']);

    $schoolName = session('active_school_name') ?? session('school_name') ?? null;
    $schoolLogoUrl = session('active_school_logo_url');
    $primaryRole = $roleCodes->first();
    $sidebarClass = $sidebarClass ?? '';
    $isEduMockSidebar = $sidebarClass === 'sidebar--edu-mock';
    $eduMockBrandLogoUrl = null;
    if ($isEduMockSidebar && $activeSchoolId > 0) {
        $schoolForBrand = \App\Models\School::query()->find($activeSchoolId);
        if ($schoolForBrand !== null) {
            $eduMockBrandLogoUrl = $schoolForBrand->schoolSealLogoUrl() ?? $schoolForBrand->logo_url;
        }
        if ($eduMockBrandLogoUrl === null && $schoolLogoUrl) {
            $eduMockBrandLogoUrl = $schoolLogoUrl;
        }
    }
@endphp

<aside class="sidebar {{ $sidebarClass }}">
    <div class="sidebar-brand">
        @if ($isEduMockSidebar)
            <div class="sidebar-brand-mock-mark {{ $eduMockBrandLogoUrl ? 'sidebar-brand-mock-mark--has-logo' : 'sidebar-brand-mock-mark--fallback' }}">
                @if ($eduMockBrandLogoUrl)
                    <img src="{{ $eduMockBrandLogoUrl }}" alt="" width="44" height="44" decoding="async">
                @else
                    <span class="sidebar-brand-mock-fallback" aria-hidden="true">{{ strtoupper(mb_substr((string) ($schoolName ?: 'S'), 0, 1)) }}</span>
                @endif
            </div>
            <div class="sidebar-brand-text-block sidebar-brand-text-block--edu-mock">
                <div class="sidebar-brand-primary sidebar-brand-primary--edu-mock">{{ $schoolName ?? 'School' }}</div>
                @if ($primaryRole)
                    <div class="sidebar-brand-role">
                        <span class="sidebar-brand-role-pill sidebar-brand-role-pill--edu-mock">{{ strtoupper($primaryRole) }}</span>
                    </div>
                @endif
            </div>
        @else
            @if ($schoolLogoUrl)
                <div class="sidebar-brand-logo-wrap">
                    <img src="{{ $schoolLogoUrl }}" alt="{{ $schoolName ?? 'School' }}" class="sidebar-brand-logo" width="44" height="44" decoding="async">
                </div>
            @else
                <div class="sidebar-brand-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
            @endif
            <div class="sidebar-brand-text-block">
                <div class="sidebar-brand-primary">{{ $schoolName ?? 'School' }}</div>
                <div class="sidebar-brand-platform">EduPlatform</div>
                @if($primaryRole)
                    <div class="sidebar-brand-role">
                        <span class="sidebar-brand-role-pill">{{ strtoupper($primaryRole) }}</span>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section-label">Navigation</div>

        <a href="{{ url('/tenant/dashboard') }}" class="sidebar-link {{ $active === 'dashboard' ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
            </svg>
            Dashboard
        </a>

        @if ($showEnrollmentsLink || $showClassMenu || $showGradesLink || $showLmsLink)
            <div class="sidebar-section-label">Academics</div>

            @if ($showEnrollmentsLink)
                <a href="{{ url('/tenant/enrollments') }}" class="sidebar-link {{ $active === 'enrollments' ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    {{ $isSchoolAdmin ? 'Registrar' : 'Enrollments' }}
                </a>
            @endif

            @if ($showClassMenu)
                <a href="{{ url('/tenant/class') }}" class="sidebar-link {{ $active === 'class' ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                    </svg>
                    Classes
                </a>
            @endif

            @if ($showGradesLink)
                <a href="{{ url('/tenant/grades') }}" class="sidebar-link {{ $active === 'grades' ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    Grades
                </a>
            @endif

            @if ($showLmsLink)
                <a href="{{ url('/tenant/lms') }}" class="sidebar-link {{ $active === 'lms' ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a4 4 0 0 0-4-4H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a4 4 0 0 1 4-4h6z"/>
                    </svg>
                    LMS
                </a>
            @endif
        @endif

        @if ($showStudentPayments)
            <div class="sidebar-section-label">Payments</div>

            <a href="{{ url('/tenant/payments') }}" class="sidebar-link {{ $active === 'payments' ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
                Payments
            </a>
        @endif

        @if ($showFinanceMenu)
            @php
                $financeActives = ['billing', 'cashier', 'discount', 'scholarship', 'student-wallet'];
                $isFinanceActive = in_array($active, $financeActives, true);
            @endphp
            <div class="sidebar-section-label">Finance</div>

            <button class="sidebar-submenu-toggle sidebar-submenu-toggle--finance {{ $isFinanceActive ? 'has-active open' : '' }}"
                    type="button" aria-expanded="{{ $isFinanceActive ? 'true' : 'false' }}">
                <svg class="icon-main" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
                Finance
                <svg class="sidebar-submenu-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            <div class="sidebar-submenu {{ $isFinanceActive ? 'open' : '' }}">
                <div class="sidebar-submenu-inner">
                    <a href="{{ url('/tenant/billing') }}" class="sidebar-sublink {{ $active === 'billing' ? 'active' : '' }}">
                        Billings
                    </a>
                    <a href="{{ url('/tenant/cashier') }}" class="sidebar-sublink {{ $active === 'cashier' ? 'active' : '' }}">
                        Cashier
                    </a>
                    <a href="{{ url('/tenant/discount') }}" class="sidebar-sublink {{ $active === 'discount' ? 'active' : '' }}">
                        Discount
                    </a>
                    <a href="{{ url('/tenant/scholarship') }}" class="sidebar-sublink {{ $active === 'scholarship' ? 'active' : '' }}">
                        Scholarship
                    </a>
                    <a href="{{ url('/tenant/student-wallet') }}" class="sidebar-sublink {{ $active === 'student-wallet' ? 'active' : '' }}">
                        Student Wallet
                    </a>
                </div>
            </div>
        @endif

        @if ($showSchoolPageLink || $showAdminLink)
            <div class="sidebar-section-label">School</div>

            @if ($showSchoolPageLink)
                <a href="{{ url('/tenant/school-page') }}" class="sidebar-link {{ $active === 'school-page' ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Branding &amp; Public Page
                </a>
            @endif

            @if ($showAdminLink)
                <a href="{{ url('/tenant/admin') }}" class="sidebar-link {{ $active === 'admin' ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/>
                    </svg>
                    Academic Management
                </a>
            @endif

            @php
                $settingsActives = ['settings-appearance', 'settings-account'];
                $isSettingsActive = in_array($active, $settingsActives, true);
            @endphp
            <div class="sidebar-section-label">Settings</div>

            <button class="sidebar-submenu-toggle {{ $isSettingsActive ? 'has-active open' : '' }}"
                    type="button"
                    aria-expanded="{{ $isSettingsActive ? 'true' : 'false' }}"
                    aria-controls="sidebar-settings-submenu">
                <svg class="icon-main" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
                Settings
                <svg class="sidebar-submenu-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            <div id="sidebar-settings-submenu" class="sidebar-submenu {{ $isSettingsActive ? 'open' : '' }}">
                <div class="sidebar-submenu-inner">
                    <a href="{{ url('/tenant/settings/appearance') }}" class="sidebar-sublink {{ $active === 'settings-appearance' ? 'active' : '' }}">
                        Appearance
                    </a>
                    <a href="{{ url('/tenant/settings/account') }}" class="sidebar-sublink {{ $active === 'settings-account' ? 'active' : '' }}">
                        Account
                    </a>
                    <form method="post" action="{{ url('/logout') }}" style="margin-top: 10px;" class="sidebar-logout-form">
                        @csrf
                        <button type="submit" class="sidebar-sublink" style="width:100%; text-align:left; background:transparent; border:0; padding-left: 10px; padding-right: 10px; cursor:pointer;">
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </nav>
</aside>

<div class="sidebar-overlay"></div>

