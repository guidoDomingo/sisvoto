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
        Schema::create('choferes', function (Blueprint $table) {
            $table->id();
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('ci', 20)->unique();
            $table->string('telefono', 20);
            $table->string('licencia', 30)->nullable(); // número de licencia de conducir
            $table->date('licencia_vencimiento')->nullable();
            $table->decimal('costo_por_viaje', 10, 2)->default(0); // pago por viaje
            $table->boolean('disponible')->default(true);
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->index('ci');
            $table->index('disponible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('choferes');
    }
};
