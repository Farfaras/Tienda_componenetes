<?php

namespace App\Application\UseCases\Marca;

use App\Domain\Repositories\MarcaRepositoryInterface;

class GetAllMarcasUseCase
{
    public function __construct(
        private MarcaRepositoryInterface $marcaRepository
    ) {}

    public function execute()
    {
        return $this->marcaRepository->getAll();
    }
}