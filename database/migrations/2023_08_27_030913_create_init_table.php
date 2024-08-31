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
        Schema::create('tipo_documentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
        });

        DB::table('tipo_documentos')->insert([
            ['nombre' => 'Cedula'],
        ]);

        Schema::create('tipo_empleados', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
            $table->timestamps();
        });

        Schema::create('asignaturas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('periodos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 15);
            $table->string('anio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_documentos');
        Schema::dropIfExists('tipo_empleados');
        Schema::dropIfExists('asignaturas');
        Schema::dropIfExists('periodos');
    }
};
