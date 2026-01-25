<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'telefono',
        'ci',
        'activo',
        'ultimo_acceso',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'activo' => 'boolean',
        'ultimo_acceso' => 'datetime',
    ];

    /**
     * Relación con rol
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Relación con líder (si el usuario es líder)
     */
    public function lider()
    {
        return $this->hasOne(Lider::class, 'usuario_id');
    }

    /**
     * Relación con votantes creados por este usuario
     */
    public function votantesCreados()
    {
        return $this->hasMany(Votante::class, 'creado_por_usuario_id');
    }

    /**
     * Relación con contactos realizados
     */
    public function contactosRealizados()
    {
        return $this->hasMany(ContactoVotante::class, 'usuario_id');
    }

    /**
     * Relación con gastos registrados
     */
    public function gastosRegistrados()
    {
        return $this->hasMany(Gasto::class, 'usuario_registro_id');
    }

    /**
     * Relación con auditorías
     */
    public function auditorias()
    {
        return $this->hasMany(Auditoria::class, 'usuario_id');
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function tieneRol(string $rol): bool
    {
        return $this->role && $this->role->slug === $rol;
    }

    /**
     * Verificar si tiene un rol (alias para compatibilidad con Livewire)
     */
    public function hasRole(string $rol): bool
    {
        return $this->role && $this->role->nombre === $rol;
    }

    /**
     * Verificar si es super admin
     */
    public function esSuperAdmin(): bool
    {
        return $this->tieneRol('superadmin');
    }

    /**
     * Verificar si es coordinador
     */
    public function esCoordinador(): bool
    {
        return $this->tieneRol('coordinador');
    }

    /**
     * Verificar si es líder
     */
    public function esLider(): bool
    {
        return $this->tieneRol('lider');
    }

    /**
     * Verificar si es voluntario
     */
    public function esVoluntario(): bool
    {
        return $this->tieneRol('voluntario');
    }

    /**
     * Verificar si tiene un permiso específico
     */
    public function tienePermiso(string $permiso): bool
    {
        return $this->role && $this->role->tienePermiso($permiso);
    }
}
