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
        Schema::create('location_opportunity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained('opportunities')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->string('building_name', 100)->nullable();
            $table->integer('floor_number')->nullable();
            $table->integer('apartment_number')->nullable();
            $table->string('landmark', 255)->nullable();
            $table->timestamps();

            $table->unique(['opportunity_id', 'location_id']);
            $table->index(['opportunity_id']);
            $table->index(['location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_opportunity');
    }
};
