<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PredictionService;
use App\Models\Votante;
use Illuminate\Http\Request;

class PrediccionController extends Controller
{
    protected $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * Obtener predicción según modelo especificado
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $modelo = $request->get('modelo', 'heuristico');
        $iteraciones = $request->get('iteraciones', config('campana.prediccion.iteraciones_default', 1000));
        
        // Filtros opcionales
        $votantes = $this->aplicarFiltros($request);

        switch ($modelo) {
            case 'montecarlo':
                $prediccion = $this->predictionService->monteCarloPrediction($iteraciones, $votantes);
                break;

            case 'combinado':
                $prediccion = $this->predictionService->combinedPrediction($iteraciones, $votantes);
                break;

            case 'heuristico':
            default:
                $prediccion = $this->predictionService->heuristicPrediction($votantes);
                break;
        }

        return response()->json($prediccion);
    }

    /**
     * Predicción heurística
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function heuristico(Request $request)
    {
        $votantes = $this->aplicarFiltros($request);
        $prediccion = $this->predictionService->heuristicPrediction($votantes);

        return response()->json($prediccion);
    }

    /**
     * Predicción Monte Carlo
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function montecarlo(Request $request)
    {
        $iteraciones = $request->get('iteraciones', 1000);
        
        if ($iteraciones < 100 || $iteraciones > 10000) {
            return response()->json([
                'error' => 'El número de iteraciones debe estar entre 100 y 10000'
            ], 422);
        }

        $votantes = $this->aplicarFiltros($request);
        $prediccion = $this->predictionService->monteCarloPrediction($iteraciones, $votantes);

        return response()->json($prediccion);
    }

    /**
     * Predicción combinada (heurístico + Monte Carlo)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function combinado(Request $request)
    {
        $iteraciones = $request->get('iteraciones', 1000);
        $votantes = $this->aplicarFiltros($request);
        $prediccion = $this->predictionService->combinedPrediction($iteraciones, $votantes);

        return response()->json($prediccion);
    }

    /**
     * Aplicar filtros a la consulta de votantes
     *
     * @param Request $request
     * @return \Illuminate\Support\Collection|null
     */
    private function aplicarFiltros(Request $request)
    {
        $query = Votante::query();

        if ($request->has('lider_id')) {
            $query->where('lider_asignado_id', $request->lider_id);
        }

        if ($request->has('barrio')) {
            $query->where('barrio', $request->barrio);
        }

        if ($request->has('zona')) {
            $query->where('zona', $request->zona);
        }

        if ($request->has('distrito')) {
            $query->where('distrito', $request->distrito);
        }

        if ($request->has('codigo_intencion')) {
            $query->where('codigo_intencion', $request->codigo_intencion);
        }

        // Si no hay filtros, retornar null para usar todos los votantes
        if (!$request->hasAny(['lider_id', 'barrio', 'zona', 'distrito', 'codigo_intencion'])) {
            return null;
        }

        return $query->get();
    }
}
