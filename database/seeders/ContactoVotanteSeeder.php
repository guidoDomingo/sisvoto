<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContactoVotante;
use App\Models\Votante;
use App\Models\User;
use Faker\Factory as Faker;

class ContactoVotanteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        
        // Obtener votantes contactados (no "Nuevo")
        $votantes = Votante::whereIn('estado_contacto', [
            'Contactado', 'Re-contacto', 'Comprometido', 'Crítico'
        ])->get();

        $usuarios = User::whereHas('role', function($query) {
            $query->whereIn('slug', ['lider', 'voluntario', 'coordinador']);
        })->get();

        $metodos = ['Puerta a puerta', 'WhatsApp', 'Llamada', 'Visita programada', 'Evento'];
        $resultados = ['Exitoso', 'No responde', 'Rechaza', 'Solicita más info', 'Comprometido', 'Pendiente seguimiento'];
        $intenciones = ['A', 'B', 'C', 'D', 'E'];

        foreach ($votantes as $votante) {
            // Crear entre 1 y 4 contactos por votante contactado
            $numContactos = rand(1, 4);
            
            for ($i = 0; $i < $numContactos; $i++) {
                ContactoVotante::create([
                    'votante_id' => $votante->id,
                    'usuario_id' => $usuarios->random()->id,
                    'contactado_en' => $faker->dateTimeBetween('-60 days', 'now'),
                    'metodo' => $faker->randomElement($metodos),
                    'resultado' => $faker->randomElement($resultados),
                    'notas' => rand(0, 1) ? $faker->sentence() : null,
                    'intencion_detectada' => rand(0, 1) ? $faker->randomElement($intenciones) : null,
                ]);
            }
        }
    }
}
