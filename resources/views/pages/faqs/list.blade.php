<x-layouts.app
    title="Projectsave FAQs"
    meta-description="Browse frequently asked questions and biblical answers from Projectsave International."
>
    <x-ui.public-page-hero
        eyebrow="FAQs"
        title="Biblical answers for real ministry questions"
        subtitle="Explore clear, Scripture-shaped answers that support spiritual growth, understanding, and next steps in your walk and service."
    >
        <x-slot:actions>
            <a href="{{ route('search') }}?q=faith" class="surface-button-primary">
                <i class="bi bi-search me-2"></i>Search answers
            </a>
            <a href="{{ route('contact.show') }}" class="surface-button-secondary">Ask for help</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="row g-5">

                {{-- FAQ grid --}}
                <div class="col-lg-8">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <span class="page-section-eyebrow">Knowledge base</span>
                            <h2 class="page-section-title mb-0" style="font-size:1.5rem;">All questions</h2>
                        </div>
                    </div>

                    <div class="row g-4">
                        @forelse($faqs as $faq)
                            <div class="col-md-6">
                                <article class="faq-card">
                                    <span class="public-chip" style="align-self:flex-start;">{{ $faq->created_at->format('M d, Y') }}</span>
                                    <h3 class="faq-card-title">
                                        <a href="{{ route('faqs.show', $faq->slug) }}">{{ $faq->title }}</a>
                                    </h3>
                                    <p class="faq-card-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($faq->details), 145) }}</p>
                                    <a href="{{ route('faqs.show', $faq->slug) }}" class="surface-button-secondary" style="font-size:0.82rem;padding:0.45rem 0.9rem;align-self:flex-start;margin-top:auto;">
                                        Read answer <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </article>
                            </div>
                        @empty
                            <div class="col-12">
                                <x-ui.empty-state
                                    title="No FAQs published yet"
                                    message="Published FAQ articles will appear here as soon as they are available."
                                    icon="bi bi-patch-question"
                                />
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-5 d-flex justify-content-center">
                        {{ $faqs->links() }}
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">Quick search</span>
                            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:0.8rem;">Search the knowledge base</h3>
                            <form action="{{ route('search') }}" method="GET">
                                <label for="faq-search" class="visually-hidden">Keyword</label>
                                <div class="d-flex gap-2">
                                    <input id="faq-search" type="text" class="form-control" name="q" placeholder="Search articles...">
                                    <button class="surface-button-primary" type="submit"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">Need more help?</span>
                            <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin-bottom:0.4rem;">Talk to the team</h3>
                            <p style="font-size:0.87rem;color:#6b7280;line-height:1.65;margin-bottom:1rem;">If your question isn't answered here, contact us directly and we'll respond by email.</p>
                            <div class="d-grid gap-2">
                                <a href="{{ route('contact.show') }}" class="surface-button-primary justify-content-center">
                                    <i class="bi bi-envelope me-2"></i>Contact support
                                </a>
                                <a href="{{ route('blog.index') }}" class="surface-button-secondary justify-content-center">
                                    <i class="bi bi-journal-richtext me-2"></i>Read devotionals
                                </a>
                            </div>
                        </div>

                        <div style="background:linear-gradient(135deg,rgba(193,18,31,0.04),rgba(188,111,44,0.04));border:1px solid rgba(193,18,31,0.1);border-radius:14px;padding:1.25rem 1.5rem;">
                            <div class="public-kicker mb-2">
                                <i class="bi bi-lightbulb me-1"></i>Quick tip
                            </div>
                            <p style="font-size:0.87rem;color:#6b7280;line-height:1.7;margin:0;">Use the search above to look for specific topics, Scripture references, or ministry themes.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
