<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Add unique constraint on city_id, latitude, longitude
            $table->unique(['city_id', 'latitude', 'longitude'], 'locations_city_lat_long_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('locations_city_lat_long_unique');
        });
    }
};
