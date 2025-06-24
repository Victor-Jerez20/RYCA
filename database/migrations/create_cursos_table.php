<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        
        Schema::create('cursos', function (Blueprint $table) {
            $table->id('id_curso');
            $table->string('nombre', 100);
            $table->string('id_carrera', 10);
            $table->string('id_sede', 10);
            $table->string('ciclo', 20);

            $table->foreign('id_carrera')->references('id_carrera')->on('carreras');
            $table->foreign('id_sede')->references('id_sede')->on('sedes');
        });
    }

    public function down(): void {
        Schema::dropIfExists('cursos');
    }
};
