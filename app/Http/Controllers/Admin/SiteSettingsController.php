<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FileUploadService;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingsController extends Controller
{
    public function __construct(private readonly SiteSettings $siteSettings)
    {
        $this->middleware('permission:manage-settings,admin');
    }

    public function edit()
    {
        return view('admin.settings.site', [
            'settings' => $this->siteSettings->publicData(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => 'nullable|string|max:120',
            'site_short_name' => 'nullable|string|max:80',
            'site_tagline' => 'nullable|string|max:180',
            'site_description' => 'nullable|string|max:500',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:4096',
            'favicon' => 'nullable|file|mimes:jpg,jpeg,png,webp,svg|max:2048',
            'remove_logo' => 'nullable|boolean',
            'remove_favicon' => 'nullable|boolean',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email:rfc|max:120',
            'privacy_email' => 'nullable|email:rfc|max:120',
            'contact_address' => 'nullable|string|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'x_url' => 'nullable|url|max:255',
            'mission_statement' => 'nullable|string|max:500',
            'vision_statement' => 'nullable|string|max:500',
        ]);

        $current = $this->siteSettings->all();

        $payload = [
            'site_name' => $this->clean($validated['site_name'] ?? null),
            'site_short_name' => $this->clean($validated['site_short_name'] ?? null),
            'site_tagline' => $this->clean($validated['site_tagline'] ?? null),
            'site_description' => $this->clean($validated['site_description'] ?? null),
            'logo_path' => $current['logo_path'] ?? null,
            'favicon_path' => $current['favicon_path'] ?? null,
            'contact_phone' => $this->clean($validated['contact_phone'] ?? null),
            'contact_email' => $this->clean($validated['contact_email'] ?? null),
            'privacy_email' => $this->clean($validated['privacy_email'] ?? null),
            'contact_address' => $this->clean($validated['contact_address'] ?? null),
            'facebook_url' => $this->clean($validated['facebook_url'] ?? null),
            'instagram_url' => $this->clean($validated['instagram_url'] ?? null),
            'youtube_url' => $this->clean($validated['youtube_url'] ?? null),
            'x_url' => $this->clean($validated['x_url'] ?? null),
            'mission_statement' => $this->clean($validated['mission_statement'] ?? null),
            'vision_statement' => $this->clean($validated['vision_statement'] ?? null),
        ];

        if ($request->boolean('remove_logo')) {
            $this->deleteManagedFile($payload['logo_path']);
            $payload['logo_path'] = '';
        }

        if ($request->boolean('remove_favicon')) {
            $this->deleteManagedFile($payload['favicon_path']);
            $payload['favicon_path'] = '';
        }

        if ($request->hasFile('logo')) {
            $this->deleteManagedFile($payload['logo_path']);
            $payload['logo_path'] = FileUploadService::uploadImage(
                $request->file('logo'),
                'site-settings',
                'public',
                ['jpg', 'jpeg', 'png', 'webp', 'svg'],
                'logo'
            );
        }

        if ($request->hasFile('favicon')) {
            $this->deleteManagedFile($payload['favicon_path']);
            $payload['favicon_path'] = FileUploadService::uploadImage(
                $request->file('favicon'),
                'site-settings',
                'public',
                ['jpg', 'jpeg', 'png', 'webp', 'svg'],
                'favicon'
            );
        }

        $this->siteSettings->save($payload);

        return redirect()
            ->route('admin.site-settings.edit')
            ->with('success', 'Site settings updated successfully.');
    }

    private function clean(?string $value): string
    {
        return trim((string) $value);
    }

    private function deleteManagedFile(?string $path): void
    {
        if (! filled($path)) {
            return;
        }

        $path = ltrim($path, '/');

        if (! str_starts_with($path, 'site-settings/')) {
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
