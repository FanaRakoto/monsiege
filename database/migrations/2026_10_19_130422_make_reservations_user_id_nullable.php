<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Autoriser les réservations sans compte utilisateur.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->change();
        });
    }

    /**
     * Restaurer le caractère obligatoire de user_id.
     * Attention : cette opération nécessite qu'aucune réservation
     * n'ait un user_id égal à NULL.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
        });
    }
};