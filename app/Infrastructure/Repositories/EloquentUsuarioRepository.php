<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\UsuarioRepositoryInterface;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class EloquentUsuarioRepository implements UsuarioRepositoryInterface
{
    public function getAll()
    {
        return Usuario::with('rol')
            ->orderBy('id_usuario', 'desc')
            ->get();
    }

    public function findById(int $id): ?Usuario
    {
        return Usuario::with('rol')
            ->where('id_usuario', $id)
            ->first();
    }

    public function create(array $data): Usuario
    {
        $data['password'] = Hash::make($data['password']);

        return Usuario::create($data);
    }

    public function update(int $id, array $data): ?Usuario
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return null;
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $usuario->update($data);

        return $usuario->fresh('rol');
    }

    public function delete(int $id): bool
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return false;
        }

        $usuario->estado = false;
        $usuario->save();

        return true;
    }

    public function toggleEstado(int $id, bool $estado): ?Usuario
    {
        $usuario = Usuario::find($id);

        if (!$usuario) {
            return null;
        }

        $usuario->estado = $estado;
        $usuario->save();

        return $usuario->fresh('rol');
    }
    public function countActivos(): int
    {
        return Usuario::where('estado', true)->count();
    }
}