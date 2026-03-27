<?php

namespace App\Services\AiImages\Providers;

use App\Contracts\AiImageProvider;
use RuntimeException;

class NullAiImageProvider implements AiImageProvider
{
    public function generate(array $payload): array
    {
        throw new RuntimeException('No AI image provider is configured.');
    }

    public function testConnection(): array
    {
        return [
            'ok' => false,
            'message' => 'No AI image provider is configured.',
            'details' => [],
        ];
    }
}