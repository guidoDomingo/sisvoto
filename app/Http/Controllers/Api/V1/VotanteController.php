<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Votante;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VotanteController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Listar votantes con filtros
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Votante::with(['lider.usuario', 'creadoPor']);

        // Filtros
        if ($request->has('lider_id')) {
            $query->where('lider_asignado_id', $request->lider_id);
        }

        if ($request->has('codigo_intencion')) {
            $query->where('codigo_intencion', $request->codigo_intencion);
        }

        if ($request->has('estado_contacto')) {
            $query->where('estado_contacto', $request->estado_contacto);
        }

        if ($request->has('ya_voto')) {
            $query->where('ya_voto', $request->boolean('ya_voto'));
        }

        if ($request->has('necesita_transporte')) {
            $query->where('necesita_transporte', $request->boolean('necesita_transporte'));
        }

        if ($request->has('barrio')) {
            $query->where('barrio', 'like', '%' . $request->barrio . '%');
        }

        if ($request->has('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('nombres', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('ci', 'like', "%{$buscar}%")
                    ->orWhere('telefono', 'like', "%{$buscar}%");
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        // Paginación
        $perPage = $request->get('per_page', 15);
        $votantes = $query->paginate($perPage);

        return response()->json($votantes);
    }

    /**
     * Crear votante
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ci' => 'nullable|string|max:20|unique:votantes,ci',
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'barrio' => 'nullable|string|max:100',
            'zona' => 'nullable|string|max:100',
            'distrito' => 'nullable|string|max:100',
            'lider_asignado_id' => 'required|exists:lideres,id',
            'codigo_intencion' => 'nullable|in:A,B,C,D,E',
            'necesita_transporte' => 'nullable|boolean',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'detalles' => $validator->errors()
            ], 422);
        }

        $datos = $validator->validated();
        $datos['creado_por_usuario_id'] = auth()->id();
        $datos['actualizado_por_usuario_id'] = auth()->id();

        $votante = Votante::create($datos);

        // Auditar
        $this->auditService->registrarCreacion($votante);

        return response()->json([
            'mensaje' => 'Votante creado exitosamente',
            'votante' => $votante->load(['lider.usuario'])
        ], 201);
    }

    /**
     * Mostrar votante específico
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $votante = Votante::with([
            'lider.usuario',
            'creadoPor',
            'contactos.usuario',
            'viajes.vehiculo',
            'viajes.chofer'
        ])->find($id);

        if (!$votante) {
            return response()->json(['error' => 'Votante no encontrado'], 404);
        }

        return response()->json($votante);
    }

    /**
     * Actualizar votante
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $votante = Votante::find($id);

        if (!$votante) {
            return response()->json(['error' => 'Votante no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ci' => 'nullable|string|max:20|unique:votantes,ci,' . $id,
            'nombres' => 'sometimes|required|string|max:100',
            'apellidos' => 'sometimes|required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string',
            'barrio' => 'nullable|string|max:100',
            'codigo_intencion' => 'nullable|in:A,B,C,D,E',
            'estado_contacto' => 'nullable|in:Nuevo,Contactado,Re-contacto,Comprometido,Crítico',
            'necesita_transporte' => 'nullable|boolean',
            'lider_asignado_id' => 'nullable|exists:lideres,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'detalles' => $validator->errors()
            ], 422);
        }

        $valoresAnteriores = $votante->toArray();
        
        $datos = $validator->validated();
        $datos['actualizado_por_usuario_id'] = auth()->id();
        
        $votante->update($datos);

        // Auditar
        $this->auditService->registrarActualizacion($votante, $valoresAnteriores);

        return response()->json([
            'mensaje' => 'Votante actualizado exitosamente',
            'votante' => $votante->fresh(['lider.usuario'])
        ]);
    }

    /**
     * Marcar votante como "ya votó"
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function marcarVoto(Request $request, $id)
    {
        $votante = Votante::find($id);

        if (!$votante) {
            return response()->json(['error' => 'Votante no encontrado'], 404);
        }

        if ($votante->ya_voto) {
            return response()->json([
                'advertencia' => 'Votante ya fue marcado como votado',
                'voto_registrado_en' => $votante->voto_registrado_en
            ], 200);
        }

        $valoresAnteriores = $votante->only(['ya_voto', 'voto_registrado_en']);

        $votante->ya_voto = true;
        $votante->voto_registrado_en = now();
        $votante->actualizado_por_usuario_id = auth()->id();
        $votante->save();

        // Auditar
        $this->auditService->registrarAccion('Marcar voto', $votante, [
            'ya_voto' => true,
            'voto_registrado_en' => $votante->voto_registrado_en,
        ]);

        return response()->json([
            'mensaje' => 'Voto registrado exitosamente',
            'votante' => $votante
        ]);
    }

    /**
     * Eliminar votante (soft delete)
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $votante = Votante::find($id);

        if (!$votante) {
            return response()->json(['error' => 'Votante no encontrado'], 404);
        }

        // Auditar antes de eliminar
        $this->auditService->registrarEliminacion($votante);

        $votante->delete();

        return response()->json([
            'mensaje' => 'Votante eliminado exitosamente'
        ]);
    }

    /**
     * Reasignar líder a votante(s)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reasignarLider(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'votante_ids' => 'required|array',
            'votante_ids.*' => 'exists:votantes,id',
            'nuevo_lider_id' => 'required|exists:lideres,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'detalles' => $validator->errors()
            ], 422);
        }

        $votantesIds = $request->votante_ids;
        $nuevoLiderId = $request->nuevo_lider_id;
        $votantes = Votante::whereIn('id', $votantesIds)->get();

        foreach ($votantes as $votante) {
            $liderAnterior = $votante->lider_asignado_id;
            $votante->lider_asignado_id = $nuevoLiderId;
            $votante->actualizado_por_usuario_id = auth()->id();
            $votante->save();

            // Auditar
            $this->auditService->registrarAccion('Reasignar líder', $votante, [
                'lider_anterior_id' => $liderAnterior,
                'lider_nuevo_id' => $nuevoLiderId,
            ]);
        }

        return response()->json([
            'mensaje' => 'Votantes reasignados exitosamente',
            'total_reasignados' => count($votantesIds)
        ]);
    }
}
