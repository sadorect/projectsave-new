<x-layouts.app
    title="Projectsave FAQs"
    meta-description="Browse frequently asked questions and biblical answers from Projectsave International."
>
    <x-ui.public-page-hero
        eyebrow="FAQs"
        title="Biblical answers for real ministry and life questions"
        subtitle="Explore clear, Scripture-shaped answers that support spiritual growth, understanding, and next steps."
    >
        <x-slot:actions>
            <a href="{{ route('search') }}?q=faith" class="surface-button-primary">Search answers</a>
            <a href="{{ route('contact.show') }}" class="surface-button-secondary">Ask for help</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-4">
                        @forelse($faqs as $faq)
                            <div class="col-md-6">
                                <article class="public-card p-4">
                                    <div class="public-chip mb-3">{{ $faq->created_at->format('M d, Y') }}</div>
                                    <h3 class="h5 mb-3">
                                        <a href="{{ route('faqs.show', $faq->slug) }}" class="text-decoration-none">{{ $faq->title }}</a>
                                    </h3>
                                    <p class="mb-4 text-muted">{{ \Illuminate\Support\Str::limit(strip_tags($faq->details), 150) }}</p>
                                    <a href="{{ route('faqs.show', $faq->slug) }}" class="surface-button-secondary">Read answer</a>
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

                    <div class="mt-5">
                        {{ $faqs->links() }}
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Quick search"
                                title="Search the knowledge base"
                            />

                            <form action="{{ route('search') }}" method="GET" class="mt-4">
                                <label for="faq-search" class="form-label fw-semibold">Keyword</label>
                                <div class="d-flex gap-2">
                                    <input id="faq-search" type="text" class="form-control" name="q" placeholder="Search articles...">
                                    <button class="surface-button-primary" type="submit">Go</button>
                                </div>
                            </form>
                        </div>

                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Need more help?"
                                title="Talk to the team"
                                description="If your question is not answered here, contact us directly."
                            />

                            <div class="d-grid gap-2 mt-4">
                                <a href="{{ route('contact.show') }}" class="surface-button-primary justify-content-center">Contact support</a>
                                <a href="{{ route('blog.index') }}" class="surface-button-secondary justify-content-center">Read devotionals</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
