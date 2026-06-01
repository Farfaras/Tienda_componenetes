<?php

namespace App\Application\UseCases\Venta;

use App\Domain\Repositories\VentaRepositoryInterface;

class GetVentasAnuladasUseCase
{
    public function __construct(
        private VentaRepositoryInterface $ventaRepository
    ) {}

    public function execute()
    {
        return $this->ventaRepository->getAnuladas();
    }
}