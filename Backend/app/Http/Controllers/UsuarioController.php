<?php

namespace App\Http\Controllers;

use App\Application\DTOs\Usuario\CreateUsuarioDTO;
use App\Application\DTOs\Usuario\UpdateUsuarioDTO;
use App\Application\UseCases\Usuario\CreateUsuarioUseCase;
use App\Application\UseCases\Usuario\DeleteUsuarioUseCase;
use App\Application\UseCases\Usuario\GetAllUsuariosUseCase;
use App\Application\UseCases\Usuario\GetUsuarioByIdUseCase;
use App\Application\UseCases\Usuario\ToggleUsuarioEstadoUseCase;
use App\Application\UseCases\Usuario\UpdateUsuarioUseCase;
use App\Http\Requests\Usuario\StoreUsuarioRequest;
use App\Http\Requests\Usuario\ToggleUsuarioEstadoRequest;
use App\Http\Requests\Usuario\UpdateUsuarioRequest;
use Illuminate\Http\JsonResponse;

class UsuarioController extends Controller
{
    public function index(GetAllUsuariosUseCase $useCase): JsonResponse
    {
        $usuarios = $useCase->execute();

        return response()->json($usuarios);
    }

    public function show(int $id, GetUsuarioByIdUseCase $useCase): JsonResponse
    {
        $usuario = $useCase->execute($id);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json($usuario);
    }

    public function store(StoreUsuarioRequest $request, CreateUsuarioUseCase $useCase): JsonResponse
    {
        $dto = new CreateUsuarioDTO(
            nombre: $request->string('nombre')->toString(),
            apellido: $request->string('apellido')->toString(),
            email: $request->string('email')->toString(),
            password: $request->string('password')->toString(),
            direccion: $request->input('direccion'),
            estado: (bool) $request->input('estado'),
            id_rol: (int) $request->input('id_rol')
        );

        $usuario = $useCase->execute($dto);

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'data' => $usuario
        ], 201);
    }

    public function update(int $id, UpdateUsuarioRequest $request, UpdateUsuarioUseCase $useCase): JsonResponse
    {
        $dto = new UpdateUsuarioDTO(
            nombre: $request->string('nombre')->toString(),
            apellido: $request->string('apellido')->toString(),
            email: $request->string('email')->toString(),
            password: $request->input('password'),
            direccion: $request->input('direccion'),
            estado: (bool) $request->input('estado'),
            id_rol: (int) $request->input('id_rol')
        );

        $usuario = $useCase->execute($id, $dto);

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data' => $usuario
        ]);
    }

    public function destroy(int $id, DeleteUsuarioUseCase $useCase): JsonResponse
    {
        $deleted = $useCase->execute($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Usuario desactivado correctamente'
        ]);
    }

    public function toggleEstado(int $id, ToggleUsuarioEstadoRequest $request, ToggleUsuarioEstadoUseCase $useCase): JsonResponse
    {
        $usuario = $useCase->execute($id, (bool) $request->input('estado'));

        if (!$usuario) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'message' => 'Estado del usuario actualizado correctamente',
            'data' => $usuario
        ]);
    }
}