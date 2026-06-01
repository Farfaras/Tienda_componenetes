<?php

namespace App\Application\UseCases\Categoria;

use App\Domain\Repositories\CategoriaRepositoryInterface;

class DeleteCategoriaUseCase
{
    public function __construct(
        private CategoriaRepositoryInterface $categoriaRepository
    ) {}

    public function execute(int $id): bool
    {
        return $this->categoriaRepository->delete($id);
    }
}