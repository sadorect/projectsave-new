@php
    $existingGallery = collect(old('existing_gallery', $report->gallery ?? []));
@endphp

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Report Details</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $report->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Report Type</label>
                            <select name="report_type" class="form-select @error('report_type') is-invalid @enderror" required>
                                <option value="">Choose a report type</option>
                                @foreach($typeOptions as $typeOption)
                                    <option value="{{ $typeOption }}" @selected(old('report_type', $report->report_type) === $typeOption)>{{ $typeOption }}</option>
                                @endforeach
                            </select>
                            @error('report_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Report Date</label>
                            <input type="date" name="report_date" class="form-control @error('report_date') is-invalid @enderror" value="{{ old('report_date', optional($report->report_date)->format('Y-m-d')) }}" required>
                            @error('report_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $report->location) }}" placeholder="City, state, campus, or region">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Lead Team</label>
                            <input type="text" name="lead_team" class="form-control @error('lead_team') is-invalid @enderror" value="{{ old('lead_team', $report->lead_team) }}" placeholder="Projectsave Outreach Team">
                            @error('lead_team')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Summary</label>
                    <textarea name="summary" rows="3" class="form-control @error('summary') is-invalid @enderror" required>{{ old('summary', $report->summary) }}</textarea>
                    <div class="form-text">This is the short overview used on the report listing and search results.</div>
                    @error('summary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Full Report</label>
                    <textarea name="details" rows="10" class="form-control @error('details') is-invalid @enderror" required data-rich-text-editor>{{ old('details', $report->details) }}</textarea>
                    @error('details')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">People Reached</label>
                            <input type="number" min="0" name="people_reached" class="form-control @error('people_reached') is-invalid @enderror" value="{{ old('people_reached', $report->people_reached ?? 0) }}">
                            @error('people_reached')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Souls Impacted</label>
                            <input type="number" min="0" name="souls_impacted" class="form-control @error('souls_impacted') is-invalid @enderror" value="{{ old('souls_impacted', $report->souls_impacted ?? 0) }}">
                            @error('souls_impacted')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Volunteers</label>
                            <input type="number" min="0" name="volunteers_count" class="form-control @error('volunteers_count') is-invalid @enderror" value="{{ old('volunteers_count', $report->volunteers_count ?? 0) }}">
                            @error('volunteers_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Testimony Title</label>
                    <input type="text" name="testimony_title" class="form-control @error('testimony_title') is-invalid @enderror" value="{{ old('testimony_title', $report->testimony_title) }}" placeholder="A life-changing moment from the outreach">
                    @error('testimony_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Testimony / Quote</label>
                    <textarea name="testimony_quote" rows="4" class="form-control @error('testimony_quote') is-invalid @enderror" data-rich-text-editor>{{ old('testimony_quote', $report->testimony_quote) }}</textarea>
                    @error('testimony_quote')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Prayer Points</label>
                    <textarea name="prayer_points" rows="5" class="form-control @error('prayer_points') is-invalid @enderror" data-rich-text-editor>{{ old('prayer_points', $report->prayer_points) }}</textarea>
                    @error('prayer_points')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="form-label">Next Steps</label>
                    <textarea name="next_steps" rows="5" class="form-control @error('next_steps') is-invalid @enderror" data-rich-text-editor>{{ old('next_steps', $report->next_steps) }}</textarea>
                    @error('next_steps')
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
                    <label class="form-label">Publish At</label>
                    <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', optional($report->published_at)->format('Y-m-d\TH:i')) }}">
                    <div class="form-text">Leave blank to keep this report as a draft.</div>
                    @error('published_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" value="1" id="is_featured" name="is_featured" @checked(old('is_featured', $report->is_featured))>
                    <label class="form-check-label" for="is_featured">Feature this report on the reports page</label>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Featured Image</h5>
            </div>
            <div class="card-body">
                @if($report->featured_image)
                    <img src="{{ asset('storage/' . $report->featured_image) }}" alt="Featured image" class="img-fluid rounded border mb-3">
                @endif

                <input type="file" name="featured_image" class="form-control @error('featured_image') is-invalid @enderror">
                <div class="form-text">Used for the hero card and report cover.</div>
                @error('featured_image')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Gallery</h5>
            </div>
            <div class="card-body">
                <input type="file" name="gallery_images[]" multiple class="form-control @error('gallery_images') is-invalid @enderror @error('gallery_images.*') is-invalid @enderror">
                <div class="form-text">Upload multiple outreach photos for the public gallery.</div>
                @error('gallery_images')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('gallery_images.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                @if($existingGallery->isNotEmpty())
                    <div class="mt-3">
                        <div class="small fw-semibold mb-2">Existing gallery images</div>
                        <div class="row g-2">
                            @foreach($existingGallery as $imagePath)
                                <div class="col-6">
                                    <div class="border rounded p-2 h-100">
                                        <img src="{{ asset('storage/' . $imagePath) }}" alt="Gallery image" class="img-fluid rounded mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="remove_gallery[]" value="{{ $imagePath }}" id="remove_gallery_{{ $loop->index }}">
                                            <label class="form-check-label small" for="remove_gallery_{{ $loop->index }}">Remove</label>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
