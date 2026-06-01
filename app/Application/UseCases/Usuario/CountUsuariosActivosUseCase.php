<?php

namespace App\Application\UseCases\Usuario;

use App\Domain\Repositories\UsuarioRepositoryInterface;

class CountUsuariosActivosUseCase
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function execute(): int
    {
        return $this->usuarioRepository->countActivos();
    }
}