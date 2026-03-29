@php
    $faq = $faq ?? null;
    $detailsValue = old('details', $faq->details ?? '');
    $statusValue = old('status', $faq->status ?? 'draft');
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">FAQ Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input
                        type="text"
                        class="form-control @error('title') is-invalid @enderror"
                        id="title"
                        name="title"
                        value="{{ old('title', $faq->title ?? '') }}"
                        required
                    >
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="details" class="form-label">Details</label>
                    <textarea
                        class="form-control @error('details') is-invalid @enderror"
                        id="details"
                        name="details"
                        rows="12"
                        data-rich-text-editor
                        required
                    >{{ $detailsValue }}</textarea>
                    <div class="form-text">Use rich text formatting for emphasis, lists, links, and pasted content.</div>
                    @error('details')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Publishing</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="draft" @selected($statusValue === 'draft')>Draft</option>
                        <option value="published" @selected($statusValue === 'published')>Published</option>
                    </select>
                </div>

                <p class="text-muted small mb-0">Published FAQs appear on the public FAQ pages and search results. Drafts stay in admin only.</p>
            </div>
        </div>
    </div>
</div>
