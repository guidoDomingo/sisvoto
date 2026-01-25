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
        Schema::create('pasajeros_viaje', function (Blueprint $table) {
            $table->id();
            $table->foreignId('viaje_id')->constrained('viajes')->cascadeOnDelete();
            $table->foreignId('votante_id')->constrained('votantes')->cascadeOnDelete();
            
            $table->integer('orden_recogida')->nullable(); // orden en la ruta
            $table->string('punto_recogida')->nullable(); // dirección específica de recogida
            $table->boolean('fue_recogido')->default(false);
            $table->timestamp('recogido_en')->nullable();
            $table->boolean('confirmo_voto')->default(false); // confirmó que votó
            
            $table->timestamps();
            
            $table->unique(['viaje_id', 'votante_id']); // un votante no puede estar dos veces en el mismo viaje
            $table->index('viaje_id');
            $table->index('votante_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasajeros_viaje');
    }
};
