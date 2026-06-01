<?php

namespace App\Application\UseCases\Usuario;

use App\Domain\Repositories\UsuarioRepositoryInterface;

class GetAllUsuariosUseCase
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function execute()
    {
        return $this->usuarioRepository->getAll();
    }
}