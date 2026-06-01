<?php

namespace App\Domain\Repositories;

use App\Models\Producto;

interface ProductoRepositoryInterface
{
    public function getAll();
    public function findById(int $id): ?Producto;
    public function create(array $data): Producto;
    public function update(int $id, array $data): ?Producto;
    public function delete(int $id): bool;
}