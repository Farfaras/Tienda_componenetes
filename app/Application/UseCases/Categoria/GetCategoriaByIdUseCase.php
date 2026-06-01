<?php

namespace App\Application\UseCases\Categoria;

use App\Domain\Repositories\CategoriaRepositoryInterface;

class GetCategoriaByIdUseCase
{
    public function __construct(
        private CategoriaRepositoryInterface $categoriaRepository
    ) {}

    public function execute(int $id)
    {
        return $this->categoriaRepository->findById($id);
    }
}