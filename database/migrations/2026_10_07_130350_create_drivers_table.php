<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignUuid('agency_id')
                ->constrained('agencies')
                ->restrictOnDelete();

            $table->string('license_number', 100)
                ->unique();

            $table->date('license_expiry_date')
                ->nullable();

            /*
             * active
             * suspended
             * inactive
             */
            $table->string('status', 20)
                ->default('active')
                ->index();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->index(['agency_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};