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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            
            $table->enum('categoria', [
                'Combustible',
                'Transporte',
                'Publicidad',
                'Material impreso',
                'Eventos',
                'Alimentos',
                'Tecnología',
                'Personal',
                'Otros'
            ]);
            
            $table->string('descripcion');
            $table->decimal('monto', 12, 2);
            $table->date('fecha_gasto');
            
            $table->foreignId('usuario_registro_id')->constrained('users')->cascadeOnDelete(); // quién registró
            $table->foreignId('viaje_id')->nullable()->constrained('viajes')->nullOnDelete(); // si es gasto de viaje
            
            $table->string('numero_recibo', 50)->nullable();
            $table->string('proveedor', 100)->nullable();
            $table->string('archivo_recibo')->nullable(); // path al archivo del recibo
            
            $table->boolean('aprobado')->default(false);
            $table->foreignId('aprobado_por_usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('aprobado_en')->nullable();
            
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->index('categoria');
            $table->index('fecha_gasto');
            $table->index('aprobado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
