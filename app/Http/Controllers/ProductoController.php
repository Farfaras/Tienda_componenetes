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
use App\Infrastructure\Services\ImageUploadService;
use Illuminate\Http\JsonResponse;

class ProductoController extends Controller
{
    public function __construct(
        private ImageUploadService $imageUploadService
    ) {}

    public function index(GetAllProductosUseCase $useCase): JsonResponse
    {
        $productos = $useCase->execute();
        
        // Transformar para incluir URL de imagen
        $productos->transform(function ($producto) {
            $producto->imagen_url = $producto->imagen_url;
            return $producto;
        });

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

        $producto->imagen_url = $producto->imagen_url;

        return response()->json($producto);
    }

    public function store(StoreProductoRequest $request, CreateProductoUseCase $useCase): JsonResponse
    {
        // Subir imagen si existe
        $imagenPath = null;
        if ($request->hasFile('imagen')) {
            $imagenPath = $this->imageUploadService->upload($request->file('imagen'));
        }

        $dto = new CreateProductoDTO(
            modelo: $request->string('modelo')->toString(),
            nombre: $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            imagen: $imagenPath,
            precio: (float) $request->input('precio'),
            stock: (int) $request->input('stock'),
            estado: (bool) $request->input('estado'),
            id_categoria: (int) $request->input('id_categoria'),
            id_marca: (int) $request->input('id_marca')
        );

        $producto = $useCase->execute($dto);
        $producto->imagen_url = $producto->imagen_url;

        return response()->json([
            'message' => 'Producto creado correctamente',
            'data' => $producto
        ], 201);
    }

    public function update(int $id, UpdateProductoRequest $request, UpdateProductoUseCase $useCase): JsonResponse
    {
        // Obtener producto actual para manejar imagen
        $getProductoUseCase = app(GetProductoByIdUseCase::class);
        $productoActual = $getProductoUseCase->execute($id);

        if (!$productoActual) {
            return response()->json([
                'message' => 'Producto no encontrado'
            ], 404);
        }

        // Subir nueva imagen si existe
        $imagenPath = $productoActual->imagen;
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior
            if ($productoActual->imagen) {
                $this->imageUploadService->delete($productoActual->imagen);
            }
            $imagenPath = $this->imageUploadService->upload($request->file('imagen'));
        }

        $dto = new UpdateProductoDTO(
            modelo: $request->string('modelo')->toString(),
            nombre: $request->string('nombre')->toString(),
            descripcion: $request->input('descripcion'),
            imagen: $imagenPath,
            precio: (float) $request->input('precio'),
            stock: (int) $request->input('stock'),
            estado: (bool) $request->input('estado'),
            id_categoria: (int) $request->input('id_categoria'),
            id_marca: (int) $request->input('id_marca')
        );

        $producto = $useCase->execute($id, $dto);
        $producto->imagen_url = $producto->imagen_url;

        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'data' => $producto
        ]);
    }

    public function destroy(int $id, DeleteProductoUseCase $useCase): JsonResponse
    {
        // Obtener producto para eliminar su imagen
        $getProductoUseCase = app(GetProductoByIdUseCase::class);
        $producto = $getProductoUseCase->execute($id);

        if ($producto && $producto->imagen) {
            $this->imageUploadService->delete($producto->imagen);
        }

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