<?php

namespace App\Application\UseCases\Usuario;

use App\Domain\Repositories\UsuarioRepositoryInterface;

class ToggleUsuarioEstadoUseCase
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function execute(int $id, bool $estadoActual)
    {
        // Invertir el estado: si viene true, guardar false; si viene false, guardar true
        $nuevoEstado = !$estadoActual;
        
        return $this->usuarioRepository->toggleEstado($id, $nuevoEstado);
    }
}