<?php

namespace App\Application\DTOs\Cliente;

class CreateClienteDTO
{
    public function __construct(
        public readonly string $ci,
        public readonly string $nombre,
        public readonly string $apellido,
        public readonly ?string $telefono,
        public readonly bool $estado
    ) {}

    public function toArray(): array
    {
        return [
            'ci' => $this->ci,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'telefono' => $this->telefono,
            'estado' => $this->estado,
        ];
    }
}