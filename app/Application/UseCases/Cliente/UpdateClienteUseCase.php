<?php

namespace App\Application\UseCases\Cliente;

use App\Application\DTOs\Cliente\UpdateClienteDTO;
use App\Domain\Repositories\ClienteRepositoryInterface;

class UpdateClienteUseCase
{
    public function __construct(
        private ClienteRepositoryInterface $clienteRepository
    ) {}

    public function execute(int $id, UpdateClienteDTO $dto)
    {
        return $this->clienteRepository->update($id, $dto->toArray());
    }
}