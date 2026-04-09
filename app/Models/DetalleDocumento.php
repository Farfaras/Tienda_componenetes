<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleDocumento extends Model
{
    use HasFactory;

    protected $table = 'detalle_documento';
    protected $primaryKey = 'id_detalle';

    protected $fillable = [
        'id_documento',
        'id_producto',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function documento()
    {
        return $this->belongsTo(DocumentoComercial::class, 'id_documento', 'id_documento');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }
}