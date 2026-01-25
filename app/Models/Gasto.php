<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    use HasFactory;

    protected $table = 'gastos';

    protected $fillable = [
        'categoria',
        'descripcion',
        'monto',
        'fecha_gasto',
        'usuario_registro_id',
        'viaje_id',
        'numero_recibo',
        'proveedor',
        'archivo_recibo',
        'aprobado',
        'aprobado_por_usuario_id',
        'aprobado_en',
        'notas',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_gasto' => 'date',
        'aprobado' => 'boolean',
        'aprobado_en' => 'datetime',
    ];

    /**
     * Relación con usuario que registró
     */
    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }

    /**
     * Relación con usuario que aprobó
     */
    public function usuarioAprobo()
    {
        return $this->belongsTo(User::class, 'aprobado_por_usuario_id');
    }

    /**
     * Relación con viaje (si aplica)
     */
    public function viaje()
    {
        return $this->belongsTo(Viaje::class, 'viaje_id');
    }

    /**
     * Scope para gastos por categoría
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Scope para gastos aprobados
     */
    public function scopeAprobados($query)
    {
        return $query->where('aprobado', true);
    }

    /**
     * Scope para gastos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->where('aprobado', false);
    }

    /**
     * Scope para gastos por rango de fechas
     */
    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha_gasto', [$inicio, $fin]);
    }
}
