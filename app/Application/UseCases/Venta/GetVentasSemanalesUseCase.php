<?php

namespace App\Application\UseCases\Venta;

use App\Domain\Repositories\VentaRepositoryInterface;

class GetVentasSemanalesUseCase
{
    public function __construct(
        private VentaRepositoryInterface $ventaRepository
    ) {}

    public function execute(): array
    {
        return $this->ventaRepository->getVentasSemanales();
    }
}