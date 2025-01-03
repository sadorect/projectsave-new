<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\FacebookService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['categories', 'tags'])
            ->latest()
            ->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }
    
    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|max:255',
        'scripture' => 'nullable',
        'bible_text' => 'nullable',
        'subtitle' => 'nullable',
        'details' => 'required',
        'action_point' => 'nullable',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'author' => 'nullable',
        'category_ids' => 'array',
        'tag_ids' => 'array',
        'published_at' => 'required|date_format:Y-m-d\TH:i',
    ]);

    $validated['slug'] = Str::slug($validated['title']);
    $validated['user_id'] = auth()->id();
    $validated['published_at'] = Carbon::parse($request->published_at);

    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('posts', 'public');
    }

    $post = Post::create($validated);

    // Get category IDs, use Uncategorized if none selected
    $categoryIds = $request->category_ids ?? [Category::firstOrCreate(
        ['slug' => 'uncategorized'],
        ['name' => 'Uncategorized']
    )->id];
    
    $post->categories()->sync($categoryIds);
    $post->tags()->sync($request->tag_ids);

    if ($request->has('share_to_facebook')) {
        $facebook = new FacebookService();
        $facebook->sharePost(
            $post->title,
            $post->excerpt,
            route('blog.show', $post->slug),
            $post->featured_image
        );
    }

    return redirect()->route('admin.posts.index')->with('success', 'Post created successfully');
}

    

    public function edit(Post $post)
    {
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'scripture' => 'nullable',
            'bible_text' => 'nullable',
            'subtitle' => 'nullable',
            'details' => 'required',
            'action_point' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'author' => 'nullable',
            'category_ids' => 'array',
            'tag_ids' => 'array',
            'published_at' => 'required|date_format:Y-m-d\TH:i',


        ]);

        //$validated['published_at'] = Carbon::parse($request->published_at);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('posts', 'public');
        }

        $post->update($validated);
        $post->categories()->sync($request->category_ids);
        $post->tags()->sync($request->tag_ids);

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully');
    }
}
