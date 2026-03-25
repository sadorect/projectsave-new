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
            <a href="{{ route('faqs.list') }}" class="surface-button-secondary">
                <i class="bi bi-arrow-left me-2"></i>All FAQs
            </a>
            <a href="{{ route('contact.show') }}" class="surface-button-primary">
                <i class="bi bi-chat-dots me-2"></i>Still need help?
            </a>
        </x-slot:actions>
    </x-ui.public-page-hero>

    <section class="surface-section">
        <div class="surface-frame">
            <div class="row g-5 align-items-start">

                {{-- Article --}}
                <div class="col-lg-8">
                    <article class="faq-article">
                        <div class="public-richtext">
                            {!! $faq->details !!}
                        </div>

                        <div class="article-nav">
                            @if($previous)
                                <a href="{{ route('faqs.show', $previous->slug) }}" class="article-nav-link">
                                    <i class="bi bi-arrow-left"></i>Previous
                                </a>
                            @else
                                <span></span>
                            @endif

                            @if($next)
                                <a href="{{ route('faqs.show', $next->slug) }}" class="article-nav-link">
                                    Next<i class="bi bi-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </article>
                </div>

                {{-- Sidebar --}}
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4">

                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">Search</span>
                            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:0.8rem;">Find another answer</h3>
                            <form action="{{ route('search') }}" method="GET">
                                <label for="faq-search-detail" class="visually-hidden">Keyword</label>
                                <div class="d-flex gap-2">
                                    <input id="faq-search-detail" type="text" class="form-control" name="q" placeholder="Search...">
                                    <button class="surface-button-primary" type="submit"><i class="bi bi-search"></i></button>
                                </div>
                            </form>
                        </div>

                        <div class="public-sidebar-card">
                            <span class="page-section-eyebrow mb-2 d-block">Recent answers</span>
                            <h3 style="font-size:1rem;font-weight:700;color:#0f172a;margin-bottom:0.8rem;">More FAQs</h3>
                            <div class="d-flex flex-column gap-2">
                                @foreach($recentFaqs as $recentFaq)
                                    <a href="{{ route('faqs.show', $recentFaq->slug) }}" class="text-decoration-none">
                                        <div style="border:1px solid rgba(148,163,184,0.15);border-radius:12px;padding:0.75rem 1rem;background:#f8fafc;">
                                            <p style="font-size:0.88rem;font-weight:600;color:#0f172a;margin:0 0 0.15rem;">{{ $recentFaq->title }}</p>
                                            <small style="color:#94a3b8;font-size:0.75rem;">{{ $recentFaq->created_at->format('M d, Y') }}</small>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <div style="background:linear-gradient(135deg,rgba(193,18,31,0.04),rgba(188,111,44,0.04));border:1px solid rgba(193,18,31,0.1);border-radius:14px;padding:1.25rem 1.5rem;">
                            <div class="public-kicker mb-2">Need personal follow-up?</div>
                            <p style="font-size:0.87rem;color:#6b7280;line-height:1.7;margin-bottom:1rem;">If your question is more specific, contact our team directly and we'll respond by email.</p>
                            <a href="{{ route('contact.show') }}" class="surface-button-primary" style="font-size:0.85rem;">
                                <i class="bi bi-envelope me-2"></i>Contact support
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
