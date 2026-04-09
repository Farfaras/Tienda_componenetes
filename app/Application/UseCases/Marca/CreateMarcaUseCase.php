<?php

namespace App\Application\UseCases\Marca;

use App\Application\DTOs\Marca\CreateMarcaDTO;
use App\Domain\Repositories\MarcaRepositoryInterface;

class CreateMarcaUseCase
{
    public function __construct(
        private MarcaRepositoryInterface $marcaRepository
    ) {}

    public function execute(CreateMarcaDTO $dto)
    {
        return $this->marcaRepository->create($dto->toArray());
    }
}