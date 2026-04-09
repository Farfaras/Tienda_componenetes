<?php

namespace App\Application\UseCases\Cliente;

use App\Application\DTOs\Cliente\CreateClienteDTO;
use App\Domain\Repositories\ClienteRepositoryInterface;

class CreateClienteUseCase
{
    public function __construct(
        private ClienteRepositoryInterface $clienteRepository
    ) {}

    public function execute(CreateClienteDTO $dto)
    {
        return $this->clienteRepository->create($dto->toArray());
    }
}