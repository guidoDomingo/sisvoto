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
        Schema::create('votantes', function (Blueprint $table) {
            $table->id();
            
            // Datos personales
            $table->string('ci', 20)->nullable()->unique(); // cédula de identidad
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['M', 'F', 'Otro', 'Prefiero no decir'])->nullable();
            $table->string('ocupacion', 100)->nullable();
            
            // Ubicación
            $table->text('direccion')->nullable();
            $table->string('barrio', 100)->nullable();
            $table->string('zona', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            
            // Asignación y organización
            $table->foreignId('lider_asignado_id')->nullable()->constrained('lideres')->nullOnDelete();
            $table->foreignId('creado_por_usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('actualizado_por_usuario_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Estado de contacto y voto
            $table->enum('codigo_intencion', ['A', 'B', 'C', 'D', 'E'])->default('C'); // A=seguro, E=contrario
            $table->enum('estado_contacto', [
                'Nuevo',
                'Contactado', 
                'Re-contacto',
                'Comprometido',
                'Crítico'
            ])->default('Nuevo');
            
            // Día D - Estado de voto
            $table->boolean('ya_voto')->default(false);
            $table->timestamp('voto_registrado_en')->nullable();
            $table->boolean('necesita_transporte')->default(false);
            
            // Notas y observaciones
            $table->text('notas')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes(); // para no eliminar definitivamente
            
            // Índices para optimización
            $table->index('ci');
            $table->index('telefono');
            $table->index('lider_asignado_id');
            $table->index('codigo_intencion');
            $table->index('estado_contacto');
            $table->index('ya_voto');
            $table->index('necesita_transporte');
            $table->index(['barrio', 'zona', 'distrito']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votantes');
    }
};
