<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Role;
use App\Models\User;

class DiagnosticarRoles extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'diagnosticar:roles {--user-id=}';

    /**
     * The console command description.
     */
    protected $description = 'Diagnostica problemas con roles y permisos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== DIAGNÓSTICO DE ROLES Y PERMISOS ===');
        $this->newLine();

        // Mostrar todos los roles
        $this->info('ROLES REGISTRADOS:');
        $roles = Role::all(['id', 'nombre', 'slug', 'permisos']);
        
        foreach ($roles as $role) {
            $this->line("ID: {$role->id} | Nombre: {$role->nombre} | Slug: {$role->slug}");
            $permisos = is_array($role->permisos) ? $role->permisos : json_decode($role->permisos ?? '[]', true);
            $this->line("  Permisos: " . implode(', ', $permisos ?: ['Sin permisos']));
            $this->newLine();
        }

        // Mostrar usuarios y sus roles
        $this->info('USUARIOS Y SUS ROLES:');
        $users = User::with('role')->get(['id', 'name', 'email', 'role_id']);
        
        foreach ($users as $user) {
            $this->line("ID: {$user->id} | Nombre: {$user->name} | Email: {$user->email}");
            
            if ($user->role) {
                $this->line("  Rol: {$user->role->nombre} (slug: {$user->role->slug})");
                $this->line("  esAdmin(): " . ($user->esAdmin() ? 'SÍ' : 'NO'));
                $this->line("  puedeVerVotantes(): " . ($user->puedeVerVotantes() ? 'SÍ' : 'NO'));
                $this->line("  tieneRol('superadmin'): " . ($user->tieneRol('superadmin') ? 'SÍ' : 'NO'));
                $this->line("  tieneRol('admin'): " . ($user->tieneRol('admin') ? 'SÍ' : 'NO'));
            } else {
                $this->warn("  Sin rol asignado");
            }
            $this->newLine();
        }

        // Si se especifica un usuario, mostrar detalles específicos
        if ($this->option('user-id')) {
            $userId = $this->option('user-id');
            $user = User::with('role')->find($userId);
            
            if ($user) {
                $this->info("DETALLES ESPECÍFICOS PARA USUARIO ID: {$userId}");
                $this->line("Nombre: {$user->name}");
                $this->line("Email: {$user->email}");
                
                if ($user->role) {
                    $this->line("Rol ID: {$user->role_id}");
                    $this->line("Rol Nombre: {$user->role->nombre}");
                    $this->line("Rol Slug: {$user->role->slug}");
                    
                    $permisos = is_array($user->role->permisos) ? $user->role->permisos : json_decode($user->role->permisos ?? '[]', true);
                    $this->line("Permisos: " . implode(', ', $permisos ?: ['Sin permisos']));
                } else {
                    $this->warn("Sin rol asignado");
                }
            } else {
                $this->error("Usuario con ID {$userId} no encontrado");
            }
        }

        $this->newLine();
        $this->info('=== FIN DEL DIAGNÓSTICO ===');

        return 0;
    }
}