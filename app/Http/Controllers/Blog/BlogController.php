<?php

namespace App\Http\Controllers\Blog;

use console;
use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
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
    public function index()
    {
        $posts = Post::with(['categories', 'tags'])
            ->orderBy('published_at', 'desc')
            ->paginate(6);
        return view('pages.blog.index', compact('posts'));
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
            $validated['image'] = $request->file('image')->store('posts', 'public');
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

        $recentPosts = Post::latest()
            ->where('id', '!=', $post->id)
            ->take(5)
            ->get();
            
        $categories = Category::withCount('posts')->get();
        
        $relatedPosts = Post::whereHas('categories', function($query) use ($post) {
            $query->whereIn('categories.id', $post->categories->pluck('id'));
        })
        ->where('id', '!=', $post->id)
        ->latest()
        ->take(3)
        ->get();

          // Get dates that have posts
          $calendar = $this->generateCalendar();
          $currentMonth = Carbon::now()->format('F Y');

          $postDates = Post::whereRaw('MONTH(created_at) = ?', [11])
          ->whereRaw('YEAR(created_at) = ?', [now()->year])
          ->get(['created_at'])
          ->map(fn($post) => $post->created_at->format('Y-m-d'))
          ->toArray();
      
          $previous = Post::where('status', 'published')
                   ->where('published_at', '<', $post->published_at)
                   ->orderBy('published_at', 'desc')
                   ->first();
                   
    $next = Post::where('status', 'published')
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
            'postDates',
            'previous',
            'next'
        ));
    }

    public function showFaqs(Post $post)
    {

        $recentPosts = Post::latest()
            ->where('id', '!=', $post->id)
            ->take(5)
            ->get();
            
        $categories = Category::withCount('posts')->get();
        
        $relatedPosts = Post::whereHas('categories', function($query) use ($post) {
            $query->whereIn('categories.id', $post->categories->pluck('id'));
        })
        ->where('id', '!=', $post->id)
        ->latest()
        ->take(3)
        ->get();

          // Get dates that have posts
          $calendar = $this->generateCalendar();
          $currentMonth = Carbon::now()->format('F Y');

          $postDates = Post::whereRaw('MONTH(created_at) = ?', [11])
          ->whereRaw('YEAR(created_at) = ?', [now()->year])
          ->get(['created_at'])
          ->map(fn($post) => $post->created_at->format('Y-m-d'))
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
    $month = 11;//$month ?? Carbon::now()->month;
    $year = 2024;//$year ?? Carbon::now()->year;
    
    $date = Carbon::createFromDate($year, $month, 1);
    Log::info("Initial date:", ['date' => $date->format('Y-m-d')]);
    $calendar = collect();
    
    // Add empty days for the start of the month
    for ($i = 0; $i < $date->dayOfWeek; $i++) {
        
         $calendar->push(Carbon::createFromDate($year, $month, 1)->subDays($date->dayOfWeek - $i));
    
        $calendar->push(Carbon::createFromDate($year, $month, 1)->subDays($date->dayOfWeek - $i));
    }
    
    // Add all days of the month
    while ($date->month === (int) $month) {
        $calendar->push(clone $date);
        $date->addDay();
        Log::info("Adding current month date:", ['date' => $calendar->last()->format('Y-m-d')]);
    }
    
    // Add remaining days to complete the grid
    $remainingDays = 42 - $calendar->count(); // 6 rows * 7 days
    for ($i = 0; $i < $remainingDays; $i++) {
        $calendar->push(clone $date);
        $date->addDay();
    }
    
    return $calendar;
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
            $validated['image'] = $request->file('image')->store('posts', 'public');
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




}