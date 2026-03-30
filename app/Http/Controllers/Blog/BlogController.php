<?php

namespace App\Http\Controllers\Blog;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use App\Services\FileUploadService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

/**
 * Handles the blog-related functionality in the application.
 *
 * This controller provides methods for managing blog posts, including
 * listing all posts, creating new posts, displaying individual posts,
 * editing existing posts, and deleting posts.
 *
 * The controller also handles the logic for associating posts with
 * categories and tags, and for displaying related information on the
 * blog pages.
 */
class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::with(['categories', 'tags'])
            ->published();
        $calendarDate = now();
        $selectedDate = null;
        $selectedCategory = null;
        $selectedTag = null;
            
        if ($request->filled('date')) {
            try {
                $calendarDate = Carbon::parse($request->get('date'));
                $selectedDate = $calendarDate->format('F d, Y');
                $query->whereDate('published_at', $calendarDate->toDateString());
            } catch (\Throwable $exception) {
                $calendarDate = now();
                $selectedDate = null;
            }
        }

        if ($request->filled('category')) {
            $selectedCategory = Category::query()
                ->where('slug', (string) $request->string('category'))
                ->first();

            if ($selectedCategory) {
                $query->whereHas('categories', function ($builder) use ($selectedCategory) {
                    $builder->whereKey($selectedCategory->getKey());
                });
            }
        }

        if ($request->filled('tag')) {
            $selectedTag = Tag::query()
                ->where('slug', (string) $request->string('tag'))
                ->first();

            if ($selectedTag) {
                $query->whereHas('tags', function ($builder) use ($selectedTag) {
                    $builder->whereKey($selectedTag->getKey());
                });
            }
        }
        
        $posts = $query->orderBy('published_at', 'desc')
            ->paginate(6)
            ->appends($request->query());

        $categories = Category::withCount([
            'posts' => fn ($builder) => $builder->published(),
        ])->get();

        $recentPosts = Post::published()
            ->latest('published_at')
            ->take(5)
            ->get();

        [
            'calendar' => $calendar,
            'currentMonth' => $currentMonth,
            'calendarMonth' => $calendarMonth,
            'calendarYear' => $calendarYear,
            'calendarStartMonth' => $calendarStartMonth,
            'calendarStartYear' => $calendarStartYear,
            'calendarEndMonth' => $calendarEndMonth,
            'calendarEndYear' => $calendarEndYear,
            'postCalendarDays' => $postCalendarDays,
        ] = $this->buildCalendarPayload(
            $calendarDate->month,
            $calendarDate->year
        );
            
        return view('pages.blog.index', compact(
            'posts',
            'categories',
            'recentPosts',
            'calendar',
            'currentMonth',
            'calendarMonth',
            'calendarYear',
            'calendarStartMonth',
            'calendarStartYear',
            'calendarEndMonth',
            'calendarEndYear',
            'postCalendarDays',
            'selectedDate',
            'selectedCategory',
            'selectedTag'
        ));
    }

    public function indexFaqs()
    {
        $posts = Post::with(['categories', 'tags'])
            ->whereHas('categories', function($query) {
                $query->where('name', 'faqs');
            })
            ->orderBy('published_at', 'desc')
            ->paginate(6);
        return view('pages.faqs.index', compact('posts'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('pages.blog.create', compact('categories', 'tags'));
    }
    /**
      * Store a newly created resource in storage.
      */
    public function store(Request $request)
    {
        //dd($request->all());

        $validated = $request->validate([
            'title' => 'required|max:255',
            'scripture' => 'nullable',
            'subtitle' => 'nullable',
            'details' => 'required',
            'action_point' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'author' => 'nullable',
            'category_ids' => 'array',
            'tag_ids' => 'array',
            'published_at' => 'required|date'
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['user_id'] = auth()->id();
        
        if ($request->hasFile('image')) {
            $validated['image'] = FileUploadService::uploadImage(
                $request->file('image'),
                'posts',
                'public',
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'image'
            );
        }

        $post = Post::create($validated);
        $post->categories()->sync($request->category_ids);
        $post->tags()->sync($request->tag_ids);

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully');
    }
    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        abort_unless(
            optional($post->published_at)->lte(now()),
            404
        );

        $post = $post->load(['categories', 'tags']);
        $this->trackPostView($post);
        $post->refresh()->load(['categories', 'tags']);

        $recentPosts = Post::published()
            ->whereKeyNot($post->getKey())
            ->latest('published_at')
            ->take(5)
            ->get();
            
        $categories = Category::withCount([
            'posts' => fn ($query) => $query->published(),
        ])->get();
        
        $relatedPosts = Post::published()
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('categories.id', $post->categories->pluck('id'));
            })
            ->whereKeyNot($post->getKey())
            ->latest('published_at')
            ->take(3)
            ->get();

        [
            'calendar' => $calendar,
            'currentMonth' => $currentMonth,
            'calendarMonth' => $calendarMonth,
            'calendarYear' => $calendarYear,
            'calendarStartMonth' => $calendarStartMonth,
            'calendarStartYear' => $calendarStartYear,
            'calendarEndMonth' => $calendarEndMonth,
            'calendarEndYear' => $calendarEndYear,
            'postCalendarDays' => $postCalendarDays,
        ] = $this->buildCalendarPayload(
            $post->published_at?->month ?? now()->month,
            $post->published_at?->year ?? now()->year
        );
      
        $previous = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->orderBy('published_at', 'desc')
            ->first();
                   
        $next = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at', 'asc')
            ->first();
   
        return view('pages.blog.show', compact(
            'post', 
            'recentPosts', 
            'categories', 
            'relatedPosts',
            'calendar',
            'currentMonth',
            'calendarMonth',
            'calendarYear',
            'calendarStartMonth',
            'calendarStartYear',
            'calendarEndMonth',
            'calendarEndYear',
            'postCalendarDays',
            'previous',
            'next'
        ));
    }

    public function showFaqs(Post $post)
    {
        abort_unless(
            optional($post->published_at)->lte(now()),
            404
        );

        $recentPosts = Post::published()
            ->whereKeyNot($post->getKey())
            ->latest('published_at')
            ->take(5)
            ->get();
            
        $categories = Category::withCount([
            'posts' => fn ($query) => $query->published(),
        ])->get();
        
        $relatedPosts = Post::published()
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('categories.id', $post->categories->pluck('id'));
            })
            ->whereKeyNot($post->getKey())
            ->latest('published_at')
            ->take(3)
            ->get();

        $calendar = $this->generateCalendar();
        $currentMonth = Carbon::now()->format('F Y');

        $postDates = Post::published()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->get(['created_at'])
            ->map(fn ($post) => $post->created_at->format('Y-m-d'))
            ->toArray();
      
        return view('pages.faqs.show', compact(
            'post', 
            'recentPosts', 
            'categories', 
            'relatedPosts',
            'calendar',
            'currentMonth',
            'postDates'
        ));
    }

    private function generateCalendar($month = null, $year = null)
    {
        $month = $month ?? Carbon::now()->month;
        $year = $year ?? Carbon::now()->year;
        
        $date = Carbon::createFromDate($year, $month, 1);
        $calendar = collect();
        
        for ($i = 0; $i < $date->dayOfWeek; $i++) {
            $calendar->push(Carbon::createFromDate($year, $month, 1)->subDays($date->dayOfWeek - $i));
        }
        
        while ($date->month === (int) $month) {
            $calendar->push(clone $date);
            $date->addDay();
        }
        
        $remainingDays = 42 - $calendar->count();
        for ($i = 0; $i < $remainingDays; $i++) {
            $calendar->push(clone $date);
            $date->addDay();
        }
        
        return $calendar;
    }

    private function buildCalendarPayload(?int $month = null, ?int $year = null): array
    {
        $publishedBounds = Post::published()
            ->selectRaw('MIN(published_at) as first_published_at')
            ->first();

        $firstPublishedAt = $publishedBounds?->first_published_at
            ? Carbon::parse($publishedBounds->first_published_at)
            : now();

        $calendarStartMonth = $firstPublishedAt->month;
        $calendarStartYear = $firstPublishedAt->year;
        $calendarEndMonth = now()->month;
        $calendarEndYear = now()->year;

        $requestedMonth = $month ?? now()->month;
        $requestedYear = $year ?? now()->year;
        $requestedDate = Carbon::createFromDate($requestedYear, $requestedMonth, 1)->startOfMonth();
        $minimumDate = Carbon::createFromDate($calendarStartYear, $calendarStartMonth, 1)->startOfMonth();
        $maximumDate = Carbon::createFromDate($calendarEndYear, $calendarEndMonth, 1)->startOfMonth();
        $normalizedDate = $requestedDate;

        if ($requestedDate->lt($minimumDate)) {
            $normalizedDate = $minimumDate;
        } elseif ($requestedDate->gt($maximumDate)) {
            $normalizedDate = $maximumDate;
        }

        $calendarMonth = $normalizedDate->month;
        $calendarYear = $normalizedDate->year;
        $calendar = $this->generateCalendar($calendarMonth, $calendarYear);
        $currentMonth = Carbon::createFromDate($calendarYear, $calendarMonth, 1)->format('F Y');

        $postCalendarDays = Post::published()
            ->whereYear('published_at', $calendarYear)
            ->whereMonth('published_at', $calendarMonth)
            ->orderBy('published_at')
            ->get(['title', 'slug', 'published_at'])
            ->groupBy(fn (Post $post) => $post->published_at->format('Y-m-d'))
            ->map(function ($posts, string $date) {
                $firstPost = $posts->first();
                $count = $posts->count();

                return [
                    'count' => $count,
                    'url' => $count === 1
                        ? route('posts.show', $firstPost->slug)
                        : route('blog.index', ['date' => $date]),
                    'titles' => $posts->pluck('title')->values()->all(),
                ];
            })
            ->all();

        return compact(
            'calendar',
            'currentMonth',
            'calendarMonth',
            'calendarYear',
            'calendarStartMonth',
            'calendarStartYear',
            'calendarEndMonth',
            'calendarEndYear',
            'postCalendarDays'
        );
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('pages.blog.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'scripture' => 'nullable',
            'subtitle' => 'nullable',
            'details' => 'required',
            'action_point' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'author' => 'nullable',
            'category_ids' => 'array',
            'tag_ids' => 'array'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = FileUploadService::uploadImage(
                $request->file('image'),
                'posts',
                'public',
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'image'
            );
        }

        $post->update($validated);
        $post->categories()->sync($request->category_ids);
        $post->tags()->sync($request->tag_ids);

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('posts.index')->with('success', 'Post deleted successfully');
    }

    public function getCalendarData($year, $month)
    {
        [
            'calendar' => $calendar,
            'currentMonth' => $currentMonth,
            'calendarMonth' => $calendarMonth,
            'calendarYear' => $calendarYear,
            'calendarStartMonth' => $calendarStartMonth,
            'calendarStartYear' => $calendarStartYear,
            'calendarEndMonth' => $calendarEndMonth,
            'calendarEndYear' => $calendarEndYear,
            'postCalendarDays' => $postCalendarDays,
        ] = $this->buildCalendarPayload((int) $month, (int) $year);
            
        return response()->json([
            'calendar' => $calendar->map(fn ($date) => $date->format('Y-m-d'))->toArray(),
            'currentMonth' => $currentMonth,
            'month' => $calendarMonth,
            'year' => $calendarYear,
            'startMonth' => $calendarStartMonth,
            'startYear' => $calendarStartYear,
            'endMonth' => $calendarEndMonth,
            'endYear' => $calendarEndYear,
            'postCalendarDays' => $postCalendarDays,
        ]);
    }

    private function trackPostView(Post $post): void
    {
        $sessionKey = 'analytics.post_viewed.' . $post->getKey() . '.' . now()->toDateString();

        if (!request()->hasSession() || request()->session()->has($sessionKey)) {
            return;
        }

        $post->increment('view_count');
        request()->session()->put($sessionKey, true);
    }

}
