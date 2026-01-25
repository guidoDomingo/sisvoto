<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasajeroViaje extends Model
{
    use HasFactory;

    protected $table = 'pasajeros_viaje';

    protected $fillable = [
        'viaje_id',
        'votante_id',
        'orden_recogida',
        'punto_recogida',
        'fue_recogido',
        'recogido_en',
        'confirmo_voto',
    ];

    protected $casts = [
        'orden_recogida' => 'integer',
        'fue_recogido' => 'boolean',
        'recogido_en' => 'datetime',
        'confirmo_voto' => 'boolean',
    ];

    /**
     * Relación con viaje
     */
    public function viaje()
    {
        return $this->belongsTo(Viaje::class, 'viaje_id');
    }

    /**
     * Relación con votante
     */
    public function votante()
    {
        return $this->belongsTo(Votante::class, 'votante_id');
    }
}
