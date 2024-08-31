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
        Schema::table('asistencias', function (Blueprint $table) {
            $table->decimal('nota', 10, 2)->nullable()->after('tipo');
            $table->char('observacion', 1)->nullable()->after('nota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
