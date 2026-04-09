<?php

namespace App\Http\Requests\Usuario;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $idUsuario = $this->route('usuario');

        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('usuarios', 'email')->ignore($idUsuario, 'id_usuario'),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'direccion' => 'nullable|string|max:255',
            'estado' => 'required|boolean',
            'id_rol' => 'required|integer|exists:roles,id_rol',
        ];
    }
}