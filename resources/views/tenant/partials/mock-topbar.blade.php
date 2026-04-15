{{-- Compact mock topbar (matches dashboard): hamburger + notifications + settings + avatar --}}
@php
    $u = auth()->user();
    $raw = trim((string) ($u->full_name ?? ''));
    $school = session('active_school_name');
    $clean = $raw;
    if ($school !== null && $school !== '' && strcasecmp($raw, $school . ' Admin') === 0) {
        $clean = $school;
    }
    $mockTopbarUserLabel = $clean;
    if ($school !== null && $school !== '' && strcasecmp($clean, $school) === 0) {
        $mockTopbarUserLabel = (string) ($u->email ?? $clean);
    }
    if ($mockTopbarUserLabel === '') {
        $mockTopbarUserLabel = (string) ($u->email ?? 'User');
    }
    $mockTopbarAvatarChar = strtoupper(mb_substr($mockTopbarUserLabel, 0, 1));
    $mockTopbarEmail = (string) ($u->email ?? 'student@school.edu');
    $mockTopbarTimezone = (string) ($u->timezone ?? 'Asia/Manila');
    $mockStudentProfile = $u?->studentProfile;
    $mockProgramModel = $mockStudentProfile?->program;
    $mockTopbarProgram = (string) (session('student_program_name')
        ?? trim((string) (($mockProgramModel?->code ?? '') . ' ' . ($mockProgramModel?->name ?? '')))
        ?? session('student_program')
        ?? 'Not set');
    if ($mockTopbarProgram === '') {
        $mockTopbarProgram = 'Not set';
    }
    $mockTopbarCourse = (string) (session('student_course_name') ?? session('student_course') ?? $mockTopbarProgram);
    // Strict avatar source rule:
    // uploaded profile_photo_path => show real image; otherwise => letter fallback only.
    $mockTopbarAvatarUrl = null;
    if ($u instanceof \App\Models\User && trim((string) ($u->profile_photo_path ?? '')) !== '') {
        $avatarVersion = urlencode((string) ($u->updated_at?->timestamp ?? time()));
        $mockTopbarAvatarUrl = url('/tenant/settings/account/avatar/view?v=' . $avatarVersion);
    }
    $roleCodes = collect((array) session('role_codes', []))
        ->map(fn ($code) => strtolower(trim((string) $code)))
        ->filter()
        ->values();
    $profileLabel = $roleCodes->contains('teacher') ? 'Teacher Profile' : 'Profile';
    // Notification payload can be supplied by controller/view;
    // fallback keeps UI functional even before backend wiring.
    $mockNotifications = collect($mockNotifications ?? session('mock_notifications', []))
        ->filter(fn ($n) => is_array($n))
        ->values();
    $mockNotificationCount = $mockNotifications->count();
@endphp
<header class="topbar topbar--tenant-mock">
    <div class="topbar-mock-left">
        <button class="hamburger" aria-label="Toggle menu">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>
    <div class="topbar-mock-actions">
        <div class="topbar-mock-notify">
        <button
            type="button"
            class="topbar-mock-icon-btn topbar-mock-icon-btn--badge"
            aria-label="Notifications"
            aria-expanded="false"
            aria-controls="topbar-notification-panel"
            id="topbar-notification-toggle"
        >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
            </svg>
            @if($mockNotificationCount > 0)
                <span class="topbar-mock-badge" aria-hidden="true">{{ $mockNotificationCount > 99 ? '99+' : $mockNotificationCount }}</span>
            @endif
        </button>
        <div id="topbar-notification-panel" class="topbar-notification-panel" role="dialog" aria-label="Notifications" hidden>
            <div class="topbar-notification-panel__head">
                <h4>Notifications</h4>
                @if($mockNotificationCount > 0)
                    <span>{{ $mockNotificationCount }}</span>
                @endif
            </div>
            @if($mockNotificationCount === 0)
                <div class="topbar-notification-empty">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <p>No notifications yet.</p>
                </div>
            @else
                <div class="topbar-notification-list">
                    @foreach($mockNotifications as $n)
                        <article class="topbar-notification-item">
                            <p class="topbar-notification-title">{{ $n['title'] ?? 'Notification' }}</p>
                            @if(!empty($n['message']))
                                <p class="topbar-notification-text">{{ $n['message'] }}</p>
                            @endif
                            @if(!empty($n['time']))
                                <time class="topbar-notification-time">{{ $n['time'] }}</time>
                            @endif
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
        </div>
        <button type="button" class="topbar-mock-icon-btn" aria-label="Messages">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        </button>
        <div class="topbar-mock-avatar-wrap" title="{{ $mockTopbarUserLabel }}">
            <div class="topbar-mock-avatar">
                @if($mockTopbarAvatarUrl)
                    <img id="topbar-avatar-img" src="{{ $mockTopbarAvatarUrl }}" alt="{{ $mockTopbarUserLabel }}">
                @else
                    <span id="topbar-avatar-fallback">{{ $mockTopbarAvatarChar }}</span>
                @endif
            </div>
            <button
                type="button"
                class="topbar-mock-chevron"
                aria-label="User menu"
                aria-expanded="false"
                aria-controls="topbar-user-menu"
                id="topbar-user-menu-toggle"
            >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div id="topbar-user-menu" class="topbar-user-menu" role="menu" hidden>
                <button type="button" class="topbar-user-menu__item" role="menuitem" id="topbar-profile-open">Profile</button>
                <a href="#" class="topbar-user-menu__item" role="menuitem">Help</a>
                <a href="#" class="topbar-user-menu__item" role="menuitem">Faculty Evaluation</a>
                <form method="POST" action="{{ url('/logout') }}" class="topbar-user-menu__logout-form sidebar-logout-form">
                    @csrf
                    <button type="submit" class="topbar-user-menu__item topbar-user-menu__item--danger" role="menuitem">Log out</button>
                </form>
            </div>
        </div>
    </div>
</header>

<div id="student-profile-modal" class="student-profile-modal" hidden>
    <div class="student-profile-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="student-profile-title">
        <div class="student-profile-modal__head">
            <h3 id="student-profile-title">{{ $profileLabel }}</h3>
            <button type="button" class="student-profile-modal__close" id="student-profile-close" aria-label="Close profile dialog">&times;</button>
        </div>
        <form method="POST" action="{{ url('/tenant/settings/account/avatar') }}" enctype="multipart/form-data">
            @csrf
        <div class="student-profile-modal__body">
            <div class="student-profile-avatar-block">
                <label class="student-profile-avatar-picker" for="student-profile-photo-input" title="Change profile picture">
                    @if($mockTopbarAvatarUrl)
                        <img
                            id="student-profile-photo-preview"
                            src="{{ $mockTopbarAvatarUrl }}"
                            alt="Profile photo"
                        >
                        <span id="student-profile-photo-fallback" class="student-profile-avatar-fallback" style="display:none;">{{ $mockTopbarAvatarChar }}</span>
                    @else
                        <img
                            id="student-profile-photo-preview"
                            src=""
                            alt="Profile photo"
                            style="display:none;"
                        >
                        <span id="student-profile-photo-fallback" class="student-profile-avatar-fallback">{{ $mockTopbarAvatarChar }}</span>
                    @endif
                    <span class="student-profile-avatar-edit">+</span>
                </label>
                <input id="student-profile-photo-input" name="profile_photo" type="file" accept="image/*" hidden>
                <p class="student-profile-avatar-hint">Tap photo to upload new image</p>
            </div>

            <div class="student-profile-grid">
                <div class="student-profile-field">
                    <label>Full name</label>
                    <p class="student-profile-value">{{ $mockTopbarUserLabel }}</p>
                </div>
                <div class="student-profile-field">
                    <label>Email</label>
                    <p class="student-profile-value">{{ $mockTopbarEmail }}</p>
                </div>
                <div class="student-profile-field">
                    <label>Time zone</label>
                    <p class="student-profile-value">{{ $mockTopbarTimezone }}</p>
                </div>
                <div class="student-profile-field">
                    <label>Course</label>
                    <p class="student-profile-value">{{ $mockTopbarCourse }}</p>
                </div>
                <div class="student-profile-field student-profile-field--span">
                    <label>Program</label>
                    <p class="student-profile-value">{{ $mockTopbarProgram }}</p>
                </div>
            </div>
        </div>

        <div class="student-profile-modal__foot">
            <button type="button" class="btn ghost sm" id="student-profile-cancel">Close</button>
            <button type="submit" class="btn primary sm" id="student-profile-save" disabled>Save changes</button>
        </div>
        </form>
    </div>
</div>

<script>
(() => {
    const toggle = document.getElementById('topbar-notification-toggle');
    const panel = document.getElementById('topbar-notification-panel');
    if (!toggle || !panel) return;

    const open = () => {
        panel.hidden = false;
        panel.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
    };
    const close = () => {
        panel.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        window.setTimeout(() => {
            if (!panel.classList.contains('is-open')) panel.hidden = true;
        }, 160);
    };
    const isOpen = () => panel.classList.contains('is-open');

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        isOpen() ? close() : open();
    });
    panel.addEventListener('click', (e) => e.stopPropagation());
    document.addEventListener('click', () => { if (isOpen()) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen()) close(); });
})();

(() => {
    const toggle = document.getElementById('topbar-user-menu-toggle');
    const menu = document.getElementById('topbar-user-menu');
    if (!toggle || !menu) return;

    const open = () => {
        menu.hidden = false;
        menu.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
    };
    const close = () => {
        menu.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        window.setTimeout(() => {
            if (!menu.classList.contains('is-open')) menu.hidden = true;
        }, 150);
    };
    const isOpen = () => menu.classList.contains('is-open');

    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        isOpen() ? close() : open();
    });
    menu.addEventListener('click', (e) => e.stopPropagation());
    document.addEventListener('click', () => { if (isOpen()) close(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && isOpen()) close(); });
})();

(() => {
    const openBtn = document.getElementById('topbar-profile-open');
    const modal = document.getElementById('student-profile-modal');
    const closeBtn = document.getElementById('student-profile-close');
    const cancelBtn = document.getElementById('student-profile-cancel');
    const photoInput = document.getElementById('student-profile-photo-input');
    const photoPreview = document.getElementById('student-profile-photo-preview');
    const photoFallback = document.getElementById('student-profile-photo-fallback');
    const saveBtn = document.getElementById('student-profile-save');
    const topbarAvatarWrap = document.querySelector('.topbar-mock-avatar');
    const topbarAvatarImg = document.getElementById('topbar-avatar-img');
    const topbarAvatarFallback = document.getElementById('topbar-avatar-fallback');
    if (!openBtn || !modal) return;

    const open = () => {
        modal.hidden = false;
        requestAnimationFrame(() => modal.classList.add('is-open'));
    };
    const close = () => {
        modal.classList.remove('is-open');
        setTimeout(() => { if (!modal.classList.contains('is-open')) modal.hidden = true; }, 160);
    };

    openBtn.addEventListener('click', (e) => {
        e.preventDefault();
        open();
    });
    closeBtn?.addEventListener('click', close);
    cancelBtn?.addEventListener('click', close);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) close();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) close();
    });

    photoInput?.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = () => {
            if (!photoPreview || !photoFallback) return;
            const previewSrc = String(reader.result || '');
            photoPreview.src = previewSrc;
            photoPreview.style.display = 'block';
            photoFallback.style.display = 'none';
            // Keep topbar avatar in sync immediately after choosing image.
            if (topbarAvatarWrap) {
                if (topbarAvatarImg) {
                    topbarAvatarImg.src = previewSrc;
                } else {
                    if (topbarAvatarFallback) topbarAvatarFallback.style.display = 'none';
                    const img = document.createElement('img');
                    img.id = 'topbar-avatar-img';
                    img.alt = 'Profile photo';
                    img.src = previewSrc;
                    topbarAvatarWrap.appendChild(img);
                }
            }
            if (saveBtn) saveBtn.disabled = false;
        };
        reader.readAsDataURL(file);
    });

    // Allow selecting the same file again and still trigger "change"
    photoInput?.addEventListener('click', () => {
        photoInput.value = '';
    });
})();
</script>
