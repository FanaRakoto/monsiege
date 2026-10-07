<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passengers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('reservation_id')
                ->constrained('reservations')
                ->cascadeOnDelete();

            $table->string('first_name', 100);

            $table->string('last_name', 100);

            $table->string('phone', 30)
                ->nullable();

            $table->string('email')
                ->nullable();

            /*
             * cin
             * passport
             * other
             */
            $table->string('id_type', 30)
                ->nullable();

            $table->string('id_number', 100)
                ->nullable();

            $table->boolean('is_primary')
                ->default(false);

            $table->timestamps();

            $table->index([
                'reservation_id',
                'is_primary'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passengers');
    }
};