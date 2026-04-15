<style>
            .student-global-footer {
                background: #050608;
                color: #e5e7eb;
                margin-top: 40px;
            }
            /* Student layout placement: align footer with right content area,
               not under the fixed left sidebar */
            .student-global-footer--tenant {
                margin-left: var(--sidebar-w, 240px);
                width: calc(100% - var(--sidebar-w, 240px));
            }
            .student-global-footer__inner {
                max-width: 1120px;
                margin: 0 auto;
                padding: 28px 24px 18px;
                display: grid;
                grid-template-columns: 1.3fr 1fr 1.1fr;
                gap: 32px;
            }
            .student-global-footer h3 {
                font-size: 0.9rem;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                color: #9ca3af;
                margin-bottom: 10px;
            }
            .student-footer-brand {
                display: flex;
                gap: 14px;
                align-items: flex-start;
                margin-bottom: 10px;
            }
            .student-footer-logo {
                width: 40px;
                height: 40px;
                border-radius: 12px;
                background: #111827;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                overflow: hidden;
            }
            .student-footer-logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
            .student-footer-logo-fallback {
                width: 100%;
                height: 100%;
                border-radius: inherit;
                background: radial-gradient(circle at 30% 20%, #22c55e 0, #15803d 40%, #020617 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 800;
                font-size: 1.1rem;
                color: #ecfdf5;
            }
            .student-footer-school-name {
                font-size: 0.96rem;
                font-weight: 700;
                color: #f9fafb;
                margin-bottom: 4px;
            }
            .student-footer-desc {
                font-size: 0.8rem;
                color: #9ca3af;
                line-height: 1.6;
                max-width: 28rem;
            }
            .student-footer-copy {
                font-size: 0.75rem;
                color: #6b7280;
                margin-top: 10px;
            }
            .student-footer-links ul,
            .student-footer-contact ul {
                list-style: none;
            }
            .student-footer-links li + li,
            .student-footer-contact li + li {
                margin-top: 6px;
            }
            .student-footer-link {
                font-size: 0.8rem;
                color: #e5e7eb;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }
            .student-footer-link:hover {
                color: #a5b4fc;
            }
            .student-footer-contact-text {
                font-size: 0.8rem;
                color: #d1d5db;
            }
            .student-footer-contact-text a {
                color: var(--student-footer-accent, #22c55e);
                text-decoration: none;
            }
            .student-footer-contact-text a:hover {
                text-decoration: underline;
            }
            .student-footer-socials {
                display: flex;
                gap: 8px;
                margin-top: 10px;
            }
            .student-footer-social-btn {
                width: 30px;
                height: 30px;
                border-radius: 999px;
                border: 1px solid #1f2937;
                background: #020617;
                color: #e5e7eb;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                transition: background 0.15s, border-color 0.15s, transform 0.1s;
            }
            .student-footer-social-btn:hover {
                background: #111827;
                border-color: var(--student-footer-accent, #22c55e);
                transform: translateY(-1px);
            }
            .student-footer-bottom {
                border-top: 1px solid #111827;
                padding: 10px 24px 18px;
            }
            .student-footer-bottom-inner {
                max-width: 1120px;
                margin: 0 auto;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                font-size: 0.75rem;
                color: #6b7280;
            }
            .student-footer-powered {
                font-weight: 500;
            }
            @media (max-width: 900px) {
                .student-global-footer__inner {
                    grid-template-columns: 1.4fr 1fr;
                    gap: 24px;
                }
            }
            @media (max-width: 768px) {
                .student-global-footer--tenant {
                    margin-left: 0;
                    width: 100%;
                }
            }
            @media (max-width: 640px) {
                .student-global-footer__inner {
                    grid-template-columns: 1fr;
                    text-align: left;
                }
                .student-footer-bottom-inner {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }
</style>

@php
    $schoolName = session('active_school_name') ?? 'Davao del Norte State College';
    $schoolShort = session('school_footer_short', 'Empowering students through quality education, innovation, and community engagement in Davao del Norte and beyond.');
    $schoolAddress = session('school_footer_address', 'Panabo City, Davao del Norte, Philippines 8105');
    $schoolEmail = session('school_footer_email', 'info@dnsc.edu.ph');
    $schoolPhone = session('school_footer_phone', '+63 912 345 6789');
    $schoolFooterLogoUrl = session('active_school_footer_logo_url') ?? session('active_school_logo_url');
    $accentColor = data_get(session('admin_appearance', []), 'accent', '#22c55e');
    $year = now()->year;
@endphp

<footer class="student-global-footer student-global-footer--tenant" aria-label="Student site footer" style="--student-footer-accent: {{ $accentColor }};">
    <div class="student-global-footer__inner">
        {{-- Column 1: School info --}}
        <div>
            <div class="student-footer-brand">
                <div class="student-footer-logo">
                    @if (!empty($schoolFooterLogoUrl))
                        <img src="{{ $schoolFooterLogoUrl }}" alt="{{ $schoolName }} logo">
                    @else
                        @php $initial = strtoupper(mb_substr($schoolName, 0, 1)); @endphp
                        <div class="student-footer-logo-fallback" aria-hidden="true">{{ $initial }}</div>
                    @endif
                </div>
                <div>
                    <div class="student-footer-school-name">{{ $schoolName }}</div>
                    <p class="student-footer-desc">{{ $schoolShort }}</p>
                </div>
            </div>
            <p class="student-footer-copy">© {{ $year }} {{ $schoolName }}. All rights reserved.</p>
        </div>

        {{-- Column 2: Quick links --}}
        <div class="student-footer-links">
            <h3>Quick Links</h3>
            <ul>
                <li><a class="student-footer-link" href="{{ url('/tenant/dashboard') }}">Dashboard</a></li>
                <li><a class="student-footer-link" href="{{ url('/tenant/enrollments') }}">Enrollments</a></li>
                <li><a class="student-footer-link" href="{{ url('/tenant/classes') }}">Classes</a></li>
                <li><a class="student-footer-link" href="{{ url('/tenant/grades') }}">Grades</a></li>
                <li><a class="student-footer-link" href="{{ url('/tenant/payments') }}">Payments</a></li>
            </ul>
        </div>

        {{-- Column 3: Contact --}}
        <div class="student-footer-contact">
            <h3>Contact</h3>
            <ul>
                <li class="student-footer-contact-text">{{ $schoolAddress }}</li>
                <li class="student-footer-contact-text"><a href="mailto:{{ $schoolEmail }}">{{ $schoolEmail }}</a></li>
                <li class="student-footer-contact-text"><a href="tel:{{ preg_replace('/[^0-9+]/', '', $schoolPhone) }}">{{ $schoolPhone }}</a></li>
            </ul>
            <div class="student-footer-socials" aria-label="School social media">
                <a href="#" class="student-footer-social-btn" aria-label="Facebook">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M13 22v-7h3l1-4h-4V8c0-1.1.9-2 2-2h2V2h-3a5 5 0 0 0-5 5v4H7v4h3v7h3z"/>
                    </svg>
                </a>
                <a href="#" class="student-footer-social-btn" aria-label="Instagram">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>
                    </svg>
                </a>
                <a href="#" class="student-footer-social-btn" aria-label="X">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M4 4l16 16M20 4L4 20"/>
                    </svg>
                </a>
                <a href="#" class="student-footer-social-btn" aria-label="YouTube">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M21.8 8.001a2.75 2.75 0 0 0-1.94-1.95C18.25 5.5 12 5.5 12 5.5s-6.25 0-7.86.55A2.75 2.75 0 0 0 2.2 8.001 28.66 28.66 0 0 0 1.5 12a28.66 28.66 0 0 0 .7 3.999 2.75 2.75 0 0 0 1.94 1.95C5.75 18.5 12 18.5 12 18.5s6.25 0 7.86-.551a2.75 2.75 0 0 0 1.94-1.95A28.66 28.66 0 0 0 22.5 12a28.66 28.66 0 0 0-.7-3.999zM10 15.25V8.75L15 12l-5 3.25z"/>
                    </svg>
                </a>
                <a href="#" class="student-footer-social-btn" aria-label="Website">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/><path d="M3 12h18"/><path d="M12 3a15 15 0 0 1 4 9 15 15 0 0 1-4 9 15 15 0 0 1-4-9 15 15 0 0 1 4-9z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
    <div class="student-footer-bottom">
        <div class="student-footer-bottom-inner">
            <span>Student Portal</span>
            <span class="student-footer-powered">Powered by EduPlatform</span>
        </div>
    </div>
</footer>

