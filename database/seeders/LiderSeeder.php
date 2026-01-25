<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lider;
use App\Models\User;
use App\Models\Role;

class LiderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleLider = Role::where('slug', 'lider')->first();
        $roleCoordinador = Role::where('slug', 'coordinador')->first();
        
        $usuariosLideres = User::where('role_id', $roleLider->id)->get();
        $coordinador = User::where('role_id', $roleCoordinador->id)->first();

        $territorios = [
            [
                'territorio' => 'Centro - Zona 1',
                'descripcion' => 'Incluye barrios: Villa Morra, Carmelitas, Las Mercedes',
                'meta_votos' => 200,
                'latitud' => -25.2867,
                'longitud' => -57.6333,
            ],
            [
                'territorio' => 'Norte - Zona 2',
                'descripcion' => 'Incluye barrios: San Vicente, Trinidad, Ycua Satí',
                'meta_votos' => 180,
                'latitud' => -25.2637,
                'longitud' => -57.5759,
            ],
            [
                'territorio' => 'Este - Zona 3',
                'descripcion' => 'Incluye barrios: Madame Lynch, Mburucuyá, Bernardino Caballero',
                'meta_votos' => 150,
                'latitud' => -25.2948,
                'longitud' => -57.5698,
            ],
            [
                'territorio' => 'Sur - Zona 4',
                'descripcion' => 'Incluye barrios: Sajonia, Banco San Miguel, Villa Aurelia',
                'meta_votos' => 220,
                'latitud' => -25.3167,
                'longitud' => -57.6167,
            ],
            [
                'territorio' => 'Oeste - Zona 5',
                'descripcion' => 'Incluye barrios: Zeballos Cué, Santa Ana, Botánico',
                'meta_votos' => 190,
                'latitud' => -25.2833,
                'longitud' => -57.6500,
            ],
        ];

        foreach ($usuariosLideres as $index => $usuario) {
            if (isset($territorios[$index])) {
                Lider::create([
                    'usuario_id' => $usuario->id,
                    'territorio' => $territorios[$index]['territorio'],
                    'descripcion_territorio' => $territorios[$index]['descripcion'],
                    'meta_votos' => $territorios[$index]['meta_votos'],
                    'latitud' => $territorios[$index]['latitud'],
                    'longitud' => $territorios[$index]['longitud'],
                    'coordinador_id' => $coordinador->id,
                    'activo' => true,
                ]);
            }
        }
    }
}
