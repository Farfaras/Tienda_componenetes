<?php

namespace App\Application\UseCases\Cliente;

use App\Domain\Repositories\ClienteRepositoryInterface;

class GetAllClientesUseCase
{
    public function __construct(
        private ClienteRepositoryInterface $clienteRepository
    ) {}

    public function execute()
    {
        return $this->clienteRepository->getAll();
    }
}