<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Votante;
use App\Models\Lider;
use App\Services\PredictionService;
use Illuminate\Support\Facades\Auth;

class LeaderDashboard extends Component
{
    public $lider;
    public $estadisticas = [];
    public $prediccion = [];
    public $votantesRecientes = [];
    public $votantesCriticos = [];

    // Modal para acciones rápidas
    public $showContactoModal = false;
    public $votanteSeleccionado = null;
    public $metodoContacto = 'Llamada';
    public $resultadoContacto = '';
    public $nuevaIntencion = '';

    public function mount()
    {
        $user = Auth::user();
        
        if ($user->hasRole('Líder') && $user->lider) {
            $this->lider = $user->lider;
            $this->cargarDatos();
        }
    }

    public function cargarDatos()
    {
        if (!$this->lider) {
            return;
        }

        // Estadísticas del líder
        $this->estadisticas = [
            'total_asignados' => $this->lider->votantes()->count(),
            'contactados' => $this->lider->votantes()->where('estado_contacto', '!=', 'Nuevo')->count(),
            'intencion_a_b' => $this->lider->votantes()->whereIn('codigo_intencion', ['A', 'B'])->count(),
            'necesitan_transporte' => $this->lider->votantes()->where('necesita_transporte', true)->count(),
            'ya_votaron' => $this->lider->votantes()->where('ya_voto', true)->count(),
        ];

        // Predicción de votos
        $predictionService = new PredictionService();
        $this->prediccion = $predictionService->heuristicPrediction([
            'lider_id' => $this->lider->id
        ]);

        // Votantes recientes
        $this->votantesRecientes = $this->lider->votantes()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Votantes críticos (requieren atención)
        $this->votantesCriticos = $this->lider->votantes()
            ->where(function ($query) {
                $query->where('estado_contacto', 'Crítico')
                    ->orWhere(function ($q) {
                        $q->where('estado_contacto', 'Nuevo')
                            ->where('created_at', '<', now()->subDays(3));
                    });
            })
            ->take(10)
            ->get();
    }

    public function abrirModalContacto($votanteId)
    {
        $this->votanteSeleccionado = Votante::findOrFail($votanteId);
        $this->nuevaIntencion = $this->votanteSeleccionado->codigo_intencion;
        $this->showContactoModal = true;
    }

    public function registrarContacto()
    {
        $this->validate([
            'metodoContacto' => 'required',
            'resultadoContacto' => 'required|string|max:500',
            'nuevaIntencion' => 'required|in:A,B,C,D,E',
        ]);

        if (!$this->votanteSeleccionado) {
            return;
        }

        // Actualizar votante
        $this->votanteSeleccionado->update([
            'codigo_intencion' => $this->nuevaIntencion,
            'estado_contacto' => 'Contactado',
        ]);

        // Registrar contacto
        $this->votanteSeleccionado->contactos()->create([
            'contactado_en' => now(),
            'metodo' => $this->metodoContacto,
            'resultado' => $this->resultadoContacto,
            'codigo_intencion_momento' => $this->nuevaIntencion,
            'usuario_id' => Auth::id(),
        ]);

        $this->showContactoModal = false;
        $this->reset(['votanteSeleccionado', 'metodoContacto', 'resultadoContacto', 'nuevaIntencion']);
        $this->cargarDatos();

        session()->flash('message', 'Contacto registrado exitosamente.');
    }

    public function marcarVoto($votanteId)
    {
        $votante = Votante::findOrFail($votanteId);
        $votante->update([
            'ya_voto' => true,
            'voto_registrado_en' => now(),
        ]);

        $this->cargarDatos();
        session()->flash('message', 'Voto registrado exitosamente.');
    }

    public function exportarLista()
    {
        $votantes = $this->lider->votantes()->get();

        $csv = "CI,Nombres,Apellidos,Teléfono,Dirección,Barrio,Intención,Estado,Transporte,Ya Votó\n";
        
        foreach ($votantes as $v) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s,%s,%s,%s\n",
                $v->ci,
                $v->nombres,
                $v->apellidos,
                $v->telefono,
                $v->direccion,
                $v->barrio,
                $v->codigo_intencion,
                $v->estado_contacto,
                $v->necesita_transporte ? 'Sí' : 'No',
                $v->ya_voto ? 'Sí' : 'No'
            );
        }

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'votantes_' . $this->lider->territorio . '_' . now()->format('Y-m-d') . '.csv');
    }

    public function render()
    {
        return view('livewire.leader-dashboard')
            ->layout('layouts.app');
    }
}
