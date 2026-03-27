<?php

namespace App\Services\AiImages;

use App\Contracts\AiImageProvider;
use App\Services\AiImages\Providers\FalImageProvider;
use App\Services\AiImages\Providers\NullAiImageProvider;
use App\Services\AiImages\Providers\OpenAiCompatibleImageProvider;
use App\Services\AiImages\Providers\ReplicateImageProvider;
use App\Services\AiImages\Providers\StabilityImageProvider;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class AiImageProviderManager
{
    public function __construct(private readonly AiImageSettings $settings)
    {
    }

    public function provider(?string $name = null): AiImageProvider
    {
        $providerName = $name ?: $this->settings->defaultProvider();
        $providerConfig = $this->settings->providerConfig($providerName);

        return $this->providerFromConfig($providerConfig);
    }

    /**
     * @param array<string, mixed> $providerConfig
     */
    public function providerFromConfig(array $providerConfig): AiImageProvider
    {

        if (!is_array($providerConfig)) {
            return new NullAiImageProvider();
        }

        return match (Arr::get($providerConfig, 'driver')) {
            'openai-compatible' => new OpenAiCompatibleImageProvider($providerConfig),
            'replicate' => new ReplicateImageProvider($providerConfig),
            'stability' => new StabilityImageProvider($providerConfig),
            'fal' => new FalImageProvider($providerConfig),
            null => new NullAiImageProvider(),
            default => throw new InvalidArgumentException("Unsupported AI image driver [{$providerConfig['driver']}]."),
        };
    }

    /**
     * @return array{ok:bool, message:string, details:array<string, mixed>}
     */
    public function testConnection(string $name): array
    {
        return $this->provider($name)->testConnection();
    }

    /**
     * @param array<string, mixed> $providerConfig
     * @return array{ok:bool, message:string, details:array<string, mixed>}
     */
    public function testConnectionWithConfig(array $providerConfig): array
    {
        return $this->providerFromConfig($providerConfig)->testConnection();
    }
}