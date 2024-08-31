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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('persona_id')->unsigned()->nullable()->after('remember_token');
            $table->integer('rol_id')->unsigned()->after('persona_id');
            $table->char('state', 1)->default('1')->after('rol_id');

            $table->foreign('persona_id')->references('id')->on('personas');
            $table->foreign('rol_id')->references('id')->on('roles');
        });

        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn('nombre');
        });

        Schema::table('empleados', function (Blueprint $table) {
            $table->dropForeign('empleados_tipo_empleado_id_foreign');
            $table->dropColumn('tipo_empleado_id');
            $table->dropColumn('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
