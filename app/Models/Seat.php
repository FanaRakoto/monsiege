<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seat extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'vehicle_id',
        'seat_number',
        'row_number',
        'column_number',
        'seat_type',
        'is_window',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'row_number' => 'integer',
            'column_number' => 'integer',
            'is_window' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Véhicule auquel appartient le siège.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Historique/occupations du siège.
     */
    public function reservationSeats(): HasMany
    {
        return $this->hasMany(ReservationSeat::class);
    }
}