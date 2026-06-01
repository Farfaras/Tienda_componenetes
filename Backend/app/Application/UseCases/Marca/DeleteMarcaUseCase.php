<?php

namespace App\Application\UseCases\Marca;

use App\Domain\Repositories\MarcaRepositoryInterface;

class DeleteMarcaUseCase
{
    public function __construct(
        private MarcaRepositoryInterface $marcaRepository
    ) {}

    public function execute(int $id): bool
    {
        return $this->marcaRepository->delete($id);
    }
}