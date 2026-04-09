<?php

namespace App\Application\UseCases\Producto;

use App\Application\DTOs\Producto\CreateProductoDTO;
use App\Domain\Repositories\ProductoRepositoryInterface;

class CreateProductoUseCase
{
    public function __construct(
        private ProductoRepositoryInterface $productoRepository
    ) {}

    public function execute(CreateProductoDTO $dto)
    {
        return $this->productoRepository->create($dto->toArray());
    }
}