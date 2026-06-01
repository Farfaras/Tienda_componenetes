<?php

namespace App\Application\UseCases\Categoria;

use App\Domain\Repositories\CategoriaRepositoryInterface;

class GetAllCategoriasUseCase
{
    public function __construct(
        private CategoriaRepositoryInterface $categoriaRepository
    ) {}

    public function execute()
    {
        return $this->categoriaRepository->getAll();
    }
}