<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Illuminate\Support\hours;

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
            $table->integer('hours');
            $table->enum('status', ["in progress", "active", "completed", "cancelled"])->default("in progress");
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
