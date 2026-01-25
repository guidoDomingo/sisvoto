<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Viaje;
use App\Models\Vehiculo;
use App\Models\Chofer;
use App\Services\TripPlannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ViajeController extends Controller
{
    protected $tripPlannerService;

    public function __construct(TripPlannerService $tripPlannerService)
    {
        $this->tripPlannerService = $tripPlannerService;
    }

    /**
     * Listar viajes
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Viaje::with(['vehiculo', 'chofer', 'liderResponsable.usuario', 'votantes']);

        // Filtros
        if ($request->has('fecha')) {
            $query->whereDate('fecha_viaje', $request->fecha);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('lider_id')) {
            $query->where('lider_responsable_id', $request->lider_id);
        }

        $perPage = $request->get('per_page', 15);
        $viajes = $query->orderBy('fecha_viaje', 'desc')->paginate($perPage);

        return response()->json($viajes);
    }

    /**
     * Crear viaje
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'chofer_id' => 'required|exists:choferes,id',
            'lider_responsable_id' => 'nullable|exists:lideres,id',
            'fecha_viaje' => 'required|date',
            'hora_salida' => 'nullable|date_format:H:i',
            'punto_partida' => 'nullable|string',
            'destino' => 'nullable|string',
            'distancia_estimada_km' => 'nullable|numeric|min:0',
            'viaticos' => 'nullable|numeric|min:0',
            'votantes' => 'nullable|array',
            'votantes.*' => 'exists:votantes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'detalles' => $validator->errors()
            ], 422);
        }

        $datos = $validator->validated();
        $precioCombustible = config('campana.precio_combustible', 7500);

        // Calcular costos
        $vehiculo = Vehiculo::find($datos['vehiculo_id']);
        $chofer = Chofer::find($datos['chofer_id']);

        if (isset($datos['distancia_estimada_km'])) {
            $datos['costo_combustible'] = $datos['distancia_estimada_km'] 
                * $vehiculo->consumo_por_km 
                * $precioCombustible;
        }

        $datos['costo_chofer'] = $chofer->costo_por_viaje;
        $datos['viaticos'] = $datos['viaticos'] ?? 0;
        $datos['costo_total'] = ($datos['costo_combustible'] ?? 0) 
            + $datos['costo_chofer'] 
            + $datos['viaticos'];

        $votantesIds = $datos['votantes'] ?? [];
        unset($datos['votantes']);

        $viaje = Viaje::create($datos);

        // Asignar votantes al viaje
        if (!empty($votantesIds)) {
            foreach ($votantesIds as $index => $votanteId) {
                $viaje->votantes()->attach($votanteId, [
                    'orden_recogida' => $index + 1,
                ]);
            }
        }

        return response()->json([
            'mensaje' => 'Viaje creado exitosamente',
            'viaje' => $viaje->load(['vehiculo', 'chofer', 'votantes'])
        ], 201);
    }

    /**
     * Mostrar viaje específico
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $viaje = Viaje::with([
            'vehiculo',
            'chofer',
            'liderResponsable.usuario',
            'votantes'
        ])->find($id);

        if (!$viaje) {
            return response()->json(['error' => 'Viaje no encontrado'], 404);
        }

        return response()->json($viaje);
    }

    /**
     * Actualizar viaje
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $viaje = Viaje::find($id);

        if (!$viaje) {
            return response()->json(['error' => 'Viaje no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'nullable|in:Planificado,Confirmado,En curso,Completado,Cancelado',
            'hora_salida' => 'nullable|date_format:H:i',
            'hora_regreso_estimada' => 'nullable|date_format:H:i',
            'distancia_estimada_km' => 'nullable|numeric|min:0',
            'viaticos' => 'nullable|numeric|min:0',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'detalles' => $validator->errors()
            ], 422);
        }

        $viaje->update($validator->validated());

        // Recalcular costos si cambió la distancia
        if ($request->has('distancia_estimada_km')) {
            $viaje->actualizarCostos();
        }

        return response()->json([
            'mensaje' => 'Viaje actualizado exitosamente',
            'viaje' => $viaje->fresh(['vehiculo', 'chofer', 'votantes'])
        ]);
    }

    /**
     * Generar plan de viajes para un líder
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generarPlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lider_id' => 'required|exists:lideres,id',
            'fecha' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'detalles' => $validator->errors()
            ], 422);
        }

        $plan = $this->tripPlannerService->generarPlanViajes(
            $request->lider_id,
            $request->fecha
        );

        return response()->json($plan);
    }
}
