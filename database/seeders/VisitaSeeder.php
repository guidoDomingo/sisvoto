<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Visita;
use App\Models\Votante;
use App\Models\Lider;
use App\Models\User;
use Carbon\Carbon;

class VisitaSeeder extends Seeder
{
    public function run()
    {
        $lideres = Lider::with('usuario')->get();
        $votantes = Votante::all();
        
        if ($lideres->isEmpty() || $votantes->isEmpty()) {
            $this->command->warn('No hay líderes o votantes para crear visitas');
            return;
        }

        $tiposVisita = ['Primera visita', 'Seguimiento', 'Convencimiento', 'Confirmación', 'Urgente'];
        $resultados = ['Favorable', 'Indeciso', 'No favorable', 'No contactado', 'Rechazado'];
        
        $observacionesEjemplos = [
            'Votante muy receptivo, mostró gran interés en la propuesta',
            'Necesita más información sobre las políticas del candidato',
            'Tiene dudas sobre temas de seguridad y empleo',
            'Familia completa comprometida con el voto',
            'Solicita visita del candidato para su barrio',
            'Preocupado por temas de salud y educación',
            'Votó anteriormente por el partido, confirma apoyo',
            'Indeciso entre dos candidatos, requiere seguimiento',
            'No estuvo en casa, se dejó información',
            'Rechaza participar por desconfianza en política'
        ];

        $compromisosEjemplos = [
            'Asistirá al evento del 15 de marzo',
            'Traerá a 3 familiares el día de la votación',
            'Distribuirá material de campaña en su barrio',
            'Organizará reunión con vecinos la próxima semana',
            'Confirmó voto para toda su familia (4 personas)',
            'Compartirá información en redes sociales',
            'Ayudará en la movilización del día electoral',
            null,
            null,
            null
        ];

        $this->command->info('Creando 80 visitas de prueba...');

        for ($i = 0; $i < 80; $i++) {
            $lider = $lideres->random();
            $votante = $votantes->random();
            $tipoVisita = $tiposVisita[array_rand($tiposVisita)];
            $resultado = $resultados[array_rand($resultados)];
            
            // Fecha de visita en los últimos 60 días
            $fechaVisita = Carbon::now()->subDays(rand(0, 60))->subHours(rand(0, 12));
            
            // 30% de las visitas requieren seguimiento
            $requiereSeguimiento = rand(1, 100) <= 30;
            
            // Si requiere seguimiento, agregar fecha próxima visita
            $proximaVisita = null;
            if ($requiereSeguimiento) {
                $proximaVisita = Carbon::now()->addDays(rand(1, 15));
            }
            
            // Solo visitas recientes tienen foto (50% de probabilidad)
            $fotoEvidencia = null;
            if ($fechaVisita->diffInDays(now()) < 30 && rand(1, 100) <= 50) {
                $fotoEvidencia = 'visitas/foto_' . uniqid() . '.jpg';
            }

            Visita::create([
                'votante_id' => $votante->id,
                'lider_id' => $lider->id,
                'usuario_registro_id' => $lider->usuario->id,
                'fecha_visita' => $fechaVisita,
                'tipo_visita' => $tipoVisita,
                'resultado' => $resultado,
                'observaciones' => $observacionesEjemplos[array_rand($observacionesEjemplos)],
                'compromisos' => $compromisosEjemplos[array_rand($compromisosEjemplos)],
                'proxima_visita' => $proximaVisita,
                'requiere_seguimiento' => $requiereSeguimiento,
                'foto_evidencia' => $fotoEvidencia,
                'duracion_minutos' => rand(5, 90)
            ]);
        }

        $this->command->info('✓ 80 visitas creadas exitosamente');
        
        // Estadísticas
        $favorables = Visita::where('resultado', 'Favorable')->count();
        $indecisos = Visita::where('resultado', 'Indeciso')->count();
        $seguimiento = Visita::where('requiere_seguimiento', true)->count();
        
        $this->command->info("\nEstadísticas:");
        $this->command->info("- Resultados favorables: $favorables");
        $this->command->info("- Votantes indecisos: $indecisos");
        $this->command->info("- Requieren seguimiento: $seguimiento");
    }
}
