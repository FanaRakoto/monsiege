<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Agence dont cet utilisateur est propriétaire.
     */
    public function agency(): HasOne
    {
        return $this->hasOne(
            Agency::class,
            'owner_user_id'
        );
    }

    /**
     * Profil chauffeur de cet utilisateur.
     */
    public function driver(): HasOne
    {
        return $this->hasOne(Driver::class);
    }

    /**
     * Réservations effectuées par cet utilisateur.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAgency(): bool
    {
        return $this->role === 'agency';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }
}