<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('agency_id')
                ->constrained('agencies')
                ->restrictOnDelete();

            $table->string('registration_number', 50)
                ->unique();

            $table->string('brand', 100)
                ->nullable();

            $table->string('model', 100)
                ->nullable();

            /*
             * taxi
             * taxi_brousse
             * minibus
             * bus
             * van
             */
            $table->string('type', 50)
                ->default('minibus');

            $table->unsignedSmallInteger('capacity');

            /*
             * active
             * maintenance
             * inactive
             */
            $table->string('status', 20)
                ->default('active')
                ->index();

            $table->text('description')
                ->nullable();

            $table->timestamps();

            $table->index(['agency_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};