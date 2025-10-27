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
            $table->enum('turno', ['M', 'T'])->nullable()->after('puesto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guardavidas', function (Blueprint $table) {
            $table->dropColumn('turno');
        });
    }
};
