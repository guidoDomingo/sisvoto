<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Viaje;
use App\Models\Vehiculo;
use App\Models\Chofer;
use App\Models\Lider;
use App\Models\Votante;
use Faker\Factory as Faker;

class ViajeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        
        $vehiculos = Vehiculo::where('disponible', true)->get();
        $choferes = Chofer::where('disponible', true)->get();
        $lideres = Lider::all();

        $estados = ['Planificado', 'Confirmado', 'En curso', 'Completado', 'Cancelado'];
        $destinos = [
            'Escuela Básica N° 1',
            'Colegio Nacional de la Capital',
            'Centro Comunitario Villa Morra',
            'Salón Parroquial San Roque',
            'Club Social y Deportivo',
            'Escuela República Argentina',
        ];

        $precioCombustible = config('campana.precio_combustible', 7500);

        // Crear 15 viajes de ejemplo
        for ($i = 0; $i < 15; $i++) {
            $vehiculo = $vehiculos->random();
            $chofer = $choferes->random();
            $lider = $lideres->random();
            
            $distanciaKm = rand(5, 30);
            $costoCombustible = $distanciaKm * $vehiculo->consumo_por_km * $precioCombustible;
            $costoChofer = $chofer->costo_por_viaje;
            $viaticos = rand(0, 20000);
            $costoTotal = $costoCombustible + $costoChofer + $viaticos;

            // Fechas: algunos pasados, algunos futuros
            $fechaViaje = $i < 5 
                ? $faker->dateTimeBetween('-7 days', '-1 day')->format('Y-m-d')
                : $faker->dateTimeBetween('today', '+14 days')->format('Y-m-d');

            $estado = $i < 5 ? 'Completado' : $faker->randomElement(['Planificado', 'Confirmado']);

            $viaje = Viaje::create([
                'vehiculo_id' => $vehiculo->id,
                'chofer_id' => $chofer->id,
                'lider_responsable_id' => $lider->id,
                'fecha_viaje' => $fechaViaje,
                'hora_salida' => $faker->time('H:i'),
                'hora_regreso_estimada' => $faker->time('H:i'),
                'punto_partida' => $faker->streetAddress(),
                'destino' => $faker->randomElement($destinos),
                'distancia_estimada_km' => $distanciaKm,
                'costo_combustible' => $costoCombustible,
                'costo_chofer' => $costoChofer,
                'viaticos' => $viaticos,
                'costo_total' => $costoTotal,
                'estado' => $estado,
                'notas' => rand(0, 1) ? $faker->sentence() : null,
            ]);

            // Asignar votantes al viaje (pasajeros)
            $numPasajeros = min(rand(2, $vehiculo->capacidad_pasajeros), 8);
            $votantes = Votante::where('lider_asignado_id', $lider->id)
                ->where('necesita_transporte', true)
                ->where('ya_voto', false)
                ->inRandomOrder()
                ->limit($numPasajeros)
                ->get();

            foreach ($votantes as $index => $votante) {
                $viaje->votantes()->attach($votante->id, [
                    'orden_recogida' => $index + 1,
                    'punto_recogida' => $votante->direccion,
                    'fue_recogido' => $estado === 'Completado' ? true : false,
                    'recogido_en' => $estado === 'Completado' ? $faker->dateTimeBetween('-7 days', 'now') : null,
                    'confirmo_voto' => $estado === 'Completado' ? (rand(0, 1) == 1) : false,
                ]);
            }
        }
    }
}
