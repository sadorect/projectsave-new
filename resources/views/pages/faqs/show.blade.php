<x-layouts.app
    :title="$faq->title . ' | Projectsave FAQ'"
    :meta-description="\Illuminate\Support\Str::limit(strip_tags($faq->details), 160)"
>
    <x-ui.public-page-hero
        eyebrow="FAQ"
        :title="$faq->title"
        :subtitle="'Published ' . $faq->created_at->format('F d, Y')"
    >
        <x-slot:actions>
            <a href="{{ route('faqs.list') }}" class="surface-button-secondary">Back to FAQs</a>
            <a href="{{ route('contact.show') }}" class="surface-button-primary">Still need help?</a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section pt-2">
        <div class="surface-frame">
            <div class="row g-4 align-items-start">
                <div class="col-lg-8">
                    <article class="public-card p-4 p-lg-5">
                        <div class="public-richtext">
                            {!! $faq->details !!}
                        </div>

                        <div class="d-flex justify-content-between gap-3 mt-5">
                            @if($previous)
                                <a href="{{ route('faqs.show', $previous->slug) }}" class="surface-button-secondary">
                                    <i class="bi bi-arrow-left"></i>
                                    Previous article
                                </a>
                            @else
                                <span></span>
                            @endif

                            @if($next)
                                <a href="{{ route('faqs.show', $next->slug) }}" class="surface-button-secondary">
                                    Next article
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </article>
                </div>

                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">
                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Search"
                                title="Find another answer"
                            />

                            <form action="{{ route('search') }}" method="GET" class="mt-4">
                                <label for="faq-search-detail" class="form-label fw-semibold">Keyword</label>
                                <div class="d-flex gap-2">
                                    <input id="faq-search-detail" type="text" class="form-control" name="q" placeholder="Search the knowledge base...">
                                    <button class="surface-button-primary" type="submit">Go</button>
                                </div>
                            </form>
                        </div>

                        <div class="public-sidebar-card">
                            <x-ui.public-section-heading
                                eyebrow="Recent answers"
                                title="More FAQs"
                            />

                            <div class="d-flex flex-column gap-3 mt-4">
                                @foreach($recentFaqs as $recentFaq)
                                    <a href="{{ route('faqs.show', $recentFaq->slug) }}" class="text-decoration-none">
                                        <div class="border rounded-4 p-3">
                                            <h3 class="h6 mb-1">{{ $recentFaq->title }}</h3>
                                            <small class="text-muted">{{ $recentFaq->created_at->format('M d, Y') }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div class="public-sidebar-card">
                            <div class="public-kicker mb-3">Need personal follow-up?</div>
                            <p class="mb-3 text-muted">If your question is more specific than a published FAQ can cover, contact the team directly.</p>
                            <a href="{{ route('contact.show') }}" class="surface-button-primary">Contact support</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
