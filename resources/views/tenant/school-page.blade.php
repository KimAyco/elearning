@extends('layouts.app')

@section('title', 'Branding & Public Page - School Portal')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css" integrity="sha512-hvNR0F/e2J7zPPfLC1au5t/7vOuRqJ8+4RJRPr4VZT1H0f4ZGW+3EHTBzI3hmU1dXoy6nE0TxAhPMErrnbQ+fg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')
@include('tenant.partials.tenant-mock-ui')
<div class="app-shell tenant-ui-mock">
    @include('tenant.partials.sidebar', ['active' => $active ?? 'school-page', 'sidebarClass' => 'sidebar--edu-mock'])

    <div class="main-content">
        @include('tenant.partials.mock-topbar')

        <main class="page-body">
            <div class="school-page-intro school-page-intro--row">
                <div class="school-page-intro__left">
                    <h1 class="school-page-title">School space</h1>
                    <p class="school-page-sub">Customize how your school appears on the public enrollment page: logo, cover, theme, content, and footer.</p>
                    <p class="school-page-footer-lead">Use the live preview above for text and images. <a href="#school-page-footer-settings">Footer, contact, quick links &amp; social icons</a> are edited in the section below the preview—then click <strong>Save changes</strong>.</p>
                </div>
                <div class="school-page-intro__right">
                    <button type="button" class="btn secondary sm school-page-preview-btn" id="open-public-preview">
                        Preview
                    </button>
                </div>
            </div>

            @php
                $profile = $school->profile;
                $existingFooterLinks = ($profile && is_array($profile->footer_quick_links)) ? $profile->footer_quick_links : [];
                if (old('footer_link_labels') !== null || old('footer_link_urls') !== null) {
                    $lbls = array_pad((array) old('footer_link_labels', []), 8, '');
                    $urls = array_pad((array) old('footer_link_urls', []), 8, '');
                    $footerLinkRows = [];
                    for ($i = 0; $i < 8; $i++) {
                        $footerLinkRows[] = ['label' => (string) ($lbls[$i] ?? ''), 'url' => (string) ($urls[$i] ?? '')];
                    }
                } else {
                    $footerLinkRows = [];
                    for ($i = 0; $i < 8; $i++) {
                        $footerLinkRows[] = [
                            'label' => (string) ($existingFooterLinks[$i]['label'] ?? ''),
                            'url' => (string) ($existingFooterLinks[$i]['url'] ?? ''),
                        ];
                    }
                }
            @endphp

            <section class="school-page-preview-shell">
                <div class="school-page-preview-header">
                    <div>
                        <span class="school-page-preview-label">Live public page preview</span>
                        <span class="school-page-preview-hint">Click any area to edit text or images.</span>
                    </div>
                    <div class="school-page-theme-picker">
                        <label for="theme-picker">Theme</label>
                        @php
                            $currentTheme = old('theme', $school->theme ?? '');
                        @endphp
                        <select id="theme-picker" class="input">
                            <option value="">Default</option>
                            <option value="blue" {{ $currentTheme === 'blue' ? 'selected' : '' }}>Blue</option>
                            <option value="green" {{ $currentTheme === 'green' ? 'selected' : '' }}>Green</option>
                            <option value="indigo" {{ $currentTheme === 'indigo' ? 'selected' : '' }}>Indigo</option>
                            <option value="slate" {{ $currentTheme === 'slate' ? 'selected' : '' }}>Slate</option>
                            <option value="teal" {{ $currentTheme === 'teal' ? 'selected' : '' }}>Teal</option>
                            <option value="amber" {{ $currentTheme === 'amber' ? 'selected' : '' }}>Amber</option>
                            <option value="rose" {{ $currentTheme === 'rose' ? 'selected' : '' }}>Rose</option>
                            <option value="purple" {{ $currentTheme === 'purple' ? 'selected' : '' }}>Purple</option>
                            <option value="emerald" {{ $currentTheme === 'emerald' ? 'selected' : '' }}>Emerald</option>
                            <option value="sky" {{ $currentTheme === 'sky' ? 'selected' : '' }}>Sky</option>
                        </select>
                    </div>
                </div>
                <div class="school-detail-card school-preview-card school-theme-{{ $school->theme ?? 'blue' }}">
                    <div
                        class="school-cover"
                        id="preview-cover"
                        aria-hidden="true"
                        @if($school->cover_image_url)
                            style="background-image: linear-gradient(120deg, rgba(15,23,42,0.15), rgba(37,99,235,0.35)), url('{{ $school->cover_image_url }}');"
                        @endif
                    ></div>

                    <header class="school-detail-header">
                        <div class="school-avatar" id="preview-logo">
                            @if ($school->logo_url)
                                <img src="{{ $school->logo_url }}" alt="" width="112" height="112" class="school-avatar-img">
                            @else
                                <span class="school-avatar-initial">
                                    {{ strtoupper(mb_substr($school->name, 0, 1)) }}
                                </span>
                            @endif
                            <span class="school-avatar-plus" aria-hidden="true">+</span>
                        </div>
                        <div class="school-heading">
                            <div class="school-heading-top">
                                <h1 id="preview-name" contenteditable="true">{{ old('name', $school->name) }}</h1>
                                <span class="school-code-pill">{{ $school->school_code }}</span>
                            </div>
                            <p class="school-location" id="preview-location" contenteditable="true">{{ $school->short_description ?: 'School short description / location' }}</p>
                        </div>
                        <div class="school-header-actions">
                            <button type="button" class="btn primary lg" disabled>Enroll now</button>
                            <button type="button" class="btn secondary lg" disabled>Sign in</button>
                        </div>
                    </header>

                    <div class="school-detail-body">
                        <div class="school-detail-main">
                            <h2>Welcome to {{ $school->name }}</h2>
                            @if ($profile && $profile->intro)
                                <p class="school-intro" id="preview-intro" contenteditable="true">{{ $profile->intro }}</p>
                            @else
                                <p class="school-intro" id="preview-intro" contenteditable="true">
                                    A <span class="school-highlight">student‑centered</span> institution offering
                                    <span class="school-highlight">industry‑aligned programs</span>, supportive faculty, and a
                                    modern digital campus powered by EduPlatform.
                                </p>
                            @endif

                            <div class="school-tags">
                                <span class="school-tag primary" id="preview-tag-primary" contenteditable="true">
                                    {{ $profile && $profile->tag_primary ? $profile->tag_primary : 'Primary tag' }}
                                </span>
                                <span class="school-tag neutral" id="preview-tag-neutral" contenteditable="true">
                                    {{ $profile && $profile->tag_neutral ? $profile->tag_neutral : 'Secondary tag' }}
                                </span>
                                <span class="school-tag accent" id="preview-tag-accent" contenteditable="true">
                                    {{ $profile && $profile->tag_accent ? $profile->tag_accent : 'Accent tag' }}
                                </span>
                            </div>

                            @php
                                $facts = [
                                    [
                                        'label' => $profile->fact1_label ?? null,
                                        'value' => $profile->fact1_value ?? null,
                                        'caption' => $profile->fact1_caption ?? null,
                                        'fallback_label' => 'Label 1',
                                        'fallback_value' => 'Val 1',
                                        'fallback_caption' => 'Cap 1',
                                        'prefix' => 'fact1',
                                    ],
                                    [
                                        'label' => $profile->fact2_label ?? null,
                                        'value' => $profile->fact2_value ?? null,
                                        'caption' => $profile->fact2_caption ?? null,
                                        'fallback_label' => 'Label 2',
                                        'fallback_value' => 'Val 2',
                                        'fallback_caption' => 'Cap 2',
                                        'prefix' => 'fact2',
                                    ],
                                    [
                                        'label' => $profile->fact3_label ?? null,
                                        'value' => $profile->fact3_value ?? null,
                                        'caption' => $profile->fact3_caption ?? null,
                                        'fallback_label' => 'Label 3',
                                        'fallback_value' => 'Val 3',
                                        'fallback_caption' => 'Cap 3',
                                        'prefix' => 'fact3',
                                    ],
                                ];
                            @endphp
                            <div class="school-facts-grid">
                                @foreach ($facts as $fact)
                                    <div class="school-fact" id="preview-{{ $fact['prefix'] }}">
                                        <div class="fact-label" id="preview-{{ $fact['prefix'] }}-label" contenteditable="true">
                                            {{ $fact['label'] ?: $fact['fallback_label'] }}
                                        </div>
                                        <div class="fact-value" id="preview-{{ $fact['prefix'] }}-value" contenteditable="true">
                                            {{ $fact['value'] ?: $fact['fallback_value'] }}
                                        </div>
                                        <div class="fact-caption" id="preview-{{ $fact['prefix'] }}-caption" contenteditable="true">
                                            {{ $fact['caption'] ?: $fact['fallback_caption'] }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="school-section" id="preview-campus">
                                <h3 id="preview-campus-title" contenteditable="true">{{ $profile && $profile->campus_title ? $profile->campus_title : 'Campus life & student support' }}</h3>
                                <ul class="school-bullets">
                                    @php
                                        $bullets = [
                                            $profile->campus_bullet1 ?? null,
                                            $profile->campus_bullet2 ?? null,
                                            $profile->campus_bullet3 ?? null,
                                            $profile->campus_bullet4 ?? null,
                                        ];
                                        $hasBullets = collect($bullets)->filter()->isNotEmpty();
                                    @endphp
                                    @if ($hasBullets)
                                        @foreach ($bullets as $idx => $bullet)
                                            @if ($bullet)
                                                <li id="preview-campus-bullet-{{ $idx + 1 }}" contenteditable="true">{{ $bullet }}</li>
                                            @endif
                                        @endforeach
                                    @else
                                        <li id="preview-campus-bullet-1" contenteditable="true">Modern smart classrooms, laboratories, and learning resource centers.</li>
                                        <li id="preview-campus-bullet-2" contenteditable="true">Active student organizations, athletics, and cultural affairs office.</li>
                                        <li id="preview-campus-bullet-3" contenteditable="true">Scholarship and financial assistance programs for qualified students.</li>
                                        <li id="preview-campus-bullet-4" contenteditable="true">Career & placement services to support internships and graduate employability.</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="school-page-footer-cta" role="note">
                <p><strong>Public page footer</strong> — description, address, email, phone, quick links, and social URLs (shown as icons only on your public page) are configured below.</p>
                <a href="#school-page-footer-settings" class="school-page-footer-cta__jump">Jump to footer settings</a>
            </div>

            @if (session('status'))
                <div class="alert success" style="margin-bottom:1rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert error" style="margin-bottom:1rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ url('/tenant/school-page') }}" method="post" enctype="multipart/form-data" class="school-page-form">
                @csrf

                {{-- Hidden header logo inputs (changed via live preview avatar) --}}
                <input type="file" name="logo" id="logo" accept="image/*" class="school-logo-picker__input" data-crop="logo" tabindex="-1">
                <input type="hidden" name="logo_data" id="logo_data" value="">

                <div class="school-page-card">
                    <h2 class="school-page-card-title">Basic info</h2>
                    <div class="form-group">
                        <label for="name">School name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $school->name) }}" maxlength="150" class="input">
                    </div>
                    <div class="form-group">
                        <label for="short_description">Short description / location</label>
                        <input type="text" id="short_description" name="short_description" value="{{ old('short_description', $school->short_description) }}" maxlength="255" class="input">
                    </div>
                </div>

                <div class="school-page-card">
                    <h2 class="school-page-card-title">Cover / background photo</h2>
                    <p class="school-page-card-hint">Banner at the top of the public school card. Upload an image and crop it to a square (1:1) before saving.</p>
                    <div class="school-page-preview-row">
                        <div class="school-page-cover-preview">
                            <div class="school-page-cover-placeholder" id="cover-placeholder" style="{{ $school->cover_image_url ? 'display:none;' : '' }}">No cover image</div>
                            <img src="{{ $school->cover_image_url ?? '' }}" alt="Cover" id="cover-preview-img" style="width:100%; max-width:320px; height:320px; object-fit:cover; border-radius:8px; {{ $school->cover_image_url ? '' : 'display:none;' }}">
                        </div>
                        <div class="school-page-upload-actions">
                            <input type="file" name="cover" id="cover" accept="image/*" class="input" data-crop="cover">
                            <input type="hidden" name="cover_data" id="cover_data" value="">
                            <label class="school-page-checkbox-label" style="margin-top:8px;">
                                <input type="checkbox" name="remove_cover" value="1" id="remove_cover"> Remove cover (use default)
                            </label>
                        </div>
                    </div>
                </div>

                <div class="school-page-card">
                    <h2 class="school-page-card-title">Color theme</h2>
                    <p class="school-page-card-hint">Applies to buttons and accents on the public school page.</p>
                    <div class="form-group">
                        <label for="theme">Theme</label>
                        <select id="theme" name="theme" class="input" style="max-width:200px;">
                            <option value="">Default</option>
                            <option value="blue" {{ old('theme', $school->theme) === 'blue' ? 'selected' : '' }}>Blue</option>
                            <option value="green" {{ old('theme', $school->theme) === 'green' ? 'selected' : '' }}>Green</option>
                            <option value="indigo" {{ old('theme', $school->theme) === 'indigo' ? 'selected' : '' }}>Indigo</option>
                            <option value="slate" {{ old('theme', $school->theme) === 'slate' ? 'selected' : '' }}>Slate</option>
                            <option value="teal" {{ old('theme', $school->theme) === 'teal' ? 'selected' : '' }}>Teal</option>
                            <option value="amber" {{ old('theme', $school->theme) === 'amber' ? 'selected' : '' }}>Amber</option>
                            <option value="rose" {{ old('theme', $school->theme) === 'rose' ? 'selected' : '' }}>Rose</option>
                            <option value="purple" {{ old('theme', $school->theme) === 'purple' ? 'selected' : '' }}>Purple</option>
                            <option value="emerald" {{ old('theme', $school->theme) === 'emerald' ? 'selected' : '' }}>Emerald</option>
                            <option value="sky" {{ old('theme', $school->theme) === 'sky' ? 'selected' : '' }}>Sky</option>
                        </select>
                    </div>
                </div>

                <div class="school-page-card">
                    <h2 class="school-page-card-title">Public page content</h2>
                    <p class="school-page-card-hint">Customize the welcome text, highlight tags, and key facts shown on your public school page.</p>
                    <div class="form-group">
                        <label for="intro">Intro paragraph</label>
                        <textarea id="intro" name="intro" class="input" rows="3" style="resize:vertical;">{{ old('intro', optional($profile)->intro) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Highlight tags</label>
                        <div style="display:flex; flex-wrap:wrap; gap:8px;">
                            <input type="text" id="tag_primary" name="tag_primary" class="input" placeholder="Primary tag" style="flex:1; min-width:160px;" value="{{ old('tag_primary', optional($profile)->tag_primary) }}">
                            <input type="text" id="tag_neutral" name="tag_neutral" class="input" placeholder="Secondary tag" style="flex:1; min-width:160px;" value="{{ old('tag_neutral', optional($profile)->tag_neutral) }}">
                            <input type="text" id="tag_accent" name="tag_accent" class="input" placeholder="Accent tag" style="flex:1; min-width:160px;" value="{{ old('tag_accent', optional($profile)->tag_accent) }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Key facts (3 columns)</label>
                        <div class="school-page-facts-grid">
                            <div>
                                <input type="text" id="fact1_label" name="fact1_label" class="input" placeholder="Fact 1 label" value="{{ old('fact1_label', optional($profile)->fact1_label) }}" style="margin-bottom:6px;">
                                <input type="text" id="fact1_value" name="fact1_value" class="input" placeholder="Fact 1 value" value="{{ old('fact1_value', optional($profile)->fact1_value) }}" style="margin-bottom:6px;">
                                <input type="text" id="fact1_caption" name="fact1_caption" class="input" placeholder="Fact 1 caption" value="{{ old('fact1_caption', optional($profile)->fact1_caption) }}">
                            </div>
                            <div>
                                <input type="text" id="fact2_label" name="fact2_label" class="input" placeholder="Fact 2 label" value="{{ old('fact2_label', optional($profile)->fact2_label) }}" style="margin-bottom:6px;">
                                <input type="text" id="fact2_value" name="fact2_value" class="input" placeholder="Fact 2 value" value="{{ old('fact2_value', optional($profile)->fact2_value) }}" style="margin-bottom:6px;">
                                <input type="text" id="fact2_caption" name="fact2_caption" class="input" placeholder="Fact 2 caption" value="{{ old('fact2_caption', optional($profile)->fact2_caption) }}">
                            </div>
                            <div>
                                <input type="text" id="fact3_label" name="fact3_label" class="input" placeholder="Fact 3 label" value="{{ old('fact3_label', optional($profile)->fact3_label) }}" style="margin-bottom:6px;">
                                <input type="text" id="fact3_value" name="fact3_value" class="input" placeholder="Fact 3 value" value="{{ old('fact3_value', optional($profile)->fact3_value) }}" style="margin-bottom:6px;">
                                <input type="text" id="fact3_caption" name="fact3_caption" class="input" placeholder="Fact 3 caption" value="{{ old('fact3_caption', optional($profile)->fact3_caption) }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="campus_title">Campus life section title</label>
                        <input type="text" id="campus_title" name="campus_title" class="input" value="{{ old('campus_title', optional($profile)->campus_title) }}" placeholder="e.g. Campus life & student support">
                    </div>
                    <div class="form-group">
                        <label>Campus life bullets</label>
                        <input type="text" id="campus_bullet1" name="campus_bullet1" class="input" style="margin-bottom:6px;" placeholder="Bullet 1" value="{{ old('campus_bullet1', optional($profile)->campus_bullet1) }}">
                        <input type="text" id="campus_bullet2" name="campus_bullet2" class="input" style="margin-bottom:6px;" placeholder="Bullet 2" value="{{ old('campus_bullet2', optional($profile)->campus_bullet2) }}">
                        <input type="text" id="campus_bullet3" name="campus_bullet3" class="input" style="margin-bottom:6px;" placeholder="Bullet 3" value="{{ old('campus_bullet3', optional($profile)->campus_bullet3) }}">
                        <input type="text" id="campus_bullet4" name="campus_bullet4" class="input" placeholder="Bullet 4" value="{{ old('campus_bullet4', optional($profile)->campus_bullet4) }}">
                    </div>
                </div>

                <div id="school-page-footer-settings" class="school-page-card school-page-card--visible">
                    <h2 class="school-page-card-title">Public page footer</h2>
                    <p class="school-page-card-hint">Shown at the bottom of your public school enrollment page. Social links display as icons only; leave a field empty to hide that network. Link and contact colors follow your <strong>Color theme</strong> in the preview header.</p>

                    <div class="school-page-logo-pair">
                        <div class="school-page-logo-pair__col">
                            <h3 class="school-page-card-title school-page-card-title--sub">Footer logo</h3>
                            <p class="school-page-card-hint">Shown only in the public page footer. Click the circle to change. Square (1:1) crop.</p>
                            <div class="school-logo-picker-wrap">
                                <label for="footer_logo" class="school-logo-picker__trigger" title="Change footer logo">
                                    <span class="school-logo-picker__media">
                                        <span class="school-logo-picker__initial" id="footer-logo-placeholder" style="{{ $school->footer_logo_url ? 'display:none;' : '' }}">{{ strtoupper(mb_substr($school->name, 0, 1)) }}</span>
                                        <img src="{{ $school->footer_logo_url ?? '' }}" alt="" id="footer-logo-preview-img" width="112" height="112" style="border-radius:999px; object-fit:cover; {{ $school->footer_logo_url ? '' : 'display:none;' }}">
                                    </span>
                                    <span class="school-logo-picker__plus" aria-hidden="true">+</span>
                                </label>
                                <input type="file" name="footer_logo" id="footer_logo" accept="image/*" class="school-logo-picker__input" data-crop="footer_logo" tabindex="-1">
                                <input type="hidden" name="footer_logo_data" id="footer_logo_data" value="">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="footer_title">Footer title (optional)</label>
                        <input type="text" id="footer_title" name="footer_title" class="input" maxlength="255" placeholder="Defaults to school name if empty" value="{{ old('footer_title', optional($profile)->footer_title) }}">
                    </div>
                    <div class="form-group">
                        <label for="footer_description">Short footer description</label>
                        <textarea id="footer_description" name="footer_description" class="input" rows="2" style="resize:vertical;" maxlength="1000" placeholder="One or two lines about your institution">{{ old('footer_description', optional($profile)->footer_description) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="footer_address">Address</label>
                        <textarea id="footer_address" name="footer_address" class="input" rows="2" style="resize:vertical;" placeholder="Street, city, region">{{ old('footer_address', optional($profile)->footer_address) }}</textarea>
                    </div>
                    <div class="form-group school-footer-contact-row">
                        <div>
                            <label for="footer_email">Email</label>
                            <input type="email" id="footer_email" name="footer_email" class="input" value="{{ old('footer_email', optional($profile)->footer_email) }}" placeholder="info@school.edu">
                        </div>
                        <div>
                            <label for="footer_phone">Phone</label>
                            <input type="text" id="footer_phone" name="footer_phone" class="input" maxlength="80" value="{{ old('footer_phone', optional($profile)->footer_phone) }}" placeholder="+63 …">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="footer_copyright">Copyright line (optional)</label>
                        <input type="text" id="footer_copyright" name="footer_copyright" class="input" maxlength="500" placeholder="e.g. © {{ date('Y') }} {{ $school->name }}. All rights reserved." value="{{ old('footer_copyright', optional($profile)->footer_copyright) }}">
                    </div>

                    <div class="form-group">
                        <label>Quick links</label>
                        <p class="school-page-card-hint" style="margin-top:0;">Up to 8 links. Use full URLs (https://…).</p>
                        <div class="school-footer-links-editor">
                            @foreach($footerLinkRows as $idx => $row)
                                <div class="school-footer-links-editor__row">
                                    <input type="text" name="footer_link_labels[]" class="input" placeholder="Label" maxlength="120" value="{{ $row['label'] }}">
                                    <input type="text" name="footer_link_urls[]" class="input" placeholder="https://…" maxlength="500" value="{{ $row['url'] }}">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Social media (URL only)</label>
                        <p class="school-page-card-hint" style="margin-top:0;">Icons appear on the public page only when a URL is set.</p>
                        <div style="display:grid; gap:10px;">
                            <input type="text" name="footer_social_facebook" class="input" placeholder="Facebook URL" value="{{ old('footer_social_facebook', optional($profile)->footer_social_facebook) }}">
                            <input type="text" name="footer_social_instagram" class="input" placeholder="Instagram URL" value="{{ old('footer_social_instagram', optional($profile)->footer_social_instagram) }}">
                            <input type="text" name="footer_social_x" class="input" placeholder="X (Twitter) URL" value="{{ old('footer_social_x', optional($profile)->footer_social_x) }}">
                            <input type="text" name="footer_social_youtube" class="input" placeholder="YouTube URL" value="{{ old('footer_social_youtube', optional($profile)->footer_social_youtube) }}">
                            <input type="text" name="footer_social_website" class="input" placeholder="Main website URL" value="{{ old('footer_social_website', optional($profile)->footer_social_website) }}">
                        </div>
                    </div>
                </div>

                <div class="school-page-actions">
                    <button type="submit" class="btn primary">Save changes</button>
                </div>
            </form>
        </main>
    </div>
</div>

{{-- Crop modal: logo (square) --}}
<div id="crop-modal-logo" class="crop-modal" aria-hidden="true">
    <div class="crop-modal-backdrop"></div>
    <div class="crop-modal-box">
        <div class="crop-modal-header">
            <h3>Crop logo to square</h3>
            <button type="button" class="crop-modal-close" data-dismiss="crop-modal-logo" aria-label="Close">&times;</button>
        </div>
        <div class="crop-modal-body">
            <div class="crop-container" style="max-height:70vh;">
                <img id="crop-img-logo" src="" alt="Crop">
            </div>
        </div>
        <div class="crop-modal-footer">
            <button type="button" class="btn secondary crop-cancel" data-dismiss="crop-modal-logo">Cancel</button>
            <button type="button" class="btn primary crop-apply" data-crop="logo">Apply</button>
        </div>
    </div>
</div>

{{-- Crop modal: footer logo (square) --}}
<div id="crop-modal-footer-logo" class="crop-modal" aria-hidden="true">
    <div class="crop-modal-backdrop"></div>
    <div class="crop-modal-box">
        <div class="crop-modal-header">
            <h3>Crop footer logo to square</h3>
            <button type="button" class="crop-modal-close" data-dismiss="crop-modal-footer-logo" aria-label="Close">&times;</button>
        </div>
        <div class="crop-modal-body">
            <div class="crop-container" style="max-height:70vh;">
                <img id="crop-img-footer-logo" src="" alt="Crop">
            </div>
        </div>
        <div class="crop-modal-footer">
            <button type="button" class="btn secondary crop-cancel" data-dismiss="crop-modal-footer-logo">Cancel</button>
            <button type="button" class="btn primary crop-apply" data-crop="footer_logo">Apply</button>
        </div>
    </div>
</div>

{{-- Crop modal: cover (square) --}}
<div id="crop-modal-cover" class="crop-modal" aria-hidden="true">
    <div class="crop-modal-backdrop"></div>
    <div class="crop-modal-box">
        <div class="crop-modal-header">
            <h3>Crop cover to square</h3>
            <button type="button" class="crop-modal-close" data-dismiss="crop-modal-cover" aria-label="Close">&times;</button>
        </div>
        <div class="crop-modal-body">
            <div class="crop-container" style="max-height:70vh;">
                <img id="crop-img-cover" src="" alt="Crop">
            </div>
        </div>
        <div class="crop-modal-footer">
            <button type="button" class="btn secondary crop-cancel" data-dismiss="crop-modal-cover">Cancel</button>
            <button type="button" class="btn primary crop-apply" data-crop="cover">Apply</button>
        </div>
    </div>
</div>

{{-- Public page preview modal (iframe) --}}
<div id="public-preview-modal" class="public-preview-modal" aria-hidden="true" aria-label="Public page preview" role="dialog">
    <div class="public-preview-modal__backdrop" data-dismiss="public-preview-modal"></div>
    <div class="public-preview-modal__box" role="document">
        <div class="public-preview-modal__header">
            <div class="public-preview-modal__title">
                <strong>Public preview</strong>
                <span class="public-preview-modal__sub">This is what visitors see on your enrollment page.</span>
            </div>
            <div class="public-preview-modal__actions">
                <button type="button" class="btn secondary sm" data-dismiss="public-preview-modal">Close</button>
            </div>
        </div>
        <div class="public-preview-modal__body">
            <iframe
                id="public-preview-iframe"
                title="Public enrollment preview"
                src=""
                loading="lazy"
                referrerpolicy="no-referrer"
            ></iframe>
        </div>
    </div>
</div>

<style>
.school-page-intro { margin-bottom: 1.5rem; }
.school-page-title { font-size: 1.35rem; font-weight: 700; color: var(--ink); margin-bottom: 0.35rem; }
.school-page-sub { color: var(--muted); font-size: 0.9rem; }
.school-page-intro--row { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; }
.school-page-intro__left { min-width: 0; }
.school-page-intro__right { flex-shrink: 0; padding-top: 2px; }
.school-page-preview-btn { white-space: nowrap; }
@media (max-width: 720px) {
    .school-page-intro--row { flex-direction: column; align-items: stretch; }
    .school-page-intro__right { padding-top: 0; display: flex; justify-content: flex-end; }
}
.school-page-preview-shell {
    margin-bottom: 1.5rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border);
    background: #f3f4ff;
    box-shadow: var(--shadow-sm);
    padding: 0 0 18px;
}
.school-page-preview-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 16px 6px;
}
.school-page-preview-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #111827;
}
.school-page-preview-hint {
    font-size: 0.78rem;
    color: #6b7280;
}
.school-page-theme-picker {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
}
.school-page-theme-picker label {
    font-weight: 500;
    color: #4b5563;
}
.school-page-theme-picker select {
    min-width: 130px;
    font-size: 0.8rem;
    padding-inline: 8px;
}
.school-preview-card {
    margin: 0 10px;
}
.school-page-form { max-width: none; width: 100%; }
/* Hide legacy editor cards; inline preview + JS sync cover most fields. Footer has no preview wiring — keep it visible. */
.school-page-card {
    display: none;
}
.school-page-card.school-page-card--visible {
    display: block;
    margin-bottom: 1rem;
    padding: 1.35rem 1.25rem;
    border-radius: var(--radius-lg, 12px);
    border: 1px solid var(--border, rgba(15, 23, 42, 0.12));
    background: #fff;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
}
.school-page-footer-lead {
    margin: 0.5rem 0 0;
    font-size: 0.88rem;
    color: var(--muted, #64748b);
    line-height: 1.45;
    max-width: 52rem;
}
.school-page-footer-lead a {
    color: #15803d;
    font-weight: 600;
    text-decoration: none;
}
.school-page-footer-lead a:hover {
    text-decoration: underline;
}
.school-page-footer-cta {
    margin-bottom: 1rem;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px dashed rgba(22, 163, 74, 0.35);
    background: rgba(240, 253, 244, 0.65);
    font-size: 0.88rem;
    color: #334155;
    line-height: 1.45;
}
.school-page-footer-cta p {
    margin: 0 0 8px;
}
.school-page-footer-cta__jump {
    display: inline-block;
    font-size: 0.82rem;
    font-weight: 600;
    color: #15803d;
    text-decoration: none;
}
.school-page-footer-cta__jump:hover {
    text-decoration: underline;
}
.school-page-card-title { font-size: 1rem; font-weight: 600; color: var(--ink); margin-bottom: 0.25rem; }
.school-page-card-title--sub { font-size: 0.95rem; margin-top: 0.75rem; margin-bottom: 0.2rem; }
.school-page-logo-pair {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem 2rem;
    margin-bottom: 0.75rem;
}
@media (max-width: 720px) {
    .school-page-logo-pair { grid-template-columns: 1fr; }
}
.school-logo-picker-wrap { position: relative; }
.school-logo-picker__input {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
.school-logo-picker__trigger {
    position: relative;
    display: block;
    width: 112px;
    height: 112px;
    border-radius: 50%;
    overflow: visible;
    cursor: pointer;
    border: 2px dashed rgba(15, 23, 42, 0.14);
    background: #f8fafc;
    box-sizing: border-box;
}
.school-logo-picker__trigger:hover {
    border-color: rgba(22, 163, 74, 0.45);
}
.school-logo-picker__media {
    display: block;
    width: 100%;
    height: 100%;
    position: relative;
    overflow: hidden;
    border-radius: inherit;
}
.school-logo-picker__initial {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.25rem;
    font-weight: 800;
    color: #fff;
    background: var(--ink, #0f172a);
}
.school-logo-picker__media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.school-logo-picker__plus {
    position: absolute;
    right: -6px;
    bottom: 10px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(21, 128, 61, 0.92);
    border: 2px solid #ffffff;
    color: #fff;
    font-size: 1.1rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
    pointer-events: none;
}
.school-page-card-hint { font-size: 0.8rem; color: var(--muted); margin-bottom: 1rem; }
.school-page-preview-row { display: flex; align-items: flex-start; gap: 1.5rem; flex-wrap: wrap; }
.school-page-logo-preview { flex-shrink: 0; }
.school-page-cover-preview { flex: 0 0 320px; max-width: 100%; }
.school-page-logo-placeholder {
    width: 112px; height: 112px;
    border-radius: 999px;
    background: var(--ink);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.4rem; font-weight: 800;
}
.school-page-cover-placeholder {
    width: 320px; height: 320px;
    max-width: 100%;
    background: var(--bg-2);
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; color: var(--muted);
}
.school-page-upload-actions { flex: 1; min-width: 200px; }
.school-page-checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; cursor: pointer; }
.form-group { margin-bottom: 1rem; }
.form-group label { display: block; font-size: 0.9rem; font-weight: 500; color: var(--ink-2); margin-bottom: 0.35rem; }
.school-page-actions { margin-top: 1rem; }
.school-page-facts-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; }
.school-footer-links-editor { display: flex; flex-direction: column; gap: 8px; }
.school-footer-links-editor__row { display: grid; grid-template-columns: 1fr 1.4fr; gap: 8px; align-items: stretch; }
@media (max-width: 720px) {
    .school-footer-links-editor__row { grid-template-columns: 1fr; }
}
.school-footer-contact-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 640px) {
    .school-footer-contact-row { grid-template-columns: 1fr; }
}

/* Preview hero styles (mirrors public school page) */
.school-detail-card {
    position: relative;
    background: #ffffff;
    border-radius: 16px;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.12);
    padding: 0 0 24px;
    overflow: hidden;
    max-width: 100%;
    margin: 0;
}
.school-cover {
    position: relative;
    z-index: 1;
    height: 220px;
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
    padding: 18px 24px 14px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 8px;
    background: #ffffff;
}
.school-detail-header .school-heading {
    flex: 1;
    min-width: 0;
}
.school-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    margin-left: auto;
}
.school-avatar {
    position: relative;
    z-index: 3;
    width: 112px;
    height: 112px;
    border-radius: 999px;
    border: 4px solid #ffffff;
    background: #111827;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: -70px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.35);
    flex-shrink: 0;
    cursor: pointer;
}
.school-avatar-initial {
    display: inline-block;
    font-size: 2.2rem;
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
.school-avatar-plus {
    position: absolute;
    right: -6px;
    bottom: 10px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(21, 128, 61, 0.92);
    border: 2px solid #ffffff;
    color: #fff;
    font-size: 1.1rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,.2);
    pointer-events: none;
}
.school-heading-top {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 4px;
}
.school-heading h1 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
}
.school-code-pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    background: #eff6ff;
    color: #1d4ed8;
}
.school-location {
    margin: 0;
    font-size: 0.84rem;
    color: #6b7280;
}
.school-detail-body {
    margin-top: 4px;
    padding: 0 24px;
}
.school-detail-main h2 {
    margin: 0 0 4px;
    font-size: 1.1rem;
    font-weight: 700;
    color: #0f172a;
}
.school-intro {
    margin: 0 0 10px;
    font-size: 0.9rem;
    color: #4b5563;
    cursor: pointer;
}
.school-highlight {
    font-weight: 600;
    color: #111827;
}
.school-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 14px;
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
    cursor: pointer;
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
    gap: 10px;
    margin-bottom: 14px;
}
.school-fact {
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    padding: 8px 10px 9px;
    cursor: pointer;
}
.fact-label {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #9ca3af;
    margin-bottom: 4px;
}
.fact-value {
    font-size: 0.95rem;
    font-weight: 700;
    color: #111827;
}
.fact-caption {
    font-size: 0.78rem;
    color: #6b7280;
}
.school-section h3 {
    margin: 0 0 4px;
    font-size: 0.96rem;
    color: #111827;
    cursor: pointer;
}
.school-bullets {
    margin: 0;
    padding-left: 18px;
    font-size: 0.86rem;
    color: #4b5563;
}
.school-bullets li {
    margin-bottom: 3px;
    cursor: pointer;
}

/* Theme variants (match public school page) */
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

/* Crop modals */
.crop-modal { position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem; }
.crop-modal[aria-hidden="true"] { display: none; }
.crop-modal-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
.crop-modal-box { position: relative; background: var(--surface); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); max-width: 90vw; max-height: 90vh; display: flex; flex-direction: column; }
.crop-modal-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; border-bottom: 1px solid var(--border); }
.crop-modal-header h3 { margin: 0; font-size: 1rem; font-weight: 600; }
.crop-modal-close { background: none; border: none; font-size: 1.5rem; line-height: 1; cursor: pointer; color: var(--muted); padding: 0 4px; }
.crop-modal-body { padding: 1rem; overflow: auto; }
.crop-container { width: 100%; min-height: 280px; }
.crop-container img { max-width: 100%; display: block; }
.crop-modal-footer { display: flex; justify-content: flex-end; gap: 0.75rem; padding: 1rem 1.25rem; border-top: 1px solid var(--border); }

/* Public preview modal */
.public-preview-modal { position: fixed; inset: 0; z-index: 9998; display: flex; align-items: center; justify-content: center; padding: 18px; }
.public-preview-modal[aria-hidden="true"] { display: none; }
.public-preview-modal__backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.58); }
.public-preview-modal__box {
    position: relative;
    width: min(1180px, 96vw);
    height: min(86vh, 860px);
    background: #ffffff;
    border-radius: 14px;
    box-shadow: 0 24px 80px rgba(0,0,0,0.35);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.public-preview-modal__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 14px;
    border-bottom: 1px solid rgba(15, 23, 42, 0.10);
    background: #f8fafc;
}
.public-preview-modal__title { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.public-preview-modal__sub { font-size: 0.78rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.public-preview-modal__actions { display: flex; gap: 8px; flex-shrink: 0; }
.public-preview-modal__body { flex: 1; background: #ffffff; }
.public-preview-modal__body iframe { width: 100%; height: 100%; border: 0; background: #ffffff; }
@media (max-width: 640px) {
    .public-preview-modal { padding: 10px; }
    .public-preview-modal__box { height: 92vh; width: 98vw; border-radius: 12px; }
    .public-preview-modal__actions .btn { padding-inline: 10px; }
}
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js" integrity="sha512-9KkIqdfN7fdEYL0E3F2HIZL5OUbIm/E4Uz+gC2EtdHjRTrEzQn5s/zVGOgP5nCVqL9BRZBMi/2wBfU+GCHNmOw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
(function () {
    var Cropper = window.Cropper;

    var logoInput = document.getElementById('logo');
    var footerLogoInput = document.getElementById('footer_logo');
    var coverInput = document.getElementById('cover');
    var logoData = document.getElementById('logo_data');
    var footerLogoData = document.getElementById('footer_logo_data');
    var coverData = document.getElementById('cover_data');
    var logoPreview = document.getElementById('logo-preview-img');
    var logoPlaceholder = document.getElementById('logo-placeholder');
    var footerLogoPreview = document.getElementById('footer-logo-preview-img');
    var footerLogoPlaceholder = document.getElementById('footer-logo-placeholder');
    var coverPreview = document.getElementById('cover-preview-img');
    var coverPlaceholder = document.getElementById('cover-placeholder');

    var logoModal = document.getElementById('crop-modal-logo');
    var footerLogoModal = document.getElementById('crop-modal-footer-logo');
    var coverModal = document.getElementById('crop-modal-cover');
    var cropImgLogo = document.getElementById('crop-img-logo');
    var cropImgFooterLogo = document.getElementById('crop-img-footer-logo');
    var cropImgCover = document.getElementById('crop-img-cover');

    var pendingLogoDataUrl = null;
    var pendingFooterLogoDataUrl = null;
    var pendingCoverDataUrl = null;

    function closeCropModal(modalId) {
        var modal = document.getElementById(modalId);
        if (!modal) return;
        modal.setAttribute('aria-hidden', 'true');
        modal.style.display = 'none';
        if (modalId === 'crop-modal-logo') {
            pendingLogoDataUrl = null;
        } else if (modalId === 'crop-modal-footer-logo') {
            pendingFooterLogoDataUrl = null;
        } else if (modalId === 'crop-modal-cover') {
            pendingCoverDataUrl = null;
        }
    }

    if (logoInput) logoInput.addEventListener('change', function () {
        var file = this.files && this.files[0];
        if (!file || !file.type.match(/^image\//)) return;

        var reader = new FileReader();
        reader.onload = function (e) {
            pendingLogoDataUrl = e.target.result;
            cropImgLogo.src = pendingLogoDataUrl;
            if (logoModal) {
                logoModal.setAttribute('aria-hidden', 'false');
                logoModal.style.display = 'flex';
            }
        };
        reader.readAsDataURL(file);
        this.value = '';
    });

    if (footerLogoInput) {
        footerLogoInput.addEventListener('change', function () {
            var file = this.files && this.files[0];
            if (!file || !file.type.match(/^image\//)) return;

            var reader = new FileReader();
            reader.onload = function (e) {
                pendingFooterLogoDataUrl = e.target.result;
                if (cropImgFooterLogo) cropImgFooterLogo.src = pendingFooterLogoDataUrl;
                if (footerLogoModal) {
                    footerLogoModal.setAttribute('aria-hidden', 'false');
                    footerLogoModal.style.display = 'flex';
                }
            };
            reader.readAsDataURL(file);
            this.value = '';
        });
    }

    coverInput.addEventListener('change', function () {
        var file = this.files && this.files[0];
        if (!file || !file.type.match(/^image\//)) return;

        var reader = new FileReader();
        reader.onload = function (e) {
            pendingCoverDataUrl = e.target.result;
            cropImgCover.src = pendingCoverDataUrl;
            if (coverModal) {
                coverModal.setAttribute('aria-hidden', 'false');
                coverModal.style.display = 'flex';
            }
        };
        reader.readAsDataURL(file);
        this.value = '';
    });

    var schoolPageForm = document.querySelector('.school-page-form');

    document.querySelectorAll('.crop-apply').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var type = this.getAttribute('data-crop');
            if (type === 'logo' && pendingLogoDataUrl) {
                if (logoData) logoData.value = pendingLogoDataUrl;
                if (logoPreview) {
                    logoPreview.src = pendingLogoDataUrl;
                    logoPreview.style.display = '';
                }
                if (logoPlaceholder) logoPlaceholder.style.display = 'none';
                closeCropModal('crop-modal-logo');
            } else if (type === 'footer_logo' && pendingFooterLogoDataUrl) {
                footerLogoData.value = pendingFooterLogoDataUrl;
                if (footerLogoPreview) {
                    footerLogoPreview.src = pendingFooterLogoDataUrl;
                    footerLogoPreview.style.display = '';
                }
                if (footerLogoPlaceholder) footerLogoPlaceholder.style.display = 'none';
                closeCropModal('crop-modal-footer-logo');
            } else if (type === 'cover' && pendingCoverDataUrl) {
                coverData.value = pendingCoverDataUrl;
                coverPreview.src = pendingCoverDataUrl;
                coverPreview.style.display = '';
                if (coverPlaceholder) coverPlaceholder.style.display = 'none';
                document.getElementById('remove_cover').checked = false;
                closeCropModal('crop-modal-cover');
            } else {
                // Nothing pending; just close the modal
                closeCropModal(type === 'logo' ? 'crop-modal-logo' : type === 'footer_logo' ? 'crop-modal-footer-logo' : 'crop-modal-cover');
            }

            // Auto-save immediately after applying so user doesn't need to click "Save changes"
            if (schoolPageForm) {
                schoolPageForm.submit();
            }
        });
    });

    document.querySelectorAll('[data-dismiss]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = this.getAttribute('data-dismiss');
            closeCropModal(id);
        });
    });

    if (logoModal) {
        logoModal.querySelector('.crop-modal-backdrop').addEventListener('click', function () { closeCropModal('crop-modal-logo'); });
    }
    if (footerLogoModal) {
        footerLogoModal.querySelector('.crop-modal-backdrop').addEventListener('click', function () { closeCropModal('crop-modal-footer-logo'); });
    }
    coverModal.querySelector('.crop-modal-backdrop').addEventListener('click', function () { closeCropModal('crop-modal-cover'); });

    document.getElementById('remove_cover').addEventListener('change', function () {
        if (this.checked) { coverData.value = ''; if (coverPreview) { coverPreview.src = ''; coverPreview.style.display = 'none'; } if (coverPlaceholder) coverPlaceholder.style.display = ''; }
    });

    // Preview click-to-edit + sync wiring
    function focusField(id, clickFile) {
        var el = document.getElementById(id);
        if (!el) return;
        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
        if (clickFile && el.type === 'file') {
            el.click();
        } else {
            el.focus();
        }
    }

    var logoTarget = document.getElementById('preview-logo');
    if (logoTarget && document.getElementById('logo')) {
        logoTarget.addEventListener('click', function () { focusField('logo', true); });
    }

    var coverTarget = document.getElementById('preview-cover');
    if (coverTarget) {
        coverTarget.addEventListener('click', function () { focusField('cover', true); });
    }

    var introTarget = document.getElementById('preview-intro');
    if (introTarget) {
        introTarget.addEventListener('click', function () { focusField('intro'); });
    }

    var tagPrimary = document.getElementById('preview-tag-primary');
    if (tagPrimary) tagPrimary.addEventListener('click', function () { focusField('tag_primary'); });
    var tagNeutral = document.getElementById('preview-tag-neutral');
    if (tagNeutral) tagNeutral.addEventListener('click', function () { focusField('tag_neutral'); });
    var tagAccent = document.getElementById('preview-tag-accent');
    if (tagAccent) tagAccent.addEventListener('click', function () { focusField('tag_accent'); });

    ['fact1', 'fact2', 'fact3'].forEach(function (prefix, idx) {
        var card = document.getElementById('preview-' + prefix);
        if (!card) return;
        card.addEventListener('click', function () {
            var n = idx + 1;
            focusField('fact' + n + '_label');
        });
    });

    var campusTitle = document.querySelector('#preview-campus h3');
    if (campusTitle) {
        campusTitle.addEventListener('click', function () { focusField('campus_title'); });
    }

    [1,2,3,4].forEach(function (n) {
        var li = document.getElementById('preview-campus-bullet-' + n);
        if (!li) return;
        li.addEventListener('click', function () { focusField('campus_bullet' + n); });
    });

    // Keep hidden form fields in sync as user types inline
    var bindings = [
        { previewId: 'preview-name', inputId: 'name' },
        { previewId: 'preview-location', inputId: 'short_description' },
        { previewId: 'preview-intro', inputId: 'intro' },
        { previewId: 'preview-tag-primary', inputId: 'tag_primary' },
        { previewId: 'preview-tag-neutral', inputId: 'tag_neutral' },
        { previewId: 'preview-tag-accent', inputId: 'tag_accent' },
        { previewId: 'preview-fact1-label', inputId: 'fact1_label' },
        { previewId: 'preview-fact1-value', inputId: 'fact1_value' },
        { previewId: 'preview-fact1-caption', inputId: 'fact1_caption' },
        { previewId: 'preview-fact2-label', inputId: 'fact2_label' },
        { previewId: 'preview-fact2-value', inputId: 'fact2_value' },
        { previewId: 'preview-fact2-caption', inputId: 'fact2_caption' },
        { previewId: 'preview-fact3-label', inputId: 'fact3_label' },
        { previewId: 'preview-fact3-value', inputId: 'fact3_value' },
        { previewId: 'preview-fact3-caption', inputId: 'fact3_caption' },
        { previewId: 'preview-campus-title', inputId: 'campus_title' },
        { previewId: 'preview-campus-bullet-1', inputId: 'campus_bullet1' },
        { previewId: 'preview-campus-bullet-2', inputId: 'campus_bullet2' },
        { previewId: 'preview-campus-bullet-3', inputId: 'campus_bullet3' },
        { previewId: 'preview-campus-bullet-4', inputId: 'campus_bullet4' },
    ];

    bindings.forEach(function (map) {
        var previewEl = document.getElementById(map.previewId);
        var inputEl = document.getElementById(map.inputId);
        if (!previewEl || !inputEl) return;
        var sync = function () {
            inputEl.value = previewEl.textContent.trim();
        };
        previewEl.addEventListener('input', sync);
        previewEl.addEventListener('blur', sync);
    });

    // Theme picker -> hidden theme input + preview class
    var themePicker = document.getElementById('theme-picker');
    var hiddenThemeSelect = document.getElementById('theme');
    var previewCard = document.querySelector('.school-preview-card');
    var themeClasses = ['blue','green','indigo','slate','teal','amber','rose','purple','emerald','sky'].map(function(t){ return 'school-theme-' + t; });

    if (themePicker && hiddenThemeSelect && previewCard) {
        themePicker.addEventListener('change', function () {
            var value = this.value || '';
            hiddenThemeSelect.value = value;

            // Update preview theme class
            themeClasses.forEach(function (cls) {
                previewCard.classList.remove(cls);
            });
            if (value) {
                previewCard.classList.add('school-theme-' + value);
            }

            // Auto-save immediately when theme changes
            if (schoolPageForm) {
                schoolPageForm.submit();
            }
        });
    }

    // Public preview modal (iframe)
    var previewBtn = document.getElementById('open-public-preview');
    var publicPreviewModal = document.getElementById('public-preview-modal');
    var publicPreviewIframe = document.getElementById('public-preview-iframe');
    var publicPreviewUrl = @json(url('/schools/' . $school->school_code . '/enroll'));

    function openPublicPreview() {
        if (!publicPreviewModal) return;
        publicPreviewModal.setAttribute('aria-hidden', 'false');
        publicPreviewModal.style.display = 'flex';
        if (publicPreviewIframe) {
            // Always set src (iframe.src may be non-empty even when attribute is empty)
            publicPreviewIframe.src = publicPreviewUrl + (publicPreviewUrl.indexOf('?') === -1 ? '?' : '&') + '_preview_ts=' + Date.now();
        }
    }

    function closePublicPreview() {
        if (!publicPreviewModal) return;
        publicPreviewModal.setAttribute('aria-hidden', 'true');
        publicPreviewModal.style.display = 'none';
    }

    if (previewBtn) previewBtn.addEventListener('click', openPublicPreview);
    if (publicPreviewModal) {
        publicPreviewModal.querySelectorAll('[data-dismiss=\"public-preview-modal\"]').forEach(function (el) {
            el.addEventListener('click', closePublicPreview);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && publicPreviewModal.getAttribute('aria-hidden') === 'false') {
                closePublicPreview();
            }
        });
    }
})();
</script>
@endpush
@endsection
