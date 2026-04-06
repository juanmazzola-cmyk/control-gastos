<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class AIAnalysisService
{
    public function analyze(Collection $movements, string $periodLabel): string
    {
        $apiKey = config('services.anthropic.key');

        if (! $apiKey) {
            return 'Configurá tu ANTHROPIC_API_KEY en el archivo .env para activar el análisis IA.';
        }

        if ($movements->isEmpty()) {
            return 'No hay movimientos en el período seleccionado para analizar.';
        }

        $personaA = config('personas.persona_a');
        $personaB = config('personas.persona_b');

        $resumen = $this->buildSummary($movements, $personaA, $personaB);

        $prompt = <<<EOT
Sos un asesor financiero personal. Analiza los siguientes movimientos financieros de una pareja/familia para el período: {$periodLabel}.

{$resumen}

Respondé en español con el siguiente formato exacto:

**Resumen del período:**
[2-3 oraciones describiendo la situación financiera general del período]

**Patrones detectados:**
- [patrón 1]
- [patrón 2]
- [patrón 3 si aplica]

**3 consejos concretos:**
1. [consejo específico y accionable]
2. [consejo específico y accionable]
3. [consejo específico y accionable]

Sé directo, usa números concretos cuando sea posible, y adaptá los consejos a la situación real mostrada en los datos.
EOT;

        try {
            $response = Http::withHeaders([
                'x-api-key'         => $apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type'      => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model'      => 'claude-haiku-4-5-20251001',
                'max_tokens' => 1024,
                'messages'   => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if ($response->successful()) {
                return $response->json('content.0.text', 'No se pudo obtener respuesta.');
            }

            return 'Error al contactar la API de Claude. Verificá tu API key y tu conexión.';
        } catch (\Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }

    private function buildSummary(Collection $movements, string $personaA, string $personaB): string
    {
        $gastos  = $movements->where('type', 'gasto');
        $ingresos = $movements->where('type', 'ingreso');

        $gastosA  = $gastos->where('person', 'persona_a')->sum('amount');
        $gastosB  = $gastos->where('person', 'persona_b')->sum('amount');
        $ingresosA = $ingresos->where('person', 'persona_a')->sum('amount');
        $ingresosB = $ingresos->where('person', 'persona_b')->sum('amount');
        $totalGastos  = $gastosA + $gastosB;
        $totalIngresos = $ingresosA + $ingresosB;

        $categorias = $gastos->groupBy('category')->map(fn ($g) => $g->sum('amount'))->sortDesc();

        $lines = ["=== DATOS FINANCIEROS ==="];
        $lines[] = "Total gastos: $" . number_format($totalGastos, 2);
        $lines[] = "Total ingresos: $" . number_format($totalIngresos, 2);
        $lines[] = "Gastos de {$personaA}: $" . number_format($gastosA, 2);
        $lines[] = "Gastos de {$personaB}: $" . number_format($gastosB, 2);
        $lines[] = "Ingresos de {$personaA}: $" . number_format($ingresosA, 2);
        $lines[] = "Ingresos de {$personaB}: $" . number_format($ingresosB, 2);
        $lines[] = "";
        $lines[] = "=== GASTOS POR CATEGORÍA ===";

        foreach ($categorias as $cat => $monto) {
            $pct = $totalGastos > 0 ? round($monto / $totalGastos * 100) : 0;
            $lines[] = "{$cat}: $" . number_format($monto, 2) . " ({$pct}%)";
        }

        $lines[] = "";
        $lines[] = "=== DETALLE DE MOVIMIENTOS ===";
        foreach ($movements->sortByDesc('date') as $m) {
            $persona = $m->person === 'persona_a' ? $personaA : $personaB;
            $tipo    = $m->type === 'gasto' ? 'GASTO' : 'INGRESO';
            $lines[] = "[{$m->date->format('d/m')}] {$tipo} | {$persona} | {$m->category} | $" . number_format($m->amount, 2) . ($m->description ? " | {$m->description}" : '');
        }

        return implode("\n", $lines);
    }
}
