<?php

namespace App\Services\AiImages\Providers;

use App\Contracts\AiImageProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ReplicateImageProvider implements AiImageProvider
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
        $baseUrl = rtrim((string) Arr::get($this->providerConfig, 'base_url', 'https://api.replicate.com/v1'), '/');
        $model = trim((string) Arr::get($this->providerConfig, 'model', ''), '/');
        $timeout = (int) Arr::get($this->providerConfig, 'timeout', 120);

        if (!$apiKey || !$model) {
            throw new RuntimeException('The selected Replicate provider is missing its API configuration.');
        }

        $response = Http::timeout($timeout)
            ->withHeaders([
                'Authorization' => 'Token ' . $apiKey,
                'Prefer' => 'wait=' . min($timeout, 60),
            ])
            ->post($baseUrl . '/models/' . $model . '/predictions', [
                'input' => array_filter([
                    'prompt' => $payload['prompt'] ?? null,
                    'aspect_ratio' => $payload['options']['aspect_ratio'] ?? Arr::get($this->providerConfig, 'options.aspect_ratio'),
                    'output_format' => $payload['options']['output_format'] ?? Arr::get($this->providerConfig, 'options.output_format', 'png'),
                    'output_quality' => $payload['options']['output_quality'] ?? Arr::get($this->providerConfig, 'options.output_quality'),
                    'go_fast' => $payload['options']['go_fast'] ?? Arr::get($this->providerConfig, 'options.go_fast'),
                    'megapixels' => $payload['options']['megapixels'] ?? Arr::get($this->providerConfig, 'options.megapixels'),
                ], static fn ($value) => $value !== null && $value !== ''),
            ])
            ->throw()
            ->json();

        $prediction = $this->waitForCompletion($response, $apiKey, $timeout);
        $imageUrl = $this->extractOutputUrl($prediction);

        if (!$imageUrl) {
            throw new RuntimeException('Replicate did not return an image URL.');
        }

        $download = Http::timeout($timeout)->get($imageUrl)->throw();

        return [
            'content' => $download->body(),
            'mime_type' => (string) $download->header('Content-Type', 'image/png'),
            'revised_prompt' => null,
            'provider_image_id' => $prediction['id'] ?? null,
            'raw' => is_array($prediction) ? $prediction : [],
        ];
    }

    /**
     * @param array<string, mixed> $prediction
     * @return array<string, mixed>
     */
    protected function waitForCompletion(array $prediction, string $apiKey, int $timeout): array
    {
        $status = $prediction['status'] ?? null;

        if (in_array($status, ['succeeded', 'failed', 'canceled'], true)) {
            if ($status !== 'succeeded') {
                throw new RuntimeException((string) ($prediction['error'] ?? 'Replicate image generation failed.'));
            }

            return $prediction;
        }

        $pollUrl = Arr::get($prediction, 'urls.get');

        if (!$pollUrl) {
            throw new RuntimeException('Replicate did not return a polling URL.');
        }

        $startedAt = microtime(true);

        while ((microtime(true) - $startedAt) < $timeout) {
            usleep(750000);

            $prediction = Http::timeout($timeout)
                ->withHeaders([
                    'Authorization' => 'Token ' . $apiKey,
                ])
                ->get($pollUrl)
                ->throw()
                ->json();

            $status = $prediction['status'] ?? null;

            if ($status === 'succeeded') {
                return $prediction;
            }

            if (in_array($status, ['failed', 'canceled'], true)) {
                throw new RuntimeException((string) ($prediction['error'] ?? 'Replicate image generation failed.'));
            }
        }

        throw new RuntimeException('Replicate image generation timed out.');
    }

    /**
     * @param array<string, mixed> $prediction
     */
    protected function extractOutputUrl(array $prediction): ?string
    {
        $output = $prediction['output'] ?? null;

        if (is_string($output) && $output !== '') {
            return $output;
        }

        if (is_array($output)) {
            $first = Arr::first($output);

            if (is_string($first) && $first !== '') {
                return $first;
            }

            if (is_array($first)) {
                return $first['url'] ?? null;
            }
        }

        return null;
    }

    public function testConnection(): array
    {
        $apiKey = Arr::get($this->providerConfig, 'api_key');
        $baseUrl = rtrim((string) Arr::get($this->providerConfig, 'base_url', 'https://api.replicate.com/v1'), '/');

        if (!$apiKey) {
            return [
                'ok' => false,
                'message' => 'Missing API token.',
                'details' => [],
            ];
        }

        Http::timeout((int) Arr::get($this->providerConfig, 'timeout', 30))
            ->withHeaders([
                'Authorization' => 'Token ' . $apiKey,
            ])
            ->get($baseUrl . '/account')
            ->throw();

        return [
            'ok' => true,
            'message' => 'Connection probe succeeded against the account endpoint.',
            'details' => [],
        ];
    }
}