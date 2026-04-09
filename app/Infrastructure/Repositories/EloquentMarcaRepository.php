<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\MarcaRepositoryInterface;
use App\Models\Marca;

class EloquentMarcaRepository implements MarcaRepositoryInterface
{
    public function getAll()
    {
        return Marca::where('estado', true)
            ->orderBy('id_marca', 'desc')
            ->get();
    }

    public function findById(int $id): ?Marca
    {
        return Marca::where('id_marca', $id)
            ->where('estado', true)
            ->first();
    }

    public function create(array $data): Marca
    {
        return Marca::create($data);
    }

    public function update(int $id, array $data): ?Marca
    {
        $marca = Marca::find($id);

        if (!$marca) {
            return null;
        }

        $marca->update($data);

        return $marca->fresh();
    }

    public function delete(int $id): bool
    {
        $marca = Marca::find($id);

        if (!$marca) {
            return false;
        }

        $marca->estado = false;
        $marca->save();

        return true;
    }
}