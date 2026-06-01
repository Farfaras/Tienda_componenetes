<?php

namespace App\Application\UseCases\Venta;

use App\Domain\Repositories\VentaRepositoryInterface;

class GetTopProductosMasVendidosUseCase
{
    public function __construct(
        private VentaRepositoryInterface $ventaRepository
    ) {}

    public function execute(int $limite = 5): array
    {
        return $this->ventaRepository->getTopProductosMasVendidos($limite);
    }
}