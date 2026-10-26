<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('trips', 'image_path')) {
            Schema::table('trips', function (Blueprint $table): void {
                $table->string('image_path')->nullable()->after('arrival_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('trips', 'image_path')) {
            Schema::table('trips', function (Blueprint $table): void {
                $table->dropColumn('image_path');
            });
        }
    }
};