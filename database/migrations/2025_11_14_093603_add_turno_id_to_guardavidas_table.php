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
        Schema::table('guardavidas', function (Blueprint $table) {
            $table->unsignedBigInteger('turno_id')->nullable()->after('puesto_id');
            $table->foreign('turno_id')->references('id')->on('cambio_de_turnos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guardavidas', function (Blueprint $table) {
            //
        });
    }
};
