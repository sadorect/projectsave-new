<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Jobs\GeneratePostFeaturedImage;
use App\Services\AiImages\AiImageSettings;
use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\FacebookService;
use App\Services\FileUploadService;
use App\Services\HtmlSanitizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function __construct(private readonly AiImageSettings $aiImageSettings)
    {
        $this->middleware('can:viewAny,' . Post::class)->only('index');
        $this->middleware('can:create,' . Post::class)->only(['create', 'store']);
        $this->middleware('can:update,post')->only(['edit', 'update', 'generateFeaturedImage', 'approveFeaturedImage', 'rejectFeaturedImage']);
        $this->middleware('can:delete,post')->only('destroy');
        $this->middleware('can:bulkManage,' . Post::class)->only('bulkAction');
        $this->middleware('can:manageTaxonomy,' . Post::class)->only('createCategory');
    }

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

        if ($request->filled('image_source_filter')) {
            if ($request->image_source_filter === 'none') {
                $query->whereNull('image')->whereNull('featured_image_candidate_path');
            } elseif ($request->image_source_filter === 'ai_candidate') {
                $query->whereNotNull('featured_image_candidate_path');
            } else {
                $query->where('featured_image_source', $request->image_source_filter);
            }
        }

        if ($request->filled('ai_generation_status_filter')) {
            $query->where('featured_image_generation_status', $request->ai_generation_status_filter);
        }

        if ($request->filled('ai_approval_status_filter')) {
            $query->where('featured_image_approval_status', $request->ai_approval_status_filter);
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
        
        $imageSourceOptions = [
            'manual' => 'Manual',
            'ai' => 'AI Live',
            'ai_candidate' => 'AI Candidate Awaiting Review',
            'none' => 'No Image',
        ];

        $aiGenerationStatusOptions = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'generated' => 'Generated',
            'failed' => 'Failed',
        ];

        $aiApprovalStatusOptions = [
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];

        $aiSettings = [
            'require_approval' => $this->aiImageSettings->requireApproval(),
            'default_provider' => $this->aiImageSettings->defaultProvider(),
            'default_preset' => $this->aiImageSettings->defaultPreset(),
        ];

        $pendingAiReviewCount = Post::query()
            ->whereNotNull('featured_image_candidate_path')
            ->where('featured_image_approval_status', 'pending')
            ->count();

        return view('admin.posts.index', compact(
            'posts',
            'categories',
            'tags',
            'authors',
            'imageSourceOptions',
            'aiGenerationStatusOptions',
            'aiApprovalStatusOptions',
            'aiSettings',
            'pendingAiReviewCount'
        ));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $aiProviders = config('ai-images.providers', []);
        $aiPresets = config('ai-images.presets', []);
        $defaultAiProvider = $this->aiImageSettings->defaultProvider();
        $defaultAiPreset = $this->aiImageSettings->defaultPreset();

        return view('admin.posts.create', compact('categories', 'tags', 'aiProviders', 'aiPresets', 'defaultAiProvider', 'defaultAiPreset'));
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
                'featured_image_generation_enabled' => 'nullable|boolean',
                'featured_image_provider' => ['nullable', 'string', Rule::in(array_keys(config('ai-images.providers', [])))],
                'featured_image_preset' => ['nullable', 'string', Rule::in(array_keys(config('ai-images.presets', [])))],
                'featured_image_prompt' => 'nullable|string|max:4000',
                'featured_image_options' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $validated['slug'] = Str::slug($validated['title']);
            $validated['user_id'] = auth()->id();
            $validated['published_at'] = Carbon::parse($request->published_at);
            $validated['details'] = HtmlSanitizer::clean($validated['details']);
            if (isset($validated['bible_text'])) {
                $validated['bible_text'] = HtmlSanitizer::clean($validated['bible_text']);
            }
            if (isset($validated['action_point'])) {
                $validated['action_point'] = HtmlSanitizer::clean($validated['action_point']);
            }

            if ($request->hasFile('image')) {
                $validated['image'] = FileUploadService::uploadImage(
                    $request->file('image'),
                    'posts',
                    'public',
                    ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                    'image'
                );

                $validated['featured_image_source'] = 'manual';
                $validated['featured_image_generation_status'] = null;
                $validated['featured_image_approval_status'] = null;
                $validated['featured_image_generation_error'] = null;
                $validated['featured_image_candidate_path'] = null;
                $validated['featured_image_reviewed_by'] = null;
                $validated['featured_image_reviewed_at'] = null;
                $validated['featured_image_review_notes'] = null;
            }

            $this->syncFeaturedImageGenerationSettings($request, $validated, $request->hasFile('image'));

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

            $this->dispatchFeaturedImageJobIfNeeded($post, $request->boolean('featured_image_generation_enabled'));

            DB::commit();
            return redirect()->route('admin.posts.index')->with('success', 'Post created successfully');

        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
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
        $aiProviders = config('ai-images.providers', []);
        $aiPresets = config('ai-images.presets', []);
        $defaultAiProvider = $this->aiImageSettings->defaultProvider();
        $defaultAiPreset = $this->aiImageSettings->defaultPreset();

        return view('admin.posts.edit', compact('post', 'categories', 'tags', 'aiProviders', 'aiPresets', 'defaultAiProvider', 'defaultAiPreset'));
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'author' => 'nullable',
            'category_ids' => 'array',
            'tag_ids' => 'array',
            'published_at' => 'required|date_format:Y-m-d\TH:i',
            'featured_image_generation_enabled' => 'nullable|boolean',
            'featured_image_provider' => ['nullable', 'string', Rule::in(array_keys(config('ai-images.providers', [])))],
            'featured_image_preset' => ['nullable', 'string', Rule::in(array_keys(config('ai-images.presets', [])))],
            'featured_image_prompt' => 'nullable|string|max:4000',
            'featured_image_options' => 'nullable|string',
        ]);

        $validated['published_at'] = Carbon::parse($request->published_at);

        if ($request->hasFile('image')) {
            $this->deleteAiImageIfOwned($post->image, (bool) $post->image);
            $this->deleteAiImageIfOwned($post->featured_image_candidate_path, (bool) $post->featured_image_candidate_path);

            $validated['image'] = FileUploadService::uploadImage(
                $request->file('image'),
                'posts',
                'public',
                ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'image'
            );

            $validated['featured_image_source'] = 'manual';
            $validated['featured_image_generation_status'] = null;
            $validated['featured_image_approval_status'] = null;
            $validated['featured_image_generation_error'] = null;
            $validated['featured_image_candidate_path'] = null;
            $validated['featured_image_reviewed_by'] = null;
            $validated['featured_image_reviewed_at'] = null;
            $validated['featured_image_review_notes'] = null;
        }

        $this->syncFeaturedImageGenerationSettings($request, $validated, $request->hasFile('image'));

        $validated['details'] = HtmlSanitizer::clean($validated['details']);
        if (isset($validated['bible_text'])) {
            $validated['bible_text'] = HtmlSanitizer::clean($validated['bible_text']);
        }
        if (isset($validated['action_point'])) {
            $validated['action_point'] = HtmlSanitizer::clean($validated['action_point']);
        }

        $post->update($validated);
        $post->categories()->sync($request->category_ids);
        $post->tags()->sync($request->tag_ids);

        $this->dispatchFeaturedImageJobIfNeeded($post->fresh(), $request->boolean('featured_image_generation_enabled'));

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully');
    }

    public function generateFeaturedImage(Post $post)
    {
        abort_unless(config('ai-images.enabled'), 403);

        $post->forceFill([
            'featured_image_generation_enabled' => true,
            'featured_image_generation_status' => 'pending',
            'featured_image_generation_error' => null,
            'featured_image_provider' => $post->featured_image_provider ?: $this->aiImageSettings->defaultProvider(),
            'featured_image_preset' => $post->featured_image_preset ?: $this->aiImageSettings->defaultPreset(),
        ])->save();

        GeneratePostFeaturedImage::dispatch($post->getKey(), true);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Featured image generation has been queued.');
    }

    public function approveFeaturedImage(Post $post)
    {
        abort_unless(config('ai-images.enabled'), 403);

        if (!$post->featured_image_candidate_path) {
            return redirect()
                ->route('admin.posts.edit', $post)
                ->with('error', 'There is no generated candidate image to approve.');
        }

        if ($post->image && $post->image !== $post->featured_image_candidate_path) {
            $this->deleteAiImageIfOwned($post->image, true);
        }

        $post->forceFill([
            'image' => $post->featured_image_candidate_path,
            'featured_image_candidate_path' => null,
            'featured_image_source' => 'ai',
            'featured_image_generation_status' => 'generated',
            'featured_image_approval_status' => 'approved',
            'featured_image_reviewed_by' => auth()->id(),
            'featured_image_reviewed_at' => now(),
            'featured_image_review_notes' => null,
        ])->save();

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Generated featured image approved and applied to the post.');
    }

    public function rejectFeaturedImage(Post $post)
    {
        abort_unless(config('ai-images.enabled'), 403);

        if (!$post->featured_image_candidate_path) {
            return redirect()
                ->route('admin.posts.edit', $post)
                ->with('error', 'There is no generated candidate image to reject.');
        }

        $this->deleteAiImageIfOwned($post->featured_image_candidate_path, true);

        $post->forceFill([
            'featured_image_candidate_path' => null,
            'featured_image_generation_status' => 'generated',
            'featured_image_approval_status' => 'rejected',
            'featured_image_reviewed_by' => auth()->id(),
            'featured_image_reviewed_at' => now(),
            'featured_image_review_notes' => null,
        ])->save();

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Generated candidate image rejected.');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully');
    }

    /**
     * @param array<string, mixed> $validated
     */
    protected function syncFeaturedImageGenerationSettings(Request $request, array &$validated, bool $manualImageUploaded): void
    {
        $validated['featured_image_generation_enabled'] = $request->boolean('featured_image_generation_enabled');
        $validated['featured_image_provider'] = $request->input('featured_image_provider') ?: $this->aiImageSettings->defaultProvider();
        $validated['featured_image_preset'] = $request->input('featured_image_preset') ?: $this->aiImageSettings->defaultPreset();
        $validated['featured_image_prompt'] = $request->filled('featured_image_prompt')
            ? trim((string) $request->input('featured_image_prompt'))
            : null;
        $validated['featured_image_options'] = $this->decodeFeaturedImageOptions($request->input('featured_image_options'));

        if ($manualImageUploaded) {
            return;
        }

        if ($validated['featured_image_generation_enabled']) {
            $validated['featured_image_generation_status'] = 'pending';
            $validated['featured_image_generation_error'] = null;
        } elseif (empty($validated['image'])) {
            $validated['featured_image_generation_status'] = null;
            $validated['featured_image_approval_status'] = null;
            $validated['featured_image_generation_error'] = null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function decodeFeaturedImageOptions(?string $raw): ?array
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }

        $decoded = json_decode($raw, true);

        if (!is_array($decoded)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'featured_image_options' => 'Featured image options must be valid JSON.',
            ]);
        }

        return $decoded;
    }

    protected function dispatchFeaturedImageJobIfNeeded(Post $post, bool $generationEnabled): void
    {
        if (!config('ai-images.enabled') || !$generationEnabled) {
            return;
        }

        if ($post->image && $post->featured_image_source === 'manual') {
            return;
        }

        if ($post->published_at?->lte(now())) {
            GeneratePostFeaturedImage::dispatch($post->getKey());
        }
    }

    protected function deleteAiImageIfOwned(?string $path, bool $shouldDelete): void
    {
        if (!$path || !$shouldDelete) {
            return;
        }

        FileUploadService::deleteFile($path, config('ai-images.storage_disk'));
    }
}
