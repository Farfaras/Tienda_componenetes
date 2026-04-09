<?php

namespace App\Application\UseCases\Marca;

use App\Application\DTOs\Marca\UpdateMarcaDTO;
use App\Domain\Repositories\MarcaRepositoryInterface;

class UpdateMarcaUseCase
{
    public function __construct(
        private MarcaRepositoryInterface $marcaRepository
    ) {}

    public function execute(int $id, UpdateMarcaDTO $dto)
    {
        return $this->marcaRepository->update($id, $dto->toArray());
    }
}