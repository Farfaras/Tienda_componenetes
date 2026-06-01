<?php

namespace App\Application\UseCases\Venta;

use App\Domain\Repositories\VentaRepositoryInterface;

class DeleteVentaUseCase
{
    public function __construct(
        private VentaRepositoryInterface $ventaRepository
    ) {}

    public function execute(int $id): bool
    {
        return $this->ventaRepository->delete($id);
    }
}