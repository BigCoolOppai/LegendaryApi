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
        Schema::create('celestial_objects', function (Blueprint $table) {
            $table->id();
            //$table->string('name')->unique();
            // $table->string('type');
            // $table->text('description')->nullable();
            // $table->string('diameter_km')->nullable();
            // $table->string('mass_kg')->nullable();
            // $table->string('orbital_period_days')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('celestial_objects');
    }
};
