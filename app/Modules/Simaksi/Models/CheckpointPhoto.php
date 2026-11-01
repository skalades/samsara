<?php

namespace App\Modules\Simaksi\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CheckpointPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'checkpoint_id',
        'title',
        'description',
        'photo_url',
        'is_primary',
        'order',
        'uploaded_by',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'order' => 'integer',
    ];

    public function checkpoint(): BelongsTo
    {
        return $this->belongsTo(Checkpoint::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Resolves absolute/relative URL for display in view/lightbox.
     */
    public function getResolvedUrlAttribute(): string
    {
        if (str_starts_with($this->photo_url, 'http://') || str_starts_with($this->photo_url, 'https://') || str_starts_with($this->photo_url, '/')) {
            return $this->photo_url;
        }

        return Storage::url($this->photo_url);
    }
}
