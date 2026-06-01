<?php

namespace App\Application\UseCases\Cliente;

use App\Domain\Repositories\ClienteRepositoryInterface;

class CountClientesActivosUseCase
{
    public function __construct(
        private ClienteRepositoryInterface $clienteRepository
    ) {}

    public function execute(): int
    {
        return $this->clienteRepository->countActivos();
    }
}