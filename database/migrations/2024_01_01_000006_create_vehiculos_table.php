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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 20)->unique();
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->integer('año')->nullable();
            $table->string('color', 30)->nullable();
            $table->integer('capacidad_pasajeros')->default(4); // capacidad de pasajeros
            $table->decimal('consumo_por_km', 8, 2)->default(0.10); // litros por km
            $table->decimal('costo_por_km', 10, 2)->nullable(); // costo calculado por km
            $table->enum('tipo', ['Auto', 'Camioneta', 'Van', 'Bus', 'Moto'])->default('Auto');
            $table->boolean('disponible')->default(true);
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->index('placa');
            $table->index('disponible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
