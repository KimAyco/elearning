{{-- Prefer a person label; if full_name is "{school} Admin", show email instead. --}}
@php
    $u = auth()->user();
    $raw = trim((string) ($u->full_name ?? ''));
    $school = session('active_school_name');
    $clean = $raw;
    if ($school !== null && $school !== '' && strcasecmp($raw, $school . ' Admin') === 0) {
        $clean = $school;
    }
    $label = $clean;
    if ($school !== null && $school !== '' && strcasecmp($clean, $school) === 0) {
        $label = (string) ($u->email ?? $clean);
    }
    if ($label === '') {
        $label = (string) ($u->email ?? 'User');
    }
    $avatarChar = strtoupper(mb_substr($label, 0, 1));
    $variant = $variant ?? '';
@endphp
@if ($variant === 'dashboard')
<div class="topbar-user topbar-user--dashboard">
    <div class="avatar">{{ $avatarChar }}</div>
    <div class="topbar-user-meta">
        <span class="topbar-user-name">{{ $label }}</span>
    </div>
</div>
@elseif ($variant === 'lms')
<div class="lms-topbar-user">
    <div class="lms-topbar-avatar">{{ $avatarChar }}</div>
    <span class="lms-topbar-username">{{ $label }}</span>
    <svg class="lms-topbar-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
</div>
@else
<div class="topbar-user">
    <div class="avatar">{{ $avatarChar }}</div>
    <span>{{ $label }}</span>
</div>
@endif
