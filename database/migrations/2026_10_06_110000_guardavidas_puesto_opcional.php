<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Un guardavida puede quedar asignado solo a una playa (sin puesto): el
 * propio guardavida elige su puesto y turno la primera vez que ingresa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guardavidas', function (Blueprint $table) {
            $table->unsignedBigInteger('puesto_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Sin vuelta atrás automática: volver a NOT NULL fallaría si ya hay guardavidas sin puesto.
    }
};
