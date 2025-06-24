<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // 1. Eliminar la foreign key desde la tabla notas
        Schema::table('notas', function (Blueprint $table) {
            $table->dropForeign(['id_curso']);
        });

        // 2. Agregar una columna temporal en cursos para los nuevos IDs manuales
        Schema::table('cursos', function (Blueprint $table) {
            $table->string('id_curso_manual', 10)->nullable()->after('id_curso');
        });

        // 3. Asignar valores temporales a los IDs manuales (puedes personalizarlos luego)
        DB::statement("UPDATE cursos SET id_curso_manual = LPAD(id_curso::text, 5, '1')");

        // 4. Eliminar clave primaria actual
        DB::statement("ALTER TABLE cursos DROP CONSTRAINT cursos_pkey");

        // 5. Eliminar columna id_curso y renombrar la nueva
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('id_curso');
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->renameColumn('id_curso_manual', 'id_curso');
        });

        // 6. Declarar nueva clave primaria
        Schema::table('cursos', function (Blueprint $table) {
            $table->primary('id_curso');
        });

        // 7. Cambiar tipo de columna id_curso en notas (de bigint a varchar)
        DB::statement("ALTER TABLE notas ALTER COLUMN id_curso TYPE VARCHAR(10)");

        // 8. Restaurar la clave foránea entre notas y cursos
        Schema::table('notas', function (Blueprint $table) {
            $table->foreign('id_curso')->references('id_curso')->on('cursos');
        });
    }

    public function down(): void {
        // Revertir FK en notas
        Schema::table('notas', function (Blueprint $table) {
            $table->dropForeign(['id_curso']);
        });

        // Eliminar clave primaria nueva
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropPrimary();
        });

        // Eliminar columna nueva y restaurar la original
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('id_curso');
            $table->bigIncrements('id_curso');
        });

        // Restaurar columna id_curso en notas a bigint
        DB::statement("ALTER TABLE notas ALTER COLUMN id_curso TYPE BIGINT");

        // Restaurar FK
        Schema::table('notas', function (Blueprint $table) {
            $table->foreign('id_curso')->references('id_curso')->on('cursos');
        });
    }
};
