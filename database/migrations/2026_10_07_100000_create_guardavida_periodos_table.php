<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Períodos en los que una persona estuvo en el plantel (alta -> baja). Es lo
 * que le dice al presentismo desde cuándo y hasta cuándo se le cuentan faltas:
 * alguien que arranca en diciembre no debe figurar con faltas en noviembre, ni
 * alguien dado de baja después de su baja.
 *
 * `desde` NULL = "desde antes de que se registraran las altas" (guardavidas
 * anteriores a este registro: se les sigue contando como siempre).
 * `hasta` NULL = período abierto (sigue en el plantel).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardavida_periodos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardavida_id')->constrained('guardavidas')->cascadeOnDelete();
            $table->foreignId('temporada_id')->nullable()->constrained('temporadas')->nullOnDelete();
            $table->date('desde')->nullable();
            $table->date('hasta')->nullable();
            $table->string('motivo')->nullable();
            $table->timestamps();

            $table->index(['guardavida_id', 'desde']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardavida_periodos');
    }
};
