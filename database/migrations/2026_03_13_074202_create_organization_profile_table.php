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
            $table->unsignedBigInteger('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ["Charitable organization", "Civil society organization", "Voluntary educational/university institution", "Hospital", "Religious organization", "Company with a Corporate Social Responsibility (CSR) program", "Student club/association", "Environmental organization"]);
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
