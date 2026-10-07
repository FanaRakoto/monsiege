<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'trip_id',
        'reservation_code',
        'passenger_count',
        'total_amount',
        'status',
        'expires_at',
        'confirmed_at',
        'cancelled_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'passenger_count' => 'integer',
            'total_amount' => 'integer',
            'expires_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * Client qui a effectué la réservation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Voyage réservé.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Passagers.
     */
    public function passengers(): HasMany
    {
        return $this->hasMany(Passenger::class);
    }

    /**
     * Sièges de la réservation.
     */
    public function reservationSeats(): HasMany
    {
        return $this->hasMany(ReservationSeat::class);
    }

    /**
     * Paiements associés.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Billet généré.
     */
    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class);
    }

    /**
     * Dernier paiement.
     */
    public function latestPayment(): HasOne
    {
        return $this->hasOne(Payment::class)
            ->latestOfMany();
    }
}