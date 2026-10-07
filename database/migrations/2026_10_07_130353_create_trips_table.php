<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('agency_id')
                ->constrained('agencies')
                ->restrictOnDelete();

            $table->foreignUuid('vehicle_id')
                ->constrained('vehicles')
                ->restrictOnDelete();

            $table->foreignUuid('driver_id')
                ->nullable()
                ->constrained('drivers')
                ->nullOnDelete();

            $table->string('departure_city', 100);

            $table->string('arrival_city', 100);

            $table->string('departure_address')
                ->nullable();

            $table->string('arrival_address')
                ->nullable();

            $table->dateTime('departure_at');

            $table->dateTime('arrival_at')
                ->nullable();

            /*
             * Prix en Ariary.
             * Aucun decimal n'est nécessaire.
             */
            $table->unsignedBigInteger('price');

            $table->string('currency', 3)
                ->default('MGA');

            /*
             * scheduled
             * boarding
             * departed
             * completed
             * cancelled
             */
            $table->string('status', 30)
                ->default('scheduled')
                ->index();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->index([
                'departure_city',
                'arrival_city',
                'departure_at'
            ]);

            $table->index([
                'agency_id',
                'status',
                'departure_at'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};