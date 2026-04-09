<?php

namespace App\Application\UseCases\Usuario;

use App\Application\DTOs\Usuario\CreateUsuarioDTO;
use App\Domain\Repositories\UsuarioRepositoryInterface;

class CreateUsuarioUseCase
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarioRepository
    ) {}

    public function execute(CreateUsuarioDTO $dto)
    {
        return $this->usuarioRepository->create($dto->toArray());
    }
}