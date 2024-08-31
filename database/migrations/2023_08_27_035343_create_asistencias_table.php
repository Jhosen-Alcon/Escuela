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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('clase_id')->unsigned();
            $table->integer('estudiante_id')->unsigned();
            $table->char('tipo', 1);
            $table->text('detalle')->nullable();
            $table->timestamps();

            $table->foreign('clase_id')->references('id')->on('clases');
            $table->foreign('estudiante_id')->references('id')->on('estudiantes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
