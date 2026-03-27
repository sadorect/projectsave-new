<?php

namespace App\Services\AiImages;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;

class AiImageSettings
{
    public function defaultProvider(): string
    {
        return (string) $this->getSetting('ai_featured_images.default_provider', (string) config('ai-images.default_provider', 'openai'));
    }

    public function setDefaultProvider(string $value): void
    {
        AppSetting::set('ai_featured_images.default_provider', $value);
    }

    public function defaultPreset(): string
    {
        return (string) $this->getSetting('ai_featured_images.default_preset', (string) config('ai-images.default_preset', 'devotional-warm'));
    }

    public function setDefaultPreset(string $value): void
    {
        AppSetting::set('ai_featured_images.default_preset', $value);
    }

    public function requireApproval(): bool
    {
        return (bool) $this->getSetting('ai_featured_images.require_approval', (bool) config('ai-images.require_approval', true));
    }

    public function setRequireApproval(bool $value): void
    {
        AppSetting::set('ai_featured_images.require_approval', $value);
    }

    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    public function providerFieldDefinitions(): array
    {
        return [
            'together' => [
                'api_key' => ['label' => 'API Key', 'secret' => true, 'env' => 'TOGETHER_API_KEY'],
                'base_url' => ['label' => 'Base URL', 'env' => 'TOGETHER_BASE_URL'],
                'model' => ['label' => 'Model', 'env' => 'TOGETHER_IMAGE_MODEL'],
                'timeout' => ['label' => 'Timeout', 'env' => 'TOGETHER_IMAGE_TIMEOUT', 'cast' => 'int'],
            ],
            'replicate' => [
                'api_key' => ['label' => 'API Token', 'secret' => true, 'env' => 'REPLICATE_API_TOKEN'],
                'base_url' => ['label' => 'Base URL', 'env' => 'REPLICATE_BASE_URL'],
                'model' => ['label' => 'Model', 'env' => 'REPLICATE_IMAGE_MODEL'],
                'timeout' => ['label' => 'Timeout', 'env' => 'REPLICATE_IMAGE_TIMEOUT', 'cast' => 'int'],
            ],
            'stability' => [
                'api_key' => ['label' => 'API Key', 'secret' => true, 'env' => 'STABILITY_API_KEY'],
                'base_url' => ['label' => 'Base URL', 'env' => 'STABILITY_BASE_URL'],
                'endpoint' => ['label' => 'Endpoint', 'env' => 'STABILITY_IMAGE_ENDPOINT'],
                'timeout' => ['label' => 'Timeout', 'env' => 'STABILITY_IMAGE_TIMEOUT', 'cast' => 'int'],
            ],
            'fal' => [
                'api_key' => ['label' => 'API Key', 'secret' => true, 'env' => 'FAL_KEY'],
                'base_url' => ['label' => 'Base URL', 'env' => 'FAL_BASE_URL'],
                'model' => ['label' => 'Model', 'env' => 'FAL_IMAGE_MODEL'],
                'timeout' => ['label' => 'Timeout', 'env' => 'FAL_IMAGE_TIMEOUT', 'cast' => 'int'],
            ],
            'openai' => [
                'api_key' => ['label' => 'API Key', 'secret' => true, 'env' => 'OPENAI_API_KEY'],
                'base_url' => ['label' => 'Base URL', 'env' => 'OPENAI_BASE_URL'],
                'model' => ['label' => 'Model', 'env' => 'OPENAI_IMAGE_MODEL'],
                'timeout' => ['label' => 'Timeout', 'env' => 'OPENAI_IMAGE_TIMEOUT', 'cast' => 'int'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function providerConfig(string $provider): array
    {
        $config = config("ai-images.providers.{$provider}", []);

        if (!is_array($config) || !$this->hasSettingsTable()) {
            return is_array($config) ? $config : [];
        }

        $overrides = $this->getProviderOverrides($provider);

        foreach ($this->providerFieldDefinitions()[$provider] ?? [] as $field => $definition) {
            if (!array_key_exists($field, $overrides)) {
                continue;
            }

            $config[$field] = $this->normalizeFieldValue($definition, $overrides[$field], decrypt: true);
        }

        return $config;
    }

    /**
     * @param array<string, mixed> $values
     * @return array<string, mixed>
     */
    public function providerConfigFromInput(string $provider, array $values): array
    {
        $config = $this->providerConfig($provider);
        $definitions = $this->providerFieldDefinitions()[$provider] ?? [];

        foreach ($definitions as $field => $definition) {
            $clearKey = 'clear_' . $field;

            if (!empty($values[$clearKey])) {
                $config[$field] = null;
                continue;
            }

            if (($definition['secret'] ?? false) === true) {
                $rawValue = is_string($values[$field] ?? null) ? trim((string) $values[$field]) : '';

                if ($rawValue !== '') {
                    $config[$field] = $rawValue;
                }

                continue;
            }

            if (!array_key_exists($field, $values) || $values[$field] === null || $values[$field] === '') {
                continue;
            }

            $config[$field] = $this->normalizeFieldValue($definition, $values[$field], decrypt: false);
        }

        return $config;
    }

    /**
     * @return array<string, mixed>
     */
    public function providerViewData(string $provider): array
    {
        $effective = $this->providerConfig($provider);
        $stored = $this->getProviderOverrides($provider);
        $definitions = $this->providerFieldDefinitions()[$provider] ?? [];

        $fields = [];

        foreach ($definitions as $field => $definition) {
            $fields[$field] = [
                'label' => $definition['label'],
                'env' => $definition['env'] ?? null,
                'secret' => (bool) ($definition['secret'] ?? false),
                'value' => (bool) ($definition['secret'] ?? false)
                    ? null
                    : ($effective[$field] ?? null),
                'has_stored_value' => array_key_exists($field, $stored),
                'effective_value' => (bool) ($definition['secret'] ?? false)
                    ? (!empty($effective[$field]) ? 'Configured' : 'Missing')
                    : ($effective[$field] ?? null),
            ];
        }

        return [
            'configured' => !empty($effective['api_key']),
            'fields' => $fields,
        ];
    }

    /**
     * @param array<string, mixed> $values
     */
    public function storeProviderOverrides(string $provider, array $values): void
    {
        $this->storeProviderOverridesForActor($provider, $values, null);
    }

    /**
     * @param array<string, mixed> $values
     */
    public function storeProviderOverridesForActor(string $provider, array $values, ?User $actor): void
    {
        $definitions = $this->providerFieldDefinitions()[$provider] ?? [];
        $current = $this->getProviderOverrides($provider);
        $payload = [];

        foreach ($definitions as $field => $definition) {
            $clearKey = 'clear_' . $field;
            $rawValue = $values[$field] ?? null;
            $clear = !empty($values[$clearKey]);

            if ($clear) {
                continue;
            }

            if (($definition['secret'] ?? false) === true) {
                $rawValue = is_string($rawValue) ? trim($rawValue) : '';

                if ($rawValue !== '') {
                    $payload[$field] = Crypt::encryptString($rawValue);
                } elseif (isset($current[$field])) {
                    $payload[$field] = $current[$field];
                }

                continue;
            }

            if ($rawValue === null || $rawValue === '') {
                continue;
            }

            $payload[$field] = $this->normalizeFieldValue($definition, $rawValue, decrypt: false);
        }

        $key = $this->providerOverridesKey($provider);

        $changed = $payload !== $current;

        if ($payload === []) {
            AppSetting::query()->where('key', $key)->delete();

            if ($current !== []) {
                $this->touchProviderAudit($provider, $actor, false);
            }

            return;
        }

        AppSetting::set($key, $payload);

        if ($changed) {
            $this->touchProviderAudit($provider, $actor, true);
        }
    }

    /**
     * @return array{updated_at:?string, updated_by_id:?int, updated_by_name:?string, configured:bool}
     */
    public function providerAudit(string $provider): array
    {
        $value = $this->getSetting($this->providerAuditKey($provider), []);

        if (!is_array($value)) {
            return [
                'updated_at' => null,
                'updated_by_id' => null,
                'updated_by_name' => null,
                'configured' => false,
            ];
        }

        return [
            'updated_at' => $value['updated_at'] ?? null,
            'updated_by_id' => isset($value['updated_by_id']) ? (int) $value['updated_by_id'] : null,
            'updated_by_name' => $value['updated_by_name'] ?? null,
            'configured' => (bool) ($value['configured'] ?? false),
        ];
    }

    protected function getSetting(string $key, mixed $default): mixed
    {
        if (!$this->hasSettingsTable()) {
            return $default;
        }

        return AppSetting::get($key, $default);
    }

    protected function hasSettingsTable(): bool
    {
        try {
            return Schema::hasTable('app_settings');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getProviderOverrides(string $provider): array
    {
        $value = $this->getSetting($this->providerOverridesKey($provider), []);

        return is_array($value) ? $value : [];
    }

    protected function providerOverridesKey(string $provider): string
    {
        return 'ai_featured_images.providers.' . $provider;
    }

    protected function providerAuditKey(string $provider): string
    {
        return 'ai_featured_images.providers_meta.' . $provider;
    }

    protected function touchProviderAudit(string $provider, ?User $actor, bool $configured): void
    {
        AppSetting::set($this->providerAuditKey($provider), [
            'updated_at' => now()->toIso8601String(),
            'updated_by_id' => $actor?->getKey(),
            'updated_by_name' => $actor?->name,
            'configured' => $configured,
        ]);
    }

    protected function normalizeFieldValue(array $definition, mixed $value, bool $decrypt): mixed
    {
        if (($definition['secret'] ?? false) === true && $decrypt) {
            try {
                return Crypt::decryptString((string) $value);
            } catch (DecryptException) {
                return null;
            }
        }

        return match ($definition['cast'] ?? 'string') {
            'int' => (int) $value,
            'float' => (float) $value,
            default => is_string($value) ? trim($value) : $value,
        };
    }
}