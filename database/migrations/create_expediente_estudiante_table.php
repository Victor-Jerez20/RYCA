<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('expediente_estudiante', function (Blueprint $table) {
            $table->id('id_expediente');
            $table->string('carne', 20)->unique();
            $table->boolean('formulario')->default(false);
            $table->boolean('fotos_cedula')->default(false);
            $table->boolean('titulo')->default(false);
            $table->boolean('certificacion')->default(false);
            $table->boolean('fotocopia_dpi')->default(false);
            $table->boolean('partida_nacimiento')->default(false);
            $table->boolean('verificacion_cgc')->default(false);
            $table->string('certificador', 100)->nullable();
            $table->string('url_expediente_digitalizado', 255)->nullable();

            // Aquí se establece la relación con la tabla estudiante
            $table->foreign('carne')->references('carne')->on('estudiantes');
        });
    }

    public function down(): void {
        Schema::dropIfExists('expediente_estudiante');
    }
};
