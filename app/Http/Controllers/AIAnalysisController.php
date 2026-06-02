<?php

namespace App\Http\Controllers;

use App\Infrastructure\Services\GeminiAIService;
use App\Application\UseCases\Venta\GetVentasSemanalesUseCase;
use App\Application\UseCases\Venta\GetVentasUltimos6MesesUseCase;
use App\Application\UseCases\Venta\GetTopProductosMasVendidosUseCase;
use App\Application\UseCases\Venta\GetEstadisticasActivasVsAnuladasUseCase;
use Illuminate\Http\JsonResponse;

class AIAnalysisController extends Controller
{
    public function __construct(
        private GeminiAIService $geminiService,
        private GetVentasSemanalesUseCase $ventasSemanalesUseCase,
        private GetVentasUltimos6MesesUseCase $ventasUltimos6MesesUseCase,
        private GetTopProductosMasVendidosUseCase $topProductosUseCase,
        private GetEstadisticasActivasVsAnuladasUseCase $estadisticasUseCase
    ) {}

    /**
     * Analizar ventas semanales con IA
     */
    public function analizarVentasSemanales(): JsonResponse
    {
        try {
            $datos = $this->ventasSemanalesUseCase->execute();
            
            if (empty($datos) || $this->tieneDatosVacios($datos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay suficientes datos de ventas semanales para analizar',
                    'analysis' => null
                ]);
            }
            
            $analysis = $this->geminiService->analizarVentasSemanales($datos);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'data' => $datos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar análisis: ' . $e->getMessage(),
                'analysis' => null
            ], 500);
        }
    }

    /**
     * Analizar top productos con IA
     */
    public function analizarTopProductos(): JsonResponse
    {
        try {
            $datos = $this->topProductosUseCase->execute(5);
            
            if (!$datos['tiene_datos']) {
                return response()->json([
                    'success' => false,
                    'message' => $datos['mensaje'],
                    'analysis' => null
                ]);
            }
            
            $analysis = $this->geminiService->analizarTopProductos($datos);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'data' => $datos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar análisis: ' . $e->getMessage(),
                'analysis' => null
            ], 500);
        }
    }

    /**
     * Analizar comparativa activas vs anuladas con IA
     */
    public function analizarComparativaActivasVsAnuladas(): JsonResponse
    {
        try {
            $datos = $this->estadisticasUseCase->execute();
            
            if (!$datos['tiene_datos']) {
                return response()->json([
                    'success' => false,
                    'message' => $datos['mensaje'],
                    'analysis' => null
                ]);
            }
            
            $analysis = $this->geminiService->analizarComparativaActivasVsAnuladas($datos);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'data' => $datos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar análisis: ' . $e->getMessage(),
                'analysis' => null
            ], 500);
        }
    }

    /**
     * Analizar tendencia 6 meses con IA
     */
    public function analizarTendencia6Meses(): JsonResponse
    {
        try {
            $datos = $this->ventasUltimos6MesesUseCase->execute();
            
            if (empty($datos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay datos de tendencia para analizar',
                    'analysis' => null
                ]);
            }
            
            $analysis = $this->geminiService->analizarTendencia6Meses($datos);
            
            return response()->json([
                'success' => true,
                'analysis' => $analysis,
                'data' => $datos
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar análisis: ' . $e->getMessage(),
                'analysis' => null
            ], 500);
        }
    }

    /**
     * Verifica si los datos están vacíos
     */
    private function tieneDatosVacios(array $datos): bool
    {
        $totalVentas = array_sum(array_column($datos, 'total_ventas'));
        return $totalVentas == 0;
    }
}