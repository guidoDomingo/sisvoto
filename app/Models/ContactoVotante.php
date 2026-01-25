<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactoVotante extends Model
{
    use HasFactory;

    protected $table = 'contactos_votantes';

    protected $fillable = [
        'votante_id',
        'usuario_id',
        'contactado_en',
        'metodo',
        'resultado',
        'notas',
        'intencion_detectada',
    ];

    protected $casts = [
        'contactado_en' => 'datetime',
    ];

    /**
     * Relación con votante
     */
    public function votante()
    {
        return $this->belongsTo(Votante::class, 'votante_id');
    }

    /**
     * Relación con usuario que realizó el contacto
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Scope para filtrar por método
     */
    public function scopePorMetodo($query, $metodo)
    {
        return $query->where('metodo', $metodo);
    }

    /**
     * Scope para contactos recientes
     */
    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('contactado_en', '>=', now()->subDays($dias));
    }
}
