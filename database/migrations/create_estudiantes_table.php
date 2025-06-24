<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->string('carne', 20)->primary();
            $table->string('nombre', 150);
            $table->string('foto_dpi', 255)->nullable();
            $table->string('dpi', 20);
            $table->date('fecha_nacimiento');
            $table->string('depto_nacimiento', 100)->nullable();
            $table->string('municipio_nacimiento', 100)->nullable();
            $table->text('direccion')->nullable();
            $table->string('sexo', 10)->nullable();
            $table->string('tel', 20)->nullable();
            $table->string('tel_residencial', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('trabajo', 100)->nullable();
            $table->text('dir_trabajo')->nullable();
            $table->string('tel_trabajo', 20)->nullable();
            $table->string('ultimo_titulo', 150)->nullable();
            $table->string('id_carrera', 10);
            $table->string('id_sede', 10);

            $table->foreign('id_carrera')->references('id_carrera')->on('carreras');
            $table->foreign('id_sede')->references('id_sede')->on('sedes');
        });
    
    }

    public function down(): void {
        Schema::dropIfExists('estudiantes');
    }
};
