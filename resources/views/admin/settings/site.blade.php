@extends('admin.layouts.app')

@section('title', 'Site Settings')
@section('page_subtitle', 'Manage the public ministry identity, contacts, social links, and mission copy from one safe control panel.')

@section('content')
<div class="container-fluid">
    <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <h1 class="mb-1">Site Settings</h1>
                <p class="text-muted mb-0">Current public values remain in place until you replace or clear them here.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('home') }}" class="btn btn-outline-secondary" target="_blank" rel="noopener noreferrer">Preview public site</a>
                <button type="submit" class="btn btn-primary">Save settings</button>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="d-flex flex-column gap-4">
                    <x-ui.panel title="Brand Identity" subtitle="Keep the public-facing ministry identity consistent across header, footer, metadata, and shared site chrome.">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="site_name" class="form-label">Site name</label>
                                <input
                                    id="site_name"
                                    type="text"
                                    name="site_name"
                                    class="form-control @error('site_name') is-invalid @enderror"
                                    value="{{ old('site_name', $settings['site_name'] ?? '') }}"
                                    maxlength="120"
                                >
                                @error('site_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="site_short_name" class="form-label">Short name</label>
                                <input
                                    id="site_short_name"
                                    type="text"
                                    name="site_short_name"
                                    class="form-control @error('site_short_name') is-invalid @enderror"
                                    value="{{ old('site_short_name', $settings['site_short_name'] ?? '') }}"
                                    maxlength="80"
                                >
                                @error('site_short_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="site_tagline" class="form-label">Tagline</label>
                                <input
                                    id="site_tagline"
                                    type="text"
                                    name="site_tagline"
                                    class="form-control @error('site_tagline') is-invalid @enderror"
                                    value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}"
                                    maxlength="180"
                                >
                                @error('site_tagline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="site_description" class="form-label">Short ministry description</label>
                                <textarea
                                    id="site_description"
                                    name="site_description"
                                    rows="4"
                                    class="form-control @error('site_description') is-invalid @enderror"
                                    maxlength="500"
                                >{{ old('site_description', $settings['site_description'] ?? '') }}</textarea>
                                @error('site_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </x-ui.panel>

                    <x-ui.panel title="Contacts" subtitle="These values feed the header, footer, contact page, privacy page, and structured metadata.">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label">Primary phone</label>
                                <input
                                    id="contact_phone"
                                    type="text"
                                    name="contact_phone"
                                    class="form-control @error('contact_phone') is-invalid @enderror"
                                    value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                                    maxlength="50"
                                >
                                @error('contact_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label">Primary email</label>
                                <input
                                    id="contact_email"
                                    type="email"
                                    name="contact_email"
                                    class="form-control @error('contact_email') is-invalid @enderror"
                                    value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                                    maxlength="120"
                                >
                                @error('contact_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="privacy_email" class="form-label">Privacy email</label>
                                <input
                                    id="privacy_email"
                                    type="email"
                                    name="privacy_email"
                                    class="form-control @error('privacy_email') is-invalid @enderror"
                                    value="{{ old('privacy_email', $settings['privacy_email'] ?? '') }}"
                                    maxlength="120"
                                >
                                @error('privacy_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label for="contact_address" class="form-label">Mailing address</label>
                                <textarea
                                    id="contact_address"
                                    name="contact_address"
                                    rows="3"
                                    class="form-control @error('contact_address') is-invalid @enderror"
                                    maxlength="255"
                                >{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
                                @error('contact_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </x-ui.panel>

                    <x-ui.panel title="Social Profiles" subtitle="Leave any field blank if you do not want that network shown in the public header or footer.">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="facebook_url" class="form-label">Facebook URL</label>
                                <input
                                    id="facebook_url"
                                    type="url"
                                    name="facebook_url"
                                    class="form-control @error('facebook_url') is-invalid @enderror"
                                    value="{{ old('facebook_url', $settings['facebook_url'] ?? '') }}"
                                >
                                @error('facebook_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="instagram_url" class="form-label">Instagram URL</label>
                                <input
                                    id="instagram_url"
                                    type="url"
                                    name="instagram_url"
                                    class="form-control @error('instagram_url') is-invalid @enderror"
                                    value="{{ old('instagram_url', $settings['instagram_url'] ?? '') }}"
                                >
                                @error('instagram_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="youtube_url" class="form-label">YouTube URL</label>
                                <input
                                    id="youtube_url"
                                    type="url"
                                    name="youtube_url"
                                    class="form-control @error('youtube_url') is-invalid @enderror"
                                    value="{{ old('youtube_url', $settings['youtube_url'] ?? '') }}"
                                >
                                @error('youtube_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="x_url" class="form-label">X URL</label>
                                <input
                                    id="x_url"
                                    type="url"
                                    name="x_url"
                                    class="form-control @error('x_url') is-invalid @enderror"
                                    value="{{ old('x_url', $settings['x_url'] ?? '') }}"
                                >
                                @error('x_url') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </x-ui.panel>

                    <x-ui.panel title="Mission and Vision" subtitle="These statements power the About page summary and can be refreshed without editing Blade templates.">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="mission_statement" class="form-label">Mission statement</label>
                                <textarea
                                    id="mission_statement"
                                    name="mission_statement"
                                    rows="5"
                                    class="form-control @error('mission_statement') is-invalid @enderror"
                                    maxlength="500"
                                >{{ old('mission_statement', $settings['mission_statement'] ?? '') }}</textarea>
                                @error('mission_statement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="vision_statement" class="form-label">Vision statement</label>
                                <textarea
                                    id="vision_statement"
                                    name="vision_statement"
                                    rows="5"
                                    class="form-control @error('vision_statement') is-invalid @enderror"
                                    maxlength="500"
                                >{{ old('vision_statement', $settings['vision_statement'] ?? '') }}</textarea>
                                @error('vision_statement') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </x-ui.panel>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="d-flex flex-column gap-4">
                    <x-ui.panel title="Logo" subtitle="Used in the public header, footer, and social metadata.">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-4 border bg-light d-flex align-items-center justify-content-center" style="width:84px;height:84px;overflow:hidden;">
                                @if(filled($settings['logo_url'] ?? null))
                                    <img src="{{ $settings['logo_url'] }}" alt="Current logo" style="max-width:100%;max-height:100%;object-fit:contain;">
                                @else
                                    <span class="text-muted small">No logo</span>
                                @endif
                            </div>
                            <div class="small text-muted">
                                <div class="fw-semibold text-dark mb-1">Current public logo</div>
                                <div>Upload a replacement image or remove it and the site will safely fall back to text branding.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="logo" class="form-label">Upload new logo</label>
                            <input id="logo" type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,.svg">
                            @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="remove_logo" name="remove_logo" {{ old('remove_logo') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remove_logo">Remove current logo</label>
                        </div>
                    </x-ui.panel>

                    <x-ui.panel title="Favicon" subtitle="Used for the browser tab icon and related metadata.">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-4 border bg-light d-flex align-items-center justify-content-center" style="width:64px;height:64px;overflow:hidden;">
                                @if(filled($settings['favicon_url'] ?? null))
                                    <img src="{{ $settings['favicon_url'] }}" alt="Current favicon" style="max-width:100%;max-height:100%;object-fit:contain;">
                                @else
                                    <span class="text-muted small">No icon</span>
                                @endif
                            </div>
                            <div class="small text-muted">
                                <div class="fw-semibold text-dark mb-1">Current tab icon</div>
                                <div>PNG or SVG usually works best here.</div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="favicon" class="form-label">Upload new favicon</label>
                            <input id="favicon" type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror" accept=".jpg,.jpeg,.png,.webp,.svg">
                            @error('favicon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="remove_favicon" name="remove_favicon" {{ old('remove_favicon') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remove_favicon">Remove current favicon</label>
                        </div>
                    </x-ui.panel>

                    <x-ui.panel title="Visibility Notes" subtitle="A quick reminder of how null-safe values behave on the public site.">
                        <ul class="mb-0 ps-3 text-muted small">
                            <li>Blank social URLs are hidden automatically.</li>
                            <li>Blank contact links never render broken anchors.</li>
                            <li>Mission and vision copy falls back to the current public wording until changed.</li>
                            <li>Logo and favicon uploads can be replaced without touching templates.</li>
                        </ul>
                    </x-ui.panel>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
