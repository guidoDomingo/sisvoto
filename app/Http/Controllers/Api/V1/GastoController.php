<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GastoController extends Controller
{
    /**
     * Listar gastos
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Gasto::with(['usuarioRegistro', 'usuarioAprobo', 'viaje']);

        // Filtros
        if ($request->has('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->has('aprobado')) {
            $query->where('aprobado', $request->boolean('aprobado'));
        }

        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_gasto', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('fecha_gasto', '<=', $request->fecha_hasta);
        }

        $perPage = $request->get('per_page', 15);
        $gastos = $query->orderBy('fecha_gasto', 'desc')->paginate($perPage);

        return response()->json($gastos);
    }

    /**
     * Crear gasto
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categoria' => 'required|in:Combustible,Transporte,Publicidad,Material impreso,Eventos,Alimentos,Tecnología,Personal,Otros',
            'descripcion' => 'required|string',
            'monto' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'viaje_id' => 'nullable|exists:viajes,id',
            'numero_recibo' => 'nullable|string|max:50',
            'proveedor' => 'nullable|string|max:100',
            'notas' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'detalles' => $validator->errors()
            ], 422);
        }

        $datos = $validator->validated();
        $datos['usuario_registro_id'] = auth()->id();

        $gasto = Gasto::create($datos);

        return response()->json([
            'mensaje' => 'Gasto registrado exitosamente',
            'gasto' => $gasto
        ], 201);
    }

    /**
     * Mostrar gasto específico
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $gasto = Gasto::with(['usuarioRegistro', 'usuarioAprobo', 'viaje'])->find($id);

        if (!$gasto) {
            return response()->json(['error' => 'Gasto no encontrado'], 404);
        }

        return response()->json($gasto);
    }

    /**
     * Aprobar gasto
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function aprobar($id)
    {
        $gasto = Gasto::find($id);

        if (!$gasto) {
            return response()->json(['error' => 'Gasto no encontrado'], 404);
        }

        if ($gasto->aprobado) {
            return response()->json([
                'advertencia' => 'Gasto ya está aprobado'
            ], 200);
        }

        $gasto->aprobado = true;
        $gasto->aprobado_por_usuario_id = auth()->id();
        $gasto->aprobado_en = now();
        $gasto->save();

        return response()->json([
            'mensaje' => 'Gasto aprobado exitosamente',
            'gasto' => $gasto
        ]);
    }

    /**
     * Obtener resumen de gastos por categoría
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resumenPorCategoria(Request $request)
    {
        $query = Gasto::query();

        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_gasto', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('fecha_gasto', '<=', $request->fecha_hasta);
        }

        $resumen = $query->selectRaw('categoria, COUNT(*) as cantidad, SUM(monto) as total')
            ->groupBy('categoria')
            ->get();

        $totalGeneral = $query->sum('monto');

        return response()->json([
            'resumen' => $resumen,
            'total_general' => $totalGeneral
        ]);
    }
}
