<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignUuid('trip_id')
                ->constrained('trips')
                ->restrictOnDelete();

            /*
             * Exemple :
             * MS-20261007-A8F4C9
             */
            $table->string('reservation_code', 50)
                ->unique();

            $table->unsignedSmallInteger('passenger_count');

            $table->unsignedBigInteger('total_amount');

            /*
             * pending
             * confirmed
             * cancelled
             * expired
             */
            $table->string('status', 20)
                ->default('pending')
                ->index();

            /*
             * Fin de la réservation temporaire.
             */
            $table->dateTime('expires_at')
                ->nullable();

            $table->dateTime('confirmed_at')
                ->nullable();

            $table->dateTime('cancelled_at')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->index([
                'user_id',
                'status'
            ]);

            $table->index([
                'trip_id',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};