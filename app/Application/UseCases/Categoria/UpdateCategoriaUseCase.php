<?php

namespace App\Application\UseCases\Categoria;

use App\Application\DTOs\Categoria\UpdateCategoriaDTO;
use App\Domain\Repositories\CategoriaRepositoryInterface;

class UpdateCategoriaUseCase
{
    public function __construct(
        private CategoriaRepositoryInterface $categoriaRepository
    ) {}

    public function execute(int $id, UpdateCategoriaDTO $dto)
    {
        return $this->categoriaRepository->update($id, $dto->toArray());
    }
}