<?php

namespace App\Modules\Simaksi\Models;

use Illuminate\Database\Eloquent\Model;

class DailyQuota extends Model
{
    protected $table = 'daily_quotas';

    protected $fillable = [
        'date',
        'max_quota',
        'booked_count',
        'is_blackout',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date:Y-m-d',
            'max_quota' => 'integer',
            'booked_count' => 'integer',
            'is_blackout' => 'boolean',
        ];
    }

    public function setDateAttribute($value): void
    {
        $this->attributes['date'] = \Carbon\Carbon::parse($value)->toDateString();
    }

    public function getRemainingQuotaAttribute(): int
    {
        if ($this->is_blackout) {
            return 0;
        }
        return max(0, $this->max_quota - $this->booked_count);
    }

    public function getPercentageUsedAttribute(): float
    {
        if ($this->max_quota <= 0) {
            return 100.0;
        }
        return round(($this->booked_count / $this->max_quota) * 100, 1);
    }
}
