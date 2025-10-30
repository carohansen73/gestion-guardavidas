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
        Schema::create('playas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->decimal('lat', 10, 6);
            $table->decimal('lon', 10, 6);
            $table->string('color', 50);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playas');
    }
};
