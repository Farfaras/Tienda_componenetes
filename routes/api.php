<?php

use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\AIAnalysisController;

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

    Route::get('clientes/activos/count', [ClienteController::class, 'countActivos']);
    Route::apiResource('clientes', ClienteController::class);

    Route::get('usuarios/activos/count', [UsuarioController::class, 'countActivos']);
    Route::patch('usuarios/{usuario}/estado', [UsuarioController::class, 'toggleEstado']);
    Route::apiResource('usuarios', UsuarioController::class);
    
    Route::get('ventas/semanales', [VentaController::class, 'ventasSemanales']);
    Route::get('ventas/estadisticas-activas-anuladas', [VentaController::class, 'estadisticasActivasVsAnuladas']);
    Route::get('ventas/ultimos-6-meses', [VentaController::class, 'ventasUltimos6Meses']);
    Route::get('ventas/anuladas', [VentaController::class, 'anuladas']);
    Route::get('ventas/activas/count', [VentaController::class, 'countActivas']);
    Route::get('ventas/anuladas/count', [VentaController::class, 'countAnuladas']);
    Route::get('ventas/top-productos', [VentaController::class, 'topProductosMasVendidos']);
    Route::apiResource('ventas', VentaController::class);

    Route::get('cotizaciones/activas/count', [CotizacionController::class, 'countActivas']); 
    Route::get('cotizaciones/anuladas', [CotizacionController::class, 'anuladas']);
    Route::apiResource('cotizaciones', CotizacionController::class);

    Route::prefix('ai-analysis')->group(function () {
        Route::get('ventas-semanales', [AIAnalysisController::class, 'analizarVentasSemanales']);
        Route::get('top-productos', [AIAnalysisController::class, 'analizarTopProductos']);
        Route::get('comparativa-activas-anuladas', [AIAnalysisController::class, 'analizarComparativaActivasVsAnuladas']);
        Route::get('tendencia-6-meses', [AIAnalysisController::class, 'analizarTendencia6Meses']);
    });
});
