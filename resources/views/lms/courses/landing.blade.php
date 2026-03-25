@php
    $landingHero = $pageContent['landing_hero'];
    $landingHeroStats = $landingHero['stats'] ?? [];
    $bridgeSection = $pageContent['bridge_section'];
    $ministryBridgeLinks = $pageContent['bridge_links'];
    $reviewSection = $pageContent['review_section'];
    $reviewChecks = $pageContent['review_checks'];
    $formationPillars = $pageContent['formation_pillars'];
    $previewSection = $pageContent['preview_section'];
    $learningSection = $pageContent['learning_section'];
    $learningSteps = $pageContent['learning_steps'];
    $programSection = $pageContent['program_section'];
    $programMilestones = $pageContent['program_milestones'];
    $outcomesSection = $pageContent['outcomes_section'];
    $outcomes = $pageContent['outcomes'];
    $ctaSection = $pageContent['cta_section'];
@endphp

<x-layouts.lms
    title="ASOM | Projectsave International"
    :show-sidebar="false"
    :flush-top="true"
>
    <div class="d-grid gap-4">

        {{-- ═══════════════════════════════════════════ HERO ══ --}}
        <section class="lms-landing-hero-section">
            <div class="lms-landing-hero-shell" style="--lms-landing-image: url('{{ $landingHero['image_url'] }}');">
                <div class="lms-hero-frame">
                    <div class="lms-landing-hero">
                        <div class="row g-4 align-items-end">

                            {{-- Copy column --}}
                            <div class="col-xl-6">
                                <div class="lms-landing-copy">
                                    <span class="lms-hero-eyebrow">{{ $landingHero['eyebrow'] }}</span>
                                    <h1>{{ $landingHero['title'] }}</h1>
                                    <p class="lead lms-hero-lead mb-0">{{ $landingHero['lead'] }}</p>
                                    <p class="lms-hero-body mb-0">{{ $landingHero['body'] }}</p>

                                    <div class="lms-dashboard-actions">
                                        @auth
                                            <a href="{{ $landingHero['authenticated_primary_url'] }}" class="lms-landing-btn-primary">
                                                <i class="bi bi-play-circle-fill"></i>
                                                {{ $landingHero['authenticated_primary_label'] }}
                                            </a>
                                            <a href="{{ $landingHero['secondary_cta_url'] }}" class="lms-landing-btn-ghost">
                                                {{ $landingHero['secondary_cta_label'] }}
                                            </a>
                                        @else
                                            <a href="{{ $landingHero['primary_cta_url'] }}" class="lms-landing-btn-primary">
                                                <i class="bi bi-play-circle-fill"></i>
                                                {{ $landingHero['primary_cta_label'] }}
                                            </a>
                                            <a href="{{ $landingHero['secondary_cta_url'] }}" class="lms-landing-btn-ghost">
                                                {{ $landingHero['secondary_cta_label'] }}
                                            </a>
                                        @endauth
                                    </div>

                                    <div class="lms-landing-metrics">
                                        <article class="lms-landing-metric">
                                            <span class="label">{{ $landingHeroStats[0]['label'] ?? 'Published courses' }}</span>
                                            <span class="value">{{ $catalogStats['total_courses'] }}</span>
                                        </article>
                                        <article class="lms-landing-metric">
                                            <span class="label">{{ $landingHeroStats[1]['label'] ?? 'Total lessons' }}</span>
                                            <span class="value">{{ $catalogStats['total_lessons'] }}</span>
                                        </article>
                                        <article class="lms-landing-metric">
                                            <span class="label">{{ $landingHeroStats[2]['label'] ?? 'Active exams' }}</span>
                                            <span class="value">{{ $catalogStats['total_exams'] }}</span>
                                        </article>
                                    </div>
                                </div>
                            </div>

                            {{-- Visual column --}}
                            <div class="col-xl-6">
                                <div class="lms-landing-visual">
                                    <div class="lms-landing-welcome-note">
                                        <span class="lms-hero-eyebrow">{{ $landingHero['identity_eyebrow'] }}</span>
                                        <p class="mb-0 mt-2">{{ $landingHero['identity_copy'] }}</p>
                                    </div>
                                    <div class="lms-landing-visual-card">
                                        <span class="lms-landing-visual-label">{{ $landingHero['featured_label'] }}</span>
                                        <div class="d-grid gap-3 mt-3">
                                            @foreach($featuredCourses as $course)
                                                <a href="{{ $course->course_url }}" class="lms-landing-course-teaser">
                                                    <div>
                                                        <strong>{{ $course->title }}</strong>
                                                        <span>{{ $course->lessons_count }} {{ $landingHero['featured_lessons_label'] }} &middot; {{ $course->active_exams_count }} {{ $landingHero['featured_exams_label'] }}</span>
                                                    </div>
                                                    <i class="bi bi-arrow-up-right-circle flex-shrink-0"></i>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═════════════════════════════ BRIDGE + REVIEW ══ --}}
        <section class="row g-4">
            <div class="col-lg-7">
                <article class="lms-section-shell h-100">
                    <span class="surface-eyebrow">{{ $bridgeSection['eyebrow'] }}</span>
                    <h2 class="lms-section-title">{{ $bridgeSection['title'] }}</h2>
                    <p class="lms-section-lead">{{ $bridgeSection['copy'] }}</p>

                    <div class="lms-landing-bridge-grid mt-4">
                        @foreach($ministryBridgeLinks as $link)
                            <a href="{{ $link['url'] }}" class="lms-landing-bridge-card">
                                <strong>{{ $link['title'] }}</strong>
                                <span>{{ $link['copy'] }}</span>
                                <span class="lms-bridge-cta">{{ $link['cta'] }} <i class="bi bi-arrow-right ms-1"></i></span>
                            </a>
                        @endforeach
                    </div>
                </article>
            </div>

            <div class="col-lg-5">
                <article class="lms-section-shell h-100">
                    <span class="surface-eyebrow">{{ $reviewSection['eyebrow'] }}</span>
                    <h2 class="lms-section-title">{{ $reviewSection['title'] }}</h2>
                    <div class="d-grid gap-3 mt-4">
                        @foreach($reviewChecks as $check)
                            <div class="lms-outcome-card">
                                <div class="lms-outcome-icon"><i class="bi bi-check2-circle"></i></div>
                                <div>
                                    <strong class="d-block mb-1">{{ $check['title'] }}</strong>
                                    <span class="text-muted">{{ $check['copy'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>
        </section>

        {{-- ═════════════════════════ FORMATION PILLARS ══ --}}
        <section class="lms-section-shell">
            <div class="lms-section-head mb-4">
                <span class="surface-eyebrow">Formation</span>
                <h2 class="lms-section-title mt-2">Core pillars of the programme</h2>
            </div>
            <div class="lms-feature-strip">
                @foreach($formationPillars as $pillar)
                    <article class="lms-feature-card">
                        <div class="lms-feature-icon">
                            <i class="{{ $pillar['icon'] }}"></i>
                        </div>
                        <div>
                            <h3 class="h5 mb-2">{{ $pillar['title'] }}</h3>
                            <p class="mb-0 text-muted">{{ $pillar['copy'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- ═══════════════════════ COURSE CATALOG PREVIEW ══ --}}
        <section class="lms-section-shell">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
                <div>
                    <span class="surface-eyebrow">{{ $previewSection['eyebrow'] }}</span>
                    <h2 class="lms-section-title mt-2">{{ $previewSection['title'] }}</h2>
                    <p class="lms-section-lead mb-0">{{ $previewSection['copy'] }}</p>
                </div>
                <a href="{{ $previewSection['button_url'] }}" class="surface-button-secondary flex-shrink-0">{{ $previewSection['button_label'] }}</a>
            </div>

            <div class="lms-catalog-grid">
                @foreach($catalogPreview as $course)
                    <article class="lms-course-card">
                        <div class="lms-course-media">
                            <img src="{{ $course->featured_image_url }}" alt="{{ $course->title }}" loading="lazy">
                        </div>
                        <div class="lms-course-body">
                            <div class="lms-badge-row">
                                <span class="lms-pill"><i class="fas fa-book-open me-1"></i>{{ $course->lessons_count }} lessons</span>
                                <span class="lms-pill"><i class="fas fa-file-signature me-1"></i>{{ $course->active_exams_count }} exams</span>
                                @if($course->is_enrolled)
                                    <span class="lms-pill lms-pill-accent"><i class="fas fa-chart-line me-1"></i>{{ $course->progress }}% done</span>
                                @endif
                            </div>
                            <div>
                                <h3 class="h5 mb-2">{{ $course->title }}</h3>
                                <p class="text-muted small mb-2">{{ $course->description_excerpt }}</p>
                                <small class="text-muted">Instructor: {{ $course->instructor_name }}</small>
                            </div>
                            <div class="lms-course-footer">
                                <span class="small text-muted">{{ $course->is_enrolled ? 'In your workspace' : 'Open for review' }}</span>
                                <a href="{{ $course->course_url }}" class="{{ $course->is_enrolled ? 'surface-button-primary' : 'surface-button-secondary' }}">
                                    {{ $course->is_enrolled ? 'Continue course' : 'View details' }}
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- ══════════════════ LEARNING STEPS + MILESTONES ══ --}}
        <section class="row g-4">
            <div class="col-lg-6">
                <article class="lms-section-shell h-100">
                    <span class="surface-eyebrow">{{ $learningSection['eyebrow'] }}</span>
                    <h2 class="lms-section-title">{{ $learningSection['title'] }}</h2>
                    <div class="d-grid gap-3 mt-4">
                        @foreach($learningSteps as $step)
                            <div class="lms-step-card">
                                <span class="lms-step-number">{{ $loop->iteration }}</span>
                                <p class="mb-0">{{ $step }}</p>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>

            <div class="col-lg-6">
                <article class="lms-section-shell h-100">
                    <span class="surface-eyebrow">{{ $programSection['eyebrow'] }}</span>
                    <h2 class="lms-section-title">{{ $programSection['title'] }}</h2>
                    <div class="d-grid gap-3 mt-4">
                        @foreach($programMilestones as $milestone)
                            <div class="lms-outcome-card">
                                <div class="lms-outcome-icon"><i class="bi bi-check2-circle"></i></div>
                                <span>{{ $milestone }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="mt-4 mb-0 text-muted">{{ $programSection['copy'] }}</p>
                </article>
            </div>
        </section>

        {{-- ═══════════════════════════════ OUTCOMES ══ --}}
        <section class="lms-section-shell">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3 mb-4">
                <div>
                    <span class="surface-eyebrow">{{ $outcomesSection['eyebrow'] }}</span>
                    <h2 class="lms-section-title mt-2">{{ $outcomesSection['title'] }}</h2>
                    <p class="lms-section-lead mb-0">{{ $outcomesSection['copy'] }}</p>
                </div>
                <a href="{{ $outcomesSection['button_url'] }}" class="surface-button-secondary flex-shrink-0">{{ $outcomesSection['button_label'] }}</a>
            </div>

            <div class="row g-3">
                @foreach($outcomes as $outcome)
                    <div class="col-lg-4">
                        <div class="lms-outcome-card h-100">
                            <div class="lms-outcome-icon"><i class="bi bi-check2-circle"></i></div>
                            <span>{{ $outcome }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ═══════════════════════════════ CTA BAND ══ --}}
        <section class="lms-landing-cta">
            <div>
                <span class="lms-hero-eyebrow">{{ $ctaSection['eyebrow'] }}</span>
                <h2 class="mt-3 mb-2 text-white">{{ $ctaSection['title'] }}</h2>
                <p class="lms-hero-body mb-0">{{ $ctaSection['copy'] }}</p>
            </div>
            <div class="d-flex flex-wrap gap-3">
                @auth
                    <a href="{{ $ctaSection['authenticated_url'] }}" class="lms-landing-btn-primary">
                        {{ $ctaSection['authenticated_label'] }}
                    </a>
                @else
                    <a href="{{ $ctaSection['guest_primary_url'] }}" class="lms-landing-btn-primary">
                        {{ $ctaSection['guest_primary_label'] }}
                    </a>
                    <a href="{{ $ctaSection['guest_secondary_url'] }}" class="lms-landing-btn-ghost">
                        {{ $ctaSection['guest_secondary_label'] }}
                    </a>
                @endauth
            </div>
        </section>

    </div>
</x-layouts.lms>
