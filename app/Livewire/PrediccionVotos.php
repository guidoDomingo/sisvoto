<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\PredictionService;
use App\Models\Lider;
use Illuminate\Support\Facades\Auth;

class PrediccionVotos extends Component
{
    public $modelo = 'heuristico'; // heuristico, montecarlo, combinado
    public $iteraciones = 1000;
    public $lider_id = null;
    public $barrio = '';
    public $zona = '';
    public $distrito = '';

    public $resultado = null;
    public $cargando = false;

    public function mount()
    {
        $user = Auth::user();
        
        if ($user->hasRole('Líder') && $user->lider) {
            $this->lider_id = $user->lider->id;
        }
    }

    public function calcular()
    {
        $this->cargando = true;
        $this->resultado = null;

        try {
            $predictionService = new PredictionService();
            
            // Construir query con filtros
            $query = \App\Models\Votante::query();
            
            if ($this->lider_id) {
                $query->where('lider_id', $this->lider_id);
            }
            if ($this->barrio) {
                $query->where('barrio', $this->barrio);
            }
            if ($this->zona) {
                $query->where('zona', $this->zona);
            }
            if ($this->distrito) {
                $query->where('distrito', $this->distrito);
            }
            
            $votantes = $query->get();

            switch ($this->modelo) {
                case 'montecarlo':
                    $this->resultado = $predictionService->monteCarloPrediction(
                        $this->iteraciones,
                        $votantes
                    );
                    break;
                
                case 'combinado':
                    $this->resultado = $predictionService->combinedPrediction(
                        $this->iteraciones,
                        $votantes
                    );
                    break;
                
                default:
                    $this->resultado = $predictionService->heuristicPrediction($votantes);
                    break;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al calcular predicción: ' . $e->getMessage());
        } finally {
            $this->cargando = false;
        }
    }

    public function limpiar()
    {
        $this->reset(['resultado', 'barrio', 'zona', 'distrito']);
        
        $user = Auth::user();
        if (!($user->hasRole('Líder') && $user->lider)) {
            $this->lider_id = null;
        }
    }

    public function render()
    {
        $lideres = Lider::with('usuario')->get();

        return view('livewire.prediccion-votos', [
            'lideres' => $lideres,
        ])->layout('layouts.app');
    }
}
