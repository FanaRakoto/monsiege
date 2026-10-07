<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationSeat extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reservation_id',
        'trip_id',
        'seat_id',
        'price',
        'status',
        'locked_until',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'locked_until' => 'datetime',
        ];
    }

    /**
     * Réservation correspondante.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Voyage correspondant.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Siège correspondant.
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }
}