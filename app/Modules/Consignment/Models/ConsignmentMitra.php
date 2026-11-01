<?php

namespace App\Modules\Consignment\Models;

use App\Modules\Store\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsignmentMitra extends Model
{
    protected $table = 'consignment_mitras';

    protected $fillable = [
        'name',
        'nik',
        'village_address',
        'phone_wa',
        'bank_info',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'consignment_mitra_id');
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(ConsignmentSettlement::class, 'consignment_mitra_id')->latest();
    }
}
