<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'agency_id',
        'registration_number',
        'brand',
        'model',
        'type',
        'capacity',
        'status',
        'description',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
        ];
    }

    /**
     * Agence propriétaire du véhicule.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Sièges du véhicule.
     */
    public function seats(): HasMany
    {
        return $this->hasMany(Seat::class);
    }

    /**
     * Voyages utilisant ce véhicule.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}