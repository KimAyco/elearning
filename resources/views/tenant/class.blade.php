@extends('layouts.app')

@section('title', 'Class - School Portal')

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => 'class', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body">
            @php
                $classSubjectRows = collect($classSubjectRows ?? []);
            @endphp

            <div class="page-header">
                <div class="breadcrumb">
                    <a href="{{ url('/tenant/dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">&gt;</span>
                    <span>Classes</span>
                </div>
                <h1>Your Classes</h1>
                <p>All active subjects you are currently enrolled in.</p>
            </div>

            <div class="card mdl-course-overview-card">
                <div class="card-header mdl-course-overview-head">
                    <div class="mdl-overview-head-main">
                        <div>
                            <h2>Current Classes</h2>
                            <p class="mdl-course-overview-sub">
                                Based on your confirmed enrollments this term.
                            </p>
                        </div>
                        <span class="badge blue">{{ $classSubjectRows->count() }} subjects</span>
                    </div>
                    <div class="mdl-overview-controls" role="group" aria-label="Course overview controls">
                        <select id="mdl-status-filter" class="mdl-control mdl-control--select" aria-label="Filter by status">
                            <option value="all">All status</option>
                            <option value="in-progress">In progress</option>
                        </select>
                        <div class="mdl-control mdl-control--search">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input id="mdl-course-search" type="text" placeholder="Search">
                        </div>
                        <select id="mdl-sort-filter" class="mdl-control mdl-control--select" aria-label="Sort courses">
                            <option value="title-asc">Sort by course name</option>
                            <option value="title-desc">Sort by title Z-A</option>
                            <option value="code-asc">Sort by course code</option>
                        </select>
                        <div class="mdl-viewtype" id="mdl-viewtype">
                            <button type="button" class="mdl-viewtype-trigger" id="mdl-viewtype-trigger" aria-haspopup="true" aria-expanded="false">
                                <span class="mdl-viewtype-trigger-label" id="mdl-viewtype-trigger-label">Card</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </button>
                            <div class="mdl-viewtype-menu" id="mdl-viewtype-menu" role="menu" hidden>
                                <button type="button" class="mdl-viewtype-item is-active" data-view="card" role="menuitem">
                                    <span>Card</span><span class="mdl-viewtype-check">✔</span>
                                </button>
                                <button type="button" class="mdl-viewtype-item" data-view="list" role="menuitem">
                                    <span>List</span><span class="mdl-viewtype-check">✔</span>
                                </button>
                                <button type="button" class="mdl-viewtype-item" data-view="summary" role="menuitem">
                                    <span>Summary</span><span class="mdl-viewtype-check">✔</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($classSubjectRows->isEmpty())
                    <div class="empty-state" style="padding:24px;">
                        <p class="text-muted" style="margin:0;">No active classes yet. Once your enrollment is confirmed and tuition is cleared, your subjects will appear here.</p>
                    </div>
                @else
                    <div class="mdl-course-grid" id="mdl-course-grid">
                        @foreach ($classSubjectRows as $row)
                            @php
                                $courseKey = 'cg' . ($row['class_group_id'] ?? 0) . '-sj' . ($row['subject_id'] ?? 0);
                                $courseUrl = url('/tenant/classes/' . ($row['class_group_id'] ?? 0) . '/' . ($row['subject_id'] ?? 0));
                            @endphp
                            <article
                                class="mdl-course-card"
                                data-course-key="{{ $courseKey }}"
                                data-title="{{ strtolower((string) ($row['subject_title'] ?? '')) }}"
                                data-code="{{ strtolower((string) ($row['subject_code'] ?? '')) }}"
                                data-status="in-progress"
                            >
                                <div class="mdl-course-banner" aria-hidden="true"></div>

                                <div class="mdl-course-card-top">
                                    <div class="mdl-course-badge">{{ $row['subject_code'] }}</div>
                                    @if (!empty($row['term_code']))
                                        <div class="mdl-course-term">{{ $row['term_code'] }}</div>
                                    @endif
                                </div>
                                <h3 class="mdl-course-card-title">{{ $row['subject_title'] }}</h3>
                                <div class="mdl-course-card-sub">{{ $row['class_label'] }}</div>
                                <div class="mdl-course-card-meta">
                                    <span class="mdl-tag">In progress</span>
                                    <span class="mdl-tag">0% complete</span>
                                </div>
                                <div class="mdl-course-card-foot">
                                    <a class="mdl-course-open" href="{{ $courseUrl }}">Open course</a>
                                    <a class="mdl-course-open-arrow" href="{{ $courseUrl }}" aria-label="Open {{ $row['subject_title'] }}">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mdl-course-list" id="mdl-course-list" hidden></div>
                    <div class="mdl-course-summary" id="mdl-course-summary" hidden></div>
                @endif
            </div>
        </main>
    </div>
</div>
@endsection

@push('styles')
<style>
    .mdl-course-overview-card {
        border-radius: 16px;
        border: 1px solid color-mix(in srgb, var(--admin-primary, #334155) 12%, #e2e8f0);
        box-shadow: 0 1px 5px rgba(15,23,42,0.05);
    }
    .mdl-course-overview-head {
        align-items: stretch;
        gap: 14px;
        flex-direction: column;
    }
    .mdl-overview-head-main {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        width: 100%;
    }
    .mdl-course-overview-sub {
        margin: 2px 0 0;
        font-size: 0.85rem;
        color: var(--muted);
    }
    .mdl-overview-controls {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        flex-wrap: wrap;
    }
    .mdl-control {
        height: 36px;
        border-radius: 10px;
        border: 1px solid #dbe3ee;
        background: #ffffff;
        color: #334155;
        font-size: 0.82rem;
        display: inline-flex;
        align-items: center;
        transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
    }
    .mdl-control:focus-within,
    .mdl-control:focus {
        border-color: var(--admin-primary, #334155);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary, #334155) 14%, transparent);
        outline: none;
    }
    .mdl-control--search {
        gap: 7px;
        padding: 0 10px;
        min-width: 240px;
        flex: 1 1 340px;
    }
    .mdl-control--search svg { color: #94a3b8; }
    .mdl-control--search input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 0.82rem;
        width: 100%;
        color: #0f172a;
    }
    .mdl-control--select {
        padding: 0 10px;
        min-width: 160px;
    }
    .mdl-viewtype { position: relative; }
    .mdl-viewtype-trigger {
        height: 36px;
        min-width: 120px;
        border-radius: 10px;
        border: 1px solid #dbe3ee;
        background: #ffffff;
        color: #334155;
        padding: 0 10px;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .16s ease, border-color .16s ease, box-shadow .16s ease, color .16s ease;
    }
    .mdl-viewtype-trigger:hover { background: #f8fafc; }
    .mdl-viewtype.is-open .mdl-viewtype-trigger {
        color: var(--admin-primary, #334155);
        border-color: color-mix(in srgb, var(--admin-primary, #334155) 42%, #cbd5e1);
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--admin-primary, #334155) 14%, transparent);
    }
    .mdl-viewtype-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 6px);
        width: 170px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.14);
        padding: 8px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        z-index: 20;
        opacity: 0;
        transform: translateY(-6px);
        pointer-events: none;
        transition: opacity .16s ease, transform .16s ease;
    }
    .mdl-viewtype.is-open .mdl-viewtype-menu {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
    .mdl-viewtype-item {
        border: 0;
        background: transparent;
        border-radius: 8px;
        padding: 8px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        font-size: 0.82rem;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        text-align: left;
    }
    .mdl-viewtype-item:hover { background: #f8fafc; color: var(--admin-primary, #334155); }
    .mdl-viewtype-item.is-active {
        background: color-mix(in srgb, var(--admin-primary, #334155) 12%, #ffffff);
        color: var(--admin-primary, #334155);
    }
    .mdl-viewtype-check { opacity: 0; font-size: 0.78rem; }
    .mdl-viewtype-item.is-active .mdl-viewtype-check { opacity: 1; }

    .mdl-course-grid {
        padding: 14px 16px 18px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 14px;
    }
    .mdl-course-card {
        border-radius: 14px;
        border: 1px solid var(--border);
        background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
        padding: 14px;
        box-shadow: 0 1px 3px rgba(15,23,42,0.05);
        transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        position: relative;
        overflow: hidden;
        padding-top: 54px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .mdl-course-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 26px rgba(15,23,42,0.12);
        border-color: #93c5fd;
    }

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
    .mdl-course-card-title {
        font-weight: 900;
        color: #0f172a;
        line-height: 1.3;
    }
    .mdl-course-card-sub {
        color: #64748b;
        font-size: 0.9rem;
    }
    .mdl-course-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 2px;
    }

    .mdl-tag {
        border-radius: 999px;
        border: 1px solid var(--border);
        padding: 2px 8px;
        background: #f1f5f9;
        font-size: 0.75rem;
        color: #475569;
        font-weight: 700;
    }
    .mdl-course-meta-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        border: 1px solid #bbf7d0;
        background: #dcfce7;
        color: #15803d;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 2px 8px;
    }
    .mdl-course-meta-text {
        font-size: 0.76rem;
        color: #64748b;
    }
    .mdl-course-card-foot {
        margin-top: 6px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        color: #1d4ed8;
        font-weight: 800;
        font-size: 0.85rem;
        position: relative;
        z-index: 1;
    }
    .mdl-course-open {
        color: #1d4ed8;
        font-weight: 800;
        font-size: 0.84rem;
        text-decoration: none;
    }
    .mdl-course-open-arrow {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }
    .mdl-course-open-arrow:hover { background: #eff6ff; }

    .mdl-course-list,
    .mdl-course-summary {
        padding: 10px 16px 16px;
    }
    .mdl-course-list-item {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        background: #ffffff;
    }
    .mdl-course-list-item-title { font-weight: 700; color: #0f172a; }
    .mdl-course-list-item-sub { font-size: 0.8rem; color: #64748b; }
    .mdl-course-summary-box {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        padding: 12px;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }
    .mdl-course-summary-stat {
        border: 1px solid #f1f5f9;
        border-radius: 10px;
        padding: 10px;
        background: #fafbfc;
    }
    .mdl-course-summary-stat h4 {
        margin: 0;
        font-size: 0.76rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .mdl-course-summary-stat p {
        margin: 6px 0 0;
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
    }

    @media (max-width: 900px) {
        .mdl-overview-head-main { align-items: center; }
        .mdl-overview-controls { width: 100%; }
        .mdl-control--search { min-width: 200px; flex: 1 1 260px; }
    }
    @media (max-width: 640px) {
        .mdl-overview-head-main {
            flex-direction: column;
            align-items: flex-start;
        }
        .mdl-overview-controls { gap: 8px; align-items: stretch; }
        .mdl-control--search,
        .mdl-control--select,
        .mdl-viewtype-trigger { width: 100%; }
        .mdl-course-summary-box { grid-template-columns: 1fr; }
        .mdl-course-list-item { grid-template-columns: 1fr; }
    }
</style>
@endpush

@push('scripts')
<script>
(() => {
    const grid = document.getElementById('mdl-course-grid');
    const listWrap = document.getElementById('mdl-course-list');
    const summaryWrap = document.getElementById('mdl-course-summary');
    const search = document.getElementById('mdl-course-search');
    const statusFilter = document.getElementById('mdl-status-filter');
    const sortFilter = document.getElementById('mdl-sort-filter');
    const viewWrap = document.getElementById('mdl-viewtype');
    const viewTrigger = document.getElementById('mdl-viewtype-trigger');
    const viewLabel = document.getElementById('mdl-viewtype-trigger-label');
    const viewMenu = document.getElementById('mdl-viewtype-menu');
    if (!grid || !listWrap || !summaryWrap || !search || !statusFilter || !sortFilter || !viewWrap || !viewTrigger || !viewLabel || !viewMenu) return;

    const courseCardCustomizations = @json($courseCardCustomizations ?? new \stdClass());
    const isValidColor = (v) => /^#([0-9a-f]{3}|[0-9a-f]{6})$/i.test(v) || /^rgb\(\s*(\d{1,3}\s*,){2}\s*\d{1,3}\s*\)$/i.test(v);

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

    // Apply teacher-selected appearance to student class cards.
    Array.from(grid.querySelectorAll('.mdl-course-card[data-course-key]')).forEach((card) => {
        const cfg = courseCardCustomizations?.[card.dataset.courseKey] || {};
        applyCardTheme(card, cfg);
    });

    const getCards = () => Array.from(grid.querySelectorAll('.mdl-course-card'));

    const applyFilters = () => {
        const q = search.value.trim().toLowerCase();
        const status = statusFilter.value;
        const sort = sortFilter.value;
        const cards = getCards();

        cards.forEach((card) => {
            const title = card.dataset.title || '';
            const code = card.dataset.code || '';
            const st = card.dataset.status || '';
            const passQ = !q || title.includes(q) || code.includes(q);
            const passS = status === 'all' || st === status;
            card.style.display = (passQ && passS) ? '' : 'none';
        });

        const visible = cards.filter((card) => card.style.display !== 'none');
        visible.sort((a, b) => {
            const ta = (a.dataset.title || '').toLowerCase();
            const tb = (b.dataset.title || '').toLowerCase();
            const ca = (a.dataset.code || '').toLowerCase();
            const cb = (b.dataset.code || '').toLowerCase();
            if (sort === 'title-desc') return tb.localeCompare(ta);
            if (sort === 'code-asc') return ca.localeCompare(cb);
            return ta.localeCompare(tb);
        });
        visible.forEach((el) => grid.appendChild(el));

        renderList(visible);
        renderSummary(visible);
    };

    const renderList = (visibleCards) => {
        listWrap.innerHTML = '';
        visibleCards.forEach((card) => {
            const code = card.querySelector('.mdl-course-badge')?.textContent?.trim() || 'SUBJ';
            const title = card.querySelector('.mdl-course-card-title')?.textContent?.trim() || 'Untitled';
            const sub = card.querySelector('.mdl-course-card-sub')?.textContent?.trim() || '';
            const href = card.querySelector('.mdl-course-open')?.getAttribute('href') || '#';
            listWrap.insertAdjacentHTML('beforeend', `
                <article class="mdl-course-list-item">
                    <span class="mdl-course-badge">${code}</span>
                    <div>
                        <div class="mdl-course-list-item-title">${title}</div>
                        <div class="mdl-course-list-item-sub">${sub}</div>
                    </div>
                    <a class="mdl-course-open" href="${href}">Open course</a>
                </article>
            `);
        });
    };

    const renderSummary = (visibleCards) => {
        const total = visibleCards.length;
        const inProgress = total;
        const completion = total > 0 ? '0%' : '—';
        summaryWrap.innerHTML = `
            <div class="mdl-course-summary-box">
                <div class="mdl-course-summary-stat"><h4>Total courses</h4><p>${total}</p></div>
                <div class="mdl-course-summary-stat"><h4>In progress</h4><p>${inProgress}</p></div>
                <div class="mdl-course-summary-stat"><h4>Avg completion</h4><p>${completion}</p></div>
            </div>
        `;
    };

    const setView = (view) => {
        grid.hidden = view !== 'card';
        listWrap.hidden = view !== 'list';
        summaryWrap.hidden = view !== 'summary';
        viewLabel.textContent = view.charAt(0).toUpperCase() + view.slice(1);
    };

    const closeViewMenu = () => {
        viewWrap.classList.remove('is-open');
        viewTrigger.setAttribute('aria-expanded', 'false');
        setTimeout(() => {
            if (!viewWrap.classList.contains('is-open')) viewMenu.hidden = true;
        }, 160);
    };
    const openViewMenu = () => {
        viewMenu.hidden = false;
        viewWrap.classList.add('is-open');
        viewTrigger.setAttribute('aria-expanded', 'true');
    };

    viewTrigger.addEventListener('click', (e) => {
        e.stopPropagation();
        if (viewWrap.classList.contains('is-open')) closeViewMenu();
        else openViewMenu();
    });
    viewMenu.addEventListener('click', (e) => {
        e.stopPropagation();
        const item = e.target.closest('.mdl-viewtype-item');
        if (!item) return;
        viewMenu.querySelectorAll('.mdl-viewtype-item').forEach((btn) => btn.classList.remove('is-active'));
        item.classList.add('is-active');
        setView(item.dataset.view || 'card');
        closeViewMenu();
    });
    document.addEventListener('click', () => { if (viewWrap.classList.contains('is-open')) closeViewMenu(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && viewWrap.classList.contains('is-open')) closeViewMenu(); });

    [search, statusFilter, sortFilter].forEach((el) => el.addEventListener('input', applyFilters));
    [statusFilter, sortFilter].forEach((el) => el.addEventListener('change', applyFilters));

    applyFilters();
    setView('card');
})();
</script>
@endpush

