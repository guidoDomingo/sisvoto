<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TSJEService;

class TestTSJECommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tsje:test {ci : Cédula de Identidad para consultar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Probar la consulta de datos en el TSJE';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $ci = $this->argument('ci');
        
        $this->info("Consultando datos para CI: {$ci}");
        $this->info("=====================================");
        
        $tsje = new TSJEService();
        
        try {
            $resultado = $tsje->consultarVotante($ci);
            
            if ($resultado && $resultado['encontrado']) {
                $this->info("✅ DATOS ENCONTRADOS:");
                $this->line("");
                $this->line("CI: " . $resultado['ci']);
                $this->line("Nombres: " . $resultado['nombres']);
                $this->line("Apellidos: " . $resultado['apellidos']);
                $this->line("Dirección: " . ($resultado['direccion'] ?: 'No disponible'));
                $this->line("Distrito: " . ($resultado['distrito'] ?: 'No disponible'));
                $this->line("Barrio: " . ($resultado['barrio'] ?: 'No disponible'));
                $this->line("Departamento: " . ($resultado['departamento'] ?: 'No disponible'));
                $this->line("Mesa: " . ($resultado['mesa'] ?: 'No disponible'));
                $this->line("Local de Votación: " . ($resultado['local_votacion'] ?: 'No disponible'));
                $this->line("Fuente: " . $resultado['fuente']);
                
                $this->info("\n🎉 Datos listos para importar al sistema!");
                
            } else {
                $mensaje = $resultado['mensaje'] ?? 'No se encontraron datos';
                $this->warn("❌ NO SE ENCONTRARON DATOS: {$mensaje}");
                
                if (isset($resultado['error'])) {
                    $this->error("Error: " . $resultado['error']);
                }
            }
            
        } catch (\Exception $e) {
            $this->error("💥 ERROR DURANTE LA CONSULTA:");
            $this->error($e->getMessage());
        }
        
        $this->line("");
        $this->info("Consulta finalizada.");
    }
}