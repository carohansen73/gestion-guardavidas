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
        Schema::create('licencias', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->enum('tipo_licencia', ['Capacitación', 'Enfermedad', 'Evento deportivo', 'Exámen', 'Fallecimiento familiar','Feriado trabajado compensado', 'Lesión', 'Licencia médica', 'Permiso especial', 'Otro']);
            $table->boolean('en_tiempo');
            $table->enum('turno', ['M', 'T']);
            $table->unsignedBigInteger('guardavida_id')->nullable();
            $table->foreign('guardavida_id')->references('id')->on('guardavidas')->onDelete('cascade');
            $table->unsignedBigInteger('playa_id');
            $table->foreign('playa_id')->references('id')->on('playas')->onDelete('cascade');
            $table->unsignedBigInteger('puesto_id')->nullable();
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('cascade');
            $table->longText('detalle')->nullable();
            $table->string('archivo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('licencias');
    }
};
