<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        
        Schema::create('notas', function (Blueprint $table) {
            $table->id('id_nota');
            $table->string('carne', 20);
            $table->unsignedBigInteger('id_curso');
            $table->integer('anio'); // ← Año se traslada aquí
            $table->decimal('consolidado', 5, 2);

            $table->foreign('carne')->references('carne')->on('estudiantes');
            $table->foreign('id_curso')->references('id_curso')->on('cursos');
        });
    }

    public function down(): void {
        Schema::dropIfExists('notas');
    }
};
