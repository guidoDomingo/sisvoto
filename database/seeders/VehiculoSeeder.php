<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehiculo;

class VehiculoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehiculos = [
            [
                'placa' => 'ABC-123',
                'marca' => 'Toyota',
                'modelo' => 'Hilux',
                'año' => 2020,
                'color' => 'Blanco',
                'capacidad_pasajeros' => 5,
                'consumo_por_km' => 0.12,
                'tipo' => 'Camioneta',
                'disponible' => true,
            ],
            [
                'placa' => 'DEF-456',
                'marca' => 'Nissan',
                'modelo' => 'Versa',
                'año' => 2019,
                'color' => 'Gris',
                'capacidad_pasajeros' => 5,
                'consumo_por_km' => 0.08,
                'tipo' => 'Auto',
                'disponible' => true,
            ],
            [
                'placa' => 'GHI-789',
                'marca' => 'Hyundai',
                'modelo' => 'H-100',
                'año' => 2021,
                'color' => 'Azul',
                'capacidad_pasajeros' => 12,
                'consumo_por_km' => 0.15,
                'tipo' => 'Van',
                'disponible' => true,
            ],
            [
                'placa' => 'JKL-012',
                'marca' => 'Chevrolet',
                'modelo' => 'Onix',
                'año' => 2022,
                'color' => 'Rojo',
                'capacidad_pasajeros' => 5,
                'consumo_por_km' => 0.09,
                'tipo' => 'Auto',
                'disponible' => true,
            ],
            [
                'placa' => 'MNO-345',
                'marca' => 'Ford',
                'modelo' => 'Ranger',
                'año' => 2018,
                'color' => 'Negro',
                'capacidad_pasajeros' => 5,
                'consumo_por_km' => 0.13,
                'tipo' => 'Camioneta',
                'disponible' => true,
            ],
            [
                'placa' => 'PQR-678',
                'marca' => 'Volkswagen',
                'modelo' => 'Gol',
                'año' => 2020,
                'color' => 'Blanco',
                'capacidad_pasajeros' => 5,
                'consumo_por_km' => 0.10,
                'tipo' => 'Auto',
                'disponible' => true,
            ],
            [
                'placa' => 'STU-901',
                'marca' => 'Mercedes Benz',
                'modelo' => 'Sprinter',
                'año' => 2019,
                'color' => 'Blanco',
                'capacidad_pasajeros' => 15,
                'consumo_por_km' => 0.18,
                'tipo' => 'Van',
                'disponible' => true,
            ],
            [
                'placa' => 'VWX-234',
                'marca' => 'Honda',
                'modelo' => 'City',
                'año' => 2021,
                'color' => 'Gris',
                'capacidad_pasajeros' => 5,
                'consumo_por_km' => 0.07,
                'tipo' => 'Auto',
                'disponible' => false,
                'notas' => 'En mantenimiento',
            ],
        ];

        foreach ($vehiculos as $vehiculo) {
            // Calcular costo por km (consumo * precio combustible por defecto)
            $precioCombustible = config('campana.precio_combustible', 7500);
            $vehiculo['costo_por_km'] = $vehiculo['consumo_por_km'] * $precioCombustible;
            
            Vehiculo::create($vehiculo);
        }
    }
}
