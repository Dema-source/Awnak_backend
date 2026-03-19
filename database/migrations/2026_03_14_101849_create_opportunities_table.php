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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('organization_profile_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('location_id')->constrained()->cascadeOnDelete();
            $table->string('expected_duration');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('required_volunteers');
            $table->enum('status', ['open', 'closed', 'filled', 'cancelled'])->default('open');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
