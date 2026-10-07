<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'agency_id',
        'license_number',
        'license_expiry_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'license_expiry_date' => 'date',
        ];
    }

    /**
     * Compte utilisateur associé.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Agence du chauffeur.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Voyages conduits par ce chauffeur.
     */
    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}