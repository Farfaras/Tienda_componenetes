<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CotizacionController;

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/register/verify-2fa', [AuthController::class, 'verifyRegister2fa']);
    Route::get('/users/{id}/qr', [AuthController::class, 'showQr']);

    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/login/verify-2fa', [AuthController::class, 'verifyLogin2fa']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('marcas', MarcaController::class);
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('usuarios', UsuarioController::class);
    Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'toggleEstado']);
    Route::apiResource('ventas', VentaController::class);
    Route::apiResource('cotizaciones', CotizacionController::class);
});
