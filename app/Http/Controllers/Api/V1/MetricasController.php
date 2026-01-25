<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\MetricsService;
use Illuminate\Http\Request;

class MetricasController extends Controller
{
    protected $metricsService;

    public function __construct(MetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    /**
     * Obtener métricas generales de la campaña
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generales()
    {
        $metricas = $this->metricsService->getGeneralMetrics();
        return response()->json($metricas);
    }

    /**
     * Obtener métricas de un líder específico
     *
     * @param int $liderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function porLider($liderId)
    {
        try {
            $metricas = $this->metricsService->getLeaderMetrics($liderId);
            return response()->json($metricas);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Líder no encontrado'
            ], 404);
        }
    }

    /**
     * Obtener conversión de contactos
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function conversionContactos()
    {
        $conversion = $this->metricsService->getContactConversion();
        return response()->json($conversion);
    }

    /**
     * Obtener costo por voto
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function costoPorVoto()
    {
        $costo = $this->metricsService->getCostPerVote();
        return response()->json($costo);
    }

    /**
     * Obtener ROI estimado
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function roi(Request $request)
    {
        $valorPorVoto = $request->get('valor_por_voto', 50000);
        $roi = $this->metricsService->getROI($valorPorVoto);
        return response()->json($roi);
    }
}
