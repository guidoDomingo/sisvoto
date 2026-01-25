<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Chofer;
use Faker\Factory as Faker;

class ChoferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        $choferes = [
            [
                'nombres' => 'Carlos',
                'apellidos' => 'Rodríguez',
                'ci' => '6000001',
                'telefono' => '0981-100100',
                'licencia' => 'L-123456',
                'licencia_vencimiento' => '2026-12-31',
                'costo_por_viaje' => 50000,
                'disponible' => true,
            ],
            [
                'nombres' => 'Luis',
                'apellidos' => 'Pérez',
                'ci' => '6000002',
                'telefono' => '0981-200200',
                'licencia' => 'L-234567',
                'licencia_vencimiento' => '2025-08-15',
                'costo_por_viaje' => 45000,
                'disponible' => true,
            ],
            [
                'nombres' => 'Fernando',
                'apellidos' => 'Gómez',
                'ci' => '6000003',
                'telefono' => '0981-300300',
                'licencia' => 'L-345678',
                'licencia_vencimiento' => '2027-03-20',
                'costo_por_viaje' => 60000,
                'disponible' => true,
            ],
            [
                'nombres' => 'Jorge',
                'apellidos' => 'Martínez',
                'ci' => '6000004',
                'telefono' => '0981-400400',
                'licencia' => 'L-456789',
                'licencia_vencimiento' => '2026-06-10',
                'costo_por_viaje' => 55000,
                'disponible' => true,
            ],
            [
                'nombres' => 'Ricardo',
                'apellidos' => 'López',
                'ci' => '6000005',
                'telefono' => '0981-500500',
                'licencia' => 'L-567890',
                'licencia_vencimiento' => '2025-11-25',
                'costo_por_viaje' => 48000,
                'disponible' => false,
                'notas' => 'De vacaciones hasta fin de mes',
            ],
            [
                'nombres' => 'Miguel',
                'apellidos' => 'Sánchez',
                'ci' => '6000006',
                'telefono' => '0981-600600',
                'licencia' => 'L-678901',
                'licencia_vencimiento' => '2028-01-15',
                'costo_por_viaje' => 52000,
                'disponible' => true,
            ],
        ];

        foreach ($choferes as $chofer) {
            Chofer::create($chofer);
        }
    }
}
