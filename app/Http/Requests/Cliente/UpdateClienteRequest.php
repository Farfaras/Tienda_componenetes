<?php

namespace App\Http\Requests\Cliente;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $idCliente = $this->route('cliente');

        return [
            'ci' => [
                'required',
                'string',
                'max:20',
                Rule::unique('clientes', 'ci')->ignore($idCliente, 'id_cliente'),
            ],
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'estado' => 'required|boolean',
        ];
    }
}