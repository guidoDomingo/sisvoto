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
        Schema::create('lideres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->string('territorio')->nullable(); // zona/distrito/barrio asignado
            $table->text('descripcion_territorio')->nullable();
            $table->integer('meta_votos')->default(0); // meta de votos asignada
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            $table->foreignId('coordinador_id')->nullable()->constrained('users')->nullOnDelete(); // coordinador superior
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index('territorio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lideres');
    }
};
