<?php

namespace App\Application\DTOs\Usuario;

class UpdateUsuarioDTO
{
    public function __construct(
        public readonly string $nombre,
        public readonly string $apellido,
        public readonly string $email,
        public readonly ?string $password,
        public readonly ?string $direccion,
        public readonly bool $estado,
        public readonly int $id_rol
    ) {}

    public function toArray(): array
    {
        $data = [
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'email' => $this->email,
            'direccion' => $this->direccion,
            'estado' => $this->estado,
            'id_rol' => $this->id_rol,
        ];

        // Solo incluir password si fue proporcionado
        if ($this->password !== null) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}