<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;

/**
 * Servicio de auditoría para registrar cambios importantes
 */
class AuditService
{
    /**
     * Registrar creación de modelo
     *
     * @param Model $modelo
     * @param int|null $usuarioId
     * @return Auditoria
     */
    public function registrarCreacion(Model $modelo, ?int $usuarioId = null): Auditoria
    {
        return Auditoria::registrar(
            'Crear',
            get_class($modelo),
            $modelo->id,
            null,
            $modelo->toArray(),
            $usuarioId
        );
    }

    /**
     * Registrar actualización de modelo
     *
     * @param Model $modelo
     * @param array $valoresAnteriores
     * @param int|null $usuarioId
     * @return Auditoria
     */
    public function registrarActualizacion(Model $modelo, array $valoresAnteriores, ?int $usuarioId = null): Auditoria
    {
        return Auditoria::registrar(
            'Actualizar',
            get_class($modelo),
            $modelo->id,
            $valoresAnteriores,
            $modelo->toArray(),
            $usuarioId
        );
    }

    /**
     * Registrar eliminación de modelo
     *
     * @param Model $modelo
     * @param int|null $usuarioId
     * @return Auditoria
     */
    public function registrarEliminacion(Model $modelo, ?int $usuarioId = null): Auditoria
    {
        return Auditoria::registrar(
            'Eliminar',
            get_class($modelo),
            $modelo->id,
            $modelo->toArray(),
            null,
            $usuarioId
        );
    }

    /**
     * Registrar acción específica (ej: marcar voto, reasignar líder)
     *
     * @param string $accion
     * @param Model $modelo
     * @param array|null $datosAdicionales
     * @param int|null $usuarioId
     * @return Auditoria
     */
    public function registrarAccion(
        string $accion,
        Model $modelo,
        ?array $datosAdicionales = null,
        ?int $usuarioId = null
    ): Auditoria {
        return Auditoria::registrar(
            $accion,
            get_class($modelo),
            $modelo->id,
            null,
            $datosAdicionales,
            $usuarioId
        );
    }

    /**
     * Limpiar auditorías antiguas según retención configurada
     *
     * @return int Número de registros eliminados
     */
    public function limpiarAuditoriasAntiguas(): int
    {
        $diasRetencion = config('campana.auditoria.retention_days', 365);
        $fechaLimite = now()->subDays($diasRetencion);

        return Auditoria::where('created_at', '<', $fechaLimite)->delete();
    }

    /**
     * Obtener historial de cambios de un modelo
     *
     * @param Model $modelo
     * @param int $limite
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerHistorial(Model $modelo, int $limite = 50)
    {
        return Auditoria::deModelo(get_class($modelo), $modelo->id)
            ->with('usuario')
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();
    }
}
