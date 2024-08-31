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
        Schema::table('periodos', function (Blueprint $table) {
            $table->dropColumn('anio');
        });

        Schema::table('estudiantes', function (Blueprint $table) {
            $table->dropColumn('estado');
        });

        DB::table('periodos')->insert([
            ['nombre' => '2023-I',],
            ['nombre' => '2023-II'],
            ['nombre' => '2023-III'],
        ]);

        DB::table('asignaturas')->insert([
            ['nombre' => 'Arte',],
            ['nombre' => 'Comunicación'],
            ['nombre' => 'Matemática'],
            ['nombre' => 'Inglés'],
            ['nombre' => 'Ciencia, Tecnología y Ambiente'],
            ['nombre' => 'Historia'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
