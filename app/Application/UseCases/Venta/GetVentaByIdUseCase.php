<?php

namespace App\Application\UseCases\Venta;

use App\Domain\Repositories\VentaRepositoryInterface;

class GetVentaByIdUseCase
{
    public function __construct(
        private VentaRepositoryInterface $ventaRepository
    ) {}

    public function execute(int $id)
    {
        return $this->ventaRepository->findById($id);
    }
}