<?php

namespace App\Application\UseCases\Venta;

use App\Domain\Repositories\VentaRepositoryInterface;

class GetAllVentasUseCase
{
    public function __construct(
        private VentaRepositoryInterface $ventaRepository
    ) {}

    public function execute()
    {
        return $this->ventaRepository->getAll();
    }
}