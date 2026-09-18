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
            // 0 (domingo) a 6 (sábado), igual que Carbon::dayOfWeek. Nullable
            // porque el guardavida lo configura él mismo y puede no haberlo
            // hecho todavía.
            $table->unsignedTinyInteger('dia_franco')->nullable()->after('turno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guardavidas', function (Blueprint $table) {
            $table->dropColumn('dia_franco');
        });
    }
};
