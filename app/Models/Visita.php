<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;

    protected $table = 'visitas';

    protected $fillable = [
        'votante_id',
        'lider_id',
        'usuario_registro_id',
        'fecha_visita',
        'tipo_visita',
        'resultado',
        'observaciones',
        'compromisos',
        'proxima_visita',
        'requiere_seguimiento',
        'foto_evidencia',
        'duracion_minutos',
    ];

    protected $casts = [
        'fecha_visita' => 'datetime',
        'proxima_visita' => 'datetime',
        'requiere_seguimiento' => 'boolean',
        'duracion_minutos' => 'decimal:2',
    ];

    /**
     * Relación con votante
     */
    public function votante()
    {
        return $this->belongsTo(Votante::class, 'votante_id');
    }

    /**
     * Relación con líder
     */
    public function lider()
    {
        return $this->belongsTo(Lider::class, 'lider_id');
    }

    /**
     * Relación con usuario que registró
     */
    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }

    /**
     * Scope para visitas favorables
     */
    public function scopeFavorables($query)
    {
        return $query->where('resultado', 'Favorable');
    }

    /**
     * Scope para visitas que requieren seguimiento
     */
    public function scopeRequierenSeguimiento($query)
    {
        return $query->where('requiere_seguimiento', true);
    }

    /**
     * Scope para visitas por líder
     */
    public function scopePorLider($query, $liderId)
    {
        return $query->where('lider_id', $liderId);
    }

    /**
     * Scope para visitas por fecha
     */
    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha_visita', [$inicio, $fin]);
    }
}
