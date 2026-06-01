<?php

namespace App\Http\Controllers;

use App\Application\DTOs\Venta\CreateVentaDTO;
use App\Application\UseCases\Venta\CreateVentaUseCase;
use App\Application\UseCases\Venta\DeleteVentaUseCase;
use App\Application\UseCases\Venta\GetAllVentasUseCase;
use App\Application\UseCases\Venta\GetVentaByIdUseCase;
use App\Application\UseCases\Venta\GetVentasAnuladasUseCase;
use App\Application\UseCases\Venta\CountVentasActivasUseCase;
use App\Application\UseCases\Venta\GetVentasSemanalesUseCase;
use App\Application\UseCases\Venta\GetVentasUltimos6MesesUseCase;
use App\Application\UseCases\Venta\GetTopProductosMasVendidosUseCase;
use App\Application\UseCases\Venta\GetEstadisticasActivasVsAnuladasUseCase;
use App\Application\UseCases\Venta\CountVentasInactivasUseCase;
use App\Http\Requests\Venta\StoreVentaRequest;
use Illuminate\Http\JsonResponse;

class VentaController extends Controller
{
    public function index(GetAllVentasUseCase $useCase): JsonResponse
    {
        return response()->json($useCase->execute());
    }

    public function anuladas(GetVentasAnuladasUseCase $useCase): JsonResponse
    {
        return response()->json($useCase->execute());
    }

    public function show(int $id, GetVentaByIdUseCase $useCase): JsonResponse
    {
        $venta = $useCase->execute($id);

        if (!$venta) {
            return response()->json([
                'message' => 'Venta no encontrada'
            ], 404);
        }

        return response()->json($venta);
    }

    public function store(StoreVentaRequest $request, CreateVentaUseCase $useCase): JsonResponse
    {
        try {
            $dto = new CreateVentaDTO(
                fecha: $request->input('fecha'),
                id_cliente: (int) $request->input('id_cliente'),
                id_usuario: (int) $request->input('id_usuario'),
                detalles: $request->input('detalles')
            );

            $venta = $useCase->execute($dto);

            return response()->json([
                'message' => 'Venta creada correctamente',
                'data' => $venta
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(int $id, DeleteVentaUseCase $useCase): JsonResponse
    {
        try {
            $deleted = $useCase->execute($id);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Venta no encontrada'
                ], 404);
            }

            return response()->json([
                'message' => 'Venta anulada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function countActivas(CountVentasActivasUseCase $useCase): JsonResponse
    {
        $total = $useCase->execute();
        
        return response()->json([
            'total_activos' => $total,
            'message' => 'Total de ventas activas obtenido correctamente'
        ]);
    }

    public function countAnuladas(CountVentasInactivasUseCase $useCase): JsonResponse
    {
        $total = $useCase->execute();
        
        return response()->json([
            'total_inactivos' => $total,
            'message' => 'Total de ventas inactivas obtenido correctamente'
        ]);
    }

    public function ventasSemanales(GetVentasSemanalesUseCase $useCase): JsonResponse
    {
        $ventas = $useCase->execute();
        
        return response()->json([
            'data' => $ventas,
            'message' => 'Ventas semanales obtenidas correctamente'
        ]);
    }
    
    public function ventasUltimos6Meses(GetVentasUltimos6MesesUseCase $useCase): JsonResponse
    {
        $ventas = $useCase->execute();
        
        return response()->json([
            'data' => $ventas,
            'message' => 'Ventas de los últimos 6 meses obtenidas correctamente'
        ]);
    }

    public function topProductosMasVendidos(GetTopProductosMasVendidosUseCase $useCase): JsonResponse
    {
        $topProductos = $useCase->execute(5); // Top 5
        
        return response()->json([
            'data' => $topProductos,
            'message' => 'Top productos más vendidos obtenidos correctamente'
        ]);
    }

    public function estadisticasActivasVsAnuladas(GetEstadisticasActivasVsAnuladasUseCase $useCase): JsonResponse
    {
        $estadisticas = $useCase->execute();
        
        return response()->json([
            'data' => $estadisticas,
            'message' => 'Estadísticas de ventas activas vs anuladas obtenidas correctamente'
        ]);
    }


}