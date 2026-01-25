<?php

namespace App\Services;

use App\Models\Votante;
use Illuminate\Support\Collection;

/**
 * Servicio de predicción de votos
 * Implementa métodos heurístico y Monte Carlo
 */
class PredictionService
{
    /**
     * Predicción heurística simple
     * Suma las probabilidades de todos los votantes
     *
     * @param Collection|null $votantes
     * @return array
     */
    public function heuristicPrediction(?Collection $votantes = null): array
    {
        if (!$votantes) {
            $votantes = Votante::all();
        }

        $totalVotantes = $votantes->count();
        $votosEstimados = $votantes->sum('probabilidad_voto');

        $porIntencion = [
            'A' => $votantes->where('codigo_intencion', 'A')->count(),
            'B' => $votantes->where('codigo_intencion', 'B')->count(),
            'C' => $votantes->where('codigo_intencion', 'C')->count(),
            'D' => $votantes->where('codigo_intencion', 'D')->count(),
            'E' => $votantes->where('codigo_intencion', 'E')->count(),
        ];

        $votosEstimadosPorIntencion = [
            'A' => $porIntencion['A'] * 1.0,
            'B' => $porIntencion['B'] * 0.7,
            'C' => $porIntencion['C'] * 0.5,
            'D' => $porIntencion['D'] * 0.2,
            'E' => $porIntencion['E'] * 0.0,
        ];

        return [
            'modelo' => 'heuristico',
            'total_votantes' => $totalVotantes,
            'votos_estimados' => round($votosEstimados, 2),
            'por_intencion' => $porIntencion,
            'votos_estimados_por_intencion' => $votosEstimadosPorIntencion,
            'porcentaje_estimado' => $totalVotantes > 0 ? round(($votosEstimados / $totalVotantes) * 100, 2) : 0,
        ];
    }

    /**
     * Predicción Monte Carlo
     * Simula N iteraciones aplicando distribución de Bernoulli
     *
     * @param int $iteraciones
     * @param Collection|null $votantes
     * @return array
     */
    public function monteCarloPrediction(int $iteraciones = 1000, ?Collection $votantes = null): array
    {
        if (!$votantes) {
            $votantes = Votante::all();
        }

        if ($votantes->isEmpty()) {
            return [
                'modelo' => 'montecarlo',
                'iteraciones' => $iteraciones,
                'total_votantes' => 0,
                'error' => 'No hay votantes para simular',
            ];
        }

        $resultados = [];
        $probabilidades = $votantes->pluck('probabilidad_voto')->toArray();

        // Ejecutar simulaciones
        for ($i = 0; $i < $iteraciones; $i++) {
            $votosEnIteracion = 0;

            foreach ($probabilidades as $prob) {
                // Simular Bernoulli: generar número aleatorio entre 0 y 1
                // Si es menor que la probabilidad, cuenta como voto
                if ((mt_rand() / mt_getrandmax()) <= $prob) {
                    $votosEnIteracion++;
                }
            }

            $resultados[] = $votosEnIteracion;
        }

        // Calcular estadísticas
        sort($resultados);
        $media = array_sum($resultados) / $iteraciones;
        $mediana = $this->calcularMediana($resultados);
        $min = min($resultados);
        $max = max($resultados);
        $p10 = $this->calcularPercentil($resultados, 10);
        $p90 = $this->calcularPercentil($resultados, 90);
        $desviacionEstandar = $this->calcularDesviacionEstandar($resultados, $media);

        // Generar histograma (agrupado en bins)
        $histograma = $this->generarHistograma($resultados, 20);

        return [
            'modelo' => 'montecarlo',
            'iteraciones' => $iteraciones,
            'total_votantes' => $votantes->count(),
            'estadisticas' => [
                'media' => round($media, 2),
                'mediana' => $mediana,
                'min' => $min,
                'max' => $max,
                'p10' => $p10,
                'p90' => $p90,
                'desviacion_estandar' => round($desviacionEstandar, 2),
                'intervalo_confianza_80' => [$p10, $p90],
            ],
            'histograma' => $histograma,
            'distribucion_completa' => $resultados, // para análisis más detallado
        ];
    }

    /**
     * Predicción combinada (heurístico + Monte Carlo)
     *
     * @param int $iteraciones
     * @param Collection|null $votantes
     * @return array
     */
    public function combinedPrediction(int $iteraciones = 1000, ?Collection $votantes = null): array
    {
        $heuristico = $this->heuristicPrediction($votantes);
        $montecarlo = $this->monteCarloPrediction($iteraciones, $votantes);

        return [
            'heuristico' => $heuristico,
            'montecarlo' => $montecarlo,
            'comparacion' => [
                'diferencia_absoluta' => abs($heuristico['votos_estimados'] - $montecarlo['estadisticas']['media']),
                'diferencia_porcentual' => $heuristico['votos_estimados'] > 0
                    ? round((abs($heuristico['votos_estimados'] - $montecarlo['estadisticas']['media']) / $heuristico['votos_estimados']) * 100, 2)
                    : 0,
            ],
        ];
    }

    /**
     * Calcular mediana de un array
     *
     * @param array $arr
     * @return float
     */
    private function calcularMediana(array $arr): float
    {
        $count = count($arr);
        $middle = floor($count / 2);

        if ($count % 2 == 0) {
            return ($arr[$middle - 1] + $arr[$middle]) / 2;
        }

        return $arr[$middle];
    }

    /**
     * Calcular percentil de un array ordenado
     *
     * @param array $arr Array ordenado
     * @param int $percentil
     * @return float
     */
    private function calcularPercentil(array $arr, int $percentil): float
    {
        $index = ($percentil / 100) * (count($arr) - 1);
        $lower = floor($index);
        $upper = ceil($index);

        if ($lower == $upper) {
            return $arr[$lower];
        }

        $weight = $index - $lower;
        return $arr[$lower] * (1 - $weight) + $arr[$upper] * $weight;
    }

    /**
     * Calcular desviación estándar
     *
     * @param array $arr
     * @param float $media
     * @return float
     */
    private function calcularDesviacionEstandar(array $arr, float $media): float
    {
        $sumaCuadrados = array_reduce($arr, function ($carry, $item) use ($media) {
            return $carry + pow($item - $media, 2);
        }, 0);

        return sqrt($sumaCuadrados / count($arr));
    }

    /**
     * Generar histograma agrupando resultados en bins
     *
     * @param array $resultados Array ordenado
     * @param int $numBins
     * @return array
     */
    private function generarHistograma(array $resultados, int $numBins = 20): array
    {
        $min = min($resultados);
        $max = max($resultados);
        $rangoTotal = $max - $min;
        
        if ($rangoTotal == 0) {
            return [['rango' => [$min, $max], 'frecuencia' => count($resultados)]];
        }

        $anchoBin = $rangoTotal / $numBins;
        $histograma = [];

        for ($i = 0; $i < $numBins; $i++) {
            $inicio = $min + ($i * $anchoBin);
            $fin = $inicio + $anchoBin;

            $frecuencia = count(array_filter($resultados, function ($val) use ($inicio, $fin, $i, $numBins) {
                if ($i == $numBins - 1) {
                    // Último bin incluye el máximo
                    return $val >= $inicio && $val <= $fin;
                }
                return $val >= $inicio && $val < $fin;
            }));

            if ($frecuencia > 0) {
                $histograma[] = [
                    'rango' => [round($inicio, 1), round($fin, 1)],
                    'frecuencia' => $frecuencia,
                    'porcentaje' => round(($frecuencia / count($resultados)) * 100, 2),
                ];
            }
        }

        return $histograma;
    }
}
