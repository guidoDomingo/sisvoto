<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\VotanteController;
use App\Http\Controllers\Api\V1\PrediccionController;
use App\Http\Controllers\Api\V1\ImportacionController;
use App\Http\Controllers\Api\V1\MetricasController;
use App\Http\Controllers\Api\V1\ViajeController;
use App\Http\Controllers\Api\V1\GastoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    
    // Rutas públicas (para testing, en producción agregar autenticación)
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now()->toIso8601String(),
            'version' => '1.0.0'
        ]);
    });

    // Rutas protegidas (requieren autenticación)
    // Route::middleware('auth:sanctum')->group(function () {
    
    // VOTANTES
    Route::prefix('votantes')->name('votantes.')->group(function () {
        Route::get('/', [VotanteController::class, 'index'])->name('index');
        Route::post('/', [VotanteController::class, 'store'])->name('store');
        Route::get('/{id}', [VotanteController::class, 'show'])->name('show');
        Route::put('/{id}', [VotanteController::class, 'update'])->name('update');
        Route::delete('/{id}', [VotanteController::class, 'destroy'])->name('destroy');
        Route::put('/{id}/marcar-voto', [VotanteController::class, 'marcarVoto'])->name('marcar-voto');
        Route::post('/reasignar-lider', [VotanteController::class, 'reasignarLider'])->name('reasignar-lider');
    });

    // IMPORTACIÓN
    Route::prefix('importacion')->name('importacion.')->group(function () {
        Route::post('/votantes', [ImportacionController::class, 'importar'])->name('importar');
        Route::get('/plantilla', [ImportacionController::class, 'descargarPlantilla'])->name('plantilla');
    });

    // PREDICCIONES
    Route::prefix('predicciones')->name('predicciones.')->group(function () {
        Route::get('/', [PrediccionController::class, 'index'])->name('index');
        Route::get('/heuristico', [PrediccionController::class, 'heuristico'])->name('heuristico');
        Route::get('/montecarlo', [PrediccionController::class, 'montecarlo'])->name('montecarlo');
        Route::get('/combinado', [PrediccionController::class, 'combinado'])->name('combinado');
    });

    // MÉTRICAS
    Route::prefix('metricas')->name('metricas.')->group(function () {
        Route::get('/generales', [MetricasController::class, 'generales'])->name('generales');
        Route::get('/lider/{liderId}', [MetricasController::class, 'porLider'])->name('por-lider');
        Route::get('/conversion-contactos', [MetricasController::class, 'conversionContactos'])->name('conversion-contactos');
        Route::get('/costo-por-voto', [MetricasController::class, 'costoPorVoto'])->name('costo-por-voto');
        Route::get('/roi', [MetricasController::class, 'roi'])->name('roi');
    });

    // VIAJES
    Route::prefix('viajes')->name('viajes.')->group(function () {
        Route::get('/', [ViajeController::class, 'index'])->name('index');
        Route::post('/', [ViajeController::class, 'store'])->name('store');
        Route::get('/{id}', [ViajeController::class, 'show'])->name('show');
        Route::put('/{id}', [ViajeController::class, 'update'])->name('update');
        Route::post('/generar-plan', [ViajeController::class, 'generarPlan'])->name('generar-plan');
    });

    // GASTOS
    Route::prefix('gastos')->name('gastos.')->group(function () {
        Route::get('/', [GastoController::class, 'index'])->name('index');
        Route::post('/', [GastoController::class, 'store'])->name('store');
        Route::get('/{id}', [GastoController::class, 'show'])->name('show');
        Route::put('/{id}/aprobar', [GastoController::class, 'aprobar'])->name('aprobar');
        Route::get('/resumen/por-categoria', [GastoController::class, 'resumenPorCategoria'])->name('resumen-categoria');
    });

    // }); // Fin middleware auth:sanctum
});

