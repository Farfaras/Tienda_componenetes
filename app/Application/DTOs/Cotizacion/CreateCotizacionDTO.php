<?php

namespace App\Application\DTOs\Cotizacion;

class CreateCotizacionDTO
{
    public function __construct(
        public readonly string $fecha,
        public readonly string $fecha_vigencia,
        public readonly int $id_cliente,
        public readonly int $id_usuario,
        public readonly array $detalles
    ) {}

    public function toArray(): array
    {
        return [
            'fecha' => $this->fecha,
            'fecha_vigencia' => $this->fecha_vigencia,
            'id_cliente' => $this->id_cliente,
            'id_usuario' => $this->id_usuario,
            'detalles' => $this->detalles,
        ];
    }
}