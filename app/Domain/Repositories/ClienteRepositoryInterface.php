<?php

namespace App\Domain\Repositories;

use App\Models\Cliente;

interface ClienteRepositoryInterface
{
    public function getAll();
    public function findById(int $id): ?Cliente;
    public function create(array $data): Cliente;
    public function update(int $id, array $data): ?Cliente;
    public function delete(int $id): bool;
    public function countActivos(): int;
}