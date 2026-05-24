<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PlatformSetting extends Model
{
    public const ROW_ID = 1;

    public $incrementing = false;

    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'platform_name',
        'maintenance_mode',
        'favicon_path',
    ];

    protected function casts(): array
    {
        return [
            'maintenance_mode' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return Cache::rememberForever('platform.settings', function () {
            return static::query()->firstOrCreate(
                ['id' => self::ROW_ID],
                [
                    'platform_name' => (string) config('app.name'),
                    'maintenance_mode' => false,
                ],
            );
        });
    }

    public static function isMaintenanceMode(): bool
    {
        return (bool) static::current()->maintenance_mode;
    }

    public static function clearCache(): void
    {
        Cache::forget('platform.settings');
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }
}
