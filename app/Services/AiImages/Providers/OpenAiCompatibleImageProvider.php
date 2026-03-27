<?php

namespace App\Services\AiImages\Providers;

use App\Contracts\AiImageProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenAiCompatibleImageProvider implements AiImageProvider
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
        $baseUrl = $this->apiBaseUrl();

        if (!$apiKey || !$baseUrl) {
            throw new RuntimeException('The selected AI image provider is missing its API configuration.');
        }

        $requestPayload = array_filter([
            'model' => Arr::get($this->providerConfig, 'model'),
            'prompt' => $payload['prompt'] ?? null,
            'size' => $payload['options']['size'] ?? Arr::get($this->providerConfig, 'options.size'),
            'quality' => $payload['options']['quality'] ?? Arr::get($this->providerConfig, 'options.quality'),
            'background' => $payload['options']['background'] ?? null,
            'n' => 1,
        ], static fn ($value) => $value !== null && $value !== '');

        $response = Http::timeout((int) Arr::get($this->providerConfig, 'timeout', 120))
            ->withToken($apiKey)
            ->post($baseUrl . '/images/generations', $requestPayload)
            ->throw()
            ->json();

        $image = Arr::first($response['data'] ?? []);

        if (!$image) {
            throw new RuntimeException('The AI provider returned no image data.');
        }

        if (!empty($image['b64_json'])) {
            $content = base64_decode((string) $image['b64_json'], true);

            if ($content === false) {
                throw new RuntimeException('The AI provider returned invalid base64 image data.');
            }

            return [
                'content' => $content,
                'mime_type' => 'image/png',
                'revised_prompt' => $image['revised_prompt'] ?? null,
                'provider_image_id' => $image['id'] ?? null,
                'raw' => is_array($response) ? $response : [],
            ];
        }

        if (!empty($image['url'])) {
            $download = Http::timeout((int) Arr::get($this->providerConfig, 'timeout', 120))
                ->get((string) $image['url'])
                ->throw();

            return [
                'content' => $download->body(),
                'mime_type' => (string) $download->header('Content-Type', 'image/png'),
                'revised_prompt' => $image['revised_prompt'] ?? null,
                'provider_image_id' => $image['id'] ?? null,
                'raw' => is_array($response) ? $response : [],
            ];
        }

        throw new RuntimeException('The AI provider response format is not supported.');
    }

    public function testConnection(): array
    {
        $apiKey = Arr::get($this->providerConfig, 'api_key');
        $baseUrl = $this->apiBaseUrl();

        if (!$apiKey || !$baseUrl) {
            return [
                'ok' => false,
                'message' => 'Missing API key or base URL.',
                'details' => [],
            ];
        }

        Http::timeout((int) Arr::get($this->providerConfig, 'timeout', 30))
            ->withToken($apiKey)
            ->get($baseUrl . '/models')
            ->throw();

        return [
            'ok' => true,
            'message' => 'Connection probe succeeded against the models endpoint.',
            'details' => [],
        ];
    }

    private function apiBaseUrl(): string
    {
        $baseUrl = rtrim((string) Arr::get($this->providerConfig, 'base_url', ''), '/');

        if ($baseUrl === '') {
            return '';
        }

        $path = parse_url($baseUrl, PHP_URL_PATH);

        if ($path === null || $path === '' || $path === '/') {
            return $baseUrl . '/v1';
        }

        return $baseUrl;
    }
}