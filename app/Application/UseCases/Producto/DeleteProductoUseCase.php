<?php

namespace App\Application\UseCases\Producto;

use App\Domain\Repositories\ProductoRepositoryInterface;

class DeleteProductoUseCase
{
    public function __construct(
        private ProductoRepositoryInterface $productoRepository
    ) {}

    public function execute(int $id): bool
    {
        return $this->productoRepository->delete($id);
    }
}