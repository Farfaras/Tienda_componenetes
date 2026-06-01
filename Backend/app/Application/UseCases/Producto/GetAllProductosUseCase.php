<?php

namespace App\Application\UseCases\Producto;

use App\Domain\Repositories\ProductoRepositoryInterface;

class GetAllProductosUseCase
{
    public function __construct(
        private ProductoRepositoryInterface $productoRepository
    ) {}

    public function execute()
    {
        return $this->productoRepository->getAll();
    }
}