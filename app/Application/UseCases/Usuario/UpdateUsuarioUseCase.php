<?php

namespace App\Application\UseCases\Usuario;

use App\Application\DTOs\Usuario\UpdateUsuarioDTO;
use App\Domain\Repositories\UsuarioRepositoryInterface;

class UpdateUsuarioUseCase
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function execute(int $id, UpdateUsuarioDTO $dto)
    {
        // El repositorio ya maneja el hash del password
        return $this->usuarioRepository->update($id, $dto->toArray());
    }
}