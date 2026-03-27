<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AiImages\AiImageProviderManager;
use App\Services\AiImages\AiImageSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AiImageSettingsController extends Controller
{
    public function __construct(private readonly AiImageSettings $settings)
    {
        $this->middleware('permission:manage-ai-image-settings,admin');
    }

    public function edit()
    {
        $providerConfigs = config('ai-images.providers', []);
        $providerDefinitions = $this->settings->providerFieldDefinitions();

        $providers = collect($providerConfigs)
            ->map(function (array $providerConfig, string $providerKey) use ($providerDefinitions) {
                return [
                    'key' => $providerKey,
                    'label' => $providerConfig['label'] ?? $providerKey,
                    'tier' => $providerConfig['tier'] ?? null,
                    'driver' => $providerConfig['driver'] ?? null,
                    'fields' => $providerDefinitions[$providerKey] ?? [],
                    'state' => $this->settings->providerViewData($providerKey),
                    'audit' => $this->settings->providerAudit($providerKey),
                ];
            })
            ->values();

        return view('admin.posts.ai-settings', [
            'providers' => $providers,
            'presets' => config('ai-images.presets', []),
            'defaultProvider' => $this->settings->defaultProvider(),
            'defaultPreset' => $this->settings->defaultPreset(),
            'requireApproval' => $this->settings->requireApproval(),
        ]);
    }

    public function update(Request $request)
    {
        $providerKeys = array_keys(config('ai-images.providers', []));
        $presetKeys = array_keys(config('ai-images.presets', []));

        $rules = [
            'require_approval' => 'nullable|boolean',
            'default_provider' => ['required', 'string', Rule::in($providerKeys)],
            'default_preset' => ['required', 'string', Rule::in($presetKeys)],
        ];

        foreach ($this->settings->providerFieldDefinitions() as $providerKey => $fields) {
            foreach ($fields as $field => $definition) {
                $fieldKey = "providers.{$providerKey}.{$field}";
                $rules[$fieldKey] = ($definition['cast'] ?? null) === 'int'
                    ? 'nullable|integer|min:1|max:600'
                    : 'nullable|string|max:5000';

                if (($definition['secret'] ?? false) === true) {
                    $rules["providers.{$providerKey}.clear_{$field}"] = 'nullable|boolean';
                }
            }
        }

        $validated = $request->validate($rules);

        $this->settings->setRequireApproval((bool) ($validated['require_approval'] ?? false));
        $this->settings->setDefaultProvider($validated['default_provider']);
        $this->settings->setDefaultPreset($validated['default_preset']);

        foreach ($providerKeys as $providerKey) {
            $this->settings->storeProviderOverridesForActor($providerKey, $validated['providers'][$providerKey] ?? [], $request->user());
        }

        return redirect()
            ->route('admin.ai-images.settings.edit')
            ->with('success', 'AI image settings updated successfully.');
    }

    public function testProvider(Request $request, AiImageProviderManager $providerManager, string $provider)
    {
        abort_unless(array_key_exists($provider, config('ai-images.providers', [])), 404);

        $rules = [];

        foreach ($this->settings->providerFieldDefinitions()[$provider] ?? [] as $field => $definition) {
            $rules["providers.{$provider}.{$field}"] = ($definition['cast'] ?? null) === 'int'
                ? 'nullable|integer|min:1|max:600'
                : 'nullable|string|max:5000';

            if (($definition['secret'] ?? false) === true) {
                $rules["providers.{$provider}.clear_{$field}"] = 'nullable|boolean';
            }
        }

        $validated = $request->validate($rules);
        $providerValues = $validated['providers'][$provider] ?? [];
        $providerConfig = $this->settings->providerConfigFromInput($provider, $providerValues);

        try {
            $result = $providerManager->testConnectionWithConfig($providerConfig);

            return redirect()
                ->route('admin.ai-images.settings.edit')
                ->with($result['ok'] ? 'success' : 'error', sprintf('%s: %s', config("ai-images.providers.{$provider}.label", $provider), $result['message']));
        } catch (\Throwable $exception) {
            return redirect()
                ->route('admin.ai-images.settings.edit')
                ->with('error', sprintf('%s: %s', config("ai-images.providers.{$provider}.label", $provider), $exception->getMessage()));
        }
    }
}