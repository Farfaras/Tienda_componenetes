<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\CategoriaRepositoryInterface;
use App\Models\Categoria;

class EloquentCategoriaRepository implements CategoriaRepositoryInterface
{
    public function getAll()
    {
        return Categoria::where('estado', true)
        ->orderBy('id_categoria', 'desc')
        ->get();
    }

    public function findById(int $id): ?Categoria
    {
        return Categoria::where('id_categoria', $id)
            ->where('estado', true)
            ->first();
    }

    public function create(array $data): Categoria
    {
        return Categoria::create($data);
    }

    public function update(int $id, array $data): ?Categoria
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return null;
        }

        $categoria->update($data);

        return $categoria->fresh();
    }

    public function delete(int $id): bool
    {
        $categoria = Categoria::find($id);

        if (!$categoria) {
            return false;
        }

        $categoria->estado = false;
        $categoria->save();

        return true;
    }
}