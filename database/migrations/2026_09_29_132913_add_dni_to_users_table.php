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
        Schema::table('users', function (Blueprint $table) {
            // Único (no solo en guardavidas como hoy) para poder evitar que
            // un postulante se registre dos veces con el mismo DNI usando
            // emails distintos. Nullable porque admin/superadmin no tienen
            // por qué tener uno cargado acá.
            $table->string('dni')->nullable()->unique()->after('lastname');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('dni');
        });
    }
};
