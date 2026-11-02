<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('trips', 'receiver_name')) {
            Schema::table('trips', function (Blueprint $table): void {
                $table->string('receiver_name', 120)->nullable()->after('driver_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('trips', 'receiver_name')) {
            Schema::table('trips', function (Blueprint $table): void {
                $table->dropColumn('receiver_name');
            });
        }
    }
};