<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'agency_id',
        'vehicle_id',
        'driver_id',
        'departure_city',
        'arrival_city',
        'departure_address',
        'arrival_address',
        'departure_at',
        'arrival_at',
        'image_path',
        'price',
        'currency',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'departure_at' => 'datetime',
            'arrival_at' => 'datetime',
            'price' => 'integer',
        ];
    }

    /** Agence qui organise le voyage. */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /** Véhicule utilisé. */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /** Chauffeur affecté. */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /** Réservations de ce voyage. */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** Occupation des sièges pour ce voyage. */
    public function reservationSeats(): HasMany
    {
        return $this->hasMany(ReservationSeat::class);
    }
}   