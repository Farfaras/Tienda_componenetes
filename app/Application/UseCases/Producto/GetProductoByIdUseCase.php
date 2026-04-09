<?php

namespace App\Application\UseCases\Producto;

use App\Domain\Repositories\ProductoRepositoryInterface;

class GetProductoByIdUseCase
{
    public function __construct(
        private ProductoRepositoryInterface $productoRepository
    ) {}

    public function execute(int $id)
    {
        return $this->productoRepository->findById($id);
    }
}