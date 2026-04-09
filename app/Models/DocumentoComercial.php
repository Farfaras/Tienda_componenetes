<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoComercial extends Model
{
    use HasFactory;

    protected $table = 'documentos_comerciales';
    protected $primaryKey = 'id_documento';

    protected $fillable = [
        'nro_documento',
        'fecha',
        'fecha_vigencia',
        'tipo_documento',
        'total',
        'estado',
        'id_cliente',
        'id_usuario',
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'fecha_vigencia' => 'datetime',
        'total' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id_cliente');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleDocumento::class, 'id_documento', 'id_documento');
    }
}