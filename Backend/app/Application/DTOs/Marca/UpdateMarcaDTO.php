<?php

namespace App\Application\DTOs\Marca;

class UpdateMarcaDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly ?string $descripcion,
        public readonly bool $estado
    ) {}

    public function toArray(): array
    {
        return [
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
        ];
    }
}