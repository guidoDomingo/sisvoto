<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'placa',
        'marca',
        'modelo',
        'año',
        'color',
        'capacidad_pasajeros',
        'consumo_por_km',
        'costo_por_km',
        'tipo',
        'disponible',
        'notas',
    ];

    protected $casts = [
        'año' => 'integer',
        'capacidad_pasajeros' => 'integer',
        'consumo_por_km' => 'decimal:2',
        'costo_por_km' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    /**
     * Relación con viajes
     */
    public function viajes()
    {
        return $this->hasMany(Viaje::class, 'vehiculo_id');
    }

    /**
     * Calcular costo por km basado en consumo y precio de combustible
     */
    public function calcularCostoPorKm(float $precioCombustible): float
    {
        return $this->consumo_por_km * $precioCombustible;
    }

    /**
     * Scope para vehículos disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }
}
