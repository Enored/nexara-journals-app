<?php

namespace App\Support;

use App\Models\PlatformSetting;
use App\Models\User;

final class PlatformSettingsService
{
    /**
     * @param  array{platform_name: string, logo_text?: string|null, show_logo_text_with_image?: bool}  $data
     */
    public function updateBranding(array $data, User $actor): PlatformSetting
    {
        $settings = PlatformSetting::current();

        $before = [
            'platform_name' => $settings->platform_name,
            'logo_text' => $settings->logo_text,
            'show_logo_text_with_image' => $settings->show_logo_text_with_image,
        ];

        $settings->update([
            'platform_name' => $data['platform_name'],
            'logo_text' => $data['logo_text'] ?? $settings->logo_text,
            'show_logo_text_with_image' => (bool) ($data['show_logo_text_with_image'] ?? false),
        ]);

        $settings = $settings->fresh() ?? $settings;

        AdminAuditLogger::log(
            AdminAuditLogger::PLATFORM_SETTINGS_UPDATED,
            $actor,
            null,
            [
                'section' => 'branding',
                'before' => $before,
                'after' => [
                    'platform_name' => $settings->platform_name,
                    'logo_text' => $settings->logo_text,
                    'show_logo_text_with_image' => $settings->show_logo_text_with_image,
                ],
            ],
        );

        return $settings;
    }

    /**
     * @param  array{maintenance_mode?: bool}  $data
     */
    public function updateGeneral(array $data, User $actor): PlatformSetting
    {
        $settings = PlatformSetting::current();

        $before = [
            'maintenance_mode' => $settings->maintenance_mode,
        ];

        $settings->update([
            'maintenance_mode' => (bool) ($data['maintenance_mode'] ?? false),
        ]);

        $settings = $settings->fresh() ?? $settings;

        AdminAuditLogger::log(
            AdminAuditLogger::PLATFORM_SETTINGS_UPDATED,
            $actor,
            null,
            [
                'section' => 'general',
                'before' => $before,
                'after' => [
                    'maintenance_mode' => $settings->maintenance_mode,
                ],
            ],
        );

        return $settings;
    }

    /**
     * @deprecated Use updateBranding() or updateGeneral() instead.
     * @param  array{platform_name: string, maintenance_mode?: bool, logo_text?: string|null, show_logo_text_with_image?: bool}  $data
     */
    public function update(array $data, User $actor): PlatformSetting
    {
        $settings = PlatformSetting::current();

        $settings->update([
            'platform_name' => $data['platform_name'],
            'maintenance_mode' => (bool) ($data['maintenance_mode'] ?? false),
            'logo_text' => $data['logo_text'] ?? $settings->logo_text,
            'show_logo_text_with_image' => (bool) ($data['show_logo_text_with_image'] ?? false),
        ]);

        return $settings->fresh() ?? $settings;
    }
}
