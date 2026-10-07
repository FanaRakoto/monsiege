<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seats', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('vehicle_id')
                ->constrained('vehicles')
                ->cascadeOnDelete();

            $table->string('seat_number', 20);

            $table->unsignedSmallInteger('row_number')
                ->nullable();

            $table->unsignedSmallInteger('column_number')
                ->nullable();

            /*
             * standard
             * vip
             */
            $table->string('seat_type', 30)
                ->default('standard');

            $table->boolean('is_window')
                ->default(false);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            /*
             * Un véhicule ne peut pas avoir deux
             * sièges portant le même numéro.
             */
            $table->unique([
                'vehicle_id',
                'seat_number'
            ]);

            $table->index([
                'vehicle_id',
                'is_active'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};