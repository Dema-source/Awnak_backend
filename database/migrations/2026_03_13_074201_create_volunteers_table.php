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
        Schema::create('volunteers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id')->constrained()->cascadeOnDelete();
            $table->enum('experience_years', ["one year", "two years", "three years", "four years", "five years", "More than five years"]);
            $table->enum('status', ["active", "In_active", "pending", "blocked"]);
            $table->json('availability');
            $table->string('languages');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteers');
    }
};
