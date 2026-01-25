<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chofer extends Model
{
    use HasFactory;

    protected $table = 'choferes';

    protected $fillable = [
        'nombres',
        'apellidos',
        'ci',
        'telefono',
        'licencia',
        'licencia_vencimiento',
        'costo_por_viaje',
        'disponible',
        'notas',
    ];

    protected $casts = [
        'licencia_vencimiento' => 'date',
        'costo_por_viaje' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    /**
     * Obtener nombre completo
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }

    /**
     * Verificar si la licencia está vigente
     */
    public function getLicenciaVigenteAttribute(): bool
    {
        if (!$this->licencia_vencimiento) {
            return true;
        }

        return $this->licencia_vencimiento->isFuture();
    }

    /**
     * Relación con viajes
     */
    public function viajes()
    {
        return $this->hasMany(Viaje::class, 'chofer_id');
    }

    /**
     * Scope para choferes disponibles
     */
    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true);
    }

    /**
     * Scope para choferes con licencia vigente
     */
    public function scopeConLicenciaVigente($query)
    {
        return $query->where(function($q) {
            $q->whereNull('licencia_vencimiento')
              ->orWhere('licencia_vencimiento', '>', now());
        });
    }
}
