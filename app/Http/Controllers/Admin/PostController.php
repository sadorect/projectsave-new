<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\FacebookService;
use App\Services\FileUploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['categories', 'tags']);
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('subtitle', 'LIKE', "%{$search}%")
                  ->orWhere('details', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%")
                  ->orWhere('scripture', 'LIKE', "%{$search}%")
                  ->orWhere('action_point', 'LIKE', "%{$search}%");
            });
        }
        
        // Filter by category
        if ($request->filled('category_filter')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('id', $request->category_filter);
            });
        }
        
        // Filter by author
        if ($request->filled('author_filter')) {
            $query->where('author', 'LIKE', "%{$request->author_filter}%");
        }
        
        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('published_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('published_at', '<=', $request->date_to);
        }
        
        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->oldest('published_at');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'author':
                $query->orderBy('author', 'asc');
                break;
            default:
                $query->latest('published_at');
        }
        
        $posts = $query->paginate(10)->appends($request->query());
        
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        
        // Get unique authors for filter dropdown
        $authors = Post::whereNotNull('author')
            ->distinct()
            ->pluck('author')
            ->filter()
            ->sort()
            ->values();
        
        return view('admin.posts.index', compact('posts', 'categories', 'tags', 'authors'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Create category on the fly
     */
    public function createCategory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:categories,name',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);

            return response()->json([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create category. Please try again.'
            ], 500);
        }
    }

    /**
     * Bulk actions for posts
     */
    public function bulkAction(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:delete,change_category,add_category,remove_category',
                'post_ids' => 'required|array|min:1',
                'post_ids.*' => 'exists:posts,id',
                'category_id' => 'nullable|exists:categories,id'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->with('error', 'Invalid bulk action parameters.');
            }

            $postIds = $request->post_ids;
            $action = $request->action;
            $categoryId = $request->category_id;

            DB::beginTransaction();

            switch ($action) {
                case 'delete':
                    Post::whereIn('id', $postIds)->delete();
                    $message = count($postIds) . ' posts deleted successfully.';
                    break;

                case 'change_category':
                    if (!$categoryId) {
                        return redirect()->back()->with('error', 'Category is required for this action.');
                    }
                    
                    // Remove all existing categories and add the new one
                    foreach ($postIds as $postId) {
                        $post = Post::find($postId);
                        if ($post) {
                            $post->categories()->sync([$categoryId]);
                        }
                    }
                    $categoryName = Category::find($categoryId)->name ?? 'Unknown';
                    $message = count($postIds) . " posts moved to category '{$categoryName}'.";
                    break;

                case 'add_category':
                    if (!$categoryId) {
                        return redirect()->back()->with('error', 'Category is required for this action.');
                    }
                    
                    // Add category to posts (without removing existing ones)
                    foreach ($postIds as $postId) {
                        $post = Post::find($postId);
                        if ($post && !$post->categories->contains($categoryId)) {
                            $post->categories()->attach($categoryId);
                        }
                    }
                    $categoryName = Category::find($categoryId)->name ?? 'Unknown';
                    $message = "Category '{$categoryName}' added to " . count($postIds) . ' posts.';
                    break;

                case 'remove_category':
                    if (!$categoryId) {
                        return redirect()->back()->with('error', 'Category is required for this action.');
                    }
                    
                    // Remove specific category from posts
                    foreach ($postIds as $postId) {
                        $post = Post::find($postId);
                        if ($post) {
                            $post->categories()->detach($categoryId);
                        }
                    }
                    $categoryName = Category::find($categoryId)->name ?? 'Unknown';
                    $message = "Category '{$categoryName}' removed from " . count($postIds) . ' posts.';
                    break;

                default:
                    throw new \Exception('Invalid action');
            }

            DB::commit();
            return redirect()->route('admin.posts.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk action error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Bulk action failed. Please try again.');
        }
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|max:255',
                'scripture' => 'nullable|string|max:500',
                'bible_text' => 'nullable|string',
                'subtitle' => 'nullable|string|max:500',
                'details' => 'required|string',
                'action_point' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'author' => 'nullable|string|max:255',
                'category_ids' => 'nullable|array',
                'category_ids.*' => 'exists:categories,id',
                'tag_ids' => 'nullable|array',
                'tag_ids.*' => 'exists:tags,id',
                'published_at' => 'required|date_format:Y-m-d\TH:i',
            ]);

            DB::beginTransaction();

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
            
            if ($request->tag_ids) {
                $post->tags()->sync($request->tag_ids);
            }

            if ($request->has('share_to_facebook')) {
                try {
                    $facebook = new FacebookService();
                    $facebook->sharePost(
                        $post->title,
                        $post->excerpt ?? substr(strip_tags($post->details), 0, 100),
                        route('blog.show', $post->slug),
                        $post->featured_image ?? null
                    );
                } catch (\Exception $e) {
                    Log::warning('Facebook sharing failed: ' . $e->getMessage());
                    // Don't fail the entire operation for Facebook sharing
                }
            }

            DB::commit();
            return redirect()->route('admin.posts.index')->with('success', 'Post created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating post: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create post. Please try again.']);
        }
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
