<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_documento', function (Blueprint $table) {
            $table->bigIncrements('id_detalle');

            $table->unsignedBigInteger('id_documento');
            $table->unsignedBigInteger('id_producto');

            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2);

            $table->foreign('id_documento')
                ->references('id_documento')
                ->on('documentos_comerciales')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('productos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_documento');
    }
};