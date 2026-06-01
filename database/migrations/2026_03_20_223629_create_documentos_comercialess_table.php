<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_comerciales', function (Blueprint $table) {
            $table->bigIncrements('id_documento');
            $table->string('nro_documento', 50)->unique();
            $table->dateTime('fecha');
            $table->dateTime('fecha_vigencia')->nullable();

            $table->enum('tipo_documento', [
                'venta',
                'cotizacion',
            ]);

            $table->decimal('total', 10, 2)->default(0);

            $table->boolean('estado')->default(true);

            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_usuario');

            $table->foreign('id_cliente')
                ->references('id_cliente')
                ->on('clientes')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('usuarios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_comerciales');
    }
};