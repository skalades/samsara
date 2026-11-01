<?php

namespace App\Modules\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'ip_hash',
        'page_path',
        'page_type',
        'visited_date',
    ];

    protected $casts = [
        'visited_date' => 'date',
    ];
}
