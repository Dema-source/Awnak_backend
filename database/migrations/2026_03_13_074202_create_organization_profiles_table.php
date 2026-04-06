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
        Schema::create('organization_profiles', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Foreign key relationship: each organization belongs to one user
            $table->unsignedBigInteger('user_id')->constrained()->cascadeOnDelete();
            // Status default notactive and System admin can change it later 
            $table->enum('status', ['active', 'notactive'])->default('notactive');
            $table->string('license_number')->unique(); // Unique license number
            // Type of organization
            $table->enum('type', ["Charitable organization", "Civil society organization", "Voluntary educational/university institution", "Hospital", "Religious organization", "Company with a Corporate Social Responsibility (CSR) program", "Student club/association", "Environmental organization"]); 
            $table->text('bio')->nullable(); // Short description or background
            $table->string('website')->nullable(); // Official website (optional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_profiles');
    }
};
