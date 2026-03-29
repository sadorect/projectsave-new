<?php

namespace App\Support;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SiteSettings
{
    private const SETTINGS_KEY = 'site_settings';
    private const CACHE_KEY = 'site_settings_public_data';
    private const CACHE_TTL = 3600;

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function (): array {
            $defaults = self::defaults();
            $stored = $this->stored();

            return array_replace($defaults, array_intersect_key($stored, $defaults));
        });
    }

    public function publicData(): array
    {
        $settings = $this->all();

        $settings['logo_url'] = $this->mediaUrl($settings['logo_path'] ?? null, self::defaults()['logo_path']);
        $settings['favicon_url'] = $this->mediaUrl($settings['favicon_path'] ?? null, self::defaults()['favicon_path']);
        $settings['contact_phone_href'] = $this->telephoneHref($settings['contact_phone'] ?? null);
        $settings['contact_email_href'] = $this->emailHref($settings['contact_email'] ?? null);
        $settings['privacy_email_href'] = $this->emailHref($settings['privacy_email'] ?? null);
        $settings['social_links'] = array_values(array_filter([
            $this->socialLink('facebook', 'Facebook', 'fab fa-facebook-f', $settings['facebook_url'] ?? null),
            $this->socialLink('instagram', 'Instagram', 'fab fa-instagram', $settings['instagram_url'] ?? null),
            $this->socialLink('youtube', 'YouTube', 'fab fa-youtube', $settings['youtube_url'] ?? null),
            $this->socialLink('x', 'X', 'bi bi-twitter-x', $settings['x_url'] ?? null),
        ]));

        return $settings;
    }

    public function save(array $data): void
    {
        if (! Schema::hasTable('app_settings')) {
            return;
        }

        AppSetting::set(self::SETTINGS_KEY, array_intersect_key($data, self::defaults()));
        Cache::forget(self::CACHE_KEY);
    }

    public static function defaults(): array
    {
        return [
            'site_name' => 'Projectsave International',
            'site_short_name' => 'Projectsave',
            'site_tagline' => 'Winning the lost. Building the saints.',
            'site_description' => 'A non-denominational Christian ministry devoted to preaching the Gospel, building believers through biblical teaching, and equipping men and women for kingdom service across nations.',
            'logo_path' => 'frontend/img/psave_logo.png',
            'favicon_path' => 'frontend/img/psave_logo.png',
            'contact_phone' => '(+234) 07080100893',
            'contact_email' => 'info@projectsaveng.org',
            'privacy_email' => 'privacy@projectsaveng.org',
            'contact_address' => 'P.O.Box 358, Ota, Ogun State, Nigeria',
            'facebook_url' => 'https://facebook.com/projectsave02',
            'instagram_url' => 'https://instagram.com/projectsave_ministries',
            'youtube_url' => 'https://youtube.com',
            'x_url' => '',
            'mission_statement' => 'Reach the lost, build believers, and strengthen workers for Gospel ministry.',
            'vision_statement' => 'Christ-centred communities transformed through evangelism, discipleship, and service.',
        ];
    }

    private function stored(): array
    {
        if (! Schema::hasTable('app_settings')) {
            return [];
        }

        $stored = AppSetting::get(self::SETTINGS_KEY, []);

        return is_array($stored) ? $stored : [];
    }

    private function mediaUrl(?string $path, ?string $fallback = null): ?string
    {
        $candidate = filled($path) ? $path : $fallback;

        if (! filled($candidate)) {
            return null;
        }

        if (filter_var($candidate, FILTER_VALIDATE_URL)) {
            return $candidate;
        }

        $candidate = ltrim($candidate, '/');

        if (Storage::disk('public')->exists($candidate)) {
            return Storage::disk('public')->url($candidate);
        }

        if (is_file(public_path($candidate))) {
            return asset($candidate);
        }

        if ($fallback && $candidate !== ltrim($fallback, '/')) {
            return $this->mediaUrl($fallback);
        }

        return null;
    }

    private function telephoneHref(?string $phone): ?string
    {
        if (! filled($phone)) {
            return null;
        }

        $normalized = preg_replace('/[^0-9+]/', '', trim($phone));

        if (str_contains($normalized, '+')) {
            $normalized = '+' . ltrim(str_replace('+', '', $normalized), '+');
        }

        return filled($normalized) ? 'tel:' . $normalized : null;
    }

    private function emailHref(?string $email): ?string
    {
        return filled($email) ? 'mailto:' . trim($email) : null;
    }

    private function socialLink(string $key, string $label, string $icon, ?string $url): ?array
    {
        if (! filled($url)) {
            return null;
        }

        return [
            'key' => $key,
            'label' => $label,
            'icon' => $icon,
            'url' => $url,
        ];
    }
}
