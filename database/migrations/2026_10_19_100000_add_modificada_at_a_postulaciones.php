<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cuándo el postulante modificó una inscripción que ya había enviado (estado `pendiente`), para que
     * el admin lo note. Se limpia cuando el admin guarda la revisión. `dateTime` (no `timestamp`) para
     * que MySQL no le agregue ON UPDATE CURRENT_TIMESTAMP.
     */
    public function up(): void
    {
        Schema::table('postulaciones', function (Blueprint $table) {
            $table->dateTime('modificada_at')->nullable()->after('enviada_at');
        });
    }

    public function down(): void
    {
        Schema::table('postulaciones', function (Blueprint $table) {
            $table->dropColumn('modificada_at');
        });
    }
};
