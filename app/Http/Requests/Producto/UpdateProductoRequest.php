<?php

namespace App\Http\Requests\Producto;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modelo' => 'required|string|max:100',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|mimes:png,jpg,jpeg,tiff|max:2048',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'estado' => 'required|boolean',
            'id_categoria' => 'required|integer|exists:categorias,id_categoria',
            'id_marca' => 'required|integer|exists:marcas,id_marca',
        ];
    }

    public function messages(): array
    {
        return [
            'imagen.image' => 'El archivo debe ser una imagen',
            'imagen.mimes' => 'La imagen debe ser de tipo: png, jpg, jpeg, tiff',
            'imagen.max' => 'La imagen no debe pesar más de 2MB',
        ];
    }
}