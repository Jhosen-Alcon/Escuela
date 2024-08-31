<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('niveles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
        });

        DB::table('niveles')->insert([
            ['nombre' => 'Inicial',],
            ['nombre' => 'Primaria'],
            ['nombre' => 'Secundaria'],
        ]);

        Schema::table('grupos', function (Blueprint $table) {
            $table->dropForeign('grupos_estudiante_id_foreign');
            $table->dropColumn('estudiante_id');
            $table->integer('nivel_id')->unsigned();
            $table->foreign('nivel_id')->references('id')->on('niveles');
        });

        Schema::create('grupos_estudiantes', function (Blueprint $table) {
            $table->id();
            $table->integer('grupo_id')->unsigned();
            $table->integer('estudiante_id')->unsigned();
            $table->timestamps();

            $table->foreign('grupo_id')->references('id')->on('grupos');
            $table->foreign('estudiante_id')->references('id')->on('estudiantes');
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->integer('tipo')->unsigned()->nullable()->after('persona_id');
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos_estudiantes');
    }
};
