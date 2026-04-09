<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
        $table->bigIncrements('id_usuario');

        $table->string('nombre', 100);
        $table->string('apellido', 100);

        $table->string('email', 150)->unique();
        $table->timestamp('email_verified_at')->nullable();

        $table->string('password');

        $table->string('direccion', 255)->nullable();

        $table->boolean('estado')->default(true);

        $table->text('two_factor_secret')->nullable();
        $table->text('two_factor_recovery_codes')->nullable();
        $table->boolean('two_factor_confirmed')->default(false);

        $table->unsignedBigInteger('id_rol');

        $table->foreign('id_rol')
            ->references('id_rol')
            ->on('roles')
            ->cascadeOnUpdate()
            ->restrictOnDelete();

        $table->rememberToken();

        $table->timestamps();
    });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};