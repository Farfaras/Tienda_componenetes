<?php

namespace App\Application\DTOs\Producto;

class CreateProductoDTO
{
    public function __construct(
        public readonly string $modelo,
        public readonly string $nombre,
        public readonly ?string $descripcion,
        public readonly ?string $imagen,
        public readonly float $precio,
        public readonly int $stock,
        public readonly bool $estado,
        public readonly int $id_categoria,
        public readonly int $id_marca
    ) {}

    public function toArray(): array
    {
        return [
            'modelo' => $this->modelo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'imagen' => $this->imagen,
            'precio' => $this->precio,
            'stock' => $this->stock,
            'estado' => $this->estado,
            'id_categoria' => $this->id_categoria,
            'id_marca' => $this->id_marca,
        ];
    }
}