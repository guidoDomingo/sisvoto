<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Votante;
use App\Models\Vehiculo;
use App\Models\Chofer;
use App\Models\Lider;
use App\Models\Viaje;
use App\Services\TripPlannerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TripPlanner extends Component
{
    public $paso = 1; // 1: Seleccionar votantes, 2: Configurar viaje, 3: Resultado

    // Paso 1: Selección
    public $lider_id;
    public $votantesSeleccionados = [];
    public $filtroBarrio = '';
    public $filtroZona = '';

    // Paso 2: Configuración
    public $vehiculo_id;
    public $chofer_id;
    public $fecha_viaje;
    public $hora_salida = '07:00';
    public $punto_partida;
    public $viaticos = 20000;

    // Paso 3: Resultado
    public $planGenerado = null;

    protected $rules = [
        'votantesSeleccionados' => 'required|array|min:1',
        'vehiculo_id' => 'required|exists:vehiculos,id',
        'chofer_id' => 'required|exists:choferes,id',
        'fecha_viaje' => 'required|date|after_or_equal:today',
        'hora_salida' => 'required',
        'punto_partida' => 'required|string|max:255',
        'viaticos' => 'required|numeric|min:0',
    ];

    public function mount()
    {
        $user = Auth::user();
        
        // Solo asignar líder si el usuario es líder
        if ($user->hasRole('Líder') && $user->lider) {
            $this->lider_id = $user->lider->id;
        }
        // Si es Admin o Coordinador, no filtrar por líder

        $this->fecha_viaje = now()->addDays(1)->format('Y-m-d');
    }

    public function toggleVotante($votanteId)
    {
        if (in_array($votanteId, $this->votantesSeleccionados)) {
            $this->votantesSeleccionados = array_diff($this->votantesSeleccionados, [$votanteId]);
        } else {
            $this->votantesSeleccionados[] = $votanteId;
        }
    }

    public function seleccionarTodos()
    {
        $votantes = $this->obtenerVotantesDisponibles()->pluck('id')->toArray();
        $this->votantesSeleccionados = $votantes;
    }

    public function limpiarSeleccion()
    {
        $this->votantesSeleccionados = [];
    }

    public function siguientePaso()
    {
        if ($this->paso === 1) {
            $this->validate([
                'votantesSeleccionados' => 'required|array|min:1',
            ], [
                'votantesSeleccionados.required' => 'Debe seleccionar al menos un votante',
                'votantesSeleccionados.min' => 'Debe seleccionar al menos un votante',
            ]);
            $this->paso = 2;
        } elseif ($this->paso === 2) {
            $this->generarPlan();
        }
    }

    public function pasoAnterior()
    {
        if ($this->paso > 1) {
            $this->paso--;
        }
    }

    public function generarPlan()
    {
        $this->validate();

        try {
            $vehiculo = Vehiculo::findOrFail($this->vehiculo_id);
            $chofer = Chofer::findOrFail($this->chofer_id);

            // Obtener votantes seleccionados
            $votantes = Votante::whereIn('id', $this->votantesSeleccionados)->get();

            if ($votantes->isEmpty()) {
                session()->flash('error', 'No hay votantes seleccionados.');
                return;
            }

            // Agrupar votantes por capacidad del vehículo
            $capacidad = $vehiculo->capacidad_pasajeros;
            $grupos = $votantes->chunk($capacidad);
            
            $planGrupos = [];
            $costoTotal = 0;

            foreach ($grupos as $index => $grupo) {
                $distanciaEstimada = 20; // km aproximados por defecto
                
                // Calcular costo
                $costoCombustible = $distanciaEstimada * $vehiculo->consumo_por_km * 7500;
                $costoChofer = $chofer->costo_por_viaje;
                $costoViaje = $costoCombustible + $costoChofer + $this->viaticos;
                
                $planGrupos[] = [
                    'numero_viaje' => $index + 1,
                    'votantes' => $grupo->map(function($v) {
                        return [
                            'id' => $v->id,
                            'nombres' => $v->nombres,
                            'apellidos' => $v->apellidos,
                            'barrio' => $v->barrio,
                        ];
                    })->toArray(),
                    'distancia_estimada_km' => $distanciaEstimada,
                    'costo_estimado' => $costoViaje,
                ];
                
                $costoTotal += $costoViaje;
            }

            $this->planGenerado = [
                'grupos' => $planGrupos,
                'total_viajes' => count($planGrupos),
                'total_votantes' => $votantes->count(),
                'costo_total' => $costoTotal,
            ];

            $this->paso = 3;
        } catch (\Exception $e) {
            session()->flash('error', 'Error al generar plan: ' . $e->getMessage());
        }
    }

    public function confirmarYGuardar()
    {
        if (!$this->planGenerado) {
            session()->flash('error', 'No hay plan generado para guardar.');
            return;
        }

        DB::beginTransaction();
        try {
            $chofer = Chofer::findOrFail($this->chofer_id);
            
            // Si no hay líder asignado, usar el primero disponible o null
            $liderResponsableId = $this->lider_id ?: optional(Lider::first())->id;

            foreach ($this->planGenerado['grupos'] as $grupo) {
                // Crear viaje
                $viaje = Viaje::create([
                    'vehiculo_id' => $this->vehiculo_id,
                    'chofer_id' => $this->chofer_id,
                    'lider_responsable_id' => $liderResponsableId,
                    'fecha_viaje' => $this->fecha_viaje,
                    'hora_salida' => $this->hora_salida,
                    'punto_partida' => $this->punto_partida,
                    'destino' => 'Centro de votación',
                    'distancia_estimada_km' => $grupo['distancia_estimada_km'],
                    'costo_combustible' => ($grupo['distancia_estimada_km'] * 0.1 * 7500),
                    'costo_chofer' => $chofer->costo_por_viaje,
                    'viaticos' => $this->viaticos,
                    'costo_total' => $grupo['costo_estimado'],
                    'estado' => 'Planificado',
                ]);

                // Asociar votantes
                $votanteIds = collect($grupo['votantes'])->pluck('id')->toArray();
                $viaje->votantes()->attach($votanteIds);
            }

            DB::commit();
            
            session()->flash('message', 'Plan de viajes guardado exitosamente. ' . count($this->planGenerado['grupos']) . ' viaje(s) creado(s).');
            
            return redirect()->route('viajes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Error al guardar viajes: ' . $e->getMessage());
            \Log::error('Error al guardar viajes: ' . $e->getMessage(), ['exception' => $e]);
        }
    }

    public function reiniciar()
    {
        $this->reset(['paso', 'votantesSeleccionados', 'planGenerado']);
        $this->fecha_viaje = now()->addDays(1)->format('Y-m-d');
    }

    private function obtenerVotantesDisponibles()
    {
        $query = Votante::query()
            ->where('necesita_transporte', true)
            ->where('ya_voto', false);

        if ($this->lider_id) {
            $query->where('lider_id', $this->lider_id);
        }

        if ($this->filtroBarrio) {
            $query->where('barrio', $this->filtroBarrio);
        }

        if ($this->filtroZona) {
            $query->where('zona', $this->filtroZona);
        }

        return $query->get();
    }

    public function render()
    {
        $votantesDisponibles = $this->obtenerVotantesDisponibles();
        $vehiculos = Vehiculo::where('disponible', true)->get();
        $choferes = Chofer::where('disponible', true)->get();
        $lideres = Lider::with('usuario')->get();

        return view('livewire.trip-planner', [
            'votantesDisponibles' => $votantesDisponibles,
            'vehiculos' => $vehiculos,
            'choferes' => $choferes,
            'lideres' => $lideres,
        ])->layout('layouts.app');
    }
}
