<?php

namespace App\Domain\Repositories;

use App\Models\DocumentoComercial;

interface VentaRepositoryInterface
{
    public function getAll();
    public function findById(int $id): ?DocumentoComercial;
    public function create(array $data): DocumentoComercial;
    public function delete(int $id): bool;
}