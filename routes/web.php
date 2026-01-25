<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\VotantesList;
use App\Livewire\VotanteForm;
use App\Livewire\VotanteImporter;
use App\Livewire\TripPlanner;
use App\Livewire\LeaderDashboard;
use App\Livewire\PrediccionVotos;
use App\Livewire\ViajesList;
use App\Livewire\VisitasList;
use App\Http\Controllers\PlantillaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
require __DIR__.'/auth.php';

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Leader Dashboard
    Route::get('/lider/dashboard', LeaderDashboard::class)
        ->name('lider.dashboard');
    
    // Votantes
    Route::get('/votantes', VotantesList::class)->name('votantes.index');
    Route::get('/votantes/crear', VotanteForm::class)->name('votantes.create');
    Route::get('/votantes/{votanteId}/editar', VotanteForm::class)->name('votantes.edit');
    Route::get('/votantes/plantilla', [PlantillaController::class, 'descargarPlantillaVotantes'])->name('votantes.plantilla');
    
    // Importación
    Route::get('/importar', VotanteImporter::class)->name('importar');
    
    // Predicciones
    Route::get('/predicciones', PrediccionVotos::class)->name('predicciones');
    
    // Viajes
    Route::get('/viajes', ViajesList::class)->name('viajes.index');
    Route::get('/viajes/planificar', TripPlanner::class)->name('viajes.planner');
    
    // Visitas
    Route::get('/visitas', VisitasList::class)->name('visitas.index');
});
