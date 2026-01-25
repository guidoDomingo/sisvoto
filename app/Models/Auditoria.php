<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    use HasFactory;

    protected $table = 'auditorias';

    protected $fillable = [
        'usuario_id',
        'accion',
        'modelo',
        'modelo_id',
        'valores_anteriores',
        'valores_nuevos',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
    ];

    /**
     * Relación con usuario que realizó la acción
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Crear registro de auditoría
     */
    public static function registrar(
        string $accion,
        string $modelo,
        int $modeloId = null,
        array $valoresAnteriores = null,
        array $valoresNuevos = null,
        int $usuarioId = null
    ): self {
        return self::create([
            'usuario_id' => $usuarioId ?? auth()->id(),
            'accion' => $accion,
            'modelo' => $modelo,
            'modelo_id' => $modeloId,
            'valores_anteriores' => $valoresAnteriores,
            'valores_nuevos' => $valoresNuevos,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Scope para auditorías de un usuario
     */
    public function scopeDeUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Scope para auditorías de un modelo
     */
    public function scopeDeModelo($query, $modelo, $modeloId = null)
    {
        $query->where('modelo', $modelo);
        
        if ($modeloId) {
            $query->where('modelo_id', $modeloId);
        }
        
        return $query;
    }

    /**
     * Scope para auditorías recientes
     */
    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }
}
