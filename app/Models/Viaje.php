<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Viaje extends Model
{
    use HasFactory;

    protected $table = 'viajes';

    protected $fillable = [
        'vehiculo_id',
        'chofer_id',
        'lider_responsable_id',
        'fecha_viaje',
        'hora_salida',
        'hora_regreso_estimada',
        'punto_partida',
        'destino',
        'distancia_estimada_km',
        'costo_combustible',
        'costo_chofer',
        'viaticos',
        'costo_total',
        'estado',
        'notas',
    ];

    protected $casts = [
        'fecha_viaje' => 'date',
        'hora_salida' => 'datetime:H:i',
        'hora_regreso_estimada' => 'datetime:H:i',
        'distancia_estimada_km' => 'decimal:2',
        'costo_combustible' => 'decimal:2',
        'costo_chofer' => 'decimal:2',
        'viaticos' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    /**
     * Relación con vehículo
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    /**
     * Relación con chofer
     */
    public function chofer()
    {
        return $this->belongsTo(Chofer::class, 'chofer_id');
    }

    /**
     * Relación con líder responsable
     */
    public function liderResponsable()
    {
        return $this->belongsTo(Lider::class, 'lider_responsable_id');
    }

    /**
     * Relación con votantes (pasajeros)
     */
    public function votantes()
    {
        return $this->belongsToMany(Votante::class, 'pasajeros_viaje', 'viaje_id', 'votante_id')
            ->withPivot('orden_recogida', 'punto_recogida', 'fue_recogido', 'recogido_en', 'confirmo_voto')
            ->withTimestamps();
    }

    /**
     * Relación con pasajeros (pivot)
     */
    public function pasajeros()
    {
        return $this->hasMany(PasajeroViaje::class, 'viaje_id');
    }

    /**
     * Calcular costo total del viaje
     */
    public function calcularCostoTotal(float $precioCombustible = null): float
    {
        if (!$precioCombustible) {
            $precioCombustible = config('campana.precio_combustible', 7500);
        }

        // Costo de combustible
        $costoCombustible = 0;
        if ($this->distancia_estimada_km && $this->vehiculo) {
            $costoCombustible = $this->distancia_estimada_km 
                * $this->vehiculo->consumo_por_km 
                * $precioCombustible;
        }

        // Costo del chofer
        $costoChofer = $this->chofer ? $this->chofer->costo_por_viaje : 0;

        // Viáticos
        $viaticos = $this->viaticos ?? 0;

        return $costoCombustible + $costoChofer + $viaticos;
    }

    /**
     * Guardar el costo total calculado
     */
    public function actualizarCostos(float $precioCombustible = null): void
    {
        if (!$precioCombustible) {
            $precioCombustible = config('campana.precio_combustible', 7500);
        }

        $this->costo_combustible = $this->distancia_estimada_km 
            ? $this->distancia_estimada_km * $this->vehiculo->consumo_por_km * $precioCombustible 
            : 0;
        
        $this->costo_chofer = $this->chofer ? $this->chofer->costo_por_viaje : 0;
        $this->costo_total = $this->calcularCostoTotal($precioCombustible);
        
        $this->save();
    }

    /**
     * Scope para viajes por fecha
     */
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha_viaje', $fecha);
    }

    /**
     * Scope para viajes por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para viajes completados
     */
    public function scopeCompletados($query)
    {
        return $query->where('estado', 'Completado');
    }
}
