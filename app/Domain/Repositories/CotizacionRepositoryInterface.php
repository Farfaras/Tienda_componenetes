<?php

namespace App\Domain\Repositories;

use App\Models\DocumentoComercial;

interface CotizacionRepositoryInterface
{
    public function getAll();
    public function getAnuladas();  
    public function findById(int $id): ?DocumentoComercial;
    public function create(array $data): DocumentoComercial;
    public function delete(int $id): bool;
    public function countActivas(): int;  
}