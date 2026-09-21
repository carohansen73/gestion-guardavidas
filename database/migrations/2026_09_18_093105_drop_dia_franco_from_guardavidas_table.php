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
        // Reemplazada por guardavida_franco_historial (ver esa migración).
        // Antes de correr esto en una base real, hay que migrar los datos:
        // ver el SQL de backfill que acompaña este cambio.
        Schema::table('guardavidas', function (Blueprint $table) {
            $table->dropColumn('dia_franco');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guardavidas', function (Blueprint $table) {
            $table->unsignedTinyInteger('dia_franco')->nullable()->after('turno');
        });
    }
};
