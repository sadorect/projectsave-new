<?php

namespace App\Services\AiImages\Providers;

use App\Contracts\AiImageProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FalImageProvider implements AiImageProvider
{
    /**
     * @param array<string, mixed> $providerConfig
     */
    public function __construct(private readonly array $providerConfig)
    {
    }

    public function generate(array $payload): array
    {
        $apiKey = Arr::get($this->providerConfig, 'api_key');
        $baseUrl = rtrim((string) Arr::get($this->providerConfig, 'base_url', 'https://queue.fal.run'), '/');
        $model = trim((string) Arr::get($this->providerConfig, 'model', ''), '/');
        $timeout = (int) Arr::get($this->providerConfig, 'timeout', 120);

        if (!$apiKey || !$model) {
            throw new RuntimeException('The selected FAL provider is missing its API configuration.');
        }

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => 'Key ' . $apiKey,
            ])
            ->post($baseUrl . '/' . $model, array_filter([
                'prompt' => $payload['prompt'] ?? null,
                'image_size' => $payload['options']['image_size'] ?? Arr::get($this->providerConfig, 'options.image_size'),
                'num_inference_steps' => $payload['options']['num_inference_steps'] ?? Arr::get($this->providerConfig, 'options.num_inference_steps'),
                'guidance_scale' => $payload['options']['guidance_scale'] ?? Arr::get($this->providerConfig, 'options.guidance_scale'),
                'sync_mode' => true,
            ], static fn ($value) => $value !== null && $value !== ''))
            ->throw()
            ->json();

        $result = $this->waitForCompletion($response, $apiKey, $timeout);
        $imageUrl = $this->extractImageUrl($result);

        if (!$imageUrl) {
            throw new RuntimeException('FAL did not return an image URL.');
        }

        $download = Http::timeout($timeout)->get($imageUrl)->throw();

        return [
            'content' => $download->body(),
            'mime_type' => (string) $download->header('Content-Type', 'image/png'),
            'revised_prompt' => null,
            'provider_image_id' => $result['request_id'] ?? null,
            'raw' => is_array($result) ? $result : [],
        ];
    }

    /**
     * @param array<string, mixed> $result
     * @return array<string, mixed>
     */
    protected function waitForCompletion(array $result, string $apiKey, int $timeout): array
    {
        if ($this->extractImageUrl($result)) {
            return $result;
        }

        $statusUrl = $result['status_url'] ?? null;

        if (!$statusUrl) {
            return $result;
        }

        $startedAt = microtime(true);

        while ((microtime(true) - $startedAt) < $timeout) {
            usleep(750000);

            $result = Http::timeout($timeout)
                ->withHeaders([
                    'Authorization' => 'Key ' . $apiKey,
                ])
                ->get($statusUrl)
                ->throw()
                ->json();

            if ($this->extractImageUrl($result)) {
                return $result;
            }

            $status = $result['status'] ?? null;

            if (in_array($status, ['ERROR', 'FAILED'], true)) {
                throw new RuntimeException((string) ($result['error'] ?? 'FAL image generation failed.'));
            }
        }

        throw new RuntimeException('FAL image generation timed out.');
    }

    /**
     * @param array<string, mixed> $result
     */
    protected function extractImageUrl(array $result): ?string
    {
        $images = $result['images'] ?? Arr::get($result, 'data.images');

        if (is_array($images)) {
            $first = Arr::first($images);

            if (is_array($first) && !empty($first['url'])) {
                return (string) $first['url'];
            }
        }

        return null;
    }

    public function testConnection(): array
    {
        $apiKey = Arr::get($this->providerConfig, 'api_key');

        if (!$apiKey) {
            return [
                'ok' => false,
                'message' => 'Missing API key.',
                'details' => [],
            ];
        }

        return [
            'ok' => true,
            'message' => 'Stored API key is present. No lightweight remote probe is implemented for FAL in this build.',
            'details' => [],
        ];
    }
}