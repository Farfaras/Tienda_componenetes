<?php

namespace App\Application\UseCases\Cliente;

use App\Domain\Repositories\ClienteRepositoryInterface;

class GetClienteByIdUseCase
{
    public function __construct(
        private ClienteRepositoryInterface $clienteRepository
    ) {}

    public function execute(int $id)
    {
        return $this->clienteRepository->findById($id);
    }
}