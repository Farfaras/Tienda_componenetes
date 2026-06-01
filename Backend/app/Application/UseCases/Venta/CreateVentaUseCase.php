<?php

namespace App\Application\UseCases\Venta;

use App\Application\DTOs\Venta\CreateVentaDTO;
use App\Domain\Repositories\VentaRepositoryInterface;

class CreateVentaUseCase
{
    public function __construct(
        private VentaRepositoryInterface $ventaRepository
    ) {}

    public function execute(CreateVentaDTO $dto)
    {
        return $this->ventaRepository->create($dto->toArray());
    }
}