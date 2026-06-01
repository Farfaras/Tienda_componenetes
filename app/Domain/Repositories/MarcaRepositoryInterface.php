<?php

namespace App\Domain\Repositories;

use App\Models\Marca;

interface MarcaRepositoryInterface
{
    public function getAll();
    public function findById(int $id): ? Marca;
    public function create(array $data): Marca;
    public function update(int $id, array $data): ?Marca;
    public function delete(int $id): bool;
}