<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Throwable;

class MaintenanceModeService
{
    private const CACHE_KEY = 'site-setting:maintenance';

    /** @return array{enabled: bool, message: string, expected_return_at: ?string, enabled_at: ?string} */
    public function status(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                $value = SiteSetting::query()->where('key', 'maintenance')->value('value');
                if (is_string($value)) {
                    $value = json_decode($value, true);
                }

                return $this->normalize(is_array($value) ? $value : []);
            } catch (Throwable) {
                // The storefront must remain available before the settings migration runs.
                return $this->normalize([]);
            }
        });
    }

    public function enabled(): bool
    {
        return $this->status()['enabled'];
    }

    public function enable(User $administrator, string $message, ?string $expectedReturnAt = null): void
    {
        $value = $this->normalize([
            'enabled' => true,
            'message' => $message,
            'expected_return_at' => $expectedReturnAt,
            'enabled_at' => now()->toIso8601String(),
        ]);

        SiteSetting::query()->updateOrCreate(
            ['key' => 'maintenance'],
            ['value' => $value, 'updated_by' => $administrator->id],
        );
        Cache::forget(self::CACHE_KEY);
    }

    public function disable(User $administrator): void
    {
        $value = $this->status();
        $value['enabled'] = false;
        $value['expected_return_at'] = null;

        SiteSetting::query()->updateOrCreate(
            ['key' => 'maintenance'],
            ['value' => $value, 'updated_by' => $administrator->id],
        );
        Cache::forget(self::CACHE_KEY);
    }

    /** @param array<string, mixed> $value
     * @return array{enabled: bool, message: string, expected_return_at: ?string, enabled_at: ?string}
     */
    private function normalize(array $value): array
    {
        return [
            'enabled' => (bool) ($value['enabled'] ?? false),
            'message' => trim((string) ($value['message'] ?? 'We are refreshing the mountain pantry and will be back shortly. Thank you for your patience.')),
            'expected_return_at' => isset($value['expected_return_at']) && $value['expected_return_at'] !== '' ? (string) $value['expected_return_at'] : null,
            'enabled_at' => isset($value['enabled_at']) && $value['enabled_at'] !== '' ? (string) $value['enabled_at'] : null,
        ];
    }
}
