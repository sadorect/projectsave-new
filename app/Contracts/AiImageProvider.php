<?php

namespace App\Contracts;

interface AiImageProvider
{
    /**
     * @param array<string, mixed> $payload
     * @return array{content:string, mime_type:string, revised_prompt:?string, provider_image_id:?string, raw:array<string, mixed>}
     */
    public function generate(array $payload): array;

    /**
     * @return array{ok:bool, message:string, details:array<string, mixed>}
     */
    public function testConnection(): array;
}