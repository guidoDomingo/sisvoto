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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete(); // quién hizo la acción
            $table->string('accion', 100); // Crear, Actualizar, Eliminar, Marcar voto, etc.
            $table->string('modelo', 100); // nombre del modelo afectado
            $table->unsignedBigInteger('modelo_id')->nullable(); // ID del registro afectado
            
            $table->json('valores_anteriores')->nullable(); // valores antes del cambio
            $table->json('valores_nuevos')->nullable(); // valores después del cambio
            
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            $table->index('usuario_id');
            $table->index(['modelo', 'modelo_id']);
            $table->index('accion');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
