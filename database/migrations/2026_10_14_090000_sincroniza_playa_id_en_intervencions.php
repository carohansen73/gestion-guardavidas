<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La columna `intervencions.playa_id` existe en las bases reales (el modelo y los controladores la usan), pero
 * la migración original de la tabla no la tiene: las bases de prueba (SQLite en memoria) se armaban sin ella.
 * Esto solo alinea el repo con la realidad; donde la columna ya existe no hace nada.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('intervencions', 'playa_id')) {
            return;
        }

        Schema::table('intervencions', function (Blueprint $table) {
            $table->unsignedBigInteger('playa_id')->nullable()->after('traslado');
            $table->foreign('playa_id')->references('id')->on('playas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // No se revierte: la columna es parte del esquema real.
    }
};
