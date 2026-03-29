@php
    $post = $post ?? null;
    $isEdit = $post && $post->exists;
    $allowCategoryCreation = $allowCategoryCreation ?? false;
    $titleValue = old('title', $post->title ?? '');
    $scriptureValue = old('scripture', $post->scripture ?? '');
    $bibleTextValue = old('bible_text', $post->bible_text ?? '');
    $subtitleValue = old('subtitle', $post->subtitle ?? '');
    $detailsValue = old('details', $post->details ?? '');
    $actionPointValue = old('action_point', $post->action_point ?? '');
    $authorValue = old('author', $post->author ?? '');
    $publishedAtValue = old('published_at', optional($post?->published_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i'));
    $selectedCategoryIds = collect(old('category_ids', $isEdit ? $post->categories->pluck('id')->all() : []))->map(fn ($id) => (int) $id)->all();
    $tagNamesValue = old('tag_names', $isEdit ? $post->tags->pluck('name')->implode(', ') : '');
    $featuredImageGenerationEnabled = old('featured_image_generation_enabled', $post->featured_image_generation_enabled ?? false);
    $featuredImageProvider = old('featured_image_provider', $post->featured_image_provider ?? $defaultAiProvider);
    $featuredImagePreset = old('featured_image_preset', $post->featured_image_preset ?? $defaultAiPreset);
    $featuredImagePrompt = old('featured_image_prompt', $post->featured_image_prompt ?? '');
    $featuredImageOptions = old('featured_image_options', $isEdit && $post->featured_image_options ? json_encode($post->featured_image_options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '');
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Post Content</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ $titleValue }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Scripture</label>
                            <input type="text" name="scripture" class="form-control @error('scripture') is-invalid @enderror" value="{{ $scriptureValue }}">
                            @error('scripture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" value="{{ $authorValue }}">
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Bible Text</label>
                    <textarea name="bible_text" class="form-control @error('bible_text') is-invalid @enderror" rows="4" data-rich-text-editor>{{ $bibleTextValue }}</textarea>
                    @error('bible_text')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Subtitle</label>
                    <input type="text" name="subtitle" class="form-control @error('subtitle') is-invalid @enderror" value="{{ $subtitleValue }}">
                    @error('subtitle')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Details</label>
                    <textarea name="details" id="details" class="form-control @error('details') is-invalid @enderror" rows="10" required data-rich-text-editor>{{ $detailsValue }}</textarea>
                    @error('details')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Action Point</label>
                    <textarea name="action_point" class="form-control @error('action_point') is-invalid @enderror" rows="4" data-rich-text-editor>{{ $actionPointValue }}</textarea>
                    @error('action_point')
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
                    <label class="form-label">Publication Date &amp; Time</label>
                    <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ $publishedAtValue }}">
                    <div class="form-text">Use a publish time for a live post, or choose Save Draft to keep it unpublished.</div>
                    @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(! $isEdit)
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="shareToFacebook" name="share_to_facebook" @checked(old('share_to_facebook'))>
                        <label class="form-check-label" for="shareToFacebook">Share to Facebook after publishing</label>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Featured Image</h5>
            </div>
            <div class="card-body">
                @if($isEdit && $post->image)
                    <img src="{{ asset('storage/' . $post->image) }}" alt="Current image" class="img-fluid rounded border mb-3">
                @endif

                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                @error('image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card border-light-subtle mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">AI Image Settings</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                    <div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" value="1" id="featuredImageGenerationEnabled" name="featured_image_generation_enabled" @checked($featuredImageGenerationEnabled)>
                            <label class="form-check-label" for="featuredImageGenerationEnabled">Generate featured image automatically with AI</label>
                        </div>

                        @if($isEdit)
                            <div class="small text-muted">
                                Source: {{ $post->featured_image_source === 'manual' ? 'manual image' : ($post->featured_image_source === 'ai' ? 'ai image' : 'none') }}
                                · Generation: {{ $post->featured_image_generation_status ?? 'not queued' }}
                                @if($post->featured_image_approval_status)
                                    · Review: {{ $post->featured_image_approval_status }}
                                @endif
                                @if($post->featured_image_generated_at)
                                    · Last generated {{ $post->featured_image_generated_at->diffForHumans() }}
                                @endif
                            </div>
                            @if($post->featured_image_generation_error)
                                <div class="small text-danger mt-1">{{ $post->featured_image_generation_error }}</div>
                            @endif
                        @else
                            <div class="small text-muted">Keep this enabled to queue a generated image when the post is saved.</div>
                        @endif
                    </div>

                    @if($isEdit && config('ai-images.enabled'))
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('generate-featured-image-form').submit();">Generate Candidate</button>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-md-6 col-lg-12">
                        <label class="form-label">AI Provider</label>
                        <select name="featured_image_provider" class="form-select @error('featured_image_provider') is-invalid @enderror">
                            @foreach($aiProviders as $providerKey => $provider)
                                <option value="{{ $providerKey }}" @selected($featuredImageProvider === $providerKey)>
                                    {{ $provider['label'] ?? $providerKey }}
                                </option>
                            @endforeach
                        </select>
                        @error('featured_image_provider')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 col-lg-12">
                        <label class="form-label">Style Preset</label>
                        <select name="featured_image_preset" class="form-select @error('featured_image_preset') is-invalid @enderror">
                            @foreach($aiPresets as $presetKey => $preset)
                                <option value="{{ $presetKey }}" @selected($featuredImagePreset === $presetKey)>
                                    {{ $preset['label'] ?? $presetKey }}
                                </option>
                            @endforeach
                        </select>
                        @error('featured_image_preset')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Prompt Override</label>
                        <textarea name="featured_image_prompt" class="form-control @error('featured_image_prompt') is-invalid @enderror" rows="3" placeholder="Optional extra direction for the selected preset">{{ $featuredImagePrompt }}</textarea>
                        @error('featured_image_prompt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Advanced Options JSON</label>
                        <textarea name="featured_image_options" class="form-control @error('featured_image_options') is-invalid @enderror" rows="3" placeholder='Optional provider-specific overrides, e.g. {"size":"1024x1024","quality":"high"}'>{{ $featuredImageOptions }}</textarea>
                        <div class="form-text">Presets apply now. You can add provider-specific options later without changing the post schema.</div>
                        @error('featured_image_options')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Categories</h5>
                @if($allowCategoryCreation)
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-category-btn">
                        <i class="bi bi-plus"></i> Add New
                    </button>
                @endif
            </div>
            <div class="card-body">
                <div id="categories-container" class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
                    @forelse($categories as $category)
                        <div class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="category_ids[]"
                                value="{{ $category->id }}"
                                id="category_{{ $category->id }}"
                                {{ in_array($category->id, $selectedCategoryIds, true) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="category_{{ $category->id }}">{{ $category->name }}</label>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No categories available.</p>
                    @endforelse
                </div>
                @error('category_ids')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
                @error('category_ids.*')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Tags</h5>
            </div>
            <div class="card-body">
                <label for="tag_names" class="form-label">Comma-separated tags</label>
                <input
                    type="text"
                    id="tag_names"
                    name="tag_names"
                    class="form-control @error('tag_names') is-invalid @enderror"
                    value="{{ $tagNamesValue }}"
                    placeholder="faith, outreach, testimony"
                >
                <div class="form-text">Type tags naturally and separate them with commas. Existing tags will be reused and new ones will be created automatically.</div>
                @if($tags->isNotEmpty())
                    <div class="mt-3">
                        <div class="small text-muted mb-2">Existing tags</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tags->take(20) as $tag)
                                <span class="badge text-bg-light border">{{ $tag->name }}</span>
                            @endforeach
                            @if($tags->count() > 20)
                                <span class="badge text-bg-light border">+{{ $tags->count() - 20 }} more</span>
                            @endif
                        </div>
                    </div>
                @endif
                @error('tag_names')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>
