<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vehiculo_id')->constrained('vehiculos')->cascadeOnDelete();
            $table->foreignId('chofer_id')->constrained('choferes')->cascadeOnDelete();
            $table->foreignId('lider_responsable_id')->nullable()->constrained('lideres')->nullOnDelete();
            
            $table->date('fecha_viaje');
            $table->time('hora_salida')->nullable();
            $table->time('hora_regreso_estimada')->nullable();
            
            $table->string('punto_partida')->nullable(); // dirección de inicio
            $table->string('destino')->nullable(); // generalmente lugar de votación
            
            $table->decimal('distancia_estimada_km', 10, 2)->nullable();
            $table->decimal('costo_combustible', 10, 2)->nullable(); // calculado
            $table->decimal('costo_chofer', 10, 2)->nullable(); // pago al chofer
            $table->decimal('viaticos', 10, 2)->default(0); // otros gastos
            $table->decimal('costo_total', 10, 2)->nullable(); // suma de todo
            
            $table->enum('estado', [
                'Planificado',
                'Confirmado',
                'En curso',
                'Completado',
                'Cancelado'
            ])->default('Planificado');
            
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->index('fecha_viaje');
            $table->index('estado');
            $table->index('vehiculo_id');
            $table->index('chofer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};
