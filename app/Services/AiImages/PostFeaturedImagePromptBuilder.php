<?php

namespace App\Services\AiImages;

use App\Models\Post;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PostFeaturedImagePromptBuilder
{
    /**
     * @return array{prompt:string, preset:string, options:array<string, mixed>}
     */
    public function build(Post $post): array
    {
        $preset = $post->featured_image_preset ?: config('ai-images.default_preset');
        $presetConfig = config("ai-images.presets.{$preset}", []);
        $baseStyle = (string) config('ai-images.prompt.base_style');
        $maxExcerptLength = (int) config('ai-images.prompt.max_excerpt_length', 220);

        $excerpt = Str::of(strip_tags((string) $post->details))
            ->squish()
            ->limit($maxExcerptLength, '...')
            ->toString();

        $categories = $post->relationLoaded('categories')
            ? $post->categories->pluck('name')->filter()->implode(', ')
            : $post->categories()->pluck('name')->filter()->implode(', ');

        $sections = array_filter([
            $baseStyle,
            Arr::get($presetConfig, 'style_prompt'),
            $post->featured_image_prompt,
            'Post title: ' . $post->title,
            $post->subtitle ? 'Subtitle: ' . $post->subtitle : null,
            $post->scripture ? 'Scripture focus: ' . $post->scripture : null,
            $excerpt ? 'Content excerpt: ' . $excerpt : null,
            $categories ? 'Categories: ' . $categories : null,
        ]);

        return [
            'prompt' => implode("\n", $sections),
            'preset' => (string) $preset,
            'options' => array_merge(
                Arr::get($presetConfig, 'options', []),
                is_array($post->featured_image_options) ? $post->featured_image_options : []
            ),
        ];
    }
}