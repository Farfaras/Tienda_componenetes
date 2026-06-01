<?php

namespace App\Application\UseCases\Producto;

use App\Application\DTOs\Producto\UpdateProductoDTO;
use App\Domain\Repositories\ProductoRepositoryInterface;

class UpdateProductoUseCase
{
    public function __construct(
        private ProductoRepositoryInterface $productoRepository
    ) {}

    public function execute(int $id, UpdateProductoDTO $dto)
    {
        return $this->productoRepository->update($id, $dto->toArray());
    }
}