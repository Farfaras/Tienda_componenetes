<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:usuarios,email',
            'password' => 'required|string|min:8|confirmed',
            'direccion' => 'nullable|string|max:255',
            'id_rol' => 'required|integer|exists:roles,id_rol',
        ];
    }
}