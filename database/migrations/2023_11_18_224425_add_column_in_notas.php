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
        Schema::table('notas', function (Blueprint $table) {
            $table->decimal('nota_prediccion', 10, 2)->nullable()->after('nota_final');
            $table->char('observacion_prediccion', 1)->nullable()->after('nota_prediccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
