<?php

namespace App\Modules\Simaksi\Models;

use App\Models\User;
use App\Shared\Enums\TrailStatus;
use App\Shared\Enums\WeatherStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class TrailSetting extends Model
{
    protected $table = 'trail_settings';

    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget('trail_setting_current');
        });

        static::deleted(function () {
            Cache::forget('trail_setting_current');
        });
    }

    protected $fillable = [
        'trail_status',
        'weather_status',
        'ticket_fee_per_person',
        'eco_deposit_fee',
        'insurance_fee',
        'pad_desa_fee',
        'announcement',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'trail_status' => TrailStatus::class,
            'weather_status' => WeatherStatus::class,
            'ticket_fee_per_person' => 'integer',
            'eco_deposit_fee' => 'integer',
            'insurance_fee' => 'integer',
            'pad_desa_fee' => 'integer',
        ];
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public static function current(): self
    {
        return Cache::remember('trail_setting_current', 300, function () {
            return static::firstOrCreate([], [
                'trail_status' => TrailStatus::OPEN,
                'weather_status' => WeatherStatus::CERAH,
                'announcement' => 'Jalur pendakian via Cigedug dibuka normal. Tetap patuhi SOP keselamatan dan bawa kembali sampah.',
            ]);
        });
    }
}
