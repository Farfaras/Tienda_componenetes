<?php

namespace App\Application\DTOs\Venta;

class CreateVentaDTO
{
    public function __construct(
        public readonly string $fecha,
        public readonly int $id_cliente,
        public readonly int $id_usuario,
        public readonly array $detalles
    ) {}

    public function toArray(): array
    {
        return [
            'fecha' => $this->fecha,
            'id_cliente' => $this->id_cliente,
            'id_usuario' => $this->id_usuario,
            'detalles' => $this->detalles,
        ];
    }
}