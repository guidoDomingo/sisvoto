<?php

namespace App\Services;

use App\Models\Viaje;
use App\Models\Votante;
use App\Models\Vehiculo;
use App\Models\Chofer;
use Illuminate\Support\Collection;

/**
 * Servicio de planificación de viajes y logística
 */
class TripPlannerService
{
    /**
     * Calcular número de viajes necesarios
     *
     * @param int $numVotantes
     * @param int $capacidadVehiculo
     * @return int
     */
    public function calcularViajesNecesarios(int $numVotantes, int $capacidadVehiculo): int
    {
        return (int) ceil($numVotantes / $capacidadVehiculo);
    }

    /**
     * Calcular costo de un viaje
     *
     * @param float $distanciaKm
     * @param float $consumoPorKm
     * @param float $precioCombustible
     * @param float $costoChofer
     * @param float $viaticos
     * @return float
     */
    public function calcularCostoViaje(
        float $distanciaKm,
        float $consumoPorKm,
        float $precioCombustible,
        float $costoChofer = 0,
        float $viaticos = 0
    ): float {
        $costoCombustible = $distanciaKm * $consumoPorKm * $precioCombustible;
        return $costoCombustible + $costoChofer + $viaticos;
    }

    /**
     * Agrupar votantes por proximidad (algoritmo simple basado en coordenadas)
     *
     * @param Collection $votantes Votantes con latitud y longitud
     * @param int $maxPorGrupo Capacidad del vehículo
     * @return array
     */
    public function agruparVotantesPorProximidad(Collection $votantes, int $maxPorGrupo): array
    {
        // Filtrar votantes sin coordenadas
        $votantesConCoordenadas = $votantes->filter(function ($v) {
            return $v->latitud && $v->longitud;
        });

        $votantesSinCoordenadas = $votantes->filter(function ($v) {
            return !$v->latitud || !$v->longitud;
        });

        if ($votantesConCoordenadas->isEmpty()) {
            // Si no hay coordenadas, agrupar secuencialmente
            return $this->agruparSecuencial($votantes, $maxPorGrupo);
        }

        $grupos = [];
        $votantesDisponibles = $votantesConCoordenadas->values();

        while ($votantesDisponibles->isNotEmpty()) {
            $grupo = collect();
            
            // Tomar el primer votante disponible como centro
            $centro = $votantesDisponibles->first();
            $grupo->push($centro);
            $votantesDisponibles = $votantesDisponibles->except([0])->values();

            // Buscar los votantes más cercanos hasta llenar el grupo
            while ($grupo->count() < $maxPorGrupo && $votantesDisponibles->isNotEmpty()) {
                $masCercano = $this->encontrarMasCercano($centro, $votantesDisponibles);
                
                if ($masCercano) {
                    $grupo->push($masCercano);
                    $votantesDisponibles = $votantesDisponibles->reject(function ($v) use ($masCercano) {
                        return $v->id === $masCercano->id;
                    })->values();
                } else {
                    break;
                }
            }

            $grupos[] = $grupo;
        }

        // Agregar votantes sin coordenadas en grupos separados
        if ($votantesSinCoordenadas->isNotEmpty()) {
            $gruposSinCoordenadas = $this->agruparSecuencial($votantesSinCoordenadas, $maxPorGrupo);
            $grupos = array_merge($grupos, $gruposSinCoordenadas);
        }

        return $grupos;
    }

    /**
     * Agrupar votantes secuencialmente
     *
     * @param Collection $votantes
     * @param int $maxPorGrupo
     * @return array
     */
    private function agruparSecuencial(Collection $votantes, int $maxPorGrupo): array
    {
        return $votantes->chunk($maxPorGrupo)->values()->toArray();
    }

    /**
     * Encontrar el votante más cercano a un punto de referencia
     *
     * @param Votante $referencia
     * @param Collection $votantes
     * @return Votante|null
     */
    private function encontrarMasCercano(Votante $referencia, Collection $votantes): ?Votante
    {
        if ($votantes->isEmpty()) {
            return null;
        }

        $minDistancia = PHP_FLOAT_MAX;
        $masCercano = null;

        foreach ($votantes as $votante) {
            $distancia = $this->calcularDistanciaEuclidiana(
                $referencia->latitud,
                $referencia->longitud,
                $votante->latitud,
                $votante->longitud
            );

            if ($distancia < $minDistancia) {
                $minDistancia = $distancia;
                $masCercano = $votante;
            }
        }

        return $masCercano;
    }

    /**
     * Calcular distancia euclidiana simple entre dos puntos
     * (aproximación, no considera curvatura terrestre)
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    private function calcularDistanciaEuclidiana(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        return sqrt(pow($lat2 - $lat1, 2) + pow($lon2 - $lon1, 2));
    }

    /**
     * Calcular distancia Haversine (más precisa para coordenadas geográficas)
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distancia en kilómetros
     */
    public function calcularDistanciaHaversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $radioTierra = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $radioTierra * $c;
    }

    /**
     * Estimar distancia total de un viaje con múltiples paradas
     *
     * @param string $puntoPartida Dirección o coordenadas
     * @param Collection $votantes
     * @param string $destino
     * @return float
     */
    public function estimarDistanciaViaje(string $puntoPartida, Collection $votantes, string $destino): float
    {
        // Simplificación: estimar basado en promedio de coordenadas
        // En producción, usar API de routing (Google Maps, MapBox, etc.)
        
        $votantesConCoordenadas = $votantes->filter(fn($v) => $v->latitud && $v->longitud);

        if ($votantesConCoordenadas->isEmpty()) {
            // Estimación por defecto si no hay coordenadas
            return 10.0; // 10 km por defecto
        }

        // Calcular centro geométrico
        $latPromedio = $votantesConCoordenadas->avg('latitud');
        $lonPromedio = $votantesConCoordenadas->avg('longitud');

        // Estimar distancia: suma de distancias entre votantes + buffer
        $distanciaTotal = 0;
        
        for ($i = 0; $i < $votantesConCoordenadas->count() - 1; $i++) {
            $v1 = $votantesConCoordenadas[$i];
            $v2 = $votantesConCoordenadas[$i + 1];
            
            $distanciaTotal += $this->calcularDistanciaHaversine(
                $v1->latitud,
                $v1->longitud,
                $v2->latitud,
                $v2->longitud
            );
        }

        // Agregar 30% de buffer para ida y vuelta
        return $distanciaTotal * 1.3;
    }

    /**
     * Generar plan de viajes completo
     *
     * @param int $liderId
     * @param string $fecha
     * @return array
     */
    public function generarPlanViajes(int $liderId, string $fecha): array
    {
        $votantes = Votante::where('lider_asignado_id', $liderId)
            ->where('necesita_transporte', true)
            ->where('ya_voto', false)
            ->get();

        $vehiculosDisponibles = Vehiculo::where('disponible', true)->get();
        $choferesDisponibles = Chofer::where('disponible', true)
            ->disponibles()
            ->conLicenciaVigente()
            ->get();

        if ($votantes->isEmpty()) {
            return [
                'error' => 'No hay votantes que necesiten transporte',
            ];
        }

        if ($vehiculosDisponibles->isEmpty() || $choferesDisponibles->isEmpty()) {
            return [
                'error' => 'No hay vehículos o choferes disponibles',
            ];
        }

        // Usar el vehículo con mayor capacidad
        $vehiculo = $vehiculosDisponibles->sortByDesc('capacidad_pasajeros')->first();
        $viajesNecesarios = $this->calcularViajesNecesarios($votantes->count(), $vehiculo->capacidad_pasajeros);

        // Agrupar votantes
        $grupos = $this->agruparVotantesPorProximidad($votantes, $vehiculo->capacidad_pasajeros);

        $plan = [
            'fecha' => $fecha,
            'total_votantes' => $votantes->count(),
            'viajes_necesarios' => $viajesNecesarios,
            'vehiculo_sugerido' => [
                'id' => $vehiculo->id,
                'placa' => $vehiculo->placa,
                'capacidad' => $vehiculo->capacidad_pasajeros,
            ],
            'grupos' => [],
            'costo_total_estimado' => 0,
        ];

        $precioCombustible = config('campana.precio_combustible', 7500);

        foreach ($grupos as $index => $grupo) {
            $chofer = $choferesDisponibles[$index % $choferesDisponibles->count()];
            $distanciaEstimada = $this->estimarDistanciaViaje('', collect($grupo), 'Centro de votación');
            $costoViaje = $this->calcularCostoViaje(
                $distanciaEstimada,
                $vehiculo->consumo_por_km,
                $precioCombustible,
                $chofer->costo_por_viaje,
                0
            );

            $plan['grupos'][] = [
                'numero_viaje' => $index + 1,
                'chofer' => [
                    'id' => $chofer->id,
                    'nombre' => $chofer->nombre_completo,
                ],
                'votantes' => collect($grupo)->map(fn($v) => [
                    'id' => $v->id,
                    'nombre' => $v->nombre_completo,
                    'direccion' => $v->direccion,
                ])->toArray(),
                'num_pasajeros' => count($grupo),
                'distancia_estimada_km' => round($distanciaEstimada, 2),
                'costo_estimado' => round($costoViaje, 2),
            ];

            $plan['costo_total_estimado'] += $costoViaje;
        }

        $plan['costo_total_estimado'] = round($plan['costo_total_estimado'], 2);

        return $plan;
    }
}
