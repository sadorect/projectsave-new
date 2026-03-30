<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Event;
use App\Models\Faq;
use App\Models\MinistryReport;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class SearchController extends Controller
{
    private const PER_PAGE = 20;

    public function index(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if ($query === '') {
            return view('search.index', [
                'results' => new LengthAwarePaginator([], 0, self::PER_PAGE),
                'query'   => $query,
            ]);
        }

        // Split into individual words so multi-word queries like "finding christ"
        // match titles such as "finding and knowing christ".
        $terms = collect(preg_split('/\s+/', $query))
            ->map(fn (string $t) => trim($t))
            ->filter(fn (string $t) => $t !== '')
            ->values()
            ->all();

        // Merge results from all content types, sorted by relevance then date.
        $all = collect()
            ->merge($this->searchPosts($terms))
            ->merge($this->searchFaqs($terms))
            ->merge($this->searchReports($terms))
            ->merge($this->searchEvents($terms))
            ->sortBy([['title_score', 'asc'], ['date', 'desc']]);

        $page  = max(1, (int) $request->input('page', 1));
        $total = $all->count();

        $results = new LengthAwarePaginator(
            $all->forPage($page, self::PER_PAGE)->values(),
            $total,
            self::PER_PAGE,
            $page,
            ['path' => $request->url(), 'query' => $request->query()],
        );

        return view('search.index', compact('results', 'query'));
    }

    /** Returns 0 if any term appears in the title, 1 otherwise. */
    private function titleScore(string $title, array $terms): int
    {
        foreach ($terms as $term) {
            if (stripos($title, $term) !== false) {
                return 0;
            }
        }

        return 1;
    }

    private function searchPosts(array $terms): array
    {
        return Post::published()
            ->where(function ($builder) use ($terms) {
                foreach ($terms as $term) {
                    $builder->where(function ($q) use ($term) {
                        $q->where('title', 'like', "%{$term}%")
                            ->orWhere('details', 'like', "%{$term}%")
                            ->orWhere('scripture', 'like', "%{$term}%")
                            ->orWhere('author', 'like', "%{$term}%");
                    });
                }
            })
            ->get()
            ->map(fn (Post $post) => [
                'type'        => 'post',
                'type_label'  => 'Devotional',
                'title'       => $post->title,
                'excerpt'     => Str::limit(strip_tags((string) $post->details), 150),
                'url'         => route('posts.show', $post->slug),
                'date'        => $post->published_at,
                'meta'        => [
                    ['icon' => 'bi bi-calendar-event', 'value' => optional($post->published_at)->format('M d, Y')],
                    ['icon' => 'bi bi-person',         'value' => $post->author],
                ],
                'title_score' => $this->titleScore($post->title, $terms),
            ])
            ->all();
    }

    private function searchFaqs(array $terms): array
    {
        return Faq::published()
            ->where(function ($builder) use ($terms) {
                foreach ($terms as $term) {
                    $builder->where(function ($q) use ($term) {
                        $q->where('title', 'like', "%{$term}%")
                            ->orWhere('details', 'like', "%{$term}%");
                    });
                }
            })
            ->get()
            ->map(fn (Faq $faq) => [
                'type'        => 'faq',
                'type_label'  => 'FAQ',
                'title'       => $faq->title,
                'excerpt'     => Str::limit(strip_tags((string) $faq->details), 150),
                'url'         => route('faqs.show', $faq->slug),
                'date'        => $faq->created_at,
                'meta'        => [],
                'title_score' => $this->titleScore($faq->title, $terms),
            ])
            ->all();
    }

    private function searchReports(array $terms): array
    {
        return MinistryReport::published()
            ->where(function ($builder) use ($terms) {
                foreach ($terms as $term) {
                    $builder->where(function ($q) use ($term) {
                        $q->where('title', 'like', "%{$term}%")
                            ->orWhere('summary', 'like', "%{$term}%")
                            ->orWhere('details', 'like', "%{$term}%")
                            ->orWhere('location', 'like', "%{$term}%");
                    });
                }
            })
            ->get()
            ->map(fn (MinistryReport $report) => [
                'type'        => 'report',
                'type_label'  => $report->report_type ?: 'Report',
                'title'       => $report->title,
                'excerpt'     => Str::limit(strip_tags((string) $report->summary), 150),
                'url'         => route('reports.show', $report),
                'date'        => $report->report_date,
                'meta'        => [
                    ['icon' => 'bi bi-geo-alt',        'value' => $report->location ?: 'Multiple locations'],
                    ['icon' => 'bi bi-calendar-event', 'value' => optional($report->report_date)->format('M d, Y')],
                ],
                'title_score' => $this->titleScore($report->title, $terms),
            ])
            ->all();
    }

    private function searchEvents(array $terms): array
    {
        return Event::query()
            ->where(function ($builder) use ($terms) {
                foreach ($terms as $term) {
                    $builder->where(function ($q) use ($term) {
                        $q->where('title', 'like', "%{$term}%")
                            ->orWhere('description', 'like', "%{$term}%");
                    });
                }
            })
            ->get()
            ->map(fn (Event $event) => [
                'type'        => 'event',
                'type_label'  => 'Event',
                'title'       => $event->title,
                'excerpt'     => Str::limit(strip_tags((string) $event->description), 150),
                'url'         => route('events.show', $event),
                'date'        => $event->start_date ? \Carbon\Carbon::parse($event->start_date) : null,
                'meta'        => [
                    ['icon' => 'bi bi-geo-alt',        'value' => $event->location],
                    ['icon' => 'bi bi-calendar-event', 'value' => $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : null],
                ],
                'title_score' => $this->titleScore($event->title, $terms),
            ])
            ->all();
    }
}
