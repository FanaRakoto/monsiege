<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reservation_id',
        'ticket_number',
        'qr_token',
        'status',
        'pdf_path',
        'issued_at',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'checked_at' => 'datetime',
        ];
    }

    /**
     * Les deux colonnes reçoivent automatiquement un UUID.
     */
    public function uniqueIds(): array
    {
        return [
            'id',
            'qr_token',
        ];
    }

    /**
     * Réservation correspondant au billet.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }
}