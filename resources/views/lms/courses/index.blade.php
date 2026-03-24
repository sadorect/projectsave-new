@php
    $catalogHero = $pageContent['catalog_hero'];
    $catalogHeroStats = $catalogHero['stats'] ?? [];
    $welcomePathways = $catalogHero['pathways'] ?? [];
@endphp

<x-layouts.lms
    title="ASOM Learning Path | Projectsave International"
    :show-sidebar="false"
    :flush-top="true"
>
    <div class="d-grid gap-4">
        <section class="lms-landing-hero-section">
            <div class="lms-landing-hero-shell" style="--lms-landing-image: url('{{ $catalogHero['image_url'] }}');">
                <div class="lms-hero-frame">
                    <div class="lms-landing-hero">
                        <div class="row g-4 align-items-end">
                            <div class="col-xl-7">
                                <div class="lms-landing-copy">
                                    <span class="surface-eyebrow border-0 bg-white/10 text-white">{{ $catalogHero['eyebrow'] }}</span>
                                    <h1>{{ $catalogHero['title'] }}</h1>
                                    <p class="lead mb-0 text-white-50">{{ $catalogHero['lead'] }}</p>
                                    <p class="mb-0 text-white-50">{{ $catalogHero['body'] }}</p>

                                    <div class="lms-landing-welcome-note">
                                        <strong>{{ $catalogHero['welcome_title'] }}</strong>
                                        <span>{{ $catalogHero['welcome_copy'] }}</span>
                                    </div>

                                    <div class="lms-dashboard-actions mt-4">
                                        @auth
                                            <a href="{{ $catalogHero['authenticated_primary_url'] }}" class="btn btn-light rounded-pill px-4">{{ $catalogHero['authenticated_primary_label'] }}</a>
                                            <a href="{{ $catalogHero['secondary_cta_url'] }}" class="surface-button-ghost text-white">{{ $catalogHero['secondary_cta_label'] }}</a>
                                        @else
                                            <a href="{{ $catalogHero['primary_cta_url'] }}" class="btn btn-light rounded-pill px-4">{{ $catalogHero['primary_cta_label'] }}</a>
                                            <a href="{{ $catalogHero['secondary_cta_url'] }}" class="surface-button-ghost text-white">{{ $catalogHero['secondary_cta_label'] }}</a>
                                        @endauth
                                    </div>

                                    <div class="lms-landing-metrics mt-4">
                                        <article class="lms-landing-metric">
                                            <span class="label">{{ $catalogHeroStats[0]['label'] ?? 'Published courses' }}</span>
                                            <span class="value">{{ $catalogStats['total_courses'] }}</span>
                                        </article>
                                        <article class="lms-landing-metric">
                                            <span class="label">{{ $catalogHeroStats[1]['label'] ?? 'Total lessons' }}</span>
                                            <span class="value">{{ $catalogStats['total_lessons'] }}</span>
                                        </article>
                                        <article class="lms-landing-metric">
                                            <span class="label">{{ $catalogHeroStats[2]['label'] ?? 'Active exams' }}</span>
                                            <span class="value">{{ $catalogStats['total_exams'] }}</span>
                                        </article>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5">
                                <div class="lms-landing-visual">
                                    <div class="lms-landing-visual-card">
                                        <span class="lms-landing-visual-label">{{ $catalogHero['visual_label'] }}</span>
                                        <div class="lms-landing-pathways mt-3">
                                            @foreach($welcomePathways as $pathway)
                                                <article class="lms-landing-pathway">
                                                    <strong>{{ $pathway['title'] }}</strong>
                                                    <span>{{ $pathway['copy'] }}</span>
                                                </article>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="lms-landing-visual-note">
                                        <span class="surface-eyebrow border-0 bg-white/10 text-white">{{ $catalogHero['identity_eyebrow'] }}</span>
                                        <p class="mb-0">{{ $catalogHero['identity_copy'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="lms-section-shell">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-end gap-3">
                <div>
                    <span class="surface-eyebrow">Published catalog</span>
                    <h2 class="mt-3 mb-2">Course Catalog</h2>
                    <p class="mb-0 text-muted">
                        Review every published ASOM course below, compare the lesson and exam load, and move forward into
                        a learning path that now opens with ministry context before the course inventory itself.
                    </p>
                </div>
                @auth
                    <a href="{{ route('lms.dashboard') }}" class="surface-button-secondary">Return to dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="surface-button-secondary">Sign in to continue</a>
                @endauth
            </div>

            <div class="lms-catalog-grid mt-4">
                @forelse($courses as $course)
                    <article class="lms-course-card">
                        <div class="lms-course-media">
                            <img src="{{ $course->featured_image_url }}" alt="{{ $course->title }}" loading="lazy">
                        </div>
                        <div class="lms-course-body">
                            <div class="lms-badge-row">
                                <span class="lms-pill"><i class="fas fa-book-open"></i>{{ $course->lessons_count }} lessons</span>
                                <span class="lms-pill"><i class="fas fa-file-signature"></i>{{ $course->active_exams_count }} exams</span>
                                @if($course->is_enrolled)
                                    <span class="lms-pill"><i class="fas fa-chart-line"></i>{{ $course->progress }}% complete</span>
                                @endif
                            </div>
                            <div>
                                <h3 class="h4 mb-2">{{ $course->title }}</h3>
                                <p class="text-muted mb-2">{{ $course->description_excerpt }}</p>
                                <small class="text-muted">Instructor: {{ $course->instructor_name }}</small>
                            </div>
                            @if($course->is_enrolled)
                                <div>
                                    <div class="d-flex justify-content-between small text-muted mb-2">
                                        <span>Current progress</span>
                                        <span>{{ $course->progress }}%</span>
                                    </div>
                                    <div class="lms-course-progress">
                                        <div class="lms-course-progress-fill" style="width: {{ $course->progress }}%"></div>
                                    </div>
                                </div>
                            @endif
                            <div class="lms-course-footer">
                                <span class="small text-muted">
                                    {{ $course->is_enrolled ? ucfirst($course->status ?? 'active') . ' student' : 'Ready to enroll' }}
                                </span>
                                <a href="{{ $course->course_url }}" class="{{ $course->is_enrolled ? 'surface-button-primary' : 'surface-button-secondary' }}">
                                    {{ $course->is_enrolled ? 'Continue course' : 'View details' }}
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-12">
                        <x-ui.empty-state
                            title="No published courses yet"
                            message="Published ASOM courses will appear here as soon as they are ready for students."
                        />
                    </div>
                @endforelse
            </div>
        </section>

        <div class="d-flex justify-content-center">
            {{ $courses->links() }}
        </div>
    </div>
</x-layouts.lms>
