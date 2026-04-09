<?php

namespace App\Domain\Repositories;

use App\Models\Categoria;

interface CategoriaRepositoryInterface
{
    public function getAll();
    public function findById(int $id): ?Categoria;
    public function create(array $data): Categoria;
    public function update(int $id, array $data): ?Categoria;
    public function delete(int $id): bool;
}