<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TSJEService
{
    /**
     * Consultar datos del votante usando APIs públicas de Paraguay
     * 
     * @param string $ci Cédula de Identidad
     * @return array|null
     */
    public function consultarVotante($ci)
    {
        try {
            // Limpiar CI (solo números)
            $ci = preg_replace('/[^0-9]/', '', $ci);
            
            if (empty($ci) || strlen($ci) < 6) {
                return [
                    'encontrado' => false,
                    'ci' => $ci,
                    'mensaje' => 'CI debe tener al menos 6 dígitos'
                ];
            }

            Log::info("Consultando CI: {$ci}");

            // Intentar consultar en diferentes APIs públicas de Paraguay
            
            // 1. API del TSJE (Tribunal Superior de Justicia Electoral) - Prioridad alta
            $resultado = $this->consultarTSJEOficial($ci);
            if ($resultado && $resultado['encontrado']) {
                Log::info("Datos encontrados en TSJE para CI: {$ci}");
                return $resultado;
            }
            
            // 2. API del Registro Civil (SET - Identificaciones)
            $resultado = $this->consultarRegistroCivil($ci);
            if ($resultado && $resultado['encontrado']) {
                Log::info("Datos encontrados en Registro Civil para CI: {$ci}");
                return $resultado;
            }
            
            // 3. API de validación de CI
            $resultado = $this->consultarValidacionCI($ci);
            if ($resultado && $resultado['encontrado']) {
                Log::info("Datos encontrados en validación CI para CI: {$ci}");
                return $resultado;
            }

            Log::info("No se encontraron datos para CI: {$ci}");
            return [
                'encontrado' => false,
                'ci' => $ci,
                'mensaje' => 'CI no encontrado en las bases de datos del TSJE. Verifique el número o complete los datos manualmente.'
            ];

        } catch (\Exception $e) {
            Log::error('Error consultando datos del votante: ' . $e->getMessage());
            return [
                'encontrado' => false,
                'ci' => $ci,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Consultar en el TSJE oficial
     */
    private function consultarTSJEOficial($ci)
    {
        try {
            Log::info("Consultando TSJE oficial para CI: {$ci}");
            
            // Endpoint principal del TSJE
            $response = Http::timeout(30)
                ->withoutVerifying() // Desactivar verificación SSL en desarrollo
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/json, text/javascript, */*; q=0.01',
                    'Accept-Language' => 'es-ES,es;q=0.9',
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Referer' => 'https://tsje.gov.py/',
                ])
                ->get('https://tsje.gov.py/servicio/consulta-padron.php', [
                    'cedula' => $ci,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['resultado']) && $data['resultado'] === 'ENCONTRADO') {
                    $datos = $data['datos'] ?? $data;
                    
                    return [
                        'encontrado' => true,
                        'ci' => $ci,
                        'nombres' => $this->limpiarTexto($datos['nombres'] ?? $datos['nombre'] ?? ''),
                        'apellidos' => $this->limpiarTexto($datos['apellidos'] ?? $datos['apellido'] ?? ''),
                        'direccion' => $this->limpiarTexto($datos['direccion'] ?? ''),
                        'distrito' => $this->limpiarTexto($datos['distrito'] ?? $datos['municipio'] ?? ''),
                        'barrio' => $this->limpiarTexto($datos['barrio'] ?? $datos['compania'] ?? ''),
                        'departamento' => $this->limpiarTexto($datos['departamento'] ?? ''),
                        'mesa' => $datos['mesa'] ?? $datos['numero_mesa'] ?? null,
                        'local_votacion' => $this->limpiarTexto($datos['local'] ?? $datos['local_votacion'] ?? $datos['centro_votacion'] ?? ''),
                        'fuente' => 'TSJE - Padrón Electoral'
                    ];
                }
            }
            
            // Método alternativo - Scraping del formulario web
            $response = Http::timeout(25)
                ->withoutVerifying() // Desactivar verificación SSL en desarrollo
                ->asForm()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'es-ES,es;q=0.9',
                    'Referer' => 'https://tsje.gov.py/',
                ])
                ->post('https://tsje.gov.py/padron-electoral.php', [
                    'ci' => $ci,
                    'consultar' => 'Consultar'
                ]);

            if ($response->successful()) {
                $html = $response->body();
                
                // Parsear HTML para extraer datos
                $datos = $this->parsearHTMLTSJE($html, $ci);
                
                if ($datos && $datos['encontrado']) {
                    Log::info("Datos extraídos del HTML TSJE para CI: {$ci}");
                    return $datos;
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error consultando TSJE oficial: ' . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Parsear HTML del TSJE para extraer datos
     */
    private function parsearHTMLTSJE($html, $ci)
    {
        try {
            // Buscar patrones comunes en el HTML
            $datos = [
                'encontrado' => false,
                'ci' => $ci,
            ];
            
            // Buscar nombres
            if (preg_match('/<td[^>]*>Nombres?:<\/td>\s*<td[^>]*>([^<]+)<\/td>/i', $html, $matches)) {
                $datos['nombres'] = $this->limpiarTexto($matches[1]);
                $datos['encontrado'] = true;
            }
            
            // Buscar apellidos
            if (preg_match('/<td[^>]*>Apellidos?:<\/td>\s*<td[^>]*>([^<]+)<\/td>/i', $html, $matches)) {
                $datos['apellidos'] = $this->limpiarTexto($matches[1]);
            }
            
            // Buscar dirección
            if (preg_match('/<td[^>]*>Direcci[oó]n:<\/td>\s*<td[^>]*>([^<]+)<\/td>/i', $html, $matches)) {
                $datos['direccion'] = $this->limpiarTexto($matches[1]);
            }
            
            // Buscar distrito/municipio
            if (preg_match('/<td[^>]*>(?:Distrito|Municipio):<\/td>\s*<td[^>]*>([^<]+)<\/td>/i', $html, $matches)) {
                $datos['distrito'] = $this->limpiarTexto($matches[1]);
            }
            
            // Buscar barrio/compañía
            if (preg_match('/<td[^>]*>(?:Barrio|Compa[ñn][ií]a):<\/td>\s*<td[^>]*>([^<]+)<\/td>/i', $html, $matches)) {
                $datos['barrio'] = $this->limpiarTexto($matches[1]);
            }
            
            // Buscar departamento
            if (preg_match('/<td[^>]*>Departamento:<\/td>\s*<td[^>]*>([^<]+)<\/td>/i', $html, $matches)) {
                $datos['departamento'] = $this->limpiarTexto($matches[1]);
            }
            
            // Buscar local de votación
            if (preg_match('/<td[^>]*>Local (?:de )?Votaci[oó]n:<\/td>\s*<td[^>]*>([^<]+)<\/td>/i', $html, $matches)) {
                $datos['local_votacion'] = $this->limpiarTexto($matches[1]);
            }
            
            // Buscar mesa
            if (preg_match('/<td[^>]*>Mesa:<\/td>\s*<td[^>]*>(\d+)<\/td>/i', $html, $matches)) {
                $datos['mesa'] = $matches[1];
            }
            
            if ($datos['encontrado']) {
                $datos['fuente'] = 'TSJE (Web)';
                return $datos;
            }
            
        } catch (\Exception $e) {
            Log::debug('Error parseando HTML: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Consultar en el Registro Civil / SET
     */
    private function consultarRegistroCivil($ci)
    {
        try {
            // API del SET (Subsecretaría de Estado de Tributación) para validación de CI
            $response = Http::timeout(15)
                ->withoutVerifying() // Desactivar verificación SSL en desarrollo
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'application/json',
                ])
                ->post('https://servicios.set.gov.py/eset-publico/ciudadano/recuperar', [
                    'nroDocumento' => $ci,
                    'tipoDocumento' => 'CI'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['resultado']) && $data['resultado']['estado'] == 'EXITOSO') {
                    $persona = $data['resultado']['persona'] ?? [];
                    
                    return [
                        'encontrado' => true,
                        'ci' => $ci,
                        'nombres' => $this->limpiarTexto($persona['nombres'] ?? ''),
                        'apellidos' => $this->limpiarTexto($persona['apellidos'] ?? ''),
                        'direccion' => '',
                        'distrito' => '',
                        'barrio' => '',
                        'fuente' => 'SET'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::debug('Error consultando Registro Civil: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Consultar API de validación de CI
     */
    private function consultarValidacionCI($ci)
    {
        try {
            // API pública de validación de cédulas paraguayas
            $response = Http::timeout(15)
                ->withoutVerifying() // Desactivar verificación SSL en desarrollo
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->get('https://www.paraguayserver.com/api/ci', [
                    'cedula' => $ci,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['valido']) && $data['valido']) {
                    return [
                        'encontrado' => true,
                        'ci' => $ci,
                        'nombres' => $this->limpiarTexto($data['nombres'] ?? ''),
                        'apellidos' => $this->limpiarTexto($data['apellidos'] ?? ''),
                        'direccion' => '',
                        'distrito' => '',
                        'barrio' => '',
                        'fuente' => 'Validación CI'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::debug('Error consultando Validación CI: ' . $e->getMessage());
        }
        
        return null;
    }

    /**
     * Limpiar y normalizar texto
     */
    private function limpiarTexto($texto)
    {
        if (empty($texto)) {
            return '';
        }
        
        // Convertir a mayúsculas y limpiar espacios
        $texto = mb_strtoupper(trim($texto), 'UTF-8');
        
        // Reemplazar múltiples espacios por uno solo
        $texto = preg_replace('/\s+/', ' ', $texto);
        
        return $texto;
    }
}
