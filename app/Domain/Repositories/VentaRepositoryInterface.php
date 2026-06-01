<?php

namespace App\Domain\Repositories;

use App\Models\DocumentoComercial;

interface VentaRepositoryInterface
{
    public function getAll();
    public function getAnuladas();  
    public function findById(int $id): ?DocumentoComercial;
    public function create(array $data): DocumentoComercial;
    public function delete(int $id): bool;
    public function countActivas(): int; 
    public function countAnuladas(): int;
    public function getVentasSemanales(): array;
    public function getVentasUltimos6Meses(): array;
    public function getTopProductosMasVendidos(int $limite = 5): array;
    public function getEstadisticasActivasVsAnuladas(): array;
    
}