<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\CotizacionRepositoryInterface;
use App\Models\DetalleDocumento;
use App\Models\DocumentoComercial;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $maxAttempts = 3;
        
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                return DB::transaction(function () use ($data) {
                    $total = 0;
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
            } catch (\Illuminate\Database\QueryException $e) {
                // Si es error de duplicado (23505 en PostgreSQL, 1062 en MySQL)
                $isDuplicateError = false;
                
                if (str_contains($e->getMessage(), '23505') || 
                    str_contains($e->getMessage(), '1062') ||
                    str_contains($e->getMessage(), 'duplicate')) {
                    $isDuplicateError = true;
                }
                
                if ($isDuplicateError && $attempt < $maxAttempts) {
                    Log::warning("Número duplicado en cotización, reintentando... Intento {$attempt}/{$maxAttempts}");
                    continue;
                }
                throw $e;
            } catch (\Exception $e) {
                throw $e;
            }
        }
        
        throw new \Exception('No se pudo generar un número único para la cotización después de ' . $maxAttempts . ' intentos');
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
     * Genera un número de cotización único verificando que no exista
     */
    private function generarNroDocumentoUnico(): int
    {
        // Obtener el máximo número actual
        $maxNumber = DocumentoComercial::where('tipo_documento', 'cotizacion')
            ->max('nro_documento');
        
        $nextNumber = $maxNumber ? $maxNumber + 1 : 1;
        
        // Verificar y ajustar si ya existe (por duplicados)
        $exists = DocumentoComercial::where('tipo_documento', 'cotizacion')
            ->where('nro_documento', $nextNumber)
            ->exists();
        
        while ($exists) {
            $nextNumber++;
            $exists = DocumentoComercial::where('tipo_documento', 'cotizacion')
                ->where('nro_documento', $nextNumber)
                ->exists();
        }
        
        Log::info("Número de cotización generado: {$nextNumber}");
        
        return $nextNumber;
    }
    
    public function countActivas(): int
    {
        return DocumentoComercial::where('tipo_documento', 'cotizacion')
            ->where('estado', true)
            ->count();
    }
    
    public function countAnuladas(): int
    {
        return DocumentoComercial::where('tipo_documento', 'cotizacion')
            ->where('estado', false)
            ->count();
    }
}