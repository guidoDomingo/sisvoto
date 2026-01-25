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
                'nombre' => 'Super Administrador',
                'slug' => 'superadmin',
                'descripcion' => 'Acceso total al sistema',
                'permisos' => [
                    'usuarios.crear',
                    'usuarios.editar',
                    'usuarios.eliminar',
                    'votantes.todos',
                    'lideres.gestionar',
                    'gastos.aprobar',
                    'reportes.avanzados',
                    'configuracion.sistema',
                    'auditorias.ver',
                ],
            ],
            [
                'nombre' => 'Coordinador',
                'slug' => 'coordinador',
                'descripcion' => 'Gestiona zonas y líderes',
                'permisos' => [
                    'lideres.gestionar',
                    'votantes.todos',
                    'viajes.gestionar',
                    'gastos.ver',
                    'reportes.zona',
                ],
            ],
            [
                'nombre' => 'Líder',
                'slug' => 'lider',
                'descripcion' => 'Gestiona sus votantes y voluntarios',
                'permisos' => [
                    'votantes.propios',
                    'votantes.asignar',
                    'contactos.registrar',
                    'viajes.solicitar',
                    'reportes.propios',
                ],
            ],
            [
                'nombre' => 'Voluntario',
                'slug' => 'voluntario',
                'descripcion' => 'Registra votantes y contactos',
                'permisos' => [
                    'votantes.crear',
                    'contactos.registrar',
                    'votantes.ver',
                ],
            ],
            [
                'nombre' => 'Auditor',
                'slug' => 'auditor',
                'descripcion' => 'Solo lectura de todo el sistema',
                'permisos' => [
                    'votantes.ver',
                    'gastos.ver',
                    'reportes.ver',
                    'auditorias.ver',
                ],
            ],
        ];

        foreach ($roles as $rol) {
            Role::create($rol);
        }
    }
}
