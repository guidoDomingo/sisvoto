<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Votante;
use App\Models\Lider;
use App\Models\User;
use Faker\Factory as Faker;

class VotanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        $lideres = Lider::all();
        $usuarios = User::all();

        $barrios = [
            'Villa Morra', 'Carmelitas', 'Las Mercedes', 'San Vicente',
            'Trinidad', 'Ycua Satí', 'Madame Lynch', 'Mburucuyá',
            'Bernardino Caballero', 'Sajonia', 'Banco San Miguel',
            'Villa Aurelia', 'Zeballos Cué', 'Santa Ana', 'Botánico',
            'Recoleta', 'Centro', 'Catedral', 'Villa Elisa', 'Lambaré'
        ];

        $zonas = ['Centro', 'Norte', 'Este', 'Sur', 'Oeste'];
        $distritos = ['Distrito 1', 'Distrito 2', 'Distrito 3', 'Distrito 4', 'Distrito 5'];
        
        $codigos = ['A', 'B', 'C', 'D', 'E'];
        $estados = ['Nuevo', 'Contactado', 'Re-contacto', 'Comprometido', 'Crítico'];
        $generos = ['M', 'F', 'Otro'];

        // Crear 250 votantes de ejemplo
        for ($i = 0; $i < 250; $i++) {
            $lider = $lideres->random();
            $creador = $usuarios->random();
            
            // Generar coordenadas cerca del líder
            $latBase = $lider->latitud ?? -25.2637;
            $lonBase = $lider->longitud ?? -57.5759;
            $latitud = $latBase + (rand(-50, 50) / 1000);
            $longitud = $lonBase + (rand(-50, 50) / 1000);

            // Distribución de intenciones: más A y B que D y E
            $codigoIntencion = $faker->randomElement([
                'A', 'A', 'A', 'A', // 40% A
                'B', 'B', 'B',       // 30% B
                'C', 'C',            // 20% C
                'D',                 // 5% D
                'E'                  // 5% E
            ]);

            // Algunos necesitan transporte (30%)
            $necesitaTransporte = rand(1, 100) <= 30;

            // Algunos ya votaron (solo para pruebas, 10%)
            $yaVoto = rand(1, 100) <= 10;

            Votante::create([
                'ci' => str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                'nombres' => $faker->firstName(),
                'apellidos' => $faker->lastName() . ' ' . $faker->lastName(),
                'telefono' => '0' . rand(961, 991) . '-' . rand(100000, 999999),
                'email' => rand(0, 1) ? $faker->unique()->safeEmail() : null,
                'fecha_nacimiento' => $faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                'genero' => $faker->randomElement($generos),
                'ocupacion' => $faker->randomElement([
                    'Comerciante', 'Docente', 'Empleado', 'Estudiante',
                    'Jubilado', 'Ama de casa', 'Profesional independiente',
                    'Obrero', 'Funcionario público', 'Agricultor', null
                ]),
                'direccion' => $faker->streetAddress(),
                'barrio' => $faker->randomElement($barrios),
                'zona' => $faker->randomElement($zonas),
                'distrito' => $faker->randomElement($distritos),
                'latitud' => $latitud,
                'longitud' => $longitud,
                'lider_asignado_id' => $lider->id,
                'creado_por_usuario_id' => $creador->id,
                'actualizado_por_usuario_id' => $creador->id,
                'codigo_intencion' => $codigoIntencion,
                'estado_contacto' => $faker->randomElement($estados),
                'ya_voto' => $yaVoto,
                'voto_registrado_en' => $yaVoto ? $faker->dateTimeBetween('-7 days', 'now') : null,
                'necesita_transporte' => $necesitaTransporte && !$yaVoto,
                'notas' => rand(0, 1) ? $faker->sentence() : null,
            ]);
        }
    }
}
