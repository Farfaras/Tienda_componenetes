<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\VentaRepositoryInterface;
use App\Models\DetalleDocumento;
use App\Models\DocumentoComercial;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class EloquentVentaRepository implements VentaRepositoryInterface
{
    public function getAll()
    {
        return DocumentoComercial::with(['cliente', 'usuario', 'detalles.producto'])
            ->where('tipo_documento', 'venta')
            ->orderBy('id_documento', 'desc')
            ->get();
    }

    public function findById(int $id): ?DocumentoComercial
    {
        return DocumentoComercial::with(['cliente', 'usuario', 'detalles.producto'])
            ->where('tipo_documento', 'venta')
            ->where('id_documento', $id)
            ->first();
    }

    public function create(array $data): DocumentoComercial
    {
        return DB::transaction(function () use ($data) {
            $total = 0;
            $nroDocumento = $this->generarNroDocumentoVenta();

            $documento = DocumentoComercial::create([
                'nro_documento' => $nroDocumento,
                'fecha' => $data['fecha'],
                'fecha_vigencia' => null,
                'tipo_documento' => 'venta',
                'total' => 0,
                'estado' => true,
                'id_cliente' => $data['id_cliente'],
                'id_usuario' => $data['id_usuario'],
            ]);

            foreach ($data['detalles'] as $detalle) {
               $producto = Producto::find($detalle['id_producto']);

                if (!$producto) {
                    throw new \Exception('Producto no encontrado: ' . $detalle['id_producto']);
                }

                if (!$producto->estado) {
                    throw new \Exception('El producto está inactivo: ' . $producto->nombre);
                }

                if ($producto->stock < $detalle['cantidad']) {
                    throw new \Exception('Stock insuficiente para el producto: ' . $producto->nombre);
                }

                $precioUnitario = (float) $detalle['precio_unitario'];
                $cantidad = (int) $detalle['cantidad'];
                $descuento = (float) ($detalle['descuento'] ?? 0);
                $subtotal = ($precioUnitario * $cantidad) - $descuento;

                DetalleDocumento::create([
                    'id_documento' => $documento->id_documento,
                    'id_producto' => $producto->id_producto,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'descuento' => $descuento,
                    'subtotal' => $subtotal,
                ]);

                $producto->decrement('stock', $cantidad);

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
            $documento = DocumentoComercial::with('detalles')
                ->where('tipo_documento', 'venta')
                ->find($id);

            if (!$documento) {
                return false;
            }

            if (!$documento->estado) {
                return true;
            }

            foreach ($documento->detalles as $detalle) {
                $producto = Producto::find($detalle->id_producto);
                if ($producto) {
                    $producto->increment('stock', $detalle->cantidad);
                }
            }

            $documento->update([
                'estado' => false,
            ]);

            return true;
        });
    }

    private function generarNroDocumentoVenta(): int
    {
        $ultimo = DocumentoComercial::where('tipo_documento', 'venta')
            ->lockForUpdate()
            ->max('nro_documento');

        return $ultimo ? ((int) $ultimo + 1) : 1;
    }
}