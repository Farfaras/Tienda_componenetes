<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->bigIncrements('id_producto');
            $table->string('modelo', 100)->nullable();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->boolean('estado')->default(true);

            $table->unsignedBigInteger('id_categoria');
            $table->unsignedBigInteger('id_marca');

            $table->foreign('id_categoria')

            
                ->references('id_categoria')
                ->on('categorias')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('id_marca')
                ->references('id_marca')
                ->on('marcas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
