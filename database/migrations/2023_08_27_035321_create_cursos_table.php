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
        Schema::create('cursos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('asignatura_id')->unsigned();
            $table->integer('grupo_id')->unsigned();
            $table->integer('empleado_id')->unsigned();
            $table->boolean('estado');
            $table->timestamps();

            $table->foreign('asignatura_id')->references('id')->on('asignaturas');
            $table->foreign('grupo_id')->references('id')->on('grupos');
            $table->foreign('empleado_id')->references('id')->on('empleados');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
