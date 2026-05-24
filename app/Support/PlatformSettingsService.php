<?php

namespace App\Support;

use App\Models\PlatformSetting;
use App\Models\User;

final class PlatformSettingsService
{
    /**
     * @param  array{platform_name: string, maintenance_mode?: bool}  $data
     */
    public function update(array $data, User $actor): PlatformSetting
    {
        $settings = PlatformSetting::current();

        $before = [
            'platform_name' => $settings->platform_name,
            'maintenance_mode' => $settings->maintenance_mode,
        ];

        $settings->update([
            'platform_name' => $data['platform_name'],
            'maintenance_mode' => (bool) ($data['maintenance_mode'] ?? false),
        ]);

        $settings = $settings->fresh() ?? $settings;

        AdminAuditLogger::log(
            AdminAuditLogger::PLATFORM_SETTINGS_UPDATED,
            $actor,
            null,
            [
                'before' => $before,
                'after' => [
                    'platform_name' => $settings->platform_name,
                    'maintenance_mode' => $settings->maintenance_mode,
                ],
            ],
        );

        return $settings;
    }
}
