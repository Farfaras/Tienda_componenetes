<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\VentaRepositoryInterface;
use App\Models\DetalleDocumento;
use App\Models\DocumentoComercial;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EloquentVentaRepository implements VentaRepositoryInterface
{
    public function getAll()
    {
        return DocumentoComercial::with(['cliente', 'usuario', 'detalles.producto'])
            ->where('tipo_documento', 'venta')
            ->orderBy('id_documento', 'desc')
            ->get();
    }

    public function getAnuladas()
    {
        return DocumentoComercial::with(['cliente', 'usuario', 'detalles.producto'])
            ->where('tipo_documento', 'venta')
            ->where('estado', false) 
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
            $nroDocumento = $this->generarNroDocumentoUnico();

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

    /**
     * Genera un número de venta único - Versión CAUSA 1 (Funcional)
     */
    private function generarNroDocumentoUnico(): int
    {
        $ultimoDocumento = DocumentoComercial::where('tipo_documento', 'venta')
            ->orderByDesc('nro_documento')
            ->lockForUpdate()
            ->first();

        $ultimo = (int) ($ultimoDocumento?->nro_documento ?? 0);

        return $ultimo + 1;
    }

    public function countActivas(): int
    {
        return DocumentoComercial::where('tipo_documento', 'venta')
            ->where('estado', true)
            ->count();
    }

    public function countAnuladas(): int
    {
        return DocumentoComercial::where('tipo_documento', 'venta')
            ->where('estado', false)
            ->count();
    }

    public function getVentasSemanales(): array
    {
        $diasSemana = [
            1 => 'Lunes',
            2 => 'Martes', 
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
        
        $resultado = [];
        
        // Obtener el lunes de la semana actual
        $lunes = now()->startOfWeek(); // Carbon empieza el lunes
        
        for ($i = 1; $i <= 6; $i++) {
            $fechaInicio = $lunes->copy()->addDays($i - 1)->startOfDay();
            $fechaFin = $lunes->copy()->addDays($i - 1)->endOfDay();
            
            $total = DocumentoComercial::where('tipo_documento', 'venta')
                ->where('estado', true)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->sum('total');
            
            $cantidad = DocumentoComercial::where('tipo_documento', 'venta')
                ->where('estado', true)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->count();
            
            $resultado[] = [
                'dia' => $diasSemana[$i],
                'fecha' => $fechaInicio->format('Y-m-d'),
                'total_ventas' => (float) $total,
                'cantidad_ventas' => $cantidad,
            ];
        }
        
        return $resultado;
    }

    public function getVentasUltimos6Meses(): array
    {
        $resultado = [];
        
        // Obtener los últimos 6 meses (incluyendo el actual)
        for ($i = 5; $i >= 0; $i--) {
            $mes = now()->subMonths($i);
            $nombreMes = $mes->locale('es')->isoFormat('MMMM YYYY');
            
            $fechaInicio = $mes->copy()->startOfMonth()->startOfDay();
            $fechaFin = $mes->copy()->endOfMonth()->endOfDay();
            
            $total = DocumentoComercial::where('tipo_documento', 'venta')
                ->where('estado', true)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->sum('total');
            
            $cantidad = DocumentoComercial::where('tipo_documento', 'venta')
                ->where('estado', true)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->count();
            
            $resultado[] = [
                'mes' => $nombreMes,
                'anio' => $mes->year,
                'mes_numero' => $mes->month,
                'fecha_inicio' => $fechaInicio->format('Y-m-d'),
                'fecha_fin' => $fechaFin->format('Y-m-d'),
                'total_ventas' => (float) $total,
                'cantidad_ventas' => $cantidad,
            ];
        }
        
        return $resultado;
    }

    public function getTopProductosMasVendidos(int $limite = 5): array
    {
        $resultado = [];
        
        $topProductos = DB::table('detalle_documento as dd')
            ->join('documentos_comerciales as dc', 'dd.id_documento', '=', 'dc.id_documento')
            ->join('productos as p', 'dd.id_producto', '=', 'p.id_producto')
            ->where('dc.tipo_documento', 'venta')
            ->where('dc.estado', true)  // Solo ventas activas
            ->where('p.estado', true)   // ← NUEVO: Solo productos activos
            ->select(
                'p.id_producto',
                'p.nombre',
                'p.modelo',
                DB::raw('SUM(dd.cantidad) as total_unidades_vendidas'),
                DB::raw('SUM(dd.subtotal) as total_ingresos'),
                DB::raw('COUNT(DISTINCT dc.id_documento) as numero_ventas')
            )
            ->groupBy('p.id_producto', 'p.nombre', 'p.modelo')
            ->orderBy('total_unidades_vendidas', 'desc')
            ->limit($limite)
            ->get();
        
        // Si no hay productos vendidos, retornar array vacío con mensaje
        if ($topProductos->isEmpty()) {
            return [
                'tiene_datos' => false,
                'mensaje' => 'No hay ventas registradas aún. ¡Vende tu primer producto!',
                'productos' => []
            ];
        }
        
        foreach ($topProductos as $producto) {
            $resultado[] = [
                'id_producto' => $producto->id_producto,
                'nombre' => $producto->nombre,
                'modelo' => $producto->modelo,
                'total_unidades_vendidas' => (int) $producto->total_unidades_vendidas,
                'total_ingresos' => (float) $producto->total_ingresos,
                'numero_ventas' => (int) $producto->numero_ventas,
            ];
        }
        
        return [
            'tiene_datos' => true,
            'mensaje' => 'Top ' . count($resultado) . ' productos más vendidos (activos)',
            'productos' => $resultado
        ];
    }

    public function getEstadisticasActivasVsAnuladas(): array
    {
        // Obtener totales
        $totalActivas = $this->countActivas();
        $totalAnuladas = $this->countAnuladas();
        $totalGeneral = $totalActivas + $totalAnuladas;
        
        // Calcular porcentajes
        $porcentajeActivas = $totalGeneral > 0 ? round(($totalActivas / $totalGeneral) * 100, 2) : 0;
        $porcentajeAnuladas = $totalGeneral > 0 ? round(($totalAnuladas / $totalGeneral) * 100, 2) : 0;
        
        // Obtener montos totales
        $montoActivas = DocumentoComercial::where('tipo_documento', 'venta')
            ->where('estado', true)
            ->sum('total');
        
        $montoAnuladas = DocumentoComercial::where('tipo_documento', 'venta')
            ->where('estado', false)
            ->sum('total');
        
        $montoTotal = $montoActivas + $montoAnuladas;
        
        // Porcentajes por monto
        $porcentajeMontoActivas = $montoTotal > 0 ? round(($montoActivas / $montoTotal) * 100, 2) : 0;
        $porcentajeMontoAnuladas = $montoTotal > 0 ? round(($montoAnuladas / $montoTotal) * 100, 2) : 0;
        
        // Determinar si hay datos
        $tieneDatos = $totalGeneral > 0;
        
        return [
            'tiene_datos' => $tieneDatos,
            'mensaje' => $tieneDatos ? 'Estadísticas obtenidas correctamente' : 'No hay ventas registradas aún',
            
            // Datos para gráfico circular (por cantidad)
            'cantidad' => [
                'activas' => $totalActivas,
                'anuladas' => $totalAnuladas,
                'total' => $totalGeneral,
                'porcentaje_activas' => $porcentajeActivas,
                'porcentaje_anuladas' => $porcentajeAnuladas,
            ],
            
            // Datos para gráfico circular (por monto)
            'monto' => [
                'activas' => (float) $montoActivas,
                'anuladas' => (float) $montoAnuladas,
                'total' => (float) $montoTotal,
                'porcentaje_activas' => $porcentajeMontoActivas,
                'porcentaje_anuladas' => $porcentajeMontoAnuladas,
            ],
            
            // Métricas adicionales útiles
            'metricas' => [
                'tasa_anulacion' => $porcentajeAnuladas, // Porcentaje de ventas anuladas
                'ingreso_promedio_activo' => $totalActivas > 0 ? round($montoActivas / $totalActivas, 2) : 0,
                'ingreso_promedio_anulado' => $totalAnuladas > 0 ? round($montoAnuladas / $totalAnuladas, 2) : 0,
            ]
        ];
    }

    /**
     * Método antiguo (deprecado)
     */
    private function generarNroDocumentoVenta(): int
    {
        $ultimoDocumento = DocumentoComercial::where('tipo_documento', 'venta')
            ->orderByDesc('nro_documento')
            ->lockForUpdate()
            ->first();

        return ($ultimoDocumento?->nro_documento ?? 0) + 1;
    }

}