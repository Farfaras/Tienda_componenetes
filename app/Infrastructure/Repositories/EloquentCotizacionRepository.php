<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\CotizacionRepositoryInterface;
use App\Models\DetalleDocumento;
use App\Models\DocumentoComercial;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Para registrar errores

class EloquentCotizacionRepository implements CotizacionRepositoryInterface
{
    public function getAll()
    {
        return DocumentoComercial::with(['cliente', 'usuario', 'detalles.producto'])
            ->where('tipo_documento', 'cotizacion')
            ->orderBy('id_documento', 'desc')
            ->get();
    }

    public function getAnuladas()
    {
        return DocumentoComercial::with(['cliente', 'usuario', 'detalles.producto'])
            ->where('tipo_documento', 'cotizacion')
            ->where('estado', false)
            ->orderBy('id_documento', 'desc')
            ->get();
    }

    public function findById(int $id): ?DocumentoComercial
    {
        return DocumentoComercial::with(['cliente', 'usuario', 'detalles.producto'])
            ->where('tipo_documento', 'cotizacion')
            ->where('id_documento', $id)
            ->first();
    }

    public function create(array $data): DocumentoComercial
    {
        return DB::transaction(function () use ($data) {
            $total = 0;
            
            // Generar número único con reintentos
            $nroDocumento = $this->generarNroDocumentoUnico();
            
            $documento = DocumentoComercial::create([
                'nro_documento'   => $nroDocumento,
                'fecha'           => $data['fecha'],
                'fecha_vigencia'  => $data['fecha_vigencia'],
                'tipo_documento'  => 'cotizacion',
                'total'           => 0,
                'estado'          => true,
                'id_cliente'      => $data['id_cliente'],
                'id_usuario'      => $data['id_usuario'],
            ]);

            foreach ($data['detalles'] as $detalle) {
                $producto = Producto::find($detalle['id_producto']);

                if (!$producto) {
                    throw new \Exception('Producto no encontrado: ' . $detalle['id_producto']);
                }

                if (!$producto->estado) {
                    throw new \Exception('El producto está inactivo: ' . $producto->nombre);
                }

                $precioUnitario = (float) $detalle['precio_unitario'];
                $cantidad = (int) $detalle['cantidad'];
                $descuento = (float) ($detalle['descuento'] ?? 0);
                $subtotal = ($precioUnitario * $cantidad) - $descuento;

                if ($subtotal < 0) {
                    throw new \Exception('El subtotal no puede ser negativo para el producto: ' . $producto->nombre);
                }

                DetalleDocumento::create([
                    'id_documento'    => $documento->id_documento,
                    'id_producto'     => $producto->id_producto,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento'       => $descuento,
                    'subtotal'        => $subtotal,
                ]);

                $total += $subtotal;
            }

            $documento->update([
                'total' => $total,
            ]);

            return $documento->fresh(['cliente', 'usuario', 'detalles.producto']);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $documento = DocumentoComercial::where('tipo_documento', 'cotizacion')
                ->find($id);

            if (!$documento) {
                return false;
            }

            if (!$documento->estado) {
                return true;
            }

            $documento->update([
                'estado' => false,
            ]);

            return true;
        });
    }

    /**
     * Genera un número de cotización único con reintentos
     */
    private function generarNroDocumentoUnico(): int
    {
        $ultimoDocumento = DocumentoComercial::where('tipo_documento', 'cotizacion')
            ->orderByDesc('nro_documento')
            ->lockForUpdate()
            ->first();

        $ultimo = (int) ($ultimoDocumento?->nro_documento ?? 0);

        return $ultimo + 1;
    }
    

    public function countActivas(): int
    {
        return DocumentoComercial::where('tipo_documento', 'cotizacion')
            ->where('estado', true)
            ->count();
    }

    /**
     * Método original (lo dejo pero ya no se usa)
     */ 
    private function generarNroDocumentoCotizacion(): int
    {
        $ultimoDocumento = DocumentoComercial::orderByDesc('nro_documento')
            ->lockForUpdate()
            ->first();

        return ((int) ($ultimoDocumento?->nro_documento ?? 0)) + 1;
    }
}