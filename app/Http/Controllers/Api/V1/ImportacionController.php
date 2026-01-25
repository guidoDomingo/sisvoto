<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\VoterImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImportacionController extends Controller
{
    protected $importService;

    public function __construct(VoterImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Importar votantes desde archivo
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function importar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'archivo' => 'required|file|mimes:csv,xlsx,xls|max:' . config('campana.importacion.max_file_size', 10240),
            'lider_asignado_id' => 'required|exists:lideres,id',
            'actualizar_duplicados' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validación fallida',
                'detalles' => $validator->errors()
            ], 422);
        }

        $archivo = $request->file('archivo');
        $liderAsignadoId = $request->lider_asignado_id;
        $actualizarDuplicados = $request->boolean('actualizar_duplicados', false);
        $usuarioId = auth()->id();

        // Guardar temporalmente el archivo
        $rutaTemporal = $archivo->store('temp');
        $rutaCompleta = storage_path('app/' . $rutaTemporal);

        try {
            $resultado = $this->importService->importar(
                $rutaCompleta,
                $liderAsignadoId,
                $usuarioId,
                $actualizarDuplicados
            );

            // Eliminar archivo temporal
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }

            return response()->json($resultado);
        } catch (\Exception $e) {
            // Eliminar archivo temporal en caso de error
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }

            return response()->json([
                'error' => 'Error al procesar importación',
                'mensaje' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar plantilla CSV de ejemplo
     *
     * @return \Illuminate\Http\Response
     */
    public function descargarPlantilla()
    {
        $contenido = VoterImportService::generarPlantillaCSV();

        return response($contenido, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_votantes.csv"',
        ]);
    }
}
