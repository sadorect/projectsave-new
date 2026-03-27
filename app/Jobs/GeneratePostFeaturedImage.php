<?php

namespace App\Jobs;

use App\Models\Post;
use App\Services\AiImages\AiImageProviderManager;
use App\Services\AiImages\AiImageSettings;
use App\Services\AiImages\GeneratedImageStorage;
use App\Services\AiImages\PostFeaturedImagePromptBuilder;
use App\Services\FileUploadService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePostFeaturedImage implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $postId,
        public readonly bool $force = false
    ) {
        $this->queue = config('ai-images.queue', 'default');
        $this->afterCommit = true;
    }

    public function uniqueId(): string
    {
        return 'post-featured-image:' . $this->postId;
    }

    public function handle(
        AiImageProviderManager $providerManager,
        AiImageSettings $settings,
        PostFeaturedImagePromptBuilder $promptBuilder,
        GeneratedImageStorage $storage
    ): void {
        $post = Post::with('categories')->find($this->postId);

        if (!$post) {
            return;
        }

        if (!$post->featured_image_generation_enabled) {
            return;
        }

        if (!$this->force && !$post->published_at?->lte(now())) {
            return;
        }

        if (!$this->force && $post->image && $post->featured_image_source === 'manual') {
            return;
        }

        $post->forceFill([
            'featured_image_generation_status' => 'processing',
            'featured_image_generation_error' => null,
        ])->save();

        $providerName = $post->featured_image_provider ?: config('ai-images.default_provider');
        $payload = $promptBuilder->build($post);
        $provider = $providerManager->provider($providerName);

        try {
            $generated = $provider->generate([
                'prompt' => $payload['prompt'],
                'options' => $payload['options'],
                'post_id' => $post->getKey(),
            ]);

            $stored = $storage->store(
                $generated['content'],
                $generated['mime_type'],
                'post-' . $post->getKey()
            );

            $requiresApproval = $settings->requireApproval();

            if ($post->featured_image_candidate_path) {
                FileUploadService::deleteFile($post->featured_image_candidate_path, config('ai-images.storage_disk'));
            }

            if ($requiresApproval) {
                $post->forceFill([
                    'featured_image_candidate_path' => $stored['path'],
                    'featured_image_generation_status' => 'generated',
                    'featured_image_approval_status' => 'pending',
                    'featured_image_generation_error' => null,
                    'featured_image_generated_at' => now(),
                    'featured_image_provider' => $providerName,
                    'featured_image_prompt' => $generated['revised_prompt'] ?: $post->featured_image_prompt,
                    'featured_image_reviewed_by' => null,
                    'featured_image_reviewed_at' => null,
                    'featured_image_review_notes' => null,
                ])->save();

                return;
            }

            if ($post->image && $post->featured_image_source === 'ai') {
                FileUploadService::deleteFile($post->image, config('ai-images.storage_disk'));
            }

            $post->forceFill([
                'image' => $stored['path'],
                'featured_image_candidate_path' => null,
                'featured_image_source' => 'ai',
                'featured_image_generation_status' => 'generated',
                'featured_image_approval_status' => 'approved',
                'featured_image_generation_error' => null,
                'featured_image_generated_at' => now(),
                'featured_image_provider' => $providerName,
                'featured_image_prompt' => $generated['revised_prompt'] ?: $post->featured_image_prompt,
                'featured_image_reviewed_by' => null,
                'featured_image_reviewed_at' => now(),
                'featured_image_review_notes' => null,
            ])->save();
        } catch (\Throwable $exception) {
            Log::warning('Failed to generate featured image for post', [
                'post_id' => $post->getKey(),
                'provider' => $providerName,
                'error' => $exception->getMessage(),
            ]);

            $post->forceFill([
                'featured_image_generation_status' => 'failed',
                'featured_image_approval_status' => null,
                'featured_image_generation_error' => $exception->getMessage(),
            ])->save();

            throw $exception;
        }
    }
}