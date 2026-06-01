<?php

namespace App\Application\UseCases\Cliente;

use App\Domain\Repositories\ClienteRepositoryInterface;

class DeleteClienteUseCase
{
    public function __construct(
        private ClienteRepositoryInterface $clienteRepository
    ) {}

    public function execute(int $id): bool
    {
        return $this->clienteRepository->delete($id);
    }
}