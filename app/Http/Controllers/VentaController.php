<?php

namespace App\Http\Controllers;

use App\Application\DTOs\Venta\CreateVentaDTO;
use App\Application\UseCases\Venta\CreateVentaUseCase;
use App\Application\UseCases\Venta\DeleteVentaUseCase;
use App\Application\UseCases\Venta\GetAllVentasUseCase;
use App\Application\UseCases\Venta\GetVentaByIdUseCase;
use App\Http\Requests\Venta\StoreVentaRequest;
use Illuminate\Http\JsonResponse;

class VentaController extends Controller
{
    public function index(GetAllVentasUseCase $useCase): JsonResponse
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
}