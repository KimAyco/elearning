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
                            A <span class="school-highlight">studentâ€‘centered</span> institution offering
                            <span class="school-highlight">industryâ€‘aligned programs</span>, supportive faculty, and a
                            modern digital campus powered by EduPlatform.
                        </p>
                    @endif

                    <div class="school-tags">
                        <span class="school-tag primary">{{ $profile && $profile->tag_primary ? $profile->tag_primary : 'Public higherâ€‘education institution' }}</span>
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
                                        <div class="program-body">BS in Computer Science Â· BS in Information Technology Â· BS in Data Science</div>
                                    </div>
                                </details>
                                <details class="school-dept">
                                    <summary class="school-dept-summary">
                                        <span class="program-title">College of Business & Accountancy</span>
                                        <span class="program-offer-pill">Programs offered</span>
                                    </summary>
                                    <div class="school-dept-body">
                                        <div class="program-body">BS in Accountancy Â· BS in Business Administration Â· BS in Entrepreneurship</div>
                                    </div>
                                </details>
                                <details class="school-dept">
                                    <summary class="school-dept-summary">
                                        <span class="program-title">College of Education & Liberal Arts</span>
                                        <span class="program-offer-pill">Programs offered</span>
                                    </summary>
                                    <div class="school-dept-body">
                                        <div class="program-body">Bachelor of Elementary Education Â· Bachelor of Secondary Education Â· AB in English</div>
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
                            $featureTitles = ['Modern labs', 'Student groups', 'Scholarships', 'Industry tieâ€‘ups'];
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
