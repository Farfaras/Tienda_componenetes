<?php

namespace App\Http\Controllers;

use App\Application\DTOs\Producto\CreateProductoDTO;
use App\Application\DTOs\Producto\UpdateProductoDTO;
use App\Application\UseCases\Producto\CreateProductoUseCase;
use App\Application\UseCases\Producto\DeleteProductoUseCase;
use App\Application\UseCases\Producto\GetAllProductosUseCase;
use App\Application\UseCases\Producto\GetProductoByIdUseCase;
use App\Application\UseCases\Producto\UpdateProductoUseCase;
use App\Http\Requests\Producto\StoreProductoRequest;
use App\Http\Requests\Producto\UpdateProductoRequest;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    public function index(GetAllProductosUseCase $useCase): JsonResponse
    {
        $productos = $useCase->execute();

        return response()->json($productos);
    }

    public function show(int $id, GetProductoByIdUseCase $useCase): JsonResponse
    {
        $producto = $useCase->execute($id);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json($producto);
    }

    public function store(StoreProductoRequest $request, CreateProductoUseCase $useCase): JsonResponse
    {
        $dto = new CreateProductoDTO(
            modelo: $request->string('modelo')->toString(),
            nombre: $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            precio: (float) $request->input('precio'),
            stock: (int) $request->input('stock'),
            estado: (bool) $request->input('estado'),
            id_categoria: (int) $request->input('id_categoria'),
            id_marca: (int) $request->input('id_marca')
        );

        $producto = $useCase->execute($dto);

        return response()->json([
            'message' => 'Producto creado correctamente',
            'data' => $producto
        ], 201);
    }

    public function update(int $id, UpdateProductoRequest $request, UpdateProductoUseCase $useCase): JsonResponse
    {
        $dto = new UpdateProductoDTO(
            modelo: $request->string('modelo')->toString(),
            nombre: $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            precio: (float) $request->input('precio'),
            stock: (int) $request->input('stock'),
            estado: (bool) $request->input('estado'),
            id_categoria: (int) $request->input('id_categoria'),
            id_marca: (int) $request->input('id_marca')
        );

        $producto = $useCase->execute($id, $dto);

        if (!$producto) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'data' => $producto
        ]);
    }

    public function destroy(int $id, DeleteProductoUseCase $useCase): JsonResponse
    {
        $deleted = $useCase->execute($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Producto eliminado correctamente'
        ]);
    }
}