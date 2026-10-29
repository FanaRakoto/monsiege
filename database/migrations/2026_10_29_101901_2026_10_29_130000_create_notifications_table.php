<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            // IMPORTANT : users.id est un UUID, donc on utilise uuidMorphs et non morphs.
            $table->uuidMorphs('notifiable');
            $table->string('type');
            $table->text('data');
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
            $table->index(['notifiable_type', 'notifiable_id', 'created_at'], 'notifications_recipient_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};