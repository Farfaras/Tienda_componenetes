<?php

namespace App\Http\Controllers;

use App\Application\DTOs\Cotizacion\CreateCotizacionDTO;
use App\Application\UseCases\Cotizacion\CreateCotizacionUseCase;
use App\Application\UseCases\Cotizacion\DeleteCotizacionUseCase;
use App\Application\UseCases\Cotizacion\GetAllCotizacionesUseCase;
use App\Application\UseCases\Cotizacion\GetCotizacionByIdUseCase;
use App\Http\Requests\Cotizacion\StoreCotizacionRequest;
use Illuminate\Http\JsonResponse;

class CotizacionController extends Controller
{
    public function index(GetAllCotizacionesUseCase $useCase): JsonResponse
    {
        return response()->json($useCase->execute());
    }

    public function show(int $id, GetCotizacionByIdUseCase $useCase): JsonResponse
    {
        $cotizacion = $useCase->execute($id);

        if (!$cotizacion) {
            return response()->json([
                'message' => 'Cotización no encontrada'
            ], 404);
        }

        return response()->json($cotizacion);
    }

    public function store(StoreCotizacionRequest $request, CreateCotizacionUseCase $useCase): JsonResponse
    {
        try {
            $dto = new CreateCotizacionDTO(
                fecha: $request->input('fecha'),
                fecha_vigencia: $request->input('fecha_vigencia'),
                id_cliente: (int) $request->input('id_cliente'),
                id_usuario: (int) $request->input('id_usuario'),
                detalles: $request->input('detalles')
            );

            $cotizacion = $useCase->execute($dto);

            return response()->json([
                'message' => 'Cotización creada correctamente',
                'data' => $cotizacion
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy(int $id, DeleteCotizacionUseCase $useCase): JsonResponse
    {
        try {
            $deleted = $useCase->execute($id);

            if (!$deleted) {
                return response()->json([
                    'message' => 'Cotización no encontrada'
                ], 404);
            }

            return response()->json([
                'message' => 'Cotización anulada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
}