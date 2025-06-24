<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('notas', function (Blueprint $table) {
            // Elimina el campo 'anio' si existe
            if (Schema::hasColumn('notas', 'anio')) {
                $table->dropColumn('anio');
            }

            // Agrega el campo fecha_aprobacion como string en formato 'YYYY-MM'
            $table->string('fecha_aprobacion', 7)->after('id_curso');
        });
    }

    public function down(): void {
        Schema::table('notas', function (Blueprint $table) {
            $table->dropColumn('fecha_aprobacion');

            // Opcional: restaurar el campo 'anio'
            $table->integer('anio')->after('id_curso');
        });
    }
};
