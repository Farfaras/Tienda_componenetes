<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\ClienteRepositoryInterface;
use App\Models\Cliente;

class EloquentClienteRepository implements ClienteRepositoryInterface
{
    public function getAll()
    {
        return Cliente::where('estado', true)
            ->orderBy('id_cliente', 'desc')
            ->get();
    }

    public function findById(int $id): ?Cliente
    {
        return Cliente::where('id_cliente', $id)
            ->where('estado', true)
            ->first();
    }

    public function create(array $data): Cliente
    {
        return Cliente::create($data);
    }

    public function update(int $id, array $data): ?Cliente
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return null;
        }

        $cliente->update($data);

        return $cliente->fresh();
    }

    public function delete(int $id): bool
    {
        $cliente = Cliente::find($id);

        if (!$cliente) {
            return false;
        }

        $cliente->estado = false;
        $cliente->save();

        return true;
    }
    public function countActivos(): int
    {
        return Cliente::where('estado', true)->count();
    }
}