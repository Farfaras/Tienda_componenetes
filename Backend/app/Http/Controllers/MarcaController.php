<?php

namespace App\Http\Controllers;

use App\Application\DTOs\Marca\CreateMarcaDTO;
use App\Application\DTOs\Marca\UpdateMarcaDTO;
use App\Application\UseCases\Marca\CreateMarcaUseCase;
use App\Application\UseCases\Marca\DeleteMarcaUseCase;
use App\Application\UseCases\Marca\GetAllMarcasUseCase;
use App\Application\UseCases\Marca\GetMarcaByIdUseCase;
use App\Application\UseCases\Marca\UpdateMarcaUseCase;
use App\Http\Requests\Marca\StoreMarcaRequest;
use App\Http\Requests\Marca\UpdateMarcaRequest;
use Illuminate\Http\JsonResponse;

class MarcaController extends Controller
{
    public function index(GetAllMarcasUseCase $useCase): JsonResponse
    {
        $marcas = $useCase->execute();

        return response()->json($marcas);
    }

    public function show(int $id, GetMarcaByIdUseCase $useCase): JsonResponse
    {
        $marca = $useCase->execute($id);

        if (!$marca) {
            return response()->json([
                'message' => 'Marca no encontrada'
            ], 404);
        }

        return response()->json($marca);
    }

    public function store(StoreMarcaRequest $request, CreateMarcaUseCase $useCase): JsonResponse
    {
        $dto = new CreateMarcaDTO(
            nombre: $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            estado: (bool) $request->input('estado')
        );

        $marca = $useCase->execute($dto);

        return response()->json([
            'message' => 'Marca creada correctamente',
            'data' => $marca
        ], 201);
    }

    public function update(int $id, UpdateMarcaRequest $request, UpdateMarcaUseCase $useCase): JsonResponse
    {
        $dto = new UpdateMarcaDTO(
            nombre: $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            estado: (bool) $request->input('estado')
        );

        $marca = $useCase->execute($id, $dto);

        if (!$marca) {
            return response()->json([
                'message' => 'Marca no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Marca actualizada correctamente',
            'data' => $marca
        ]);
    }

    public function destroy(int $id, DeleteMarcaUseCase $useCase): JsonResponse
    {
        $deleted = $useCase->execute($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Marca no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Marca eliminada correctamente'
        ]);
    }
}