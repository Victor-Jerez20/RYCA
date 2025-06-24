<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropForeign(['id_jornada']); // Eliminar la relación si existe
            $table->dropColumn('id_jornada');     // Eliminar el campo
        });
    }

    public function down(): void {
        Schema::table('cursos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_jornada')->after('id_sede');
            $table->foreign('id_jornada')->references('id_jornada')->on('jornadas');
        });
    }
};
