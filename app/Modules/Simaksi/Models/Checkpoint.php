<?php

namespace App\Modules\Simaksi\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Checkpoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'number',
        'title',
        'elevation',
        'distance',
        'time_estimate',
        'water_info',
        'water_available',
        'signal_info',
        'tent_capacity',
        'terrain',
        'default_image',
        'tagline',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'water_available' => 'boolean',
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(CheckpointPhoto::class)->orderBy('is_primary', 'desc')->orderBy('order', 'asc');
    }

    public function primaryPhoto(): ?CheckpointPhoto
    {
        return $this->photos()->where('is_primary', true)->first()
            ?? $this->photos()->first();
    }

    public function getActiveImageUrlAttribute(): string
    {
        $primary = $this->primaryPhoto();
        if ($primary && $primary->photo_url) {
            return $primary->photo_url;
        }

        return $this->default_image ?: '/assets/hero-cikuray.jpg';
    }
}
