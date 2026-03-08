<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'nombre' => 'Super Admin',
                'slug' => 'superadmin',
                'descripcion' => 'Acceso total al sistema con permisos de administración completos',
                'permisos' => [
                    'usuarios.crear',
                    'usuarios.editar',
                    'usuarios.eliminar',
                    'votantes.todos',
                    'votantes.crear',
                    'votantes.editar',
                    'votantes.eliminar',
                    'votantes.marcar_voto',
                    'lideres.gestionar',
                    'viajes.todos',
                    'viajes.crear',
                    'viajes.editar',
                    'viajes.eliminar',
                    'visitas.todas',
                    'visitas.crear',
                    'visitas.editar',
                    'visitas.eliminar',
                    'gastos.aprobar',
                    'reportes.avanzados',
                    'configuracion.sistema',
                    'auditorias.ver',
                ],
            ],
            [
                'nombre' => 'Coordinador',
                'slug' => 'coordinador',
                'descripcion' => 'Coordinador de campaña con permisos de gestión regional',
                'permisos' => [
                    'usuarios.ver',
                    'usuarios.editar',
                    'votantes.todos',
                    'votantes.crear',
                    'votantes.editar',
                    'votantes.marcar_voto',
                    'lideres.gestionar',
                    'viajes.todos',
                    'viajes.crear',
                    'viajes.editar',
                    'visitas.todas',
                    'visitas.crear',
                    'visitas.editar',
                    'gastos.ver',
                    'reportes.avanzados',
                ],
            ],
            [
                'nombre' => 'Líder',
                'slug' => 'lider',
                'descripcion' => 'Gestiona votantes, viajes y visitas sin poder marcar votos',
                'permisos' => [
                    'votantes.ver',
                    'votantes.crear',
                    'votantes.editar',
                    'votantes.propios',
                    'contactos.registrar',
                    'viajes.ver',
                    'viajes.crear',
                    'viajes.editar',
                    'viajes.solicitar',
                    'visitas.ver',
                    'visitas.crear',
                    'visitas.editar',
                    'reportes.propios',
                ],
            ],
            [
                'nombre' => 'Voluntario',
                'slug' => 'voluntario',
                'descripcion' => 'Voluntario de campaña con permisos básicos',
                'permisos' => [
                    'votantes.ver',
                    'votantes.propios',
                    'contactos.registrar',
                    'viajes.ver',
                    'viajes.solicitar',
                    'visitas.ver',
                    'visitas.crear',
                    'reportes.propios',
                ],
            ],
            [
                'nombre' => 'Auditor',
                'slug' => 'auditor',
                'descripcion' => 'Auditor del sistema con acceso de solo lectura',
                'permisos' => [
                    'votantes.ver',
                    'viajes.ver',
                    'visitas.ver',
                    'gastos.ver',
                    'reportes.avanzados',
                    'auditorias.ver',
                ],
            ],
            [
                'nombre' => 'Veedor',
                'slug' => 'veedor',
                'descripcion' => 'Solo puede marcar votos de votantes',
                'permisos' => [
                    'votantes.ver',
                    'votantes.marcar_voto',
                ],
            ],
            [
                'nombre' => 'PC Móvil',
                'slug' => 'pc_movil',
                'descripcion' => 'Operador de PC móvil con acceso completo a votantes y gestión de usuarios',
                'permisos' => [
                    'votantes.ver',
                    'votantes.todos',
                    'votantes.crear',
                    'votantes.editar',
                    'votantes.marcar_voto',
                    'usuarios.ver',
                    'usuarios.crear',
                    'usuarios.editar',
                    'usuarios.eliminar',
                    'pc_movil.usar',
                    'contactos.registrar',
                    'reportes.ver',
                ],
            ],
        ];

        foreach ($roles as $rol) {
            Role::updateOrCreate(
                ['nombre' => $rol['nombre']], // busca por nombre
                [
                    'slug' => $rol['slug'],
                    'descripcion' => $rol['descripcion'],
                    'permisos' => $rol['permisos'], // dejar que Laravel maneje la conversión automáticamente
                ]
            );
        }
    }
}
