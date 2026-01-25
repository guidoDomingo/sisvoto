<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gasto;
use App\Models\User;
use App\Models\Viaje;
use Faker\Factory as Faker;

class GastoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        
        $usuarios = User::whereHas('role', function($query) {
            $query->whereIn('slug', ['superadmin', 'coordinador', 'lider']);
        })->get();

        $superadmin = User::whereHas('role', function($query) {
            $query->where('slug', 'superadmin');
        })->first();

        $viajes = Viaje::limit(10)->get();

        $categorias = [
            'Combustible' => [500000, 1200000],
            'Transporte' => [200000, 800000],
            'Publicidad' => [1000000, 5000000],
            'Material impreso' => [300000, 1500000],
            'Eventos' => [500000, 3000000],
            'Alimentos' => [200000, 1000000],
            'Tecnología' => [500000, 2500000],
            'Personal' => [1000000, 4000000],
            'Otros' => [100000, 500000],
        ];

        $proveedores = [
            'Estación de Servicio Petrobras',
            'Copaco S.A.',
            'Imprenta La Rápida',
            'Salón de Eventos El Jardín',
            'Supermercado Stock',
            'Tecnología Total',
            'Varios',
        ];

        // Crear 50 gastos de ejemplo
        for ($i = 0; $i < 50; $i++) {
            $categoria = array_rand($categorias);
            [$minMonto, $maxMonto] = $categorias[$categoria];
            $monto = rand($minMonto, $maxMonto);

            $aprobado = rand(0, 100) > 20; // 80% aprobados

            $gasto = [
                'categoria' => $categoria,
                'descripcion' => $faker->sentence(),
                'monto' => $monto,
                'fecha_gasto' => $faker->dateTimeBetween('-60 days', 'now')->format('Y-m-d'),
                'usuario_registro_id' => $usuarios->random()->id,
                'viaje_id' => ($categoria === 'Combustible' || $categoria === 'Transporte') && rand(0, 1)
                    ? $viajes->random()->id
                    : null,
                'numero_recibo' => rand(0, 1) ? 'R-' . str_pad(rand(1, 9999), 6, '0', STR_PAD_LEFT) : null,
                'proveedor' => rand(0, 1) ? $faker->randomElement($proveedores) : null,
                'archivo_recibo' => null,
                'aprobado' => $aprobado,
                'aprobado_por_usuario_id' => $aprobado ? $superadmin->id : null,
                'aprobado_en' => $aprobado ? $faker->dateTimeBetween('-30 days', 'now') : null,
                'notas' => rand(0, 1) ? $faker->sentence() : null,
            ];

            Gasto::create($gasto);
        }
    }
}
