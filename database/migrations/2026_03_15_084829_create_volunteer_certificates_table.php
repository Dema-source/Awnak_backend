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
        Schema::create('volunteer_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('volunteer_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('certificate_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('task_id')->constrained()->cascadeOnDelete();
            $table->unique(['volunteer_id', 'certificate_id', 'task_id'],'vol_cert_task_uniq');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volunteer_certificates');
    }
};
