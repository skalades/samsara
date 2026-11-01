<?php

namespace App\Modules\Ojek\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OjekRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_name',
        'origin',
        'destination',
        'fare_amount',
        'driver_share',
        'kas_share',
        'estimated_duration_minutes',
        'is_active',
    ];

    protected $casts = [
        'fare_amount' => 'decimal:2',
        'driver_share' => 'decimal:2',
        'kas_share' => 'decimal:2',
        'estimated_duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    public function tickets(): HasMany
    {
        return $this->hasMany(OjekQueueTicket::class, 'ojek_route_id');
    }
}
