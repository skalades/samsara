<?php

namespace App\Modules\POS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashierShift extends Model
{
    protected $table = 'cashier_shifts';

    protected $fillable = [
        'cashier_user_id',
        'shift_number',
        'opened_at',
        'closed_at',
        'opening_cash',
        'closing_cash_physical',
        'closing_cash_system',
        'closing_qris_system',
        'cash_difference',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
            'shift_number' => 'integer',
            'opening_cash' => 'decimal:2',
            'closing_cash_physical' => 'decimal:2',
            'closing_cash_system' => 'decimal:2',
            'closing_qris_system' => 'decimal:2',
            'cash_difference' => 'decimal:2',
        ];
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_user_id');
    }

    public function isOpen(): bool
    {
        return $this->status === 'OPEN';
    }
}
