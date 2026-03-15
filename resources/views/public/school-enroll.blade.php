@extends('layouts.app')

@section('title', $school->name . ' â€” Enrollment')

@section('content')
<div class="schools-page schools-detail-page @if($school->theme) school-theme-{{ $school->theme }} @endif">

    <header class="schools-nav">
        <div class="schools-nav-inner">
            <div class="schools-brand">
                <span class="schools-logo">E</span>
                <span class="schools-brand-name">EduPlatform</span>
            </div>
            <div class="schools-nav-actions">
                <a href="{{ url('/') }}" class="btn secondary sm">All schools</a>
            </div>
        </div>
    </header>

    <main class="schools-main">
        @if (session('error'))
            <div class="alert error schools-alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('status'))
            <div class="alert success schools-alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <section class="school-detail-card">
            <div class="school-cover" aria-hidden="true" @if($school->cover_image_url) style="background-image: linear-gradient(120deg, rgba(15,23,42,0.15), rgba(37,99,235,0.35)), url('{{ $school->cover_image_url }}');" @endif></div>

            <header class="school-detail-header">
                <div class="school-avatar">
                    @if ($school->logo_url)
                        <img src="{{ $school->logo_url }}" alt="" width="112" height="112" class="school-avatar-img">
                    @else
                        <span class="school-avatar-initial">
                            {{ strtoupper(mb_substr($school->name, 0, 1)) }}
                        </span>
                    @endif
                </div>
                <div class="school-heading">
                    <div class="school-heading-top">
                        <h1>{{ $school->name }}</h1>
                        <span class="school-code-pill">{{ $school->school_code }}</span>
                    </div>
                    @if ($school->short_description)
                        <p class="school-location">{{ $school->short_description }}</p>
                    @else
                        <p class="school-location">A modern public college serving learners across the region.</p>
                    @endif
                </div>
                <div class="school-header-actions">
                    <a href="{{ route('enroll.step1', ['school_code' => $school->school_code]) }}" class="btn primary lg">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                        </svg>
                        Enroll now
                    </a>
                    <a href="{{ route('school.login', ['school_code' => $school->school_code]) }}" class="btn secondary lg">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                        Sign in
                    </a>
                </div>
            </header>

            <div class="school-detail-body">
                <div class="school-detail-main">
                    @php $profile = $school->profile; @endphp
                    <h2>Welcome to {{ $school->name }}</h2>
                    @if ($profile && $profile->intro)
                        <p class="school-intro">{{ $profile->intro }}</p>
                    @else
                        <p class="school-intro">
                            A <span class="school-highlight">student‑centered</span> institution offering
                            <span class="school-highlight">industry‑aligned programs</span>, supportive faculty, and a
                            modern digital campus powered by EduPlatform.
                        </p>
                    @endif

                    <div class="school-tags">
                        <span class="school-tag primary">{{ $profile && $profile->tag_primary ? $profile->tag_primary : 'Public higher‑education institution' }}</span>
                        <span class="school-tag neutral">{{ $profile && $profile->tag_neutral ? $profile->tag_neutral : 'Digital campus ready' }}</span>
                        <span class="school-tag accent">{{ $profile && $profile->tag_accent ? $profile->tag_accent : 'Scholarship & support programs' }}</span>
                    </div>

                    <div class="school-facts-grid">
                        @php
                            $facts = [
                                [
                                    'label' => $profile->fact1_label ?? null,
                                    'value' => $profile->fact1_value ?? null,
                                    'caption' => $profile->fact1_caption ?? null,
                                    'fallback_label' => 'Estimated founded',
                                    'fallback_value' => '1995',
                                    'fallback_caption' => 'Decades of academic excellence',
                                ],
                                [
                                    'label' => $profile->fact2_label ?? null,
                                    'value' => $profile->fact2_value ?? null,
                                    'caption' => $profile->fact2_caption ?? null,
                                    'fallback_label' => 'Approx. students',
                                    'fallback_value' => '8,500+',
                                    'fallback_caption' => 'Undergraduate & graduate learners',
                                ],
                                [
                                    'label' => $profile->fact3_label ?? null,
                                    'value' => $profile->fact3_value ?? null,
                                    'caption' => $profile->fact3_caption ?? null,
                                    'fallback_label' => 'Programs offered',
                                    'fallback_value' => '60+',
                                    'fallback_caption' => 'Across colleges and departments',
                                ],
                            ];
                        @endphp
                        @foreach ($facts as $fact)
                            <div class="school-fact">
                                <div class="fact-label">{{ $fact['label'] ?: $fact['fallback_label'] }}</div>
                                <div class="fact-value">{{ $fact['value'] ?: $fact['fallback_value'] }}</div>
                                <div class="fact-caption">{{ $fact['caption'] ?: $fact['fallback_caption'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="school-section">
                        <h3>Departments & programs offered</h3>
                        @php
                            $departments = collect($departments ?? [])->filter(function ($dept) {
                                return ($dept->programs ?? collect())->isNotEmpty();
                            });
                        @endphp
                        @if ($departments->isEmpty())
                            <div class="school-programs-grid">
                                <div class="school-program">
                                    <div class="program-title">College of Computing & Information Sciences</div>
                                    <div class="program-body">
                                        BS in Computer Science · BS in Information Technology · BS in Data Science
                                    </div>
                                </div>
                                <div class="school-program">
                                    <div class="program-title">College of Business & Accountancy</div>
                                    <div class="program-body">
                                        BS in Accountancy · BS in Business Administration · BS in Entrepreneurship
                                    </div>
                                </div>
                                <div class="school-program">
                                    <div class="program-title">College of Education & Liberal Arts</div>
                                    <div class="program-body">
                                        Bachelor of Elementary Education · Bachelor of Secondary Education · AB in English
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="school-programs-grid">
                                @foreach ($departments as $dept)
                                    <div class="school-program">
                                        <div class="school-program-header">
                                            <div class="program-title">{{ $dept->name }}</div>
                                            <span class="program-offer-pill">Programs offered</span>
                                        </div>
                                        <div class="program-body">
                                            <ul class="program-list">
                                                @foreach ($dept->programs as $program)
                                                    <li>
                                                        <span class="program-dot"></span>
                                                        <span class="program-name">{{ $program->name }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="school-section">
                        <h3>{{ $profile && $profile->campus_title ? $profile->campus_title : 'Campus life & student support' }}</h3>
                        <ul class="school-bullets">
                            @if ($profile && ($profile->campus_bullet1 || $profile->campus_bullet2 || $profile->campus_bullet3 || $profile->campus_bullet4))
                                @if ($profile->campus_bullet1)<li>{{ $profile->campus_bullet1 }}</li>@endif
                                @if ($profile->campus_bullet2)<li>{{ $profile->campus_bullet2 }}</li>@endif
                                @if ($profile->campus_bullet3)<li>{{ $profile->campus_bullet3 }}</li>@endif
                                @if ($profile->campus_bullet4)<li>{{ $profile->campus_bullet4 }}</li>@endif
                            @else
                                <li>Modern smart classrooms, laboratories, and learning resource centers.</li>
                                <li>Active student organizations, athletics, and cultural affairs office.</li>
                                <li>Scholarship and financial assistance programs for qualified students.</li>
                                <li>Career & placement services to support internships and graduate employability.</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="schools-footer">
        <p>© {{ date('Y') }} EduPlatform · University SaaS E‑Learning System</p>
    </footer>
</div>

<style>
.schools-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f3f4f6;
}

.schools-nav {
    border-bottom: 1px solid #e5e7eb;
    background: #ffffffcc;
    backdrop-filter: blur(16px);
}

.schools-nav-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.schools-brand {
    display: flex;
    align-items: center;
    gap: 8px;
}

.schools-logo {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    font-weight: 700;
    color: #ffffff;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
}

.schools-brand-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: #111827;
}

.schools-nav-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.schools-main {
    max-width: 100%;
    margin: 0;
    padding: 24px 0 40px;
    flex: 1;
}

.schools-alert {
    max-width: 720px;
    margin: 0 auto 16px;
}

.school-detail-card {
    position: relative;
    background: #ffffff;
    border-radius: 0;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.08);
    padding: 0 0 32px;
    overflow: hidden;
    max-width: 100%;
    margin: 0;
}

.school-cover {
    position: relative;
    z-index: 1;
    height: 340px;
    background-image:
        linear-gradient(120deg, rgba(15,23,42,0.15), rgba(37,99,235,0.35)),
        url("https://images.unsplash.com/photo-1541339907198-e08756defe93?auto=format&fit=crop&q=80&w=1600");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    overflow: hidden;
}

.school-detail-header {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 18px 40px 16px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 12px;
    background: #ffffff;
}

.school-detail-header .school-heading {
    flex: 1;
    min-width: 0;
}

.school-header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
    margin-left: auto;
}

.school-header-actions .btn {
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
}
.school-header-actions .btn.primary:hover {
    box-shadow: 0 4px 14px rgba(17, 24, 39, 0.25);
}
.school-header-actions .btn.secondary:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.15);
}

.school-avatar {
    position: relative;
    z-index: 3;
    width: 170px;
    height: 170px;
    border-radius: 999px;
    border: 4px solid #ffffff;
    background: #111827;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: -100px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.35);
    flex-shrink: 0;
}

.school-avatar-initial {
    display: inline-block;
    font-size: 2.4rem;
    font-weight: 800;
    color: #ffffff;
}

.school-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 999px;
    display: block;
}

.school-heading-top {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 4px;
}

.school-heading h1 {
    margin: 0;
    font-size: 1.4rem;
    font-weight: 800;
    color: #0f172a;
}

.school-code-pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    background: #eff6ff;
    color: #1d4ed8;
}

.school-location {
    margin: 0;
    font-size: 0.88rem;
    color: #6b7280;
}

.school-detail-body {
    margin-top: 6px;
    padding: 0 40px;
}

.school-detail-main h2 {
    margin: 0 0 4px;
    font-size: 1.35rem;
    font-weight: 700;
    color: #0f172a;
}

.school-intro {
    margin: 0 0 12px;
    font-size: 0.92rem;
    color: #4b5563;
}

.school-highlight {
    font-weight: 600;
    color: #111827;
}

.school-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 18px;
}

.school-tag {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.78rem;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    color: #4b5563;
}

.school-tag.primary {
    border-color: #bfdbfe;
    background: #eff6ff;
    color: #1d4ed8;
}

.school-tag.accent {
    border-color: #bbf7d0;
    background: #ecfdf5;
    color: #15803d;
}

.school-facts-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 18px;
}

.school-fact {
    background: #f9fafb;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    padding: 10px 12px 11px;
}

.fact-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #9ca3af;
    margin-bottom: 4px;
}

.fact-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
}

.fact-caption {
    margin-top: 2px;
    font-size: 0.78rem;
    color: #6b7280;
}

.school-section {
    margin-top: 18px;
}

.school-section h3 {
    margin: 0 0 6px;
    font-size: 0.98rem;
    color: #111827;
}

.school-programs-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 8px;
}

.school-program {
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 10px 12px;
    transition: box-shadow 0.15s ease, transform 0.1s ease, border-color 0.15s ease;
}

.school-program:hover {
    border-color: #2563eb;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    transform: translateY(-1px);
}

.program-title {
    font-size: 0.9rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0;
}

.program-body {
    font-size: 0.8rem;
    color: #6b7280;
}

.school-program-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 6px;
}

.program-offer-pill {
    padding: 3px 8px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    background: #eff6ff;
    color: #1d4ed8;
    border: 1px solid #bfdbfe;
    white-space: nowrap;
}

.program-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.program-list li {
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0 0 4px;
    font-size: 0.86rem;
    color: #111827;
}

.program-dot {
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: #2563eb;
    flex-shrink: 0;
}

.program-name {
    line-height: 1.4;
}

.school-bullets {
    margin: 4px 0 0;
    padding-left: 18px;
    font-size: 0.8rem;
    color: #6b7280;
    line-height: 1.6;
}

.school-cta-card {
    background: #f9fafb;
    border-radius: 18px;
    border: 1px solid #e5e7eb;
    padding: 16px 16px 18px;
    width: 100%;
    box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
}

.school-cta-card h3 {
    margin: 0 0 4px;
    font-size: 0.98rem;
}

.school-cta-card p {
    margin: 0 0 12px;
    font-size: 0.85rem;
    color: #6b7280;
}

.school-cta-card .btn {
    justify-content: center;
}

.school-cta-primary {
    margin-bottom: 8px;
}

.school-cta-secondary {
    background: #ffffff;
}

.school-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 12px 0 20px;
}

.school-hero-actions .btn.lg {
    padding-inline: 22px;
    font-size: 0.9rem;
}

.school-hero-actions .btn.primary.lg {
    box-shadow: 0 10px 24px rgba(37, 99, 235, 0.35);
    transform: translateY(0);
    transition: box-shadow 0.15s ease, transform 0.15s ease;
}

.school-hero-actions .btn.primary.lg:hover {
    box-shadow: 0 14px 32px rgba(37, 99, 235, 0.45);
    transform: translateY(-1px);
}

.school-hero-actions .btn.secondary.lg {
    border-width: 1px;
    border-color: #d1d5db;
    background: #ffffff;
}

.school-hero-actions .btn.secondary.lg:hover {
    border-color: #9ca3af;
}

.school-cta-hints {
    margin: 10px 0 0;
    padding-left: 18px;
    list-style: disc;
    font-size: 0.78rem;
    color: #6b7280;
}

.school-cta-footnote {
    margin: 10px 0 0;
    font-size: 0.78rem;
    color: #6b7280;
}

.school-cta-footnote a {
    color: #2563eb;
    font-weight: 600;
    text-decoration: none;
}

.school-cta-footnote a:hover {
    text-decoration: underline;
}

.schools-footer {
    border-top: 1px solid #e5e7eb;
    background: #ffffff;
    padding: 18px 20px 22px;
    text-align: center;
    font-size: 0.8rem;
    color: #9ca3af;
}

/* Theme overrides for school space */
.school-theme-blue .btn.primary { background: #2563eb; }
.school-theme-blue .school-code-pill { background: #eff6ff; color: #1d4ed8; }
.school-theme-blue .school-tag.primary { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
.school-theme-blue .school-detail-header { background: linear-gradient(180deg, rgba(37,99,235,0.18) 0%, #ffffff 70%); }
.school-theme-green .btn.primary { background: #15803d; }
.school-theme-green .school-code-pill { background: #eafaf0; color: #166534; }
.school-theme-green .school-tag.primary { background: #dcfce7; color: #166534; border-color: #4ade80; }
.school-theme-green .school-detail-header { background: linear-gradient(180deg, rgba(22,101,52,0.25) 0%, #ffffff 70%); }
.school-theme-indigo .btn.primary { background: #4f46e5; }
.school-theme-indigo .school-code-pill { background: #eef2ff; color: #4338ca; }
.school-theme-indigo .school-tag.primary { background: #eef2ff; color: #4338ca; border-color: #c7d2fe; }
.school-theme-indigo .school-detail-header { background: linear-gradient(180deg, rgba(79,70,229,0.18) 0%, #ffffff 70%); }
.school-theme-slate .btn.primary { background: #475569; }
.school-theme-slate .school-code-pill { background: #f1f5f9; color: #334155; }
.school-theme-slate .school-tag.primary { background: #f1f5f9; color: #334155; border-color: #cbd5e1; }
.school-theme-slate .school-detail-header { background: linear-gradient(180deg, rgba(71,85,105,0.18) 0%, #ffffff 70%); }
.school-theme-teal .btn.primary { background: #0f766e; }
.school-theme-teal .school-code-pill { background: #ecfeff; color: #0f766e; }
.school-theme-teal .school-tag.primary { background: #ecfeff; color: #0f766e; border-color: #5eead4; }
.school-theme-teal .school-detail-header { background: linear-gradient(180deg, rgba(20,184,166,0.18) 0%, #ffffff 70%); }
.school-theme-amber .btn.primary { background: #d97706; }
.school-theme-amber .school-code-pill { background: #fffbeb; color: #92400e; }
.school-theme-amber .school-tag.primary { background: #fffbeb; color: #92400e; border-color: #fbbf24; }
.school-theme-amber .school-detail-header { background: linear-gradient(180deg, rgba(245,158,11,0.18) 0%, #ffffff 70%); }
.school-theme-rose .btn.primary { background: #e11d48; }
.school-theme-rose .school-code-pill { background: #fff1f2; color: #be123c; }
.school-theme-rose .school-tag.primary { background: #fff1f2; color: #be123c; border-color: #fb7185; }
.school-theme-rose .school-detail-header { background: linear-gradient(180deg, rgba(244,63,94,0.20) 0%, #ffffff 70%); }

.school-theme-purple .btn.primary { background: #7c3aed; }
.school-theme-purple .school-code-pill { background: #f5f3ff; color: #5b21b6; }
.school-theme-purple .school-tag.primary { background: #f5f3ff; color: #5b21b6; border-color: #c4b5fd; }
.school-theme-purple .school-detail-header { background: linear-gradient(180deg, rgba(124,58,237,0.18) 0%, #ffffff 70%); }

.school-theme-emerald .btn.primary { background: #059669; }
.school-theme-emerald .school-code-pill { background: #ecfdf5; color: #047857; }
.school-theme-emerald .school-tag.primary { background: #ecfdf5; color: #047857; border-color: #6ee7b7; }
.school-theme-emerald .school-detail-header { background: linear-gradient(180deg, rgba(16,185,129,0.2) 0%, #ffffff 70%); }

.school-theme-sky .btn.primary { background: #0284c7; }
.school-theme-sky .school-code-pill { background: #e0f2fe; color: #0369a1; }
.school-theme-sky .school-tag.primary { background: #e0f2fe; color: #0369a1; border-color: #7dd3fc; }
.school-theme-sky .school-detail-header { background: linear-gradient(180deg, rgba(56,189,248,0.2) 0%, #ffffff 70%); }

@media (max-width: 900px) {
    /* layout already stacks; no grid needed */
}

@media (max-width: 640px) {
    .schools-nav-inner {
        padding-inline: 16px;
    }

    .schools-main {
        padding: 16px 0 32px;
    }

    .school-detail-header,
    .school-detail-body {
        padding-inline: 16px;
    }

    .school-detail-header {
        flex-wrap: wrap;
    }
    .school-header-actions {
        width: 100%;
        margin-left: 0;
        margin-top: 8px;
        justify-content: flex-end;
    }
}
</style>
@endsection

