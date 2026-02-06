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
                'nombre' => 'Admin',
                'slug' => 'admin',
                'descripcion' => 'Acceso total al sistema',
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
                'nombre' => 'Veedor',
                'slug' => 'veedor',
                'descripcion' => 'Acceso a votantes con opción de marcar votos',
                'permisos' => [
                    'votantes.ver',
                    'votantes.marcar_voto',
                    'contactos.ver',
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
                    'permisos' => json_encode($rol['permisos']), // recuerda que permisos suele ser JSON
                ]
            );
        }
    }
}
