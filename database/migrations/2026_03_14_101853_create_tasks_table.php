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
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('volunteer_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('opportunity_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('profile_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ["in progress", "active", "completed", "cancelled"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
