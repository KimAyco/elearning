@extends('layouts.app')

@section('title', 'EduPlatform â€” Registered Schools')

@section('content')
<div class="schools-page">

    {{-- Top banner: diagonal two-tone layout (white left, dark right) --}}
    <header class="schools-nav">
        <div class="schools-nav-left"></div>
        <div class="schools-nav-right"></div>
        {{-- Diagonal gradient stripes --}}
        <div class="schools-nav-stripe schools-nav-stripe-1" aria-hidden="true"></div>
        <div class="schools-nav-stripe schools-nav-stripe-2" aria-hidden="true"></div>
        <div class="schools-nav-stripe schools-nav-stripe-3" aria-hidden="true"></div>
        <div class="schools-nav-stripe schools-nav-stripe-4" aria-hidden="true"></div>
        <div class="schools-nav-stripe schools-nav-stripe-5" aria-hidden="true"></div>
        <div class="schools-nav-inner">
            <div class="schools-nav-section schools-nav-section-left">
                <div class="schools-brand">
                    <span class="schools-logo" aria-hidden="true">E</span>
                    <div class="schools-brand-text">
                        <h1 class="schools-brand-name">EduPlatform</h1>
                    </div>
                </div>
            </div>
            <div class="schools-nav-section schools-nav-section-right">
                <div class="schools-nav-right-headline">
                    <span class="schools-nav-right-line1">Find your</span>
                    <span class="schools-nav-right-line2">School</span>
                </div>
                <span class="schools-nav-right-tagline">Register or sign in to continue</span>
                <div class="schools-nav-actions">
                    <a href="{{ route('register.school.form') }}" class="btn btn-nav secondary sm">Register school</a>
                    <a href="{{ url('/login') }}" class="btn btn-nav primary sm">Sign in</a>
                </div>
            </div>
        </div>
    </header>

    <main class="schools-main">
        @if (session('status'))
            <div class="alert success schools-alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"></path>
                </svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="alert error schools-alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Search & filter (visual only) --}}
        <section class="schools-controls">
            <div class="schools-search">
                <span class="search-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="6"></circle>
                        <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
                    </svg>
                </span>
                <input
                    type="text"
                    name="q"
                    placeholder="Search by name or city..."
                    class="search-input"
                >
            </div>
            <div class="schools-filters">
                <span class="filters-label">Filter by type</span>
                <div class="filters-chips">
                    <button type="button" class="filter-chip filter-chip--active">All</button>
                    <button type="button" class="filter-chip">Private</button>
                    <button type="button" class="filter-chip">Public</button>
                    <button type="button" class="filter-chip">Charter</button>
                    <button type="button" class="filter-chip">Vocational</button>
                </div>
            </div>
        </section>

        @php
            $placeholderImages = [
                'https://images.unsplash.com/photo-1541339907198-e08756defe93?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1519452635265-7b1fbfd1e4e0?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&q=80&w=800',
            ];
            $placeholderTypes = ['Private', 'Public', 'Vocational', 'Private', 'Charter', 'Private'];
            $placeholderLocations = [
                'New York, NY',
                'Chicago, IL',
                'San Francisco, CA',
                'Austin, TX',
                'Seattle, WA',
                'Boston, MA',
            ];
            $placeholderStudents = ['1,200', '2,500', '800', '350', '950', '1,100'];
            $placeholderRatings = [4.8, 4.2, 4.5, 4.9, 4.6, 4.7];
            $placeholderTags = [
                ['STEM', 'Arts'],
                ['Sports', 'Community'],
                ['Engineering', 'Tech'],
                ['Childhood', 'Creative'],
                ['Robotics', 'Math'],
                ['IB Program', 'Law'],
            ];
            $fallbackImage = 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&q=80&w=800';
        @endphp

        {{-- Schools grid --}}
        <section class="schools-grid-section">
            @if ($schools->isEmpty())
                <div class="schools-empty-card">
                    <div class="empty-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <h2>No schools yet</h2>
                    <p>Once schools are registered on EduPlatform, you will see them listed here.</p>
                </div>
            @else
                <div class="schools-grid">
                    @foreach ($schools as $index => $school)
                        @php
                            $metaIndex = $index % count($placeholderImages);
                            $type = $placeholderTypes[$metaIndex] ?? 'School';
                            $location = $placeholderLocations[$metaIndex] ?? 'City, Country';
                                    $studentCount = $school->enrolled_students_count ?? null;
                                    $students = $studentCount && $studentCount > 0
                                        ? number_format($studentCount)
                                        : ($placeholderStudents[$metaIndex] ?? '—');
                            $rating = $placeholderRatings[$metaIndex] ?? 4.5;
                            $tags = $placeholderTags[$metaIndex] ?? [];
                            $image = $school->cover_image_url ?: ($placeholderImages[$metaIndex] ?? $fallbackImage);
                        @endphp
                        <article class="school-card">
                            <div class="school-card-image">
                                <img
                                    src="{{ $image }}"
                                    alt="Campus view for {{ $school->name }}"
                                    onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                                >
                                <button type="button" class="school-favorite" aria-label="Save school">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="school-card-body">
                                <div class="school-card-title-row">
                                    @if ($school->logo_url)
                                        <div class="school-card-logo">
                                            <img src="{{ $school->logo_url }}" alt="" class="school-card-logo-img">
                                        </div>
                                    @else
                                        <div class="school-card-logo school-card-logo--initial">
                                            <span>{{ strtoupper(mb_substr($school->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                    <h2 class="school-name">{{ $school->name }}</h2>
                                </div>

                                @php
                                    $address = trim((string) $school->short_description) !== '' ? $school->short_description : $location;
                                @endphp
                                <div class="school-rating-row">
                                    <span class="meta-icon">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 1 1 18 0z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                    </span>
                                    <span class="meta-text">{{ $address }}</span>
                                </div>

                                <div class="school-meta">
                                    <div class="school-meta-row">
                                        <span class="meta-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                            </svg>
                                        </span>
                                        <span class="meta-text">{{ $students }} students enrolled</span>
                                    </div>
                                </div>

                                @if (!empty($tags))
                                    <div class="school-tags">
                                        @foreach ($tags as $tag)
                                            <span class="tag-pill">{{ $tag }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <a
                                    href="{{ route('school.enroll', ['school_code' => $school->school_code]) }}"
                                    class="school-primary-action"
                                >
                                    Go to school space
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </main>

    <footer class="schools-footer">
        <p>© {{ date('Y') }} EduPlatform</p>
    </footer>
</div>

<style>
.schools-page {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f3f4f6;
}

/* ——— Banner: diagonal two-tone (white left, dark right) + stripes ——— */
.schools-nav {
    position: relative;
    min-height: 140px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

/* Left (white) and right (dark) background panels with diagonal split */
.schools-nav-left,
.schools-nav-right {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.schools-nav-left {
    background: #ffffff;
    clip-path: polygon(0 0, 52% 0, 0 100%);
    z-index: 0;
}

.schools-nav-right {
    background: #0f172a;
    clip-path: polygon(52% 0, 100% 0, 100% 100%, 0 100%);
    z-index: 0;
}

/* Diagonal gradient stripes (blue → teal) */
.schools-nav-stripe {
    position: absolute;
    z-index: 1;
    pointer-events: none;
    opacity: 0.9;
    background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 50%, #14b8a6 100%);
}

.schools-nav-stripe-1 {
    top: -20%;
    right: 5%;
    width: 28%;
    height: 140%;
    transform: rotate(-25deg);
}

.schools-nav-stripe-2 {
    top: -10%;
    right: 25%;
    width: 18%;
    height: 120%;
    transform: rotate(-22deg);
    opacity: 0.7;
}

.schools-nav-stripe-3 {
    bottom: -30%;
    left: 15%;
    width: 22%;
    height: 100%;
    transform: rotate(-20deg);
    opacity: 0.6;
}

.schools-nav-stripe-4 {
    top: 0;
    right: 0;
    width: 35%;
    height: 100%;
    transform: rotate(-18deg);
    opacity: 0.5;
}

.schools-nav-stripe-5 {
    bottom: -20%;
    right: 35%;
    width: 15%;
    height: 90%;
    transform: rotate(-28deg);
    opacity: 0.55;
}

.schools-nav-inner {
    position: relative;
    z-index: 2;
    max-width: 1100px;
    margin: 0 auto;
    padding: 24px 28px 28px;
    min-height: 140px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 32px;
}

.schools-nav-section {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.schools-nav-section-left {
    max-width: 420px;
}

/* Left: brand (blue heading) + sublabel (grey) + description */
.schools-brand {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.schools-brand-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.schools-logo {
    width: 52px;
    height: 52px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    font-weight: 700;
    color: #fff;
    background: linear-gradient(135deg, #3b82f6, #0ea5e9);
    flex-shrink: 0;
}

.schools-brand-name {
    margin: 0;
    font-weight: 700;
    font-size: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #2563eb;
}

/* Right: white + yellow headline, tagline, buttons */
.schools-nav-section-right {
    align-items: flex-end;
    text-align: right;
}

.schools-nav-right-headline {
    display: flex;
    flex-direction: column;
    gap: 0;
    line-height: 1.1;
}

.schools-nav-right-line1 {
    font-weight: 700;
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #f8fafc;
}

.schools-nav-right-line2 {
    font-weight: 700;
    font-size: 1.25rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #facc15;
}

.schools-nav-right-tagline {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #e2e8f0;
}

.schools-nav-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.btn-nav {
    padding: 10px 18px;
    font-weight: 600;
    font-size: 0.875rem;
    border-radius: 8px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.btn-nav.secondary {
    background: rgba(255, 255, 255, 0.15);
    color: #e2e8f0;
    border: 1px solid rgba(255, 255, 255, 0.25);
}

.btn-nav.secondary:hover {
    background: rgba(255, 255, 255, 0.25);
    color: #fff;
}

.btn-nav.primary {
    background: linear-gradient(135deg, #3b82f6, #0ea5e9);
    color: #fff;
    border: none;
    box-shadow: 0 2px 10px rgba(59, 130, 246, 0.4);
}

.btn-nav.primary:hover {
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.5);
    transform: translateY(-1px);
}

.schools-main {
    max-width: 1100px;
    margin: 0 auto;
    padding: 32px 20px 40px;
    flex: 1;
}

.schools-alert {
    max-width: 720px;
    margin: 0 auto 16px;
}

.schools-intro {
    margin-bottom: 20px;
    text-align: center;
}

.schools-intro h1 {
    margin: 0 0 6px;
    font-size: 1.9rem;
    font-weight: 800;
    color: #0f172a;
}

.schools-intro p {
    margin: 0 0 10px;
    font-size: 0.98rem;
    color: #6b7280;
}

.schools-count {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.78rem;
    color: #374151;
    background: #e5f0ff;
}

.schools-controls {
    margin: 24px 0 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
    justify-content: space-between;
}

.schools-search {
    position: relative;
    flex: 1 1 260px;
    max-width: 380px;
}

.search-icon {
    position: absolute;
    inset-block: 0;
    left: 12px;
    display: flex;
    align-items: center;
    color: #9ca3af;
}

.search-icon svg {
    width: 18px;
    height: 18px;
}

.search-input {
    width: 100%;
    padding: 10px 12px 10px 36px;
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    font-size: 0.9rem;
    color: #111827;
    outline: none;
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.search-input::placeholder {
    color: #9ca3af;
}

.search-input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.12);
}

.schools-filters {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}

.filters-label {
    font-size: 0.8rem;
    color: #6b7280;
}

.filters-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.filter-chip {
    border-radius: 999px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    padding: 6px 12px;
    font-size: 0.78rem;
    font-weight: 500;
    color: #4b5563;
    cursor: default;
    transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
}

.filter-chip--active {
    background: #4f46e5;
    border-color: #4f46e5;
    color: #ffffff;
    box-shadow: 0 8px 18px rgba(79, 70, 229, 0.25);
}

.schools-grid-section {
    margin-top: 8px;
}

.schools-empty-card {
    border-radius: 18px;
    border: 1px dashed #e5e7eb;
    background: #ffffff;
    padding: 40px 16px;
    text-align: center;
}

.empty-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    border-radius: 999px;
    background: #f3f4ff;
    color: #9ca3af;
    margin-bottom: 10px;
}

.empty-icon svg {
    width: 26px;
    height: 26px;
}

.schools-empty-card h2 {
    margin: 0 0 4px;
    font-size: 1rem;
    font-weight: 600;
    color: #111827;
}

.schools-empty-card p {
    margin: 0;
    font-size: 0.9rem;
    color: #6b7280;
}

.schools-grid {
    display: grid;
    grid-template-columns: repeat(1, minmax(0, 1fr));
    gap: 18px;
}

@media (min-width: 640px) {
    .schools-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 1024px) {
    .schools-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

.school-card {
    background: #ffffff;
    border-radius: 20px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    display: flex;
    flex-direction: column;
    transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
}

.school-card:hover {
    border-color: #4f46e5;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
    transform: translateY(-2px);
}

.school-card-image {
    position: relative;
    height: 180px;
    overflow: hidden;
}

.school-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.school-card:hover .school-card-image img {
    transform: scale(1.06);
}

.school-type-pill {
    position: absolute;
    top: 10px;
    left: 10px;
}

.school-type-pill span {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    background: #ffffffdd;
    color: #4f46e5;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
}

.school-favorite {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    border-radius: 999px;
    border: none;
    background: #ffffffdd;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    color: #9ca3af;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.15);
}

.school-favorite svg {
    width: 14px;
    height: 14px;
}

.school-card-body {
    padding: 14px 14px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.school-rating-row {
    display: flex;
    align-items: center;
    gap: 2px;
    margin-bottom: 2px;
}

.star-icon {
    width: 13px;
    height: 13px;
}

.star-icon--filled {
    fill: #fbbf24;
    stroke: #fbbf24;
}

.star-icon--empty {
    stroke: #d1d5db;
    fill: transparent;
}

.rating-number {
    margin-left: 4px;
    font-size: 0.72rem;
    font-weight: 600;
    color: #6b7280;
}

.school-card-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 4px;
}

.school-card-logo {
    width: 44px;
    height: 44px;
    border-radius: 999px;
    overflow: hidden;
    flex-shrink: 0;
    background: #111827;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.25);
}

.school-card-logo-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.school-card-logo--initial span {
    color: #ffffff;
    font-size: 0.9rem;
    font-weight: 700;
}

.school-name {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
}

.school-meta {
    margin-top: 4px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.school-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.82rem;
    color: #6b7280;
}

.meta-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    color: #9ca3af;
}

.meta-icon svg {
    width: 14px;
    height: 14px;
}

.meta-text {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.school-tags {
    margin-top: 6px;
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.tag-pill {
    padding: 3px 8px;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    background: #f3f4f6;
    color: #6b7280;
}

.school-primary-action {
    margin-top: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 9px 12px;
    border-radius: 999px;
    background: #0f172a;
    color: #ffffff;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
}

.school-primary-action:hover {
    background: #4f46e5;
    box-shadow: 0 10px 22px rgba(79, 70, 229, 0.4);
    transform: translateY(-1px);
}

.schools-footer {
    border-top: 1px solid #e5e7eb;
    background: #ffffff;
    padding: 18px 20px 22px;
    text-align: center;
    font-size: 0.8rem;
    color: #9ca3af;
}

@media (max-width: 640px) {
    .schools-nav {
        min-height: auto;
    }

    .schools-nav-inner {
        flex-direction: column;
        align-items: stretch;
        padding: 20px 16px;
        min-height: auto;
    }

    .schools-nav-section-left {
        max-width: none;
    }

    .schools-nav-section-right {
        align-items: flex-start;
        text-align: left;
    }

    .schools-nav-actions {
        flex-wrap: wrap;
    }

    .schools-main {
        padding-inline: 16px;
        padding-top: 24px;
    }

    .schools-controls {
        align-items: stretch;
    }

    .schools-filters {
        justify-content: flex-start;
    }
}
</style>
@endsection
