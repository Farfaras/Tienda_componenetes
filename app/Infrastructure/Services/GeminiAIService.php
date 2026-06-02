<?php

namespace App\Infrastructure\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GeminiAIService
{
    private string $apiKey;
    // Usamos el modelo gemini-1.5-flash que es rápido y gratuito en AI Studio
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent';
    
    public function __construct()
    {
        // Jalamos la API Key desde el archivo .env
        $this->apiKey = env('GEMINI_API_KEY', '');
    }
    
    /**
     * Llama a la API de Google Gemini enviando un prompt estructurado.
     */
    private function consultarGemini(string $prompt): ?string
    {
        try {
            if (empty($this->apiKey)) {
                throw new \Exception("La API Key 'GEMINI_API_KEY' no está configurada en el archivo .env");
            }

            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}?key={$this->apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $resultado = $response->json();
                return $resultado['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            throw new \Exception("Google API respondió con código " . $response->status() . ": " . $response->body());

        } catch (Throwable $e) {
            // Registramos el error exacto en storage/logs/laravel.log para que puedas revisarlo
            Log::error("Error de conexión con Gemini API: " . $e->getMessage());
            return null;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos Públicos (Interfazan con el Controlador)
    |--------------------------------------------------------------------------
    */
    
/*
    |--------------------------------------------------------------------------
    | Métodos Públicos (Optimizados para respuestas cortas)
    |--------------------------------------------------------------------------
    */
    
    public function analizarTopProductos(array $datos): string
    {
        $productos = $datos['productos'] ?? [];
        if (empty($productos)) {
            return $this->analizarTopProductosManual($datos);
        }

        $prompt = "Actúa como un analista de datos comerciales. Analiza este JSON de productos más vendidos. " .
                  "Dame un análisis ejecutivo MUY CONCISO (máximo 3 bloques cortos). " .
                  "Resalta el líder en ingresos y 2 recomendaciones súper directas. " .
                  "Ve directo al grano, sin introducciones largas. Usa Markdown y emojis:\n\n" . 
                  json_encode($productos);

        $analisisIA = $this->consultarGemini($prompt);
        return $analisisIA ?? $this->analizarTopProductosManual($datos);
    }
    
    public function analizarVentasSemanales(array $datos): string
    {
        if (empty($datos)) {
            return $this->analizarVentasSemanalesManual($datos);
        }

        $prompt = "Actúa como un analista financiero. Analiza brevemente estas ventas semanales en JSON. " .
                  "Sé ultra directo: indica el día fuerte, el día flojo y 1 sugerencia concreta en una viñeta. " .
                  "Evita rodeos. Usa Markdown y emojis:\n\n" . 
                  json_encode($datos);

        $analisisIA = $this->consultarGemini($prompt);
        return $analisisIA ?? $this->analizarVentasSemanalesManual($datos);
    }
    
    public function analizarComparativaActivasVsAnuladas(array $datos): string
    {
        if (!isset($datos['cantidad'])) {
            return $this->analizarComparativaManual($datos);
        }

        $prompt = "Actúa como un auditor comercial. Analiza este JSON de ventas activas vs anuladas. " .
                  "Dame un diagnóstico resumido de máximo 3 líneas. " .
                  "Si la tasa supera el 10% da una alerta directa, si no, una felicitación breve. Usa Markdown:\n\n" . 
                  json_encode($datos);

        $analisisIA = $this->consultarGemini($prompt);
        return $analisisIA ?? $this->analizarComparativaManual($datos);
    }
    
    public function analizarTendencia6Meses(array $datos): string
    {
        if (empty($datos)) {
            return $this->analizarTendenciaManual($datos);
        }

        $prompt = "Actúa como un consultor de negocios. Analiza este histórico de 6 meses en JSON. " .
                  "Determina de forma muy breve si la tendencia es creciente, decreciente o estable y da 1 sola recomendación clave para el futuro. " .
                  "Sé conciso, tipo resumen para gerencia. Usa Markdown:\n\n" . 
                  json_encode($datos);

        $analisisIA = $this->consultarGemini($prompt);
        return $analisisIA ?? $this->analizarTendenciaManual($datos);
    }
    
    /*
    |--------------------------------------------------------------------------
    | Métodos Privados (Tus algoritmos de Respaldo / Fallback Manual)
    |--------------------------------------------------------------------------
    */
    
    private function analizarTopProductosManual(array $datos): string
    {
        $productos = $datos['productos'] ?? [];
        
        if (empty($productos)) {
            return "📊 No hay productos vendidos aún. ¡Registra tu primera venta!";
        }
        
        $productoTop = $productos[0];
        $totalUnidades = array_sum(array_column($productos, 'total_unidades_vendidas'));
        $porcentajeTop = $totalUnidades > 0 ? round(($productoTop['total_unidades_vendidas'] / $totalUnidades) * 100) : 0;
        
        $analisis = "📈 **ANÁLISIS DE PRODUCTOS MÁS VENDIDOS (Modo Local)**\n\n";
        $analisis .= "🏆 **Producto líder:** {$productoTop['nombre']}\n";
        $analisis .= "   → {$productoTop['total_unidades_vendidas']} unidades vendidas\n";
        $analisis .= "   → Representa el {$porcentajeTop}% del total\n\n";
        
        $analisis .= "💡 **Recomendaciones:**\n";
        $analisis .= "• Mantén stock prioritario del producto líder\n";
        $analisis .= "• Ofrece promociones en productos de menor rotación\n";
        
        return $analisis;
    }
    
    private function analizarVentasSemanalesManual(array $datos): string
    {
        if (empty($datos) || array_sum(array_column($datos, 'total_ventas')) == 0) {
            return "📊 No hay datos de ventas suficientes para analizar.";
        }
        
        $mejorDia = $datos[0];
        $peorDia = $datos[0];
        foreach ($datos as $dia) {
            if ($dia['total_ventas'] > $mejorDia['total_ventas']) $mejorDia = $dia;
            if ($dia['total_ventas'] < $peorDia['total_ventas']) $peorDia = $dia;
        }
        
        $analisis = "📊 **ANÁLISIS DE VENTAS SEMANALES (Modo Local)**\n\n";
        $analisis .= "📈 **Día con más ventas:** {$mejorDia['dia']}\n";
        $analisis .= "   → Bs. " . number_format($mejorDia['total_ventas'], 2) . "\n\n";
        $analisis .= "📉 **Día con menos ventas:** {$peorDia['dia']}\n";
        $analisis .= "   → Bs. " . number_format($peorDia['total_ventas'], 2) . "\n\n";
        $analisis .= "💡 **Recomendación:**\n";
        $analisis .= "• Implementa promociones especiales los {$peorDia['dia']}\n";
        
        return $analisis;
    }
    
    private function analizarComparativaManual(array $datos): string
    {
        if (!isset($datos['cantidad']['total']) || $datos['cantidad']['total'] == 0) {
            return "⚠️ No hay ventas registradas aún.";
        }
        
        $tasa = $datos['metricas']['tasa_anulacion'] ?? 0;
        
        $analisis = "⚠️ **VENTAS ACTIVAS VS ANULADAS (Modo Local)**\n\n";
        $analisis .= "✅ Activas: {$datos['cantidad']['activas']} ventas\n";
        $analisis .= "❌ Anuladas: {$datos['cantidad']['anuladas']} ventas\n";
        $analisis .= "📊 Tasa de anulación: {$tasa}%\n\n";
        
        if ($tasa > 10) {
            $analisis .= "🔴 **ALERTA:** La tasa de anulación es alta (>10%)\n";
            $analisis .= "💡 Revisa stock, precios o atención al cliente\n";
        } else {
            $analisis .= "🟢 **Buen indicador:** Tasa de anulación aceptable\n";
        }
        
        return $analisis;
    }
    
    private function analizarTendenciaManual(array $datos): string
    {
        if (empty($datos)) {
            return "📉 No hay datos históricos suficientes.";
        }
        
        $mesesConVentas = array_filter($datos, function($mes) {
            return ($mes['total_ventas'] ?? 0) > 0;
        });
        
        if (empty($mesesConVentas)) {
            return "📉 No hay ventas registradas en los últimos 6 meses.";
        }
        
        $mesesConVentas = array_values($mesesConVentas);
        
        $primerMes = $mesesConVentas[0];
        $ultimoMes = $mesesConVentas[count($mesesConVentas) - 1];
        
        $ventaPrimerMes = $primerMes['total_ventas'];
        $ventaUltimoMes = $ultimoMes['total_ventas'];
        $variacion = $ventaUltimoMes - $ventaPrimerMes;
        
        $porcentajeVariacion = $ventaPrimerMes > 0 ? round(($variacion / $ventaPrimerMes) * 100) : 0;
        
        if ($ventaUltimoMes > $ventaPrimerMes) {
            $tendencia = "creciente 📈";
            $recomendacion = "• ✅ La tendencia es positiva. Mantén las estrategias actuales\n• 📊 Proyecta un crecimiento del {$porcentajeVariacion}% para los próximos meses";
        } elseif ($ventaUltimoMes < $ventaPrimerMes) {
            $tendencia = "decreciente 📉";
            $recomendacion = "• ⚠️ Las ventas están disminuyendo\n• 🔍 Revisa campañas de marketing y precios\n• 📢 Considera promociones especiales";
        } else {
            $tendencia = "estable ➡️";
            $recomendacion = "• 📊 Las ventas se mantienen constantes\n• 🚀 Busca nuevas estrategias para incrementar ventas";
        }
        
        $mejorMes = $mesesConVentas[0];
        foreach ($mesesConVentas as $mes) {
            if ($mes['total_ventas'] > $mejorMes['total_ventas']) {
                $mejorMes = $mes;
            }
        }
        
        $analisis = "📈 **TENDENCIA DE VENTAS (6 MESES - Modo Local)**\n\n";
        $analisis .= "📅 **Primer mes con ventas:** {$primerMes['mes']}\n";
        $analisis .= "   → Bs. " . number_format($ventaPrimerMes, 2) . "\n\n";
        $analisis .= "📅 **Último mes:** {$ultimoMes['mes']}\n";
        $analisis .= "   → Bs. " . number_format($ventaUltimoMes, 2) . "\n\n";
        $analisis .= "📊 **Tendencia:** {$tendencia}\n";
        $analisis .= "📉 **Variación:** " . ($variacion > 0 ? '+' : '') . "Bs. " . number_format($variacion, 2) . " ({$porcentajeVariacion}%)\n\n";
        $analisis .= "🏆 **Mejor mes:** {$mejorMes['mes']}\n";
        $analisis .= "   → Bs. " . number_format($mejorMes['total_ventas'], 2) . " ({$mejorMes['cantidad_ventas']} ventas)\n\n";
        $analisis .= "💡 **Recomendaciones:**\n{$recomendacion}\n";
        
        return $analisis;
    }
}