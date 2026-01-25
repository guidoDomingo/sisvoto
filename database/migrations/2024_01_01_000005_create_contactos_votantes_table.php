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
        Schema::create('contactos_votantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('votante_id')->constrained('votantes')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete(); // quien contactó
            
            $table->timestamp('contactado_en');
            $table->enum('metodo', [
                'Puerta a puerta',
                'WhatsApp',
                'Llamada',
                'Visita programada',
                'Evento',
                'Otro'
            ]);
            
            $table->enum('resultado', [
                'Exitoso',
                'No responde',
                'Rechaza',
                'Solicita más info',
                'Comprometido',
                'Pendiente seguimiento'
            ])->default('Exitoso');
            
            $table->text('notas')->nullable();
            $table->enum('intencion_detectada', ['A', 'B', 'C', 'D', 'E'])->nullable(); // intención detectada en este contacto
            
            $table->timestamps();
            
            $table->index('votante_id');
            $table->index('usuario_id');
            $table->index('contactado_en');
            $table->index('metodo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactos_votantes');
    }
};
