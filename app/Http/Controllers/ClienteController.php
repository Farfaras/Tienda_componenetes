<?php

namespace App\Http\Controllers;

use App\Application\DTOs\Cliente\CreateClienteDTO;
use App\Application\DTOs\Cliente\UpdateClienteDTO;
use App\Application\UseCases\Cliente\CreateClienteUseCase;
use App\Application\UseCases\Cliente\DeleteClienteUseCase;
use App\Application\UseCases\Cliente\GetAllClientesUseCase;
use App\Application\UseCases\Cliente\GetClienteByIdUseCase;
use App\Application\UseCases\Cliente\UpdateClienteUseCase;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Application\UseCases\Cliente\CountClientesActivosUseCase;
use Illuminate\Http\JsonResponse;


class ClienteController extends Controller
{
    public function index(GetAllClientesUseCase $useCase): JsonResponse
    {
        $clientes = $useCase->execute();

        return response()->json($clientes);
    }

    public function show(int $id, GetClienteByIdUseCase $useCase): JsonResponse
    {
        $cliente = $useCase->execute($id);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return response()->json($cliente);
    }

    public function store(StoreClienteRequest $request, CreateClienteUseCase $useCase): JsonResponse
    {
        $dto = new CreateClienteDTO(
            ci: $request->string('ci')->toString(),
            nombre: $request->string('nombre')->toString(),
            apellido: $request->string('apellido')->toString(),
            telefono: $request->input('telefono'),
            estado: (bool) $request->input('estado')
        );

        $cliente = $useCase->execute($dto);

        return response()->json([
            'message' => 'Cliente creado correctamente',
            'data' => $cliente
        ], 201);
    }

    public function update(int $id, UpdateClienteRequest $request, UpdateClienteUseCase $useCase): JsonResponse
    {
        $dto = new UpdateClienteDTO(
            ci: $request->string('ci')->toString(),
            nombre: $request->string('nombre')->toString(),
            apellido: $request->string('apellido')->toString(),
            telefono: $request->input('telefono'),
            estado: (bool) $request->input('estado')
        );

        $cliente = $useCase->execute($id, $dto);

        if (!$cliente) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Cliente actualizado correctamente',
            'data' => $cliente
        ]);
    }

    public function destroy(int $id, DeleteClienteUseCase $useCase): JsonResponse
    {
        $deleted = $useCase->execute($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Cliente no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Cliente eliminado correctamente'
        ]);
    }
    public function countActivos(CountClientesActivosUseCase $useCase): JsonResponse
    {
        $total = $useCase->execute();
        
        return response()->json([
            'total_activos' => $total,
            'message' => 'Total de clientes activos obtenido correctamente'
        ]);
    }
}