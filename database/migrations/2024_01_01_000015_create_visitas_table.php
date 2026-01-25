<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('votante_id')->constrained('votantes')->onDelete('cascade');
            $table->foreignId('lider_id')->constrained('lideres')->onDelete('cascade');
            $table->foreignId('usuario_registro_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('fecha_visita');
            $table->enum('tipo_visita', ['Primera visita', 'Seguimiento', 'Convencimiento', 'Confirmación', 'Urgente'])->default('Primera visita');
            $table->enum('resultado', ['Favorable', 'Indeciso', 'No favorable', 'No contactado', 'Rechazado'])->nullable();
            $table->text('observaciones')->nullable();
            $table->text('compromisos')->nullable();
            $table->dateTime('proxima_visita')->nullable();
            $table->boolean('requiere_seguimiento')->default(false);
            $table->string('foto_evidencia')->nullable();
            $table->decimal('duracion_minutos', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index('fecha_visita');
            $table->index('resultado');
            $table->index('requiere_seguimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
