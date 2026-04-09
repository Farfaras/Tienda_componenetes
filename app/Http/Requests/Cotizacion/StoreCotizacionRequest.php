<?php

namespace App\Http\Requests\Cotizacion;

use Illuminate\Foundation\Http\FormRequest;

class StoreCotizacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha' => 'required|date',
            'fecha_vigencia' => 'required|date|after:today',
            'id_cliente' => 'required|integer|exists:clientes,id_cliente',
            'id_usuario' => 'required|integer|exists:usuarios,id_usuario',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_vigencia.after' => 'La fecha de vigencia debe ser posterior al día de hoy.',
        ];
    }
}