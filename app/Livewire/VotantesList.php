<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Votante;
use App\Models\Lider;
use Illuminate\Support\Facades\Auth;

class VotantesList extends Component
{
    use WithPagination;

    public $search = '';
    public $filtroIntencion = '';
    public $filtroEstado = '';
    public $filtroTransporte = '';
    public $filtroLider = '';
    public $filtroDistrito = '';
    public $sortBy = 'created_at';
    public $sortDir = 'desc';
    public $perPage = 15;

    public $showModal = false;
    public $editingVotante = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filtroIntencion' => ['except' => ''],
        'filtroEstado' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'asc';
        }
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'filtroIntencion', 'filtroEstado', 'filtroTransporte', 'filtroLider', 'filtroDistrito']);
        $this->resetPage();
    }

    public function editarVotante($id)
    {
        $this->editingVotante = $id;
        $this->showModal = true;
    }

    public function marcarVoto($id)
    {
        $votante = Votante::findOrFail($id);
        $votante->ya_voto = true;
        $votante->voto_registrado_en = now();
        $votante->save();

        $this->dispatch('votante-actualizado');
        session()->flash('message', 'Voto registrado exitosamente.');
    }

    public function eliminarVotante($id)
    {
        Votante::findOrFail($id)->delete();
        $this->dispatch('votante-eliminado');
        session()->flash('message', 'Votante eliminado exitosamente.');
    }

    public function render()
    {
        $user = Auth::user();
        $query = Votante::query()->with('lider.usuario');

        // Filtrar por líder si no es admin
        if ($user->hasRole('Líder') && $user->lider) {
            $query->where('lider_asignado_id', $user->lider->id);
        }

        // Búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                    ->orWhere('ci', 'like', '%' . $this->search . '%')
                    ->orWhere('telefono', 'like', '%' . $this->search . '%');
            });
        }

        // Filtros
        if ($this->filtroIntencion) {
            $query->where('codigo_intencion', $this->filtroIntencion);
        }

        if ($this->filtroEstado) {
            $query->where('estado_contacto', $this->filtroEstado);
        }

        if ($this->filtroTransporte !== '') {
            $query->where('necesita_transporte', $this->filtroTransporte);
        }

        if ($this->filtroLider) {
            $query->where('lider_asignado_id', $this->filtroLider);
        }

        if ($this->filtroDistrito) {
            $query->where('distrito', 'like', '%' . $this->filtroDistrito . '%');
        }

        // Ordenamiento
        $query->orderBy($this->sortBy, $this->sortDir);

        $votantes = $query->paginate($this->perPage);

        // Obtener líderes para filtro
        $lideres = Lider::with('usuario')->get();

        // Obtener distritos únicos para filtro
        $distritos = Votante::whereNotNull('distrito')
                            ->distinct()
                            ->pluck('distrito')
                            ->sort()
                            ->values();

        return view('livewire.votantes-list', [
            'votantes' => $votantes,
            'lideres' => $lideres,
            'distritos' => $distritos,
        ])->layout('layouts.app');
    }
}
