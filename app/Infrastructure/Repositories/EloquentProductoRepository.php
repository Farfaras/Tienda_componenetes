<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\ProductoRepositoryInterface;
use App\Models\Producto;

class EloquentProductoRepository implements ProductoRepositoryInterface
{
    public function getAll()
    {
        return Producto::with(['categoria', 'marca'])
            ->where('estado', true)
            ->orderBy('id_producto', 'desc')
            ->get();
    }

    public function findById(int $id): ?Producto
    {
        return Producto::with(['categoria', 'marca'])
            ->where('id_producto', $id)
            ->where('estado', true)
            ->first();
    }

    public function create(array $data): Producto
    {
        return Producto::create($data);
    }

    public function update(int $id, array $data): ?Producto
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return null;
        }

        $producto->update($data);

        return $producto->fresh(['categoria', 'marca']);
    }

    public function delete(int $id): bool
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return false;
        }

        $producto->estado = false;
        $producto->save();

        return true;
    }
}