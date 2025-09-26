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
        Schema::create('guardavidas_intervenciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardavida_id')->constrained()->onDelete('cascade');
            $table->foreignId('intervencion_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardavidas_intervenciones');
    }
};
