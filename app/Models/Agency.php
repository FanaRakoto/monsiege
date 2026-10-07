<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'owner_user_id',
        'name',
        'code',
        'phone',
        'email',
        'address',
        'city',
        'description',
        'status',
    ];

    /**
     * Utilisateur propriétaire de l'agence.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'owner_user_id'
        );
    }

    /**
     * Chauffeurs de l'agence.
     */
    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    /**
     * Véhicules de l'agence.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Voyages de l'agence.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}