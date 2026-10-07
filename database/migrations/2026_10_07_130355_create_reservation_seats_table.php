<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_seats', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('reservation_id')
                ->constrained('reservations')
                ->cascadeOnDelete();

            $table->foreignUuid('trip_id')
                ->constrained('trips')
                ->cascadeOnDelete();

            $table->foreignUuid('seat_id')
                ->constrained('seats')
                ->restrictOnDelete();

            $table->unsignedBigInteger('price');

            /*
             * locked
             * confirmed
             */
            $table->string('status', 20)
                ->default('locked')
                ->index();

            /*
             * Exemple : réservation temporaire de 10 minutes.
             */
            $table->dateTime('locked_until')
                ->nullable();

            $table->timestamps();

            /*
             * Pour un voyage donné, un siège ne peut
             * être réservé qu'une seule fois.
             */
            $table->unique([
                'trip_id',
                'seat_id'
            ]);

            $table->index([
                'reservation_id',
                'status'
            ]);

            $table->index('locked_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_seats');
    }
};