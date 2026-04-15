@extends('layouts.app')

@section('title', $school->name . ' - Enrollment')

@section('content')
<div class="schools-page schools-detail-page @if($school->theme) school-theme-{{ $school->theme }} @endif">
    @php $profile = $school->profile; @endphp
    <header class="school-public-topnav-wrap">
        <div class="school-public-topnav">
            <nav class="school-public-nav" aria-label="Primary navigation">
                <a href="#home">Home</a>
                <a href="#about">About</a>
                <a href="#programs">Programs</a>
                <a href="#admissions">Admissions</a>
                <a href="#news">News & Announcements</a>
                <a href="#student-life">Student Life</a>
                <a href="#contact">Contact</a>
            </nav>

        </div>
    </header>

    <main class="schools-main" id="home">
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
            <div class="school-cover" aria-hidden="true" @if($school->cover_image_url) style="background-image: linear-gradient(120deg, rgba(10,40,10,0.20), rgba(50,146,0,0.40)), url('{{ $school->cover_image_url }}');" @endif>
                <div class="school-cover-address">Panabo City, Philippines</div>
            </div>

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
                    <h2 id="about">Welcome to {{ $school->name }}</h2>
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

                    <div class="school-facts-grid" aria-label="Quick stats">
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
                                <div class="fact-icon" aria-hidden="true">
                                    @if ($loop->index === 0)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 10h18"/><path d="M7 3v3"/><path d="M17 3v3"/><rect x="3" y="6" width="18" height="15" rx="2"/><path d="M7 14h4"/>
                                        </svg>
                                    @elseif ($loop->index === 1)
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                        </svg>
                                    @else
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 10L12 4 2 10l10 6 10-6z"/><path d="M6 12v5c0 1 3 3 6 3s6-2 6-3v-5"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="fact-meta">
                                    <div class="fact-label">{{ $fact['label'] ?: $fact['fallback_label'] }}</div>
                                    <div class="fact-value">{{ $fact['value'] ?: $fact['fallback_value'] }}</div>
                                    <div class="fact-caption">{{ $fact['caption'] ?: $fact['fallback_caption'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="school-section" id="programs">
                        <h3>Departments & programs offered</h3>
                        @php
                            $departments = collect($departments ?? [])->filter(function ($dept) {
                                return ($dept->programs ?? collect())->isNotEmpty();
                            });
                        @endphp
                        @if ($departments->isEmpty())
                            <div class="school-programs-grid">
                                <details class="school-dept" open>
                                    <summary class="school-dept-summary">
                                        <span class="program-title">College of Computing & Information Sciences</span>
                                        <span class="program-offer-pill">Programs offered</span>
                                    </summary>
                                    <div class="school-dept-body">
                                        <div class="program-body">BS in Computer Science · BS in Information Technology · BS in Data Science</div>
                                    </div>
                                </details>
                                <details class="school-dept">
                                    <summary class="school-dept-summary">
                                        <span class="program-title">College of Business & Accountancy</span>
                                        <span class="program-offer-pill">Programs offered</span>
                                    </summary>
                                    <div class="school-dept-body">
                                        <div class="program-body">BS in Accountancy · BS in Business Administration · BS in Entrepreneurship</div>
                                    </div>
                                </details>
                                <details class="school-dept">
                                    <summary class="school-dept-summary">
                                        <span class="program-title">College of Education & Liberal Arts</span>
                                        <span class="program-offer-pill">Programs offered</span>
                                    </summary>
                                    <div class="school-dept-body">
                                        <div class="program-body">Bachelor of Elementary Education · Bachelor of Secondary Education · AB in English</div>
                                    </div>
                                </details>
                            </div>
                        @else
                            <div class="school-programs-grid">
                                @foreach ($departments as $dept)
                                    <details class="school-dept" @if($loop->first) open @endif>
                                        <summary class="school-dept-summary">
                                            <span class="program-title">{{ $dept->name }}</span>
                                            <span class="program-offer-pill">{{ ($dept->programs ?? collect())->count() }} programs</span>
                                        </summary>
                                        <div class="school-dept-body">
                                            <ul class="program-list">
                                                @foreach ($dept->programs as $program)
                                                    <li>
                                                        <span class="program-dot"></span>
                                                        <span class="program-name">{{ $program->name }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </details>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="school-section" id="student-life">
                        <h3>{{ $profile && $profile->campus_title ? $profile->campus_title : 'Campus life & student support' }}</h3>
                        @php
                            $campusBullets = [];
                            if ($profile) {
                                foreach (['campus_bullet1','campus_bullet2','campus_bullet3','campus_bullet4'] as $k) {
                                    $v = trim((string)($profile->$k ?? ''));
                                    if ($v !== '') $campusBullets[] = $v;
                                }
                            }
                            if (count($campusBullets) === 0) {
                                $campusBullets = [
                                    'Modern smart classrooms, laboratories, and learning resource centers.',
                                    'Active student organizations, athletics, and cultural affairs office.',
                                    'Scholarship and financial assistance programs for qualified students.',
                                    'Career & placement services to support internships and graduate employability.',
                                ];
                            }
                            $featureTitles = ['Modern labs', 'Student groups', 'Scholarships', 'Industry tie‑ups'];
                            $featureIcons = ['flask','users','award','briefcase'];
                        @endphp
                        <div class="school-features-grid">
                            @foreach ($campusBullets as $i => $bullet)
                                @php
                                    $title = $featureTitles[$i] ?? ('Support ' . ($i + 1));
                                    $icon = $featureIcons[$i] ?? 'spark';
                                @endphp
                                <article class="school-feature">
                                    <div class="feature-icon" aria-hidden="true">
                                        @if ($icon === 'flask')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10 2v6l-5 9a3 3 0 0 0 2.6 4.5h8.8A3 3 0 0 0 19 17l-5-9V2"/><path d="M8 8h8"/>
                                            </svg>
                                        @elseif ($icon === 'users')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="9" cy="7" r="4"/><path d="M17 11a4 4 0 1 0-4-4"/><path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/><path d="M17 21v-2a4 4 0 0 0-3-3.87"/>
                                            </svg>
                                        @elseif ($icon === 'award')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="8" r="6"/><path d="M15.5 13.5 17 22l-5-3-5 3 1.5-8.5"/>
                                            </svg>
                                        @elseif ($icon === 'briefcase')
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M2 12h20"/>
                                            </svg>
                                        @else
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 2v4"/><path d="M12 18v4"/><path d="M4.93 4.93l2.83 2.83"/><path d="M16.24 16.24l2.83 2.83"/><path d="M2 12h4"/><path d="M18 12h4"/><path d="M4.93 19.07l2.83-2.83"/><path d="M16.24 7.76l2.83-2.83"/><circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="feature-body">
                                        <h4 class="feature-title">{{ $title }}</h4>
                                        <p class="feature-text">{{ $bullet }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="school-section school-trust-grid">
                        <article class="school-trust-card" id="achievements">
                            <h3>Achievements & highlights</h3>
                            <ul class="school-trust-list">
                                <li>Recognized for quality instruction and student-centered services.</li>
                                <li>Strong board and employment outcomes across priority programs.</li>
                                <li>Active partnerships for internships, outreach, and industry immersion.</li>
                            </ul>
                        </article>

                        <article class="school-trust-card" id="news">
                            <h3>News & announcements</h3>
                            <ul class="school-trust-list">
                                <li>Enrollment period is open for incoming first-year and transferees.</li>
                                <li>Scholarship orientation and student support briefing this month.</li>
                                <li>Visit the registrar office or contact us for admissions updates.</li>
                            </ul>
                        </article>
                    </div>

                    <section class="school-section school-contact-block" id="contact">
                        <h3>Contact & visit information</h3>
                        <div class="school-contact-grid">
                            <article>
                                <h4>Campus address</h4>
                                <p>{{ $profile && $profile->contact_address ? $profile->contact_address : 'Main Campus, Provincial Road, Davao del Norte, Philippines' }}</p>
                            </article>
                            <article>
                                <h4>Email</h4>
                                <p>{{ $profile && $profile->contact_email ? $profile->contact_email : 'admissions@school.edu.ph' }}</p>
                            </article>
                            <article>
                                <h4>Phone</h4>
                                <p>{{ $profile && $profile->contact_phone ? $profile->contact_phone : '+63 (82) 123 4567' }}</p>
                            </article>
                        </div>
                    </section>
                </div>
            </div>
        </section>
    </main>

    @include('public.partials.school-public-footer', ['school' => $school])
</div>

<style>
.schools-detail-page{
    --green:#329200;
    --green-soft:#eaf9ec;
    --green-1:#6cc840;
    --green-2:#56b61f;
    --ink:#0f172a;
    --muted:rgba(15,23,42,0.62);
    --border:rgba(15,23,42,0.10);
    --shadow:0 14px 34px rgba(15, 23, 42, 0.08);
    --shadow-soft:0 10px 22px rgba(15, 23, 42, 0.06);
    --radius:16px;
    /* Footer links / contact / social hover — default matches page green when no theme */
    --footer-accent: #329200;
    --footer-accent-hover: #3cb712;
}
/* Footer accent matches chosen school theme (Branding & Public Page → Color theme) */
.schools-detail-page.school-theme-blue { --footer-accent: #2563eb; --footer-accent-hover: #3b82f6; }
.schools-detail-page.school-theme-green { --footer-accent: #15803d; --footer-accent-hover: #22c55e; }
.schools-detail-page.school-theme-indigo { --footer-accent: #4f46e5; --footer-accent-hover: #818cf8; }
.schools-detail-page.school-theme-slate { --footer-accent: #475569; --footer-accent-hover: #94a3b8; }
.schools-detail-page.school-theme-teal { --footer-accent: #0f766e; --footer-accent-hover: #2dd4bf; }
.schools-detail-page.school-theme-amber { --footer-accent: #d97706; --footer-accent-hover: #fbbf24; }
.schools-detail-page.school-theme-rose { --footer-accent: #e11d48; --footer-accent-hover: #fb7185; }
.schools-detail-page.school-theme-purple { --footer-accent: #7c3aed; --footer-accent-hover: #a78bfa; }
.schools-detail-page.school-theme-emerald { --footer-accent: #059669; --footer-accent-hover: #34d399; }
.schools-detail-page.school-theme-sky { --footer-accent: #0284c7; --footer-accent-hover: #38bdf8; }
.schools-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: linear-gradient(180deg, rgba(234,249,236,0.55) 0%, #f6f8f6 48%, #f6f8f6 100%);
}

.school-public-topnav-wrap{
    position: sticky;
    top: 0;
    z-index: 60;
    background: rgba(255,255,255,0.96);
    border-bottom: 1px solid rgba(15,23,42,0.08);
    backdrop-filter: saturate(180%) blur(10px);
}
.school-public-topnav{
    max-width: 1200px;
    margin: 0 auto;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.school-public-nav{
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
    justify-content: center;
}
.school-public-nav a{
    text-decoration: none;
    font-size: 0.84rem;
    font-weight: 600;
    color: #334155;
    padding: 7px 10px;
    border-radius: 999px;
    transition: background-color 0.15s ease, color 0.15s ease;
    position: relative;
}
.school-public-nav a:hover{
    background: rgba(15,23,42,0.06);
    color: #0f172a;
}
.school-public-nav a::after{
    content: '';
    position: absolute;
    left: 10px;
    right: 10px;
    bottom: -8px;
    height: 3px;
    border-radius: 999px;
    background: var(--green);
    opacity: 0;
    transform: scaleX(0.2);
    transition: opacity 0.18s ease, transform 0.18s ease;
}
.school-public-nav a.is-active{
    color: #0f172a;
    background: rgba(50,146,0,0.08);
}
.school-public-nav a.is-active::after{
    opacity: 1;
    transform: scaleX(1);
}

.schools-main {
    max-width: 100%;
    width: 100%;
    margin: 0;
    padding: 0 0 46px;
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
    border: 0;
    box-shadow: none;
    padding: 0 0 32px;
    overflow: hidden;
    max-width: 100%;
    margin: 0 auto;
}

.school-cover {
    position: relative;
    z-index: 1;
    height: 240px;
    background-image:
        linear-gradient(120deg, rgba(10,40,10,0.22), rgba(50,146,0,0.45)),
        url("https://images.unsplash.com/photo-1541339907198-e08756defe93?auto=format&fit=crop&q=80&w=1600");
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    overflow: hidden;
}
.school-cover::after{
    content:'';
    position:absolute;
    inset:0;
    background:
        radial-gradient(60% 90% at 15% 75%, rgba(234,249,236,0.85) 0%, rgba(234,249,236,0.0) 70%),
        radial-gradient(50% 70% at 92% 30%, rgba(50,146,0,0.22) 0%, rgba(50,146,0,0.0) 62%);
    pointer-events:none;
}
.school-cover-address{
    position: absolute;
    top: 14px;
    left: 20px;
    z-index: 2;
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.35);
    background: rgba(15,23,42,0.34);
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    backdrop-filter: blur(2px);
}

.school-detail-header {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 16px 26px 16px;
    border-bottom: 1px solid rgba(15,23,42,0.07);
    margin-bottom: 12px;
    background: linear-gradient(180deg, rgba(234,249,236,0.65) 0%, #ffffff 72%);
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
    background: #eaf9ec;
    color: #329200;
    border: 1px solid rgba(50,146,0,0.25);
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
    border-color: rgba(50,146,0,0.30);
    background: #eaf9ec;
    color: #329200;
}

.school-tag.accent {
    border-color: rgba(50,146,0,0.30);
    background: linear-gradient(135deg, #eaf9ec 0%, #dff4e2 100%);
    color: #329200;
}

.school-facts-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 18px;
}

.school-fact {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid rgba(50,146,0,0.12);
    padding: 14px 14px;
    box-shadow: var(--shadow-soft);
    display:flex;
    gap:12px;
    align-items:flex-start;
}
.fact-icon{
    width:42px;
    height:42px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    background: rgba(234,249,236,0.95);
    color: #329200;
    flex:0 0 auto;
}
.fact-icon svg{ width:22px; height:22px; }
.fact-meta{ min-width:0; }

.fact-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(15,23,42,0.45);
    margin-bottom: 4px;
}

.fact-value {
    font-size: 1.1rem;
    font-weight: 700;
    color: rgba(15,23,42,0.92);
}

.fact-caption {
    margin-top: 2px;
    font-size: 0.78rem;
    color: rgba(15,23,42,0.58);
}

.school-section {
    margin-top: 18px;
}
.school-trust-grid{
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}
.school-trust-card{
    background: #ffffff;
    border: 1px solid rgba(15,23,42,0.12);
    border-radius: 14px;
    box-shadow: var(--shadow-soft);
    padding: 16px;
}
.school-trust-list{
    margin: 10px 0 0;
    padding-left: 18px;
    color: rgba(15,23,42,0.66);
    font-size: 0.9rem;
    line-height: 1.6;
}
.school-contact-block{
    border: 1px solid rgba(15,23,42,0.10);
    border-radius: 14px;
    background: #ffffff;
    box-shadow: var(--shadow-soft);
    padding: 16px;
}
.school-contact-grid{
    margin-top: 10px;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 12px;
}
.school-contact-grid article{
    border: 1px solid rgba(15,23,42,0.08);
    border-radius: 12px;
    padding: 12px;
    background: #f8fafc;
}
.school-contact-grid h4{
    margin: 0 0 6px;
    font-size: 0.82rem;
    color: #334155;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.school-contact-grid p{
    margin: 0;
    color: rgba(15,23,42,0.72);
    font-size: 0.9rem;
    line-height: 1.45;
}

.school-section h3 {
    margin: 0 0 10px;
    font-size: 1.25rem;
    font-weight: 900;
    color: rgba(15,23,42,0.92);
}

.school-programs-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 8px;
}

.school-dept{
    background:#fff;
    border-radius:14px;
    border:1px solid rgba(50,146,0,0.12);
    box-shadow: var(--shadow-soft);
    overflow:hidden;
}
.school-dept[open]{
    border-color: rgba(50,146,0,0.22);
}
.school-dept-summary{
    list-style:none;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    padding:12px 14px;
    cursor:pointer;
    user-select:none;
}
.school-dept-summary::-webkit-details-marker{ display:none; }
.school-dept-summary::after{
    content:'';
    width:10px;
    height:10px;
    border-right:2px solid rgba(15,23,42,0.42);
    border-bottom:2px solid rgba(15,23,42,0.42);
    transform:rotate(45deg);
    margin-left:auto;
}
.school-dept[open] .school-dept-summary::after{
    transform:rotate(225deg);
}
.school-dept-body{
    padding:0 14px 12px;
    border-top:1px solid rgba(15,23,42,0.06);
}

.program-title {
    font-size: 0.98rem;
    font-weight: 800;
    color: rgba(15,23,42,0.92);
    margin-bottom: 0;
}

.program-body {
    font-size: 0.9rem;
    color: rgba(15,23,42,0.62);
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
    background: #eaf9ec;
    color: #329200;
    border: 1px solid rgba(50,146,0,0.28);
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
    background: #329200;
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

.school-features-grid{
    display:grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap:12px;
    margin-top:10px;
}
.school-feature{
    background:#fff;
    border:1px solid rgba(50,146,0,0.12);
    border-radius:14px;
    box-shadow: var(--shadow-soft);
    padding:14px;
    display:flex;
    gap:12px;
    align-items:flex-start;
}
.feature-icon{
    width:42px;
    height:42px;
    border-radius:12px;
    background: rgba(234,249,236,0.95);
    color:#329200;
    display:flex;
    align-items:center;
    justify-content:center;
    flex:0 0 auto;
}
.feature-icon svg{ width:22px; height:22px; }
.feature-title{
    margin:0 0 4px;
    font-size:0.95rem;
    font-weight:900;
    color: rgba(15,23,42,0.92);
}
.feature-text{
    margin:0;
    font-size:0.86rem;
    line-height:1.55;
    color: rgba(15,23,42,0.62);
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
    background: linear-gradient(135deg, #56b61f 0%, #329200 100%);
    box-shadow: 0 10px 24px rgba(50, 146, 0, 0.30);
    transform: translateY(0);
    transition: box-shadow 0.15s ease, transform 0.15s ease;
}

.school-hero-actions .btn.primary.lg:hover {
    box-shadow: 0 14px 32px rgba(50, 146, 0, 0.40);
    transform: translateY(-1px);
}

.school-hero-actions .btn.secondary.lg {
    border-width: 1px;
    border-color: rgba(50,146,0,0.30);
    background: #eaf9ec;
    color: #329200;
}

.school-hero-actions .btn.secondary.lg:hover {
    border-color: #329200;
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

/* Public school footer — Moodle-inspired dark bar (editable from Branding & Public Page) */
.school-public-footer--moodle.school-public-footer {
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    background: #000000;
    margin-top: auto;
    color: #e5e5e5;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.school-public-footer--moodle .school-public-footer__inner {
    max-width: 1140px;
    margin: 0 auto;
    padding: 32px 24px 22px;
}
.school-public-footer--moodle .school-public-footer__grid {
    display: grid;
    grid-template-columns: minmax(0, 1.35fr) minmax(0, 1fr) minmax(0, 1fr);
    gap: 32px 40px;
    align-items: start;
}
@media (max-width: 900px) {
    .school-public-footer--moodle .school-public-footer__grid {
        grid-template-columns: 1fr 1fr;
        gap: 28px 24px;
    }
    .school-public-footer--moodle .school-public-footer__col--brand {
        grid-column: 1 / -1;
    }
}
@media (max-width: 560px) {
    .school-public-footer--moodle .school-public-footer__grid {
        grid-template-columns: 1fr;
    }
    .school-public-footer--moodle .school-public-footer__col--brand {
        grid-column: auto;
    }
}
/* Footer brand row: school logo (same as Branding upload) + text */
.school-public-footer--moodle .school-public-footer__brand {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}
.school-public-footer--moodle .school-public-footer__logo-wrap {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 14px;
    background: rgba(255, 255, 255, 0.06);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    box-sizing: border-box;
}
.school-public-footer--moodle .school-public-footer__logo {
    max-width: 100%;
    max-height: 100%;
    width: auto;
    height: auto;
    object-fit: contain;
    display: block;
}
.school-public-footer--moodle .school-public-footer__logo-fallback {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    font-weight: 800;
    color: rgba(255, 255, 255, 0.85);
    background: rgba(255, 255, 255, 0.08);
    border-radius: 10px;
}
.school-public-footer--moodle .school-public-footer__brand-text {
    min-width: 0;
    flex: 1;
}
@media (max-width: 420px) {
    .school-public-footer--moodle .school-public-footer__brand {
        flex-direction: column;
        align-items: flex-start;
    }
}
.school-public-footer--moodle .school-public-footer__title {
    font-size: 1.08rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 10px;
    line-height: 1.35;
    letter-spacing: -0.01em;
}
.school-public-footer--moodle .school-public-footer__desc {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.72);
    line-height: 1.55;
    margin: 0 0 14px;
    max-width: 36rem;
}
.school-public-footer--moodle .school-public-footer__copy {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.42);
    margin: 0;
    line-height: 1.4;
}
/* Moodle-style section labels */
.school-public-footer--moodle .school-public-footer__heading {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.45);
    margin: 0 0 12px;
    padding-bottom: 0;
    border-bottom: none;
}
.school-public-footer--moodle .school-public-footer__links {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.school-public-footer--moodle .school-public-footer__links a {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.88);
    text-decoration: none;
    transition: color 0.15s ease;
    line-height: 1.4;
}
.school-public-footer--moodle .school-public-footer__links a:hover {
    color: var(--footer-accent, #329200);
    text-decoration: underline;
    text-underline-offset: 3px;
}
.school-public-footer--moodle .school-public-footer__contact-line {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.78);
    margin: 0 0 8px;
    line-height: 1.5;
    white-space: pre-line;
}
.school-public-footer--moodle .school-public-footer__contact-line a {
    color: var(--footer-accent, #329200);
    text-decoration: none;
}
.school-public-footer--moodle .school-public-footer__contact-line a:hover {
    color: var(--footer-accent-hover, #3cb712);
    text-decoration: underline;
    text-underline-offset: 2px;
}
.school-public-footer--moodle .school-public-footer__social {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 12px;
}
.school-public-footer--moodle .school-public-footer__social-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.14);
    color: rgba(255, 255, 255, 0.92);
    transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
}
.school-public-footer--moodle .school-public-footer__social-link:hover {
    background: var(--footer-accent, #329200);
    border-color: var(--footer-accent, #329200);
    color: #ffffff;
    transform: translateY(-1px);
}
.school-public-footer--moodle .school-public-footer__powered {
    margin-top: 28px;
    padding-top: 18px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
    font-size: 0.72rem;
    color: rgba(255, 255, 255, 0.38);
    letter-spacing: 0.02em;
}
.school-public-footer--moodle .school-public-footer__powered strong {
    color: rgba(255, 255, 255, 0.55);
    font-weight: 600;
}
@media (max-width: 640px) {
    .school-public-footer--moodle .school-public-footer__inner {
        padding: 26px 18px 20px;
    }
    .school-public-footer--moodle .school-public-footer__grid {
        gap: 24px;
    }
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

/* Force requested green palette on school detail page */
.schools-detail-page .btn.primary {
    background: linear-gradient(135deg, #56b61f 0%, #329200 100%);
    border-color: #329200;
}
.schools-detail-page .btn.secondary {
    background: #eaf9ec;
    border-color: rgba(50,146,0,0.30);
    color: #329200;
}
.schools-detail-page .school-code-pill,
.schools-detail-page .school-tag.primary,
.schools-detail-page .program-offer-pill {
    background: #eaf9ec;
    color: #329200;
    border-color: rgba(50,146,0,0.28);
}

@media (max-width: 900px) {
    .school-public-topnav{
        flex-wrap: wrap;
        gap: 12px;
    }
    .school-public-nav{
        order: 3;
        width: 100%;
        margin: 0;
    }
    .school-cover{ height: 200px; }
    .school-facts-grid{ grid-template-columns: 1fr; }
    .school-features-grid{ grid-template-columns: 1fr 1fr; }
    .school-trust-grid{ grid-template-columns: 1fr; }
    .school-contact-grid{ grid-template-columns: 1fr 1fr; }
}

@media (max-width: 640px) {
    .schools-nav-inner {
        padding-inline: 16px;
    }

    .schools-main {
        padding: 16px 0 32px;
    }
    .school-public-topnav{
        padding-inline: 14px;
    }
    .school-public-nav{
        gap: 6px;
    }
    .school-public-nav a{
        font-size: 0.78rem;
        padding: 6px 9px;
    }
    .school-detail-header,
    .school-detail-body {
        padding-inline: 16px;
    }

    .school-detail-header {
        flex-wrap: wrap;
    }
    .school-cover-address{
        top: 10px;
        left: 12px;
        font-size: 0.72rem;
        padding: 5px 9px;
    }
    .school-header-actions {
        width: 100%;
        margin-left: 0;
        margin-top: 8px;
        justify-content: flex-end;
    }
    .school-features-grid,
    .school-contact-grid{
        grid-template-columns: 1fr;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const navLinks = Array.from(document.querySelectorAll('.school-public-nav a'));
    if (!navLinks.length) return;

    function setActiveByHash() {
        const hash = window.location.hash || '#home';
        navLinks.forEach(function (link) {
            const active = link.getAttribute('href') === hash;
            link.classList.toggle('is-active', active);
        });
    }

    navLinks.forEach(function (link) {
        link.addEventListener('click', function () {
            navLinks.forEach(function (item) { item.classList.remove('is-active'); });
            link.classList.add('is-active');
        });
    });

    window.addEventListener('hashchange', setActiveByHash);
    setActiveByHash();
});
</script>
@endsection

