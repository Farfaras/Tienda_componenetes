<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos_comerciales', function (Blueprint $table) {

            // Eliminar unique actual
            $table->dropUnique('documentos_comerciales_nro_documento_unique');
        });

        Schema::table('documentos_comerciales', function (Blueprint $table) {

            // Crear unique compuesto
            $table->unique(
                ['tipo_documento', 'nro_documento'],
                'documentos_tipo_nro_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('documentos_comerciales', function (Blueprint $table) {

            $table->dropUnique('documentos_tipo_nro_unique');
        });

        Schema::table('documentos_comerciales', function (Blueprint $table) {

            $table->unique('nro_documento');
        });
    }
};