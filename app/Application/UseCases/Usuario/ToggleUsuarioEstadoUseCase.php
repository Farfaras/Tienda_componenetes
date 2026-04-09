<?php

namespace App\Application\UseCases\Usuario;

use App\Domain\Repositories\UsuarioRepositoryInterface;

class ToggleUsuarioEstadoUseCase
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function execute(int $id, bool $estado)
    {
        return $this->usuarioRepository->toggleEstado($id, $estado);
    }
}