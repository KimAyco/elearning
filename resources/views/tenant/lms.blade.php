@extends('layouts.app')

@section('title', 'LMS - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'lms', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body">
            @php
                $teachable = $teachableSubjects ?? collect();
                $sessions = $weeklySessions ?? collect();

                $teacherClasses = $sessions
                    ->map(fn ($s) => $s->classGroup)
                    ->filter()
                    ->unique('id')
                    ->values();

                $classSubjectRows = $sessions
                    ->filter(fn ($s) => optional($s->classGroup)->id !== null && optional($s->subject)->id !== null)
                    ->map(fn ($s) => [
                        'class' => $s->classGroup,
                        'subject' => $s->subject,
                    ])
                    ->unique(fn ($row) => ($row['class']->id ?? 0) . '-' . ($row['subject']->id ?? 0))
                    ->sortBy([
                        fn ($row) => strtoupper($row['class']->name ?? ''),
                        fn ($row) => strtoupper($row['subject']->code ?? ''),
                    ])
                    ->values();
            @endphp

            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <span>LMS</span>
                </div>
                <h1>Teacher LMS</h1>
                <p>Manage your subjects and classes from one page.</p>
            </div>

            @if (session('status'))
                <div class="alert success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="card">
                <div class="card-header" style="align-items:center; gap:12px;">
                    <div>
                        <h2>Your Classes</h2>
                        <p style="margin:2px 0 0; font-size:0.85rem; color:var(--muted);">
                            All class groups where you are assigned as teacher.
                        </p>
                    </div>
                    <div style="margin-left:auto; display:flex; align-items:center; gap:8px;">
                        <span class="badge blue">{{ $teacherClasses->count() }} classes</span>
                        <a href="{{ url('/tenant/grades') }}" class="btn primary sm">Open Grade Workflow</a>
                    </div>
                </div>

                @if ($classSubjectRows->isEmpty())
                    <div class="empty-state" style="padding:24px;">
                        <p class="text-muted" style="margin:0;">No classes assigned yet. Once schedules are created for you, they will appear here.</p>
                    </div>
                @else
                    <div class="mdl-course-grid">
                        @foreach ($classSubjectRows as $row)
                            @php
                                $class = $row['class'];
                                $subject = $row['subject'];
                                $courseKey = 'cg' . ($class->id ?? 0) . '-sj' . ($subject->id ?? 0);
                                $courseUrl = url('/tenant/lms/classes/' . ($class->id ?? 0) . '/' . ($subject->id ?? 0));
                                $programCode = optional($class->program)->code ?? '';
                                $programName = optional($class->program)->name ? Str::limit($class->program->name, 26) : '';
                            @endphp
                            <a class="mdl-course-card"
                               href="{{ $courseUrl }}"
                               data-course-key="{{ $courseKey }}"
                               data-class-group-id="{{ $class->id ?? 0 }}"
                               data-subject-id="{{ $subject->id ?? 0 }}"
                               data-course-code="{{ $subject->code ?? 'SUBJ' }}"
                               data-course-title="{{ $subject->title ?? 'Course' }}"
                               data-course-section="{{ $class->name ?? 'Class group' }}"
                               data-program-code="{{ $programCode }}"
                               data-program-name="{{ $programName }}">
                                <div class="mdl-course-banner" aria-hidden="true"></div>
                                <button type="button" class="mdl-course-edit-btn" data-no-loader data-edit-course title="Edit course card appearance" aria-label="Edit course card appearance">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                </button>
                                <div class="mdl-course-card-top">
                                    <div class="mdl-course-badge">{{ $subject->code ?? 'SUBJ' }}</div>
                                    @if(optional($class->semester)->term_code)
                                        <div class="mdl-course-term">{{ $class->semester->term_code }}</div>
                                    @endif
                                </div>
                                <div class="mdl-course-card-title">{{ $subject->title ?? 'Course' }}</div>
                                <div class="mdl-course-card-sub">{{ $class->name ?? 'Class group' }}</div>
                                <div class="mdl-course-card-meta">
                                    @if(optional($class->program)->code)
                                        <span class="mdl-tag">{{ $class->program->code }}</span>
                                    @endif
                                    @if(optional($class->program)->name)
                                        <span class="mdl-tag">{{ Str::limit($class->program->name, 26) }}</span>
                                    @endif
                                </div>
                                <div class="mdl-course-card-foot">
                                    <span class="mdl-course-open">Open course</span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mdl-edit-course-modal" id="mdl-edit-course-modal" hidden>
                <div class="mdl-edit-course-backdrop" data-modal-close></div>
                <div class="mdl-edit-course-dialog" role="dialog" aria-modal="true" aria-labelledby="mdl-edit-course-title">
                    <div class="mdl-edit-course-head">
                        <h3 id="mdl-edit-course-title">Edit Course Card</h3>
                        <button type="button" class="mdl-modal-close-btn" data-modal-close>&times;</button>
                    </div>
                    <div class="mdl-edit-course-body">
                        <div class="mdl-edit-form">
                            <section>
                                <h4>Basic course info</h4>
                                <p id="mdl-edit-course-name" class="mdl-edit-muted">Course</p>
                            </section>
                            <section>
                                <h4>Visual customization</h4>
                                <label>Course accent color</label>
                                <div class="mdl-color-row">
                                    <input type="color" id="mdl-edit-color-picker" value="#dbeafe">
                                    <input type="text" id="mdl-edit-color-text" placeholder="#22c55e or rgb(34,197,94)">
                                </div>
                                <label>Course card background image</label>
                                <input type="file" id="mdl-edit-image-input" accept="image/*">
                                <button type="button" id="mdl-edit-remove-image" class="btn ghost sm">Remove image</button>
                            </section>
                            <section class="mdl-edit-actions">
                                <button type="button" id="mdl-edit-reset" class="btn ghost sm">Reset to default</button>
                                <button type="button" id="mdl-edit-save" class="btn primary sm">Save changes</button>
                            </section>
                        </div>
                        <div class="mdl-preview-wrap">
                            <h4>Preview</h4>
                            <article class="mdl-course-card mdl-course-card--preview" id="mdl-course-preview-card">
                                <div class="mdl-course-banner" id="mdl-course-preview-banner" aria-hidden="true"></div>
                                <div class="mdl-course-card-top">
                                    <div class="mdl-course-badge" id="mdl-preview-code">SUBJ</div>
                                    <div class="mdl-course-term">TERM</div>
                                </div>
                                <div class="mdl-course-card-title" id="mdl-preview-title">Course Title</div>
                                <div class="mdl-course-card-sub" id="mdl-preview-section">Section</div>
                                <div class="mdl-course-card-meta">
                                    <span class="mdl-tag" id="mdl-preview-program-code">PRG</span>
                                    <span class="mdl-tag" id="mdl-preview-program-name">Program Name</span>
                                </div>
                                <div class="mdl-course-card-foot">
                                    <span class="mdl-course-open">Open course</span>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@include('tenant.partials.student-footer')
@endsection

@push('styles')
<style>
    .mdl-course-grid {
        padding: 14px 16px 18px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 12px;
    }
    .mdl-course-card {
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        gap: 8px;
        border-radius: 14px;
        border: 1px solid var(--border);
        background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
        padding: 14px;
        box-shadow: 0 1px 3px rgba(15,23,42,0.05);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        position: relative;
        overflow: hidden;
        padding-top: 54px;
    }
    .mdl-course-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 26px rgba(15,23,42,0.12);
        border-color: #93c5fd;
    }
    .mdl-course-card:focus-visible { outline: 2px solid #3b82f6; outline-offset: 2px; }
    .mdl-course-banner {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 42px;
        background: linear-gradient(135deg, #e2e8f0 0%, #dbeafe 100%);
        border-bottom: 1px solid #dbe4ef;
        z-index: 0;
        overflow: hidden;
    }
    .mdl-course-banner::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15,23,42,0.10) 0%, rgba(15,23,42,0.18) 100%);
        opacity: 0;
        transition: opacity .2s ease;
    }
    .mdl-course-card.has-image .mdl-course-banner::after { opacity: 1; }
    .mdl-course-card.has-image .mdl-course-badge,
    .mdl-course-card.has-image .mdl-course-term { position: relative; z-index: 2; }
    .mdl-course-edit-btn {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 3;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,0.65);
        background: rgba(255,255,255,0.9);
        color: #334155;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    .mdl-course-edit-btn:hover { border-color: #93c5fd; color: #1d4ed8; }

    .mdl-course-card-top { display:flex; align-items:center; justify-content:space-between; gap:10px; position: relative; z-index: 1; }
    .mdl-course-badge {
        border-radius: 999px;
        border: 1px solid #bfdbfe;
        padding: 3px 10px;
        background: #eff6ff;
        color: #1d4ed8;
        font-weight: 800;
        font-size: 0.78rem;
        letter-spacing: 0.03em;
    }
    .mdl-course-term {
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        padding: 3px 10px;
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 0.78rem;
    }
    .mdl-course-card-title { font-weight: 900; color: #0f172a; line-height: 1.25; }
    .mdl-course-card-sub { color: #64748b; font-size: 0.9rem; }
    .mdl-course-card-meta { display:flex; flex-wrap:wrap; gap:6px; margin-top: 2px; }
    .mdl-tag {
        border-radius: 999px;
        border: 1px solid var(--border);
        padding: 2px 8px;
        background: #f1f5f9;
        font-size: 0.75rem;
        color: #475569;
        font-weight: 700;
    }
    .mdl-course-card-foot {
        margin-top: 6px;
        display:flex; align-items:center; justify-content:space-between;
        color: #1d4ed8;
        font-weight: 800;
        font-size: 0.85rem;
        position: relative;
        z-index: 1;
    }

    /* [hidden] must win: do not set display:flex on the base rule or the modal stays visible */
    .mdl-edit-course-modal[hidden] {
        display: none !important;
    }
    .mdl-edit-course-modal:not([hidden]) {
        position: fixed;
        inset: 0;
        z-index: 1500;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 18px;
    }
    .mdl-edit-course-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15,23,42,0.45);
        backdrop-filter: blur(3px);
    }
    .mdl-edit-course-dialog {
        position: relative;
        z-index: 1;
        width: min(860px, 100%);
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 22px 48px rgba(15,23,42,0.28);
        overflow: hidden;
    }
    .mdl-edit-course-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 18px;
        border-bottom: 1px solid #eef2f7;
    }
    .mdl-edit-course-head h3 { margin: 0; font-size: 1.08rem; color: #0f172a; }
    .mdl-modal-close-btn { border: none; background: transparent; font-size: 1.5rem; color: #94a3b8; cursor: pointer; }
    .mdl-edit-course-body {
        padding: 16px 18px 18px;
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 18px;
    }
    .mdl-edit-form section { margin-bottom: 14px; }
    .mdl-edit-form h4, .mdl-preview-wrap h4 { margin: 0 0 8px; font-size: 0.86rem; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
    .mdl-edit-muted { margin: 0; font-size: 0.9rem; color: #334155; font-weight: 700; }
    .mdl-edit-form label { display:block; margin: 8px 0 5px; font-size: 0.78rem; font-weight: 700; color: #475569; }
    .mdl-color-row { display: grid; grid-template-columns: 68px 1fr; gap: 8px; }
    .mdl-edit-form input[type="text"], .mdl-edit-form input[type="file"], .mdl-edit-form input[type="color"] {
        width: 100%;
        border: 1px solid #dbe3ee;
        border-radius: 10px;
        padding: 8px 10px;
        font-size: 0.84rem;
        background: #fff;
    }
    .mdl-edit-form input[type="color"] { padding: 3px; height: 40px; }
    .mdl-edit-actions { display: flex; justify-content: flex-end; gap: 8px; margin-bottom: 0 !important; }
    .mdl-course-card--preview { cursor: default; pointer-events: none; }
    .mdl-course-card--preview .mdl-course-edit-btn { display:none; }
    @media (max-width: 840px) {
        .mdl-edit-course-body { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const cards = Array.from(document.querySelectorAll('.mdl-course-card[data-course-key]'));
    if (!cards.length) return;

    const serverState = @json($courseCardCustomizations ?? new \stdClass());
    const storageKey = 'teacher-course-card-customizations-v2';
    let localState = {};
    try { localState = JSON.parse(localStorage.getItem(storageKey) || '{}') || {}; } catch (e) { localState = {}; }
    // Prefer server values, but keep local overrides for the current browser.
    let state = { ...(serverState || {}), ...(localState || {}) };

    const modal = document.getElementById('mdl-edit-course-modal');
    const editName = document.getElementById('mdl-edit-course-name');
    const colorPicker = document.getElementById('mdl-edit-color-picker');
    const colorText = document.getElementById('mdl-edit-color-text');
    const imageInput = document.getElementById('mdl-edit-image-input');
    const removeImageBtn = document.getElementById('mdl-edit-remove-image');
    const saveBtn = document.getElementById('mdl-edit-save');
    const resetBtn = document.getElementById('mdl-edit-reset');
    const previewCard = document.getElementById('mdl-course-preview-card');
    const previewBanner = document.getElementById('mdl-course-preview-banner');
    const previewCode = document.getElementById('mdl-preview-code');
    const previewTitle = document.getElementById('mdl-preview-title');
    const previewSection = document.getElementById('mdl-preview-section');
    const previewProgramCode = document.getElementById('mdl-preview-program-code');
    const previewProgramName = document.getElementById('mdl-preview-program-name');

    let activeCard = null;
    let pendingImageData = '';
    // Track the last valid custom color. Empty string means "default theme".
    let lastValidColor = '';

    const isValidColor = (v) => /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(v) || /^rgb\(\s*(\d{1,3}\s*,){2}\s*\d{1,3}\s*\)$/i.test(v);
    const normalizeColorText = (v) => {
        let s = String(v || '').trim();
        if (!s) return '';
        // Allow users to type hex without '#', e.g. "dbeafe" or "0f0".
        if (/^[0-9a-f]{3}([0-9a-f]{3})?$/i.test(s)) s = '#' + s;
        return s;
    };

    const applyCardTheme = (card, cfg) => {
        const banner = card.querySelector('.mdl-course-banner');
        if (!banner) return;
        const image = (cfg?.image || '').trim();
        const color = (cfg?.color || '').trim();

        if (image) {
            banner.style.background = `linear-gradient(180deg, rgba(15,23,42,0.08), rgba(15,23,42,0.28)), url("${image}") center/cover no-repeat`;
            card.classList.add('has-image');
        } else if (color && isValidColor(color)) {
            banner.style.background = `linear-gradient(135deg, color-mix(in srgb, ${color} 70%, #ffffff), ${color})`;
            card.classList.remove('has-image');
        } else {
            banner.style.background = 'linear-gradient(135deg, #e2e8f0 0%, #dbeafe 100%)';
            card.classList.remove('has-image');
        }
    };

    const applyPreview = (cfg) => {
        const image = (cfg?.image || '').trim();
        const color = (cfg?.color || '').trim();
        if (image) {
            previewBanner.style.background = `linear-gradient(180deg, rgba(15,23,42,0.08), rgba(15,23,42,0.28)), url("${image}") center/cover no-repeat`;
            previewCard.classList.add('has-image');
        } else if (color && isValidColor(color)) {
            previewBanner.style.background = `linear-gradient(135deg, color-mix(in srgb, ${color} 70%, #ffffff), ${color})`;
            previewCard.classList.remove('has-image');
        } else {
            previewBanner.style.background = 'linear-gradient(135deg, #e2e8f0 0%, #dbeafe 100%)';
            previewCard.classList.remove('has-image');
        }
    };

    cards.forEach((card) => {
        const cfg = state[card.dataset.courseKey] || {};
        applyCardTheme(card, cfg);
    });

    const openModal = (card) => {
        activeCard = card;
        pendingImageData = '';
        const key = card.dataset.courseKey;
        const cfg = state[key] || {};
        editName.textContent = `${card.dataset.courseCode} · ${card.dataset.courseSection}`;
        const safeColor = isValidColor(cfg.color || '') ? cfg.color : '';
        lastValidColor = safeColor;
        colorText.value = safeColor;
        colorPicker.value = /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(safeColor || '') ? safeColor : '#dbeafe';
        imageInput.value = '';

        previewCode.textContent = card.dataset.courseCode || 'SUBJ';
        previewTitle.textContent = card.dataset.courseTitle || 'Course Title';
        previewSection.textContent = card.dataset.courseSection || 'Section';
        previewProgramCode.textContent = card.dataset.programCode || 'PRG';
        previewProgramName.textContent = card.dataset.programName || 'Program';
        applyPreview({ ...cfg, color: safeColor });

        modal.hidden = false;
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.hidden = true;
        document.body.style.overflow = '';
        activeCard = null;
    };

    cards.forEach((card) => {
        card.querySelector('[data-edit-course]')?.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            openModal(card);
        });
    });

    colorPicker.addEventListener('input', () => {
        colorText.value = colorPicker.value;
        lastValidColor = colorPicker.value;
        applyPreview({ color: lastValidColor, image: pendingImageData || (state[activeCard?.dataset.courseKey || '']?.image || '') });
    });
    colorText.addEventListener('input', () => {
        const candidate = normalizeColorText(colorText.value);
        const image = pendingImageData || (state[activeCard?.dataset.courseKey || '']?.image || '');
        if (candidate && isValidColor(candidate)) {
            lastValidColor = candidate;
            // If it's a hex color, keep the <input type="color"> in sync.
            if (/^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(candidate)) colorPicker.value = candidate;
        }
        // If the text is temporarily invalid while typing, keep the last valid preview.
        applyPreview({ color: lastValidColor, image });
    });
    imageInput.addEventListener('change', () => {
        const file = imageInput.files?.[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = () => {
            pendingImageData = String(reader.result || '');
            applyPreview({ color: colorText.value.trim(), image: pendingImageData });
        };
        reader.readAsDataURL(file);
    });
    removeImageBtn.addEventListener('click', () => {
        pendingImageData = '';
        if (activeCard) {
            const key = activeCard.dataset.courseKey;
            const cfg = state[key] || {};
            cfg.image = '';
            cfg.color = isValidColor(cfg.color || '') ? cfg.color : lastValidColor;
            state[key] = cfg;
            localStorage.setItem(storageKey, JSON.stringify(state));
            applyCardTheme(activeCard, cfg);
            applyPreview(cfg);

            const classGroupId = activeCard.dataset.classGroupId;
            const subjectId = activeCard.dataset.subjectId;
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const saveUrl = `/tenant/lms/classes/${classGroupId}/${subjectId}/card-appearance`;
            fetch(saveUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                },
                body: JSON.stringify({
                    accent_color: cfg.color || '',
                    image_data_url: '',
                }),
            }).then(async (res) => {
                if (!res.ok) console.error('Failed to save card appearance (remove image)', res.status, await res.text());
            }).catch((err) => console.error('Failed to save card appearance (remove image)', err));
        }
    });
    saveBtn.addEventListener('click', () => {
        if (!activeCard) return;
        const key = activeCard.dataset.courseKey;
        // Cache ids before closeModal() (closeModal() clears activeCard).
        const classGroupId = activeCard.dataset.classGroupId;
        const subjectId = activeCard.dataset.subjectId;
        const normalized = normalizeColorText(colorText.value);
        const nextColor = isValidColor(normalized) ? normalized : lastValidColor;
        const next = {
            color: nextColor,
            image: pendingImageData || (state[key]?.image || ''),
        };
        state[key] = next;
        localStorage.setItem(storageKey, JSON.stringify(state));
        applyCardTheme(activeCard, next);
        lastValidColor = nextColor;
        closeModal();

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const saveUrl = `/tenant/lms/classes/${classGroupId}/${subjectId}/card-appearance`;
        fetch(saveUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
            },
            body: JSON.stringify({
                accent_color: next.color || '',
                image_data_url: next.image || '',
            }),
        }).then(async (res) => {
            if (!res.ok) console.error('Failed to save card appearance (save changes)', res.status, await res.text());
        }).catch((err) => console.error('Failed to save card appearance (save changes)', err));
    });
    resetBtn.addEventListener('click', () => {
        if (!activeCard) return;
        const key = activeCard.dataset.courseKey;
        const classGroupId = activeCard.dataset.classGroupId;
        const subjectId = activeCard.dataset.subjectId;
        delete state[key];
        localStorage.setItem(storageKey, JSON.stringify(state));
        applyCardTheme(activeCard, {});
        colorText.value = '';
        colorPicker.value = '#dbeafe';
        pendingImageData = '';
        lastValidColor = '';
        applyPreview({});

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const saveUrl = `/tenant/lms/classes/${classGroupId}/${subjectId}/card-appearance`;
        fetch(saveUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
            },
            body: JSON.stringify({
                accent_color: '',
                image_data_url: '',
            }),
        }).then(async (res) => {
            if (!res.ok) console.error('Failed to save card appearance (reset)', res.status, await res.text());
        }).catch((err) => console.error('Failed to save card appearance (reset)', err));
    });

    modal.querySelectorAll('[data-modal-close]').forEach((el) => el.addEventListener('click', closeModal));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) closeModal();
    });
})();
</script>
@endpush

