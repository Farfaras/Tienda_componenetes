<?php

namespace App\Application\UseCases\Categoria;

use App\Application\DTOs\Categoria\CreateCategoriaDTO;
use App\Domain\Repositories\CategoriaRepositoryInterface;

class CreateCategoriaUseCase
{
    public function __construct(
        private CategoriaRepositoryInterface $categoriaRepository
    ) {}

    public function execute(CreateCategoriaDTO $dto)
    {
        return $this->categoriaRepository->create($dto->toArray());
    }
}