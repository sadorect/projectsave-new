<?php

namespace App\Services\AiImages\Providers;

use App\Contracts\AiImageProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class StabilityImageProvider implements AiImageProvider
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
        $baseUrl = rtrim((string) Arr::get($this->providerConfig, 'base_url', 'https://api.stability.ai'), '/');
        $timeout = (int) Arr::get($this->providerConfig, 'timeout', 120);
        $endpoint = (string) Arr::get($this->providerConfig, 'endpoint', '/v2beta/stable-image/generate/core');

        if (!$apiKey) {
            throw new RuntimeException('The selected Stability AI provider is missing its API configuration.');
        }

        $parts = [];

        foreach (array_filter([
            'prompt' => $payload['prompt'] ?? null,
            'aspect_ratio' => $payload['options']['aspect_ratio'] ?? Arr::get($this->providerConfig, 'options.aspect_ratio', '3:2'),
            'output_format' => $payload['options']['output_format'] ?? Arr::get($this->providerConfig, 'options.output_format', 'png'),
            'style_preset' => $payload['options']['style_preset'] ?? Arr::get($this->providerConfig, 'options.style_preset'),
            'negative_prompt' => $payload['options']['negative_prompt'] ?? Arr::get($this->providerConfig, 'options.negative_prompt'),
        ], static fn ($value) => $value !== null && $value !== '') as $name => $value) {
            $parts[] = [
                'name' => $name,
                'contents' => (string) $value,
            ];
        }

        $response = Http::timeout($timeout)
            ->withToken($apiKey)
            ->accept('image/*')
            ->asMultipart()
            ->post($baseUrl . $endpoint, $parts);

        if ($response->failed()) {
            throw new RuntimeException($response->body() ?: 'Stability AI image generation failed.');
        }

        return [
            'content' => $response->body(),
            'mime_type' => (string) $response->header('Content-Type', 'image/png'),
            'revised_prompt' => null,
            'provider_image_id' => null,
            'raw' => [],
        ];
    }

    public function testConnection(): array
    {
        $apiKey = Arr::get($this->providerConfig, 'api_key');
        $baseUrl = rtrim((string) Arr::get($this->providerConfig, 'base_url', 'https://api.stability.ai'), '/');

        if (!$apiKey) {
            return [
                'ok' => false,
                'message' => 'Missing API key.',
                'details' => [],
            ];
        }

        Http::timeout((int) Arr::get($this->providerConfig, 'timeout', 30))
            ->withToken($apiKey)
            ->get($baseUrl . '/v1/user/account')
            ->throw();

        return [
            'ok' => true,
            'message' => 'Connection probe succeeded against the account endpoint.',
            'details' => [],
        ];
    }
}