<x-layouts.asom-auth
    page-title="Student Workspace"
    subtitle="Track progress, continue lessons, unlock exams, and stay connected to the ASOM learning community."
>
    <div class="d-grid gap-4">
        <section id="overview-tab" class="lms-dashboard-hero">
            <div class="row g-4 align-items-end">
                <div class="col-lg-7">
                    <span class="surface-eyebrow bg-white/10 text-white border-0">ASOM Journey</span>
                    <h2 class="mt-3 mb-3">Keep building a ministry life that is grounded, consistent, and ready for service.</h2>
                    <p class="mb-4 text-white-50">
                        Your learning space now keeps the essentials together: active courses, course progress, available exams,
                        certificates, and the student support channels that help you keep moving.
                    </p>
                    <div class="lms-dashboard-actions">
                        <a href="#courses-tab" class="btn btn-light rounded-pill px-4">Continue learning</a>
                        <a href="{{ route('lms.courses.index') }}" class="surface-button-ghost text-white">Browse catalog</a>
                        <a href="#groups-tab" class="surface-button-ghost text-white">Join community groups</a>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="lms-summary-grid">
                        <article class="lms-summary-card">
                            <span class="label">Overall progress</span>
                            <span class="value">{{ $stats['overall_progress'] }}%</span>
                        </article>
                        <article class="lms-summary-card">
                            <span class="label">Active courses</span>
                            <span class="value">{{ $stats['in_progress_courses'] }}</span>
                        </article>
                        <article class="lms-summary-card">
                            <span class="label">Available exams</span>
                            <span class="value">{{ $stats['available_exams'] }}</span>
                        </article>
                        <article class="lms-summary-card">
                            <span class="label">Certificates</span>
                            <span class="value">{{ $stats['certificates'] }}</span>
                        </article>
                    </div>
                </div>
            </div>
        </section>

        <section class="lms-metric-grid">
            <article class="lms-stat-card">
                <span class="label">Published courses</span>
                <span class="value">{{ $stats['total_courses'] }}</span>
                <p class="mb-0 mt-2 text-muted">Every available module across the current ASOM catalog.</p>
            </article>
            <article class="lms-stat-card">
                <span class="label">Enrolled courses</span>
                <span class="value">{{ $stats['enrolled_courses'] }}</span>
                <p class="mb-0 mt-2 text-muted">Courses currently attached to your student journey.</p>
            </article>
            <article class="lms-stat-card">
                <span class="label">Completed courses</span>
                <span class="value">{{ $stats['completed_courses'] }}</span>
                <p class="mb-0 mt-2 text-muted">Courses fully completed and ready for review or certification.</p>
            </article>
            <article class="lms-stat-card">
                <span class="label">Passed exams</span>
                <span class="value">{{ $stats['passed_exams'] }}</span>
                <p class="mb-0 mt-2 text-muted">Assessment milestones already cleared in this program.</p>
            </article>
        </section>

        <section id="courses-tab" class="row g-4">
            <div class="col-xl-8">
                <div class="lms-dashboard-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h3 class="mb-1">Continue learning</h3>
                            <p class="text-muted mb-0">Pick up where you left off and keep your momentum.</p>
                        </div>
                        <a href="{{ route('lms.courses.index') }}" class="surface-button-secondary">View full catalog</a>
                    </div>

                    @if($continueLearning->isNotEmpty())
                        <div class="lms-course-grid">
                            @foreach($continueLearning as $course)
                                <article class="lms-course-card">
                                    <div class="lms-course-media">
                                        <img src="{{ $course['featured_image_url'] }}" alt="{{ $course['title'] }}" loading="lazy">
                                    </div>
                                    <div class="lms-course-body">
                                        <div class="lms-badge-row">
                                            <span class="lms-pill"><i class="fas fa-book-open"></i>{{ $course['lesson_count'] }} lessons</span>
                                            <span class="lms-pill"><i class="fas fa-chart-line"></i>{{ $course['progress'] }}% complete</span>
                                        </div>
                                        <div>
                                            <h4 class="mb-2">{{ $course['title'] }}</h4>
                                            <p class="text-muted mb-3">{{ $course['description_excerpt'] }}</p>
                                            <div class="d-flex justify-content-between small text-muted mb-2">
                                                <span>{{ $course['completed_lessons'] }} of {{ $course['lesson_count'] }} completed</span>
                                                <span>{{ $course['progress'] }}%</span>
                                            </div>
                                            <div class="lms-course-progress">
                                                <div class="lms-course-progress-fill" style="width: {{ $course['progress'] }}%"></div>
                                            </div>
                                        </div>
                                        <div class="lms-course-footer">
                                            <small class="text-muted">Instructor: {{ $course['instructor_name'] }}</small>
                                            @if($course['next_lesson_url'])
                                                <a href="{{ $course['next_lesson_url'] }}" class="surface-button-primary">Continue course</a>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <x-ui.empty-state
                            title="No active course yet"
                            message="Enroll in a course to start tracking progress and unlock your lesson workspace."
                        >
                            <a href="{{ route('lms.courses.index') }}" class="surface-button-primary mt-3">Browse available courses</a>
                        </x-ui.empty-state>
                    @endif
                </div>
            </div>

            <div class="col-xl-4">
                <div class="d-grid gap-4">
                    <div class="lms-support-card">
                        <h3 class="mb-3">Course milestones</h3>
                        <div class="d-grid gap-3">
                            <div class="d-flex align-items-center justify-content-between rounded-4 border px-3 py-3 bg-light-subtle">
                                <div>
                                    <div class="fw-semibold">Program certificate</div>
                                    <small class="text-muted">
                                        @if($diplomaCertificate)
                                            {{ $diplomaCertificate->is_approved ? 'Approved and ready to download' : 'Pending admin approval' }}
                                        @else
                                            {{ $diplomaStatus['completed_requirements'] }} of {{ $diplomaStatus['required_count'] }} diploma requirements complete
                                        @endif
                                    </small>
                                </div>
                                <span class="lms-pill">{{ $diplomaStatus['progress_percentage'] }}%</span>
                            </div>

                            @foreach([25 => 'First milestone', 50 => 'Halfway through', 75 => 'Almost complete', 100 => 'Graduation ready'] as $milestone => $label)
                                <div class="d-flex align-items-center justify-content-between rounded-4 border px-3 py-3 {{ $stats['overall_progress'] >= $milestone ? 'bg-success-subtle border-success-subtle' : 'bg-light border-light' }}">
                                    <div>
                                        <div class="fw-semibold">{{ $milestone }}% complete</div>
                                        <small class="text-muted">{{ $label }}</small>
                                    </div>
                                    <i class="fas {{ $stats['overall_progress'] >= $milestone ? 'fa-check-circle text-success' : 'fa-lock text-muted' }}"></i>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="lms-support-card">
                        <h3 class="mb-3">Quick actions</h3>
                        <div class="d-grid gap-2">
                            <a href="{{ route('lms.courses.index') }}" class="surface-button-primary justify-content-center">Explore course catalog</a>
                            <a href="{{ route('lms.exams.index') }}" class="surface-button-secondary justify-content-center">Go to exams</a>
                            <a href="{{ route('lms.certificates.index') }}" class="surface-button-secondary justify-content-center">View certificates</a>
                            <a href="{{ route('user.dashboard') }}" class="surface-button-ghost justify-content-center">Open main dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="row g-4">
            <div class="col-xl-7">
                <div class="lms-dashboard-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h3 class="mb-1">Available next steps</h3>
                            <p class="text-muted mb-0">Courses you can start now or add after your current modules.</p>
                        </div>
                        <a href="{{ route('lms.courses.index') }}" class="surface-button-ghost">See every course</a>
                    </div>

                    @if($availableCourses->isNotEmpty())
                        <div class="lms-course-grid">
                            @foreach($availableCourses->take(4) as $course)
                                <article class="lms-course-card">
                                    <div class="lms-course-media">
                                        <img src="{{ $course['featured_image_url'] }}" alt="{{ $course['title'] }}" loading="lazy">
                                    </div>
                                    <div class="lms-course-body">
                                        <div class="lms-badge-row">
                                            <span class="lms-pill"><i class="fas fa-book"></i>{{ $course['lesson_count'] }} lessons</span>
                                            <span class="lms-pill"><i class="fas fa-file-signature"></i>{{ $course['exam_count'] }} exams</span>
                                        </div>
                                        <div>
                                            <h4 class="mb-2">{{ $course['title'] }}</h4>
                                            <p class="text-muted mb-0">{{ $course['description_excerpt'] }}</p>
                                        </div>
                                        <div class="lms-course-footer">
                                            <small class="text-muted">Instructor: {{ $course['instructor_name'] }}</small>
                                            <a href="{{ $course['course_url'] }}" class="surface-button-secondary">Review course</a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <x-ui.empty-state
                            title="You are enrolled in everything currently published"
                            message="As new ASOM courses go live, they will appear here automatically."
                        />
                    @endif
                </div>
            </div>

            <div class="col-xl-5">
                <div id="exams-tab" class="lms-dashboard-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h3 class="mb-1">Assessment status</h3>
                            <p class="text-muted mb-0">Ready exams and your most recent results.</p>
                        </div>
                        <a href="{{ route('lms.exams.index') }}" class="surface-button-secondary">Open exams</a>
                    </div>

                    @if($examSummaries->isNotEmpty())
                        <div class="d-grid gap-3">
                            @foreach($examSummaries->take(3) as $exam)
                                <article class="rounded-4 border p-3 bg-light-subtle">
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <h4 class="h5 mb-1">{{ $exam['title'] }}</h4>
                                            <p class="text-muted small mb-2">{{ $exam['course_title'] }}</p>
                                        </div>
                                        <span class="lms-pill">
                                            <i class="fas {{ $exam['has_passed'] ? 'fa-check-circle text-success' : 'fa-clock text-warning' }}"></i>
                                            {{ $exam['has_passed'] ? 'Passed' : 'Pending' }}
                                        </span>
                                    </div>
                                    <div class="lms-meta-row mb-3">
                                        <span class="lms-pill"><i class="fas fa-list"></i>{{ $exam['question_count'] }} questions</span>
                                        <span class="lms-pill"><i class="fas fa-rotate"></i>{{ $exam['remaining_attempts'] }} attempts left</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <small class="text-muted">
                                            @if($exam['best_score'] !== null)
                                                Best score: {{ $exam['best_score'] }}%
                                            @else
                                                No attempt yet
                                            @endif
                                        </small>
                                        <a href="{{ $exam['action_url'] }}" class="surface-button-secondary">
                                            {{ $exam['action_label'] }}
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <x-ui.empty-state
                            title="No exam unlocked yet"
                            message="Finish your course lessons to unlock eligible course assessments."
                        />
                    @endif
                </div>
            </div>
        </section>

        <section class="row g-4">
            <div class="col-xl-7">
                <div id="groups-tab" class="lms-dashboard-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h3 class="mb-1">Community and support groups</h3>
                            <p class="text-muted mb-0">Join the conversation spaces that support each module and student care.</p>
                        </div>
                        <a href="{{ route('contact.show') }}" class="surface-button-ghost">Need direct help?</a>
                    </div>

                    <div class="lms-community-grid">
                        @foreach($communityGroups as $group)
                            <article class="lms-community-card">
                                <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center" style="width: 3rem; height: 3rem;">
                                            <i class="{{ $group['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <h4 class="h5 mb-1">{{ $group['name'] }}</h4>
                                            <p class="text-muted small mb-0">{{ $group['description'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                <a href="{{ $group['url'] }}" class="surface-button-primary" target="_blank" rel="noreferrer">
                                    Join WhatsApp group
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div id="certificates-tab" class="lms-dashboard-card">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                        <div>
                            <h3 class="mb-1">Certificates</h3>
                            <p class="text-muted mb-0">Your program certificate and any admin-issued course recognitions.</p>
                        </div>
                        <a href="{{ route('lms.certificates.index') }}" class="surface-button-secondary">View all</a>
                    </div>

                    @if($featuredCertificates->isNotEmpty())
                        <div class="d-grid gap-3">
                            @foreach($featuredCertificates as $certificate)
                                <article class="lms-certificate-card">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                                        <div>
                                            <h4 class="h5 mb-1">{{ $certificate->certificate_type }}</h4>
                                            <p class="text-muted small mb-0">{{ $certificate->certificate_id }}</p>
                                        </div>
                                        <span class="lms-pill">
                                            <i class="fas {{ $certificate->is_approved ? 'fa-check-circle text-success' : 'fa-clock text-warning' }}"></i>
                                            {{ $certificate->is_approved ? 'Approved' : 'Pending' }}
                                        </span>
                                    </div>
                                    <div class="small text-muted mb-3">
                                        <div>Completed: {{ optional($certificate->completed_at)->format('M j, Y') ?? 'In review' }}</div>
                                        @if($certificate->final_grade)
                                            <div>Final grade: {{ number_format($certificate->final_grade, 1) }}%</div>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('lms.certificates.show', $certificate) }}" class="surface-button-secondary">View</a>
                                        @if($certificate->is_approved)
                                            <a href="{{ route('lms.certificates.download', $certificate) }}" class="surface-button-primary">Download</a>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <x-ui.empty-state
                            title="No certificate yet"
                            message="Your Diploma in Ministry certificate appears here after every program requirement is complete and approved."
                        />
                    @endif
                </div>
            </div>
        </section>
    </div>
</x-layouts.asom-auth>
