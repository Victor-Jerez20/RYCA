<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        
        Schema::create('carreras', function (Blueprint $table) {
            $table->string('id_carrera', 10)->primary();
            $table->string('nombre', 100);
        });
    
    }

    public function down(): void {
        Schema::dropIfExists('carreras');
    }
};
