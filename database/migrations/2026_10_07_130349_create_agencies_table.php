<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('owner_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name');

            $table->string('code', 50)
                ->unique();

            $table->string('phone', 30)
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->string('city', 100)
                ->nullable();

            $table->text('description')
                ->nullable();

            /*
             * active
             * suspended
             * inactive
             */
            $table->string('status', 20)
                ->default('active')
                ->index();

            $table->timestamps();

            $table->index(['city', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};