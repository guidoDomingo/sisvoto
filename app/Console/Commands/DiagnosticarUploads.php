<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class DiagnosticarUploads extends Command
{
    protected $signature = 'diagnosticar:uploads';
    protected $description = 'Diagnostica problemas con uploads de archivos';

    public function handle()
    {
        $this->info('=== DIAGNÓSTICO DE UPLOADS ===');
        $this->newLine();

        // 1. Configuración de PHP
        $this->info('1. LÍMITES DE PHP:');
        $this->line('upload_max_filesize: ' . ini_get('upload_max_filesize'));
        $this->line('post_max_size: ' . ini_get('post_max_size'));
        $this->line('max_execution_time: ' . ini_get('max_execution_time'));
        $this->line('memory_limit: ' . ini_get('memory_limit'));
        $this->line('max_input_time: ' . ini_get('max_input_time'));
        $this->newLine();

        // 2. Configuración de Livewire
        $this->info('2. CONFIGURACIÓN DE LIVEWIRE:');
        $uploadConfig = config('livewire.temporary_file_upload');
        $this->line('Max upload time: ' . ($uploadConfig['max_upload_time'] ?? '5') . ' minutos');
        $this->line('Middleware: ' . ($uploadConfig['middleware'] ?? 'throttle:60,1'));
        $this->line('Rules: ' . json_encode($uploadConfig['rules'] ?? ['required', 'file', 'max:12288']));
        $this->line('Directory: ' . ($uploadConfig['directory'] ?? 'livewire-tmp'));
        $this->newLine();

        // 3. Directorio de uploads temporal
        $this->info('3. DIRECTORIO TEMPORAL:');
        $tmpPath = storage_path('app/livewire-tmp');
        
        if (is_dir($tmpPath)) {
            $this->line("✓ Directorio existe: {$tmpPath}");
            $perms = substr(sprintf('%o', fileperms($tmpPath)), -4);
            $this->line("Permisos: {$perms}");
            
            // Verificar si es escribible
            if (is_writable($tmpPath)) {
                $this->line("✓ Escribible");
            } else {
                $this->error("❌ NO escribible");
            }
            
            // Contar archivos temporales
            $files = glob($tmpPath . '/*');
            $this->line('Archivos temporales: ' . count($files));
        } else {
            $this->error("❌ Directorio NO existe: {$tmpPath}");
            $this->warn('Creando directorio...');
            if (mkdir($tmpPath, 0755, true)) {
                $this->line("✓ Directorio creado");
            } else {
                $this->error("❌ No se pudo crear el directorio");
            }
        }
        $this->newLine();

        // 4. Configuración de storage
        $this->info('4. CONFIGURACIÓN DE STORAGE:');
        $this->line('Default disk: ' . config('filesystems.default'));
        $localConfig = config('filesystems.disks.local');
        $this->line('Local path: ' . ($localConfig['root'] ?? 'No configurado'));
        $this->newLine();

        // 5. Variables de entorno relevantes
        $this->info('5. VARIABLES DE ENTORNO:');
        $this->line('APP_ENV: ' . config('app.env'));
        $this->line('APP_DEBUG: ' . (config('app.debug') ? 'true' : 'false'));
        $this->line('SESSION_DRIVER: ' . config('session.driver'));
        $this->newLine();

        // 6. Recomendaciones
        $this->info('6. RECOMENDACIONES:');
        
        $uploadMaxMB = (int) str_replace('M', '', ini_get('upload_max_filesize'));
        $postMaxMB = (int) str_replace('M', '', ini_get('post_max_size'));
        
        if ($uploadMaxMB < 20) {
            $this->warn("⚠ upload_max_filesize ({$uploadMaxMB}M) es menor que el límite de Livewire (20MB)");
        }
        
        if ($postMaxMB < 20) {
            $this->warn("⚠ post_max_size ({$postMaxMB}M) es menor que el límite de Livewire (20MB)");
        }
        
        if ($uploadMaxMB >= 20 && $postMaxMB >= 20) {
            $this->line("✓ Límites de PHP son suficientes");
        }

        $this->newLine();
        $this->info('=== FIN DEL DIAGNÓSTICO ===');
        
        return 0;
    }
}