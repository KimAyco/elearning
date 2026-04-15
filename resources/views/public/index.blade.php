@extends('layouts.app')

@section('title', 'EduPlatform - Registered Schools')

@section('content')
<div class="lp-page">
    <header class="lp-nav">
        <div class="lp-container lp-nav-inner">
            <a class="lp-brand" href="{{ url('/') }}" aria-label="EduPlatform Home">
                <img class="lp-brand-logo" src="{{ asset('images/logo.png') }}" alt="EduPlatform logo">
                <span class="lp-brand-text">EDUPLATFORM</span>
            </a>

            <nav class="lp-links" aria-label="Primary navigation">
                <a class="lp-link" href="{{ url('/') }}">Home</a>
                <a class="lp-link" href="#services">Services</a>
                <a class="lp-link" href="#about">About</a>
                <a class="lp-link" href="#contact">Contact</a>
                <a class="lp-link" href="#faq">FAQ</a>
            </nav>
        </div>
    </header>

    <section class="lp-hero" aria-label="EduPlatform hero">
        <div class="lp-hero-bg-right" aria-hidden="true">
                    </div>
        <div class="lp-container lp-hero-inner">
            <div class="lp-hero-left">
                <h1 class="lp-hero-title">
                    <span class="lp-hero-edu">EDU</span>
                    <span class="lp-hero-platform">PLATFORM</span>
                </h1>

                <div class="lp-hero-subtitle">Your way to knowledge</div>

                <p class="lp-hero-description">
                    Learn faster with a modern school platform built for students, teachers, and administrators.
                </p>

                <div class="lp-hero-continue">REGISTER OR SIGN IN TO CONTINUE</div>

                <div class="lp-hero-actions">
                    <a href="{{ route('register.school.form') }}" class="lp-btn lp-btn-primary">Register school</a>
                    <a href="{{ url('/login') }}" class="lp-btn lp-btn-secondary">Sign in</a>
                </div>
            </div>

            <div class="lp-hero-right" aria-hidden="true">
                <img class="lp-hero-image" src="{{ asset('images/model.png') }}" alt="">
            </div>
        </div>
    </section>

    <main class="lp-main lp-container">
        @if (session('status'))
            <div class="lp-alert lp-alert--success">
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="lp-alert lp-alert--error">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <section class="lp-controls" aria-label="School search and filter">
            <div class="lp-search">
                <span class="lp-search-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="6"></circle>
                        <line x1="16.5" y1="16.5" x2="21" y2="21"></line>
                    </svg>
                </span>
                <input
                    type="text"
                    name="q"
                    placeholder="Search by name or city..."
                    class="lp-search-input"
                >
            </div>

            <div class="lp-filters">
                <div class="lp-filters-label">Filter by type</div>
                <div class="lp-chips" role="group" aria-label="School type filters">
                    <button type="button" class="lp-chip lp-chip-active">All</button>
                    <button type="button" class="lp-chip">Private</button>
                    <button type="button" class="lp-chip">Public</button>
                    <button type="button" class="lp-chip">Charter</button>
                    <button type="button" class="lp-chip">Vocational</button>
                </div>
            </div>
        </section>

        <section class="lp-explore" aria-label="Explore schools">
            <div class="lp-section-heading">
                <h2>EXPLORE SCHOOLS</h2>
            </div>

            @php
            $fallbackImage = 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?auto=format&fit=crop&q=80&w=800';
        @endphp

            @if ($schools->isEmpty())
                <div class="lp-empty">
                    <div class="lp-empty-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <h3>No schools yet</h3>
                    <p>Once schools are registered on EduPlatform, you will see them listed here.</p>
                </div>
            @else
                <div class="lp-carousel">
                    <button type="button" class="lp-carousel-arrow" data-carousel-prev aria-label="Previous schools">
                        &#8249;
                    </button>

                    <div class="lp-carousel-track" data-carousel-track>
                        @foreach ($schools as $school)
                            @php
                                $image = $school->cover_image_url ?: $fallbackImage;
                                $location = trim((string) ($school->short_description ?? '')) !== ''
                                    ? (string) $school->short_description
                                    : 'City, Country';
                                    $studentCount = $school->enrolled_students_count ?? null;
                                $students = $studentCount && $studentCount > 0 ? number_format($studentCount) : '—';
                        @endphp

                            <article class="lp-card lp-carousel-item">
                                <div class="lp-card-image">
                                <img
                                    src="{{ $image }}"
                                        alt="School cover for {{ $school->name }}"
                                    onerror="this.onerror=null;this.src='{{ $fallbackImage }}';"
                                >
                                </div>

                                <div class="lp-card-body">
                                    <h3 class="lp-card-title">{{ $school->name }}</h3>

                                    <div class="lp-card-row">
                                        <span class="lp-meta-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 7-9 12-9 12S3 17 3 10a9 9 0 1 1 18 0z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                    </span>
                                        <span class="lp-meta-text">{{ $location }}</span>
                                </div>

                                    <div class="lp-card-row">
                                        <span class="lp-meta-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                            </svg>
                                        </span>
                                        <span class="lp-meta-text">{{ $students }} students enrolled</span>
                                    </div>

                                <a
                                        class="lp-card-cta"
                                    href="{{ route('school.enroll', ['school_code' => $school->school_code]) }}"
                                >
                                        Go to school page
                                </a>
                            </div>
                        </article>
                    @endforeach
                    </div>

                    <button type="button" class="lp-carousel-arrow" data-carousel-next aria-label="Next schools">
                        &#8250;
                    </button>
                </div>
            @endif
        </section>
    </main>

    <footer class="lp-footer">
        <div class="lp-container">
        <p>© {{ date('Y') }} EduPlatform</p>
        </div>
    </footer>
</div>

<style>
    :root{
        --green:#22c55e;
        --green-2:#1e9e49;
        --ink:#0f172a;
        --muted:#6b7280;
        --bg:#f7faf8;
        --surface:#ffffff;
        --border:rgba(15, 23, 42, 0.10);
        --shadow:0 18px 55px rgba(2, 6, 23, 0.10);
        --shadow-soft:0 10px 28px rgba(2, 6, 23, 0.08);
        --radius:16px;
    }

    *{ box-sizing:border-box; }
    body{
        margin:0;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color:var(--ink);
        background:var(--bg);
        -webkit-font-smoothing:antialiased;
        -moz-osx-font-smoothing:grayscale;
    }

    .lp-page{ min-height:100vh; }

    .lp-container{
        max-width:1100px;
        margin:0 auto;
        padding:0 20px;
    }

    /* Navbar */
    .lp-nav{
        position:sticky;
        top:0;
        z-index:1000;
        background:var(--surface);
        box-shadow:0 6px 22px rgba(2, 6, 23, 0.06);
    }
    .lp-nav-inner{
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:16px;
        padding:12px 0;
    }
    .lp-brand{
        display:flex;
        align-items:center;
        gap:10px;
        text-decoration:none;
        color:#2e2e2e; /* match EDU color and prevent default link blue */
    }
    .lp-brand:hover,
    .lp-brand:focus,
    .lp-brand:active{
        text-decoration:none;
        color:#2e2e2e;
    }
    .lp-brand-logo{
        width:44px;
        height:44px;
        object-fit:contain;
    }
    .lp-brand-text{
        font-weight:900;
        letter-spacing:0.06em;
        font-size:1.15rem;
        color:inherit;
    }
    .lp-links{
        display:flex;
        align-items:center;
        gap:22px;
        flex-wrap:wrap;
        justify-content:flex-end;
    }
    .lp-link{
        text-decoration:none;
        color:rgba(15, 23, 42, 0.85);
        font-weight:700;
        font-size:0.85rem;
        text-transform:uppercase;
        letter-spacing:0.02em;
    }
    .lp-link:hover{
        color:var(--green);
        text-decoration:underline;
        text-underline-offset:4px;
    }

    /* Hero */
    .lp-hero{
        position:relative;
        overflow:hidden;
        padding:56px 0 46px;
        background:linear-gradient(180deg, rgba(34,197,94,0.06) 0%, rgba(34,197,94,0.00) 60%);
    }
    .lp-hero::before{
        content:'';
        position:absolute;
        left:-240px;
        bottom:-380px;
        width:760px;
        height:760px;
        background:rgba(34,197,94,0.18);
        border-radius:55% 45% 40% 60% / 45% 55% 45% 55%;
        z-index:0;
    }
    .lp-hero-bg-right{
        position:absolute;
        inset:0;
        z-index:0;
        overflow:hidden;
        pointer-events:none;
    }
    .lp-hero-bg-right::before{
        content:'';
        position:absolute;
        right:-150px;
        top:-210px;
        width:700px;
        height:660px;
        background:linear-gradient(135deg, #6cc840 0%, #56b61f 100%);
        border-radius:58% 42% 43% 57% / 56% 43% 57% 44%;
        transform:rotate(5deg);
        box-shadow:-18px 20px 0 rgba(15,23,42,0.07);
    }
    .lp-hero-inner{
        position:relative;
        z-index:1;
        display:flex;
        align-items:stretch;          /* allow columns to align to bottom */
        justify-content:space-between;
        gap:24px;
    }
    .lp-hero-left{
        max-width:520px;
        margin-top:58px; /* move hero text block toward vertical center */
    }
    .lp-hero-title{
        margin:0 0 10px;
        font-size:3.6rem;
        line-height:1;
        font-weight:1000;
        letter-spacing:0.01em;
        display:flex;
        flex-direction:column; /* EDU on top of PLATFORM */
        gap:2px;
    }
    .lp-hero-edu{ color:#2e2e2e; }
    .lp-hero-platform{ color:#58ad07; }
    .lp-hero-subtitle{
        font-weight:900;
        letter-spacing:0.04em;
        color:var(--green);
        margin-bottom:12px;
        text-transform:none;
        font-size:1.05rem;
    }
    .lp-hero-description{
        margin:0 0 20px;
        color:rgba(15, 23, 42, 0.72);
        font-size:1.08rem;
        max-width:460px;
        line-height:1.6;
    }
    .lp-hero-continue{
        margin:4px 0 14px;
        font-weight:900;
        letter-spacing:0.05em;
        text-transform:uppercase;
        font-size:0.85rem;
        color:#329200;
    }
    .lp-hero-actions{
        display:flex;
        align-items:center;
        gap:12px;
        flex-wrap:wrap;
    }

    .lp-btn{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        padding:11px 20px;
        border-radius:999px;
        font-weight:800;
        font-size:0.95rem;
        text-decoration:none;
        border:1px solid transparent;
        transition:transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease, border-color 0.25s ease;
        white-space:nowrap;
        text-decoration:none;
    }
    .lp-btn:hover,
    .lp-btn:focus,
    .lp-btn:active{
        transform:translateY(-1px);
        text-decoration:none;
    }
    .lp-btn-primary{
        background:var(--green);
        color:#fff;
        border-color:rgba(34,197,94,0.6);
        box-shadow:0 12px 26px rgba(34,197,94,0.22);
    }
    .lp-btn-secondary{
        background:#fff;
        color:var(--green);
        border-color:rgba(34,197,94,0.28);
        box-shadow:0 10px 22px rgba(2,6,23,0.06);
    }
    .lp-btn-secondary:hover{
        background:#f0fdf4;
        border-color:rgba(34,197,94,0.45);
    }

    .lp-hero-right{
        width:46%;
        position:relative;           /* anchor only the model image */
        min-height:440px;            /* match smaller model scale */
        overflow:visible;
        z-index:1;
    }
    .lp-hero-image{
        width:500px;          /* smaller to match reference better */
        max-width:none;       /* allow overflow to keep size */
        height:auto;
        display:block;
        position:absolute;
        right:-34px;          /* keep overlap on green shape */
        bottom:-62px;         /* move model further downward */
        transform:rotate(-0.5deg);
        margin:0;
    }

    /* Main */
    .lp-main{ padding:18px 0 48px; }

    .lp-controls{
        display:flex;
        align-items:flex-end;
        justify-content:space-between;
        gap:18px;
        margin-top:-8px;
        flex-wrap:wrap;
        padding-top:10px;
    }

    .lp-search{
        position:relative;
        flex:1 1 320px;
        max-width:420px;
    }
    .lp-search-icon{
        position:absolute;
        left:14px;
        top:50%;
        transform:translateY(-50%);
        color:rgba(15, 23, 42, 0.4);
        display:flex;
        align-items:center;
        justify-content:center;
        pointer-events:none;
    }
    .lp-search-icon svg{ width:18px; height:18px; }
    .lp-search-input{
        width:100%;
        padding:12px 16px 12px 44px;
        border:1px solid rgba(15, 23, 42, 0.10);
        border-radius:999px;
        background:#fff;
        outline:none;
        box-shadow:0 10px 22px rgba(2,6,23,0.04);
        transition:border-color 0.2s ease, box-shadow 0.2s ease;
        font-size:0.92rem;
    }
    .lp-search-input:focus{
        border-color:rgba(34,197,94,0.55);
        box-shadow:0 0 0 4px rgba(34,197,94,0.15);
    }

    .lp-filters{ display:flex; align-items:center; gap:14px; }
    .lp-filters-label{ font-weight:700; color:var(--muted); font-size:0.86rem; }
    .lp-chips{ display:flex; gap:8px; flex-wrap:wrap; }
    .lp-chip{
        border-radius:999px;
        border:1px solid rgba(15, 23, 42, 0.12);
        background:#fff;
        color:rgba(15, 23, 42, 0.65);
        font-weight:700;
        font-size:0.78rem;
        padding:8px 14px;
        cursor:default;
        transition:background 0.2s ease, color 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
    }
    .lp-chip:hover{ transform:translateY(-1px); }
    .lp-chip-active{
        background:var(--green);
        color:#fff;
        border-color:rgba(34,197,94,0.7);
        box-shadow:0 10px 26px rgba(34,197,94,0.22);
    }

    /* Explore */
    .lp-section-heading{
        margin:18px 0 14px;
        text-align:center;
    }
    .lp-section-heading h2{
        margin:0;
        font-weight:1000;
        font-size:1.05rem;
        letter-spacing:0.05em;
    }

    .lp-carousel{
        position:relative;
        padding:8px 0;
    }
    .lp-carousel-track{
        display:flex;
        gap:18px;
        overflow-x:auto;
        scroll-snap-type:x mandatory;
        padding:8px 52px;
        scrollbar-width:none;
    }
    .lp-carousel-track::-webkit-scrollbar{ width:0; height:0; }

    .lp-carousel-item{
        flex:0 0 calc(33.333% - 12px);
        scroll-snap-align:start;
    }

    .lp-carousel-arrow{
        position:absolute;
        top:50%;
        transform:translateY(-50%);
        width:42px;
        height:42px;
        border-radius:999px;
        border:1px solid rgba(34,197,94,0.25);
        background:#fff;
        color:var(--green);
        cursor:pointer;
        box-shadow:0 10px 26px rgba(34,197,94,0.18);
        transition:transform 0.2s ease, box-shadow 0.2s ease;
        display:flex;
        align-items:center;
        justify-content:center;
        z-index:2;
        user-select:none;
    }
    .lp-carousel-arrow:hover{
        transform:translateY(-50%) scale(1.03);
        box-shadow:0 16px 40px rgba(34,197,94,0.25);
    }
    .lp-carousel-arrow[data-carousel-prev]{ left:0; }
    .lp-carousel-arrow[data-carousel-next]{ right:0; }

    /* Cards */
    .lp-card{
        background:#fff;
        border:1px solid rgba(34,197,94,0.20);
        border-radius:var(--radius);
        overflow:hidden;
        box-shadow:0 10px 28px rgba(2,6,23,0.06);
        transition:transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        display:flex;
        flex-direction:column;
    }
    .lp-card:hover{
        transform:translateY(-3px);
        border-color:rgba(34,197,94,0.65);
        box-shadow:0 22px 60px rgba(2,6,23,0.12);
    }
    .lp-card-image{
        height:170px;
        background:#f1f5f9;
        overflow:hidden;
    }
    .lp-card-image img{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
    }
    .lp-card-body{
        padding:16px 16px 18px;
        display:flex;
        flex-direction:column;
        gap:12px;
        flex:1;
    }
    .lp-card-title{
        margin:0;
        font-size:1.02rem;
        font-weight:900;
        color:var(--ink);
        line-height:1.2;
    }
    .lp-card-row{
        display:flex;
        align-items:center;
        gap:8px;
        color:rgba(15, 23, 42, 0.68);
        font-weight:600;
        font-size:0.88rem;
    }
    .lp-meta-icon{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:18px;
        height:18px;
        color:rgba(34,197,94,0.9);
    }
    .lp-meta-icon svg{ width:16px; height:16px; }
    .lp-meta-text{
        overflow:hidden;
        text-overflow:ellipsis;
        white-space:nowrap;
    }
    .lp-card-cta{
        margin-top:auto;
        width:100%;
        text-align:center;
        text-decoration:none;
        padding:10px 14px;
        border-radius:999px;
        background:var(--green);
        color:#fff;
        font-weight:900;
        font-size:0.9rem;
        transition:transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        box-shadow:0 14px 30px rgba(34,197,94,0.22);
    }
    .lp-card-cta:hover{
        transform:translateY(-1px);
        background:var(--green-2);
        box-shadow:0 18px 44px rgba(34,197,94,0.28);
    }

    /* Empty */
    .lp-empty{
        border-radius:20px;
        border:1px dashed rgba(15, 23, 42, 0.15);
        background:#fff;
        padding:42px 16px;
        text-align:center;
    }
    .lp-empty-icon{
        width:56px;
        height:56px;
        border-radius:999px;
        display:flex;
        align-items:center;
        justify-content:center;
        background:rgba(34,197,94,0.10);
        color:rgba(15, 23, 42, 0.35);
        margin:0 auto 12px;
    }
    .lp-empty-icon svg{ width:30px; height:30px; }
    .lp-empty h3{ margin:0 0 6px; font-size:1rem; font-weight:900; }
    .lp-empty p{ margin:0; color:var(--muted); }

    /* Footer */
    .lp-footer{
        border-top:1px solid rgba(15, 23, 42, 0.08);
        background:#fff;
        padding:18px 0 24px;
        color:rgba(15, 23, 42, 0.5);
        font-size:0.88rem;
    }
    .lp-footer p{ margin:0; text-align:center; }

    /* Alerts (simple, avoid frameworks) */
    .lp-alert{
        margin:18px auto 0;
        max-width:720px;
        padding:12px 14px;
        border-radius:14px;
        border:1px solid rgba(34,197,94,0.22);
        background:rgba(34,197,94,0.08);
        color:rgba(15, 23, 42, 0.85);
        font-weight:700;
    }
    .lp-alert--error{
        border-color:rgba(239,68,68,0.25);
        background:rgba(239,68,68,0.08);
    }

    /* Responsive */
    @media (max-width: 980px){
        .lp-carousel-item{ flex:0 0 calc(50% - 9px); }
        .lp-hero-bg-right::before{
            right:-210px;
            top:-185px;
            width:620px;
            height:590px;
        }
        .lp-hero-right{
            min-height:360px;
        }
        .lp-hero-image{
            width:360px;
            max-width:100%;
            right:-12px;
            bottom:-46px;
        }
    }

    @media (max-width: 640px){
        .lp-hero-bg-right::before{
            right:-250px;
            top:-165px;
            width:560px;
            height:520px;
        }
        .lp-links{ gap:14px; justify-content:flex-start; }
        .lp-hero-inner{ flex-direction:column; align-items:flex-start; }
        .lp-hero-right{
            width:100%;
            min-height:auto;
        }
        .lp-hero-image{
            width:260px;
            max-width:100%;
            position:relative;
            right:auto;
            bottom:auto;
            margin-top:8px;
        }
        .lp-controls{ align-items:flex-start; }
        .lp-filters{ flex-direction:column; align-items:flex-start; }
        .lp-carousel-track{ padding:8px 44px; }
        .lp-carousel-item{ flex:0 0 86%; }
}
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const track = document.querySelector('[data-carousel-track]');
        if (!track) return;

        const prev = document.querySelector('[data-carousel-prev]');
        const next = document.querySelector('[data-carousel-next]');

        const amount = 300;

        if (prev) {
            prev.addEventListener('click', function () {
                track.scrollBy({ left: -amount, behavior: 'smooth' });
            });
        }

        if (next) {
            next.addEventListener('click', function () {
                track.scrollBy({ left: amount, behavior: 'smooth' });
            });
        }
    });
</script>
@endpush
@endsection

