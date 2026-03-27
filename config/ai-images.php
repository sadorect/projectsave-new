<?php

return [
    'enabled' => env('AI_FEATURED_IMAGES_ENABLED', false),
    'require_approval' => env('AI_FEATURED_IMAGES_REQUIRE_APPROVAL', true),

    'default_provider' => env('AI_FEATURED_IMAGES_PROVIDER', 'openai'),
    'default_preset' => env('AI_FEATURED_IMAGES_PRESET', 'devotional-warm'),

    'queue' => env('AI_FEATURED_IMAGES_QUEUE', 'default'),
    'storage_disk' => env('AI_FEATURED_IMAGES_DISK', env('FILESYSTEM_DISK', 'public')),
    'storage_path' => env('AI_FEATURED_IMAGES_PATH', 'posts/generated'),
    'target_format' => env('AI_FEATURED_IMAGES_FORMAT', 'webp'),
    'target_quality' => (int) env('AI_FEATURED_IMAGES_QUALITY', 85),
    'max_width' => (int) env('AI_FEATURED_IMAGES_MAX_WIDTH', 1536),
    'max_height' => (int) env('AI_FEATURED_IMAGES_MAX_HEIGHT', 1024),

    'prompt' => [
        'max_excerpt_length' => (int) env('AI_FEATURED_IMAGES_EXCERPT_LENGTH', 220),
        'base_style' => env(
            'AI_FEATURED_IMAGES_BASE_STYLE',
            'Create a high-quality featured image for a Christian devotional blog post. Keep the tone hopeful, reverent, warm, and editorial. Do not include readable text, logos, watermarks, UI chrome, or typographic overlays.'
        ),
    ],

    'providers' => [
        'together' => [
            'label' => 'Together AI',
            'driver' => 'openai-compatible',
            'base_url' => env('TOGETHER_BASE_URL', 'https://api.together.xyz/v1'),
            'api_key' => env('TOGETHER_API_KEY'),
            'model' => env('TOGETHER_IMAGE_MODEL', 'black-forest-labs/FLUX.1-schnell-Free'),
            'timeout' => (int) env('TOGETHER_IMAGE_TIMEOUT', 120),
            'tier' => 'budget',
            'options' => [
                'size' => env('TOGETHER_IMAGE_SIZE', '1536x1024'),
                'quality' => env('TOGETHER_IMAGE_QUALITY', 'standard'),
            ],
        ],
        'replicate' => [
            'label' => 'Replicate',
            'driver' => 'replicate',
            'base_url' => env('REPLICATE_BASE_URL', 'https://api.replicate.com/v1'),
            'api_key' => env('REPLICATE_API_TOKEN'),
            'model' => env('REPLICATE_IMAGE_MODEL', 'black-forest-labs/flux-schnell'),
            'timeout' => (int) env('REPLICATE_IMAGE_TIMEOUT', 120),
            'tier' => 'budget-flex',
            'options' => [
                'aspect_ratio' => env('REPLICATE_IMAGE_ASPECT_RATIO', '3:2'),
                'output_format' => env('REPLICATE_IMAGE_OUTPUT_FORMAT', 'png'),
                'output_quality' => (int) env('REPLICATE_IMAGE_OUTPUT_QUALITY', 85),
            ],
        ],
        'stability' => [
            'label' => 'Stability AI',
            'driver' => 'stability',
            'base_url' => env('STABILITY_BASE_URL', 'https://api.stability.ai'),
            'api_key' => env('STABILITY_API_KEY'),
            'endpoint' => env('STABILITY_IMAGE_ENDPOINT', '/v2beta/stable-image/generate/core'),
            'timeout' => (int) env('STABILITY_IMAGE_TIMEOUT', 120),
            'tier' => 'balanced',
            'options' => [
                'aspect_ratio' => env('STABILITY_IMAGE_ASPECT_RATIO', '3:2'),
                'output_format' => env('STABILITY_IMAGE_OUTPUT_FORMAT', 'png'),
                'style_preset' => env('STABILITY_IMAGE_STYLE_PRESET'),
            ],
        ],
        'fal' => [
            'label' => 'FAL',
            'driver' => 'fal',
            'base_url' => env('FAL_BASE_URL', 'https://queue.fal.run'),
            'api_key' => env('FAL_KEY'),
            'model' => env('FAL_IMAGE_MODEL', 'fal-ai/flux-pro/v1.1'),
            'timeout' => (int) env('FAL_IMAGE_TIMEOUT', 120),
            'tier' => 'premium',
            'options' => [
                'image_size' => env('FAL_IMAGE_SIZE', 'landscape_4_3'),
                'num_inference_steps' => (int) env('FAL_IMAGE_STEPS', 28),
                'guidance_scale' => (float) env('FAL_IMAGE_GUIDANCE', 3.5),
            ],
        ],
        'openai' => [
            'label' => 'OpenAI',
            'driver' => 'openai-compatible',
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_IMAGE_MODEL', 'gpt-image-1'),
            'timeout' => (int) env('OPENAI_IMAGE_TIMEOUT', 120),
            'tier' => 'best-quality',
            'options' => [
                'size' => env('OPENAI_IMAGE_SIZE', '1536x1024'),
                'quality' => env('OPENAI_IMAGE_QUALITY', 'medium'),
            ],
        ],
    ],

    'presets' => [
        'devotional-warm' => [
            'label' => 'Devotional Warm',
            'style_prompt' => 'Use soft morning light, a calm contemplative mood, natural environments, and a faithful editorial feel suitable for devotionals.',
            'options' => [
                'size' => '1536x1024',
                'quality' => 'medium',
            ],
        ],
        'scripture-cinematic' => [
            'label' => 'Scripture Cinematic',
            'style_prompt' => 'Use cinematic composition, dramatic but peaceful lighting, and symbolic Christian imagery without becoming literal or kitsch.',
            'options' => [
                'size' => '1536x1024',
                'quality' => 'high',
            ],
        ],
        'minimal-illustration' => [
            'label' => 'Minimal Illustration',
            'style_prompt' => 'Use a clean illustrated composition, restrained palette, soft texture, and strong focal simplicity for card-friendly layouts.',
            'options' => [
                'size' => '1536x1024',
                'quality' => 'medium',
            ],
        ],
    ],
];