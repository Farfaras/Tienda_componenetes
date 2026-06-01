<?php

namespace App\Http\Controllers;

use App\Application\DTOs\Categoria\CreateCategoriaDTO;
use App\Application\DTOs\Categoria\UpdateCategoriaDTO;
use App\Application\UseCases\Categoria\CreateCategoriaUseCase;
use App\Application\UseCases\Categoria\DeleteCategoriaUseCase;
use App\Application\UseCases\Categoria\GetAllCategoriasUseCase;
use App\Application\UseCases\Categoria\GetCategoriaByIdUseCase;
use App\Application\UseCases\Categoria\UpdateCategoriaUseCase;
use App\Http\Requests\Categoria\StoreCategoriaRequest;
use App\Http\Requests\Categoria\UpdateCategoriaRequest;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    public function index(GetAllCategoriasUseCase $useCase): JsonResponse
    {
        $categorias = $useCase->execute();

        return response()->json($categorias);
    }

    public function show(int $id, GetCategoriaByIdUseCase $useCase): JsonResponse
    {
        $categoria = $useCase->execute($id);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        return response()->json($categoria);
    }

    public function store(StoreCategoriaRequest $request, CreateCategoriaUseCase $useCase): JsonResponse
    {
        $dto = new CreateCategoriaDTO(
            nombre: $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            estado: (bool) $request->input('estado')
        );

        $categoria = $useCase->execute($dto);

        return response()->json([
            'message' => 'Categoría creada correctamente',
            'data' => $categoria
        ], 201);
    }

    public function update(int $id, UpdateCategoriaRequest $request, UpdateCategoriaUseCase $useCase): JsonResponse
    {
        $dto = new UpdateCategoriaDTO(
            nombre: $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            estado: (bool) $request->input('estado')
        );

        $categoria = $useCase->execute($id, $dto);

        if (!$categoria) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Categoría actualizada correctamente',
            'data' => $categoria
        ]);
    }

    public function destroy(int $id, DeleteCategoriaUseCase $useCase): JsonResponse
    {
        $deleted = $useCase->execute($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Categoría no encontrada'
            ], 404);
        }

        return response()->json([
            'message' => 'Categoría eliminada correctamente'
        ]);
    }
}