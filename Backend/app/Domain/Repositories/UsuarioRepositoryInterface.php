<?php

namespace App\Domain\Repositories;

use App\Models\Usuario;

interface UsuarioRepositoryInterface
{
    public function getAll();
    public function findById(int $id): ?Usuario;
    public function create(array $data): Usuario;
    public function update(int $id, array $data): ?Usuario;
    public function delete(int $id): bool;
    public function toggleEstado(int $id, bool $estado): ?Usuario;
}