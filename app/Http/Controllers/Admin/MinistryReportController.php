<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MinistryReport;
use App\Services\FileUploadService;
use App\Services\HtmlSanitizer;
use Illuminate\Http\Request;

class MinistryReportController extends Controller
{
    public function index()
    {
        $reports = MinistryReport::query()
            ->orderByDesc('report_date')
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('admin.reports.index', compact('reports'));
    }

    public function create()
    {
        $report = new MinistryReport([
            'report_date' => now()->toDateString(),
            'published_at' => now(),
        ]);

        return view('admin.reports.create', [
            'report' => $report,
            'typeOptions' => MinistryReport::typeOptions(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);
        $validated['user_id'] = auth()->id();
        $validated['gallery'] = $this->storeGalleryImages($request);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = FileUploadService::uploadImage(
                $request->file('featured_image'),
                'reports',
                'public',
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'featured_image'
            );
        }

        MinistryReport::create($validated);

        return redirect()
            ->route('admin.reports.index')
            ->with('success', 'Ministry report created successfully.');
    }

    public function edit(MinistryReport $report)
    {
        return view('admin.reports.edit', [
            'report' => $report,
            'typeOptions' => MinistryReport::typeOptions(),
        ]);
    }

    public function update(Request $request, MinistryReport $report)
    {
        $validated = $this->validateRequest($request);

        $existingGallery = collect($report->gallery ?? []);
        $removedGallery = collect($request->input('remove_gallery', []))
            ->filter(fn ($path) => is_string($path) && $existingGallery->contains($path))
            ->values();
        $galleryToKeep = $existingGallery->reject(fn ($path) => $removedGallery->contains($path))->values();

        $removedGallery->each(function ($path): void {
            if (is_string($path) && $path !== '') {
                FileUploadService::deleteFile($path, 'public');
            }
        });

        if ($request->hasFile('featured_image')) {
            if ($report->featured_image) {
                FileUploadService::deleteFile($report->featured_image, 'public');
            }

            $validated['featured_image'] = FileUploadService::uploadImage(
                $request->file('featured_image'),
                'reports',
                'public',
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'featured_image'
            );
        }

        $validated['gallery'] = $galleryToKeep
            ->merge($this->storeGalleryImages($request))
            ->values()
            ->all();

        $report->update($validated);

        return redirect()
            ->route('admin.reports.index')
            ->with('success', 'Ministry report updated successfully.');
    }

    public function destroy(MinistryReport $report)
    {
        if ($report->featured_image) {
            FileUploadService::deleteFile($report->featured_image, 'public');
        }

        collect($report->gallery ?? [])->each(function ($path): void {
            if (is_string($path) && $path !== '') {
                FileUploadService::deleteFile($path, 'public');
            }
        });

        $report->delete();

        return redirect()
            ->route('admin.reports.index')
            ->with('success', 'Ministry report deleted successfully.');
    }

    private function validateRequest(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'report_type' => ['required', 'string', 'max:255'],
            'lead_team' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'report_date' => ['required', 'date'],
            'summary' => ['required', 'string', 'max:1000'],
            'details' => ['required', 'string'],
            'featured_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'remove_gallery' => ['nullable', 'array'],
            'remove_gallery.*' => ['string'],
            'people_reached' => ['nullable', 'integer', 'min:0'],
            'souls_impacted' => ['nullable', 'integer', 'min:0'],
            'volunteers_count' => ['nullable', 'integer', 'min:0'],
            'testimony_title' => ['nullable', 'string', 'max:255'],
            'testimony_quote' => ['nullable', 'string'],
            'prayer_points' => ['nullable', 'string'],
            'next_steps' => ['nullable', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $validated['summary'] = trim(strip_tags($validated['summary']));
        $validated['details'] = HtmlSanitizer::clean($validated['details']);
        $validated['testimony_quote'] = HtmlSanitizer::clean($validated['testimony_quote'] ?? '');
        $validated['prayer_points'] = HtmlSanitizer::clean($validated['prayer_points'] ?? '');
        $validated['next_steps'] = HtmlSanitizer::clean($validated['next_steps'] ?? '');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['people_reached'] = (int) ($validated['people_reached'] ?? 0);
        $validated['souls_impacted'] = (int) ($validated['souls_impacted'] ?? 0);
        $validated['volunteers_count'] = (int) ($validated['volunteers_count'] ?? 0);

        return $validated;
    }

    private function storeGalleryImages(Request $request): array
    {
        $paths = [];

        foreach ($request->file('gallery_images', []) as $index => $image) {
            $paths[] = FileUploadService::uploadImage(
                $image,
                'reports/gallery',
                'public',
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'gallery_images.' . $index
            );
        }

        return $paths;
    }
}
