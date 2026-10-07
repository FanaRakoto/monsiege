<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('reservation_id')
                ->unique()
                ->constrained('reservations')
                ->cascadeOnDelete();

            /*
             * Numéro visible par le client.
             */
            $table->string('ticket_number', 100)
                ->unique();

            /*
             * Jeton UUID utilisé dans le QR Code.
             * Il ne dépend pas de la séquence du billet.
             */
            $table->uuid('qr_token')
                ->unique();

            /*
             * valid
             * used
             * cancelled
             */
            $table->string('status', 20)
                ->default('valid')
                ->index();

            $table->string('pdf_path')
                ->nullable();

            $table->dateTime('issued_at')
                ->nullable();

            $table->dateTime('checked_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'ticket_number',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};