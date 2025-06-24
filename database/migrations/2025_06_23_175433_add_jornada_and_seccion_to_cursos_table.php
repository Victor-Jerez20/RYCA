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
        Schema::table('cursos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_jornada')->after('id_sede');
            $table->string('seccion', 10)->after('id_jornada');

            $table->foreign('id_jornada')->references('id_jornada')->on('jornadas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropForeign(['id_jornada']);
            $table->dropColumn(['id_jornada', 'seccion']);
        });
    }
};
