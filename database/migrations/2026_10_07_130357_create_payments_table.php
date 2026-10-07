<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('reservation_id')
                ->constrained('reservations')
                ->cascadeOnDelete();

            /*
             * mobile_money
             * card
             * bank_transfer
             * cash
             */
            $table->string('method', 30);

            /*
             * mvola
             * orange_money
             * airtel_money
             * etc.
             */
            $table->string('provider', 50)
                ->nullable();

            $table->string('transaction_reference', 150)
                ->nullable()
                ->unique();

            $table->string('payer_phone', 30)
                ->nullable();

            $table->unsignedBigInteger('amount');

            $table->string('currency', 3)
                ->default('MGA');

            /*
             * pending
             * successful
             * failed
             * refunded
             */
            $table->string('status', 30)
                ->default('pending')
                ->index();

            $table->dateTime('paid_at')
                ->nullable();

            /*
             * Réponse de MVola / Orange Money /
             * Airtel Money ou autre prestataire.
             */
            $table->json('provider_response')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->index([
                'reservation_id',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};