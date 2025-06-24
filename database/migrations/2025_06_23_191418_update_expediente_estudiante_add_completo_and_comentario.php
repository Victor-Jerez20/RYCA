<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void {
        // Agregar comentario (campo normal)
        Schema::table('expediente_estudiante', function (Blueprint $table) {
            $table->text('comentario')->nullable()->after('url_expediente_digitalizado');
        });

        // Agregar campo generado 'completo'
        DB::statement("
            ALTER TABLE expediente_estudiante
            ADD COLUMN completo BOOLEAN GENERATED ALWAYS AS (
                formulario AND fotos_cedula AND titulo AND certificacion AND
                fotocopia_dpi AND partida_nacimiento AND verificacion_cgc
            ) STORED
        ");
    }

    public function down(): void {
        Schema::table('expediente_estudiante', function (Blueprint $table) {
            $table->dropColumn('comentario');
            $table->dropColumn('completo');
        });
    }
};
