<?php

namespace App\Application\UseCases\Marca;

use App\Domain\Repositories\MarcaRepositoryInterface;

class GetMarcaByIdUseCase
{
    public function __construct(
        private MarcaRepositoryInterface $marcaRepository
    ) {}

    public function execute(int $id)
    {
        return $this->marcaRepository->findById($id);
    }
}