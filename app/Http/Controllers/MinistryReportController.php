<?php

namespace App\Http\Controllers;

use App\Models\MinistryReport;
use Illuminate\Http\Request;

class MinistryReportController extends Controller
{
    public function index(Request $request)
    {
        $publishedReports = MinistryReport::published();

        $statsBaseQuery = clone $publishedReports;
        $stats = [
            'reports' => (clone $statsBaseQuery)->count(),
            'people_reached' => (clone $statsBaseQuery)->sum('people_reached'),
            'souls_impacted' => (clone $statsBaseQuery)->sum('souls_impacted'),
            'volunteers' => (clone $statsBaseQuery)->sum('volunteers_count'),
        ];

        $filters = [
            'q' => trim((string) $request->string('q')),
            'type' => trim((string) $request->string('type')),
            'location' => trim((string) $request->string('location')),
            'year' => trim((string) $request->string('year')),
        ];

        $featuredReport = null;
        $reportsQuery = MinistryReport::published()
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $query->where(function ($builder) use ($filters) {
                    $builder->where('title', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('summary', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('details', 'like', '%' . $filters['q'] . '%')
                        ->orWhere('location', 'like', '%' . $filters['q'] . '%');
                });
            })
            ->when($filters['type'] !== '', fn ($query) => $query->where('report_type', $filters['type']))
            ->when($filters['location'] !== '', fn ($query) => $query->where('location', $filters['location']))
            ->when($filters['year'] !== '', fn ($query) => $query->whereYear('report_date', $filters['year']))
            ->orderByDesc('is_featured')
            ->orderByDesc('report_date')
            ->orderByDesc('published_at');

        if (! $this->hasActiveFilters($filters)) {
            $featuredReport = MinistryReport::published()
                ->orderByDesc('is_featured')
                ->orderByDesc('report_date')
                ->orderByDesc('published_at')
                ->first();

            if ($featuredReport) {
                $reportsQuery->whereKeyNot($featuredReport->getKey());
            }
        }

        $reports = $reportsQuery->paginate(9)->withQueryString();

        $types = MinistryReport::published()
            ->select('report_type')
            ->distinct()
            ->orderBy('report_type')
            ->pluck('report_type');

        $locations = MinistryReport::published()
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        $years = MinistryReport::published()
            ->orderByDesc('report_date')
            ->get(['report_date'])
            ->pluck('report_date')
            ->filter()
            ->map(fn ($date) => $date->format('Y'))
            ->unique()
            ->values();

        return view('pages.reports.index', compact(
            'reports',
            'featuredReport',
            'stats',
            'filters',
            'types',
            'locations',
            'years'
        ));
    }

    public function show(MinistryReport $report)
    {
        abort_unless($report->isPublished(), 404);

        $relatedReports = MinistryReport::published()
            ->whereKeyNot($report->getKey())
            ->where(function ($query) use ($report) {
                $query->where('report_type', $report->report_type);

                if ($report->location) {
                    $query->orWhere('location', $report->location);
                }
            })
            ->orderByDesc('report_date')
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return view('pages.reports.show', compact('report', 'relatedReports'));
    }

    private function hasActiveFilters(array $filters): bool
    {
        return collect($filters)->contains(fn ($value) => $value !== '');
    }
}
