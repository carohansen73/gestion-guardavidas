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
        // Historial de esquemas de franco por guardavida. Reemplaza a la
        // columna guardavidas.dia_franco (un solo día) porque ese esquema
        // puede cambiar con el tiempo (ej: se reduce personal y le mueven el
        // franco de jueves a martes) y para reportes de meses pasados hace
        // falta saber qué esquema regía en cada fecha, no solo el actual.
        // dias_franco es un JSON con los días de esa semana que le
        // corresponden como franco fijo (0=domingo..6=sábado) — permite
        // tanto el caso de 1 día (guardavidas) como el de varios días fijos
        // por semana (ej. personal de aeródromo, lunes a viernes = franco
        // sábado y domingo).
        Schema::create('guardavida_franco_historial', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('guardavida_id');
            $table->foreign('guardavida_id')->references('id')->on('guardavidas')->onDelete('cascade');

            $table->json('dias_franco');
            $table->date('vigente_desde');
            $table->date('vigente_hasta')->nullable();

            $table->unsignedBigInteger('creado_por_user_id')->nullable();
            $table->foreign('creado_por_user_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();

            $table->index(['guardavida_id', 'vigente_desde']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardavida_franco_historial');
    }
};
