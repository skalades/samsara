<?php

namespace App\Modules\Simaksi\Models;

use App\Models\User;
use App\Modules\FieldOps\Models\EcoDeposit;
use App\Modules\POS\Models\PosOrder;
use App\Modules\Rental\Models\RentalOrder;
use App\Shared\Enums\BookingStatus;
use App\Shared\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SimaksiBooking extends Model
{
    protected $table = 'simaksi_bookings';

    protected $fillable = [
        'booking_code',
        'user_id',
        'hiking_date',
        'duration_days',
        'leader_name',
        'leader_nik',
        'leader_phone',
        'leader_email',
        'leader_address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'total_hikers',
        'ticket_fee_per_person',
        'total_ticket_amount',
        'eco_deposit_amount',
        'status',
        'payment_method',
        'paid_at',
        'checkin_at',
        'checkout_at',
        'current_pos',
        'qr_token',
        'payment_proof_path',
        'tent_color_details',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'hiking_date' => 'date',
            'duration_days' => 'integer',
            'total_hikers' => 'integer',
            'ticket_fee_per_person' => 'decimal:2',
            'total_ticket_amount' => 'decimal:2',
            'eco_deposit_amount' => 'decimal:2',
            'status' => BookingStatus::class,
            'payment_method' => PaymentMethod::class,
            'paid_at' => 'datetime',
            'checkin_at' => 'datetime',
            'checkout_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(SimaksiMember::class, 'booking_id');
    }

    public function checkpoints(): HasMany
    {
        return $this->hasMany(TrailCheckpoint::class, 'booking_id')->latest();
    }

    public function rentalOrders(): HasMany
    {
        return $this->hasMany(RentalOrder::class, 'booking_id');
    }

    public function posOrders(): HasMany
    {
        return $this->hasMany(PosOrder::class, 'booking_id');
    }

    public function ecoDeposit(): HasOne
    {
        return $this->hasOne(EcoDeposit::class, 'booking_id');
    }

    public function getTotalGrandAmountAttribute(): float
    {
        return (float) ($this->total_ticket_amount + $this->eco_deposit_amount);
    }

    public function isPending(): bool
    {
        return $this->status === BookingStatus::PENDING_PAYMENT;
    }

    public function isConfirmed(): bool
    {
        return $this->status === BookingStatus::CONFIRMED;
    }

    public function isOnTrail(): bool
    {
        return in_array($this->status, [BookingStatus::CHECKED_IN, BookingStatus::ON_TRAIL], true);
    }

    public function hasPaymentProof(): bool
    {
        return !empty($this->payment_proof_path);
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        if (empty($this->payment_proof_path)) {
            return null;
        }

        if (str_starts_with($this->payment_proof_path, 'http://') || str_starts_with($this->payment_proof_path, 'https://')) {
            return $this->payment_proof_path;
        }

        $cleanPath = ltrim($this->payment_proof_path, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            return asset($cleanPath);
        }

        return asset('storage/' . $cleanPath);
    }
}
