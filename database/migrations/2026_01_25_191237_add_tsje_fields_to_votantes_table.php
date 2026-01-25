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
        Schema::table('votantes', function (Blueprint $table) {
            // Campos específicos del Excel TSJE
            $table->string('nro_registro', 20)->nullable()->after('ci');
            $table->string('codigo_departamento', 10)->nullable()->after('distrito');
            $table->string('departamento', 100)->nullable()->after('codigo_departamento');
            $table->string('codigo_distrito', 10)->nullable()->after('departamento');
            $table->string('codigo_seccion', 10)->nullable()->after('codigo_distrito');
            $table->string('seccion', 100)->nullable()->after('codigo_seccion');
            $table->string('local_votacion', 200)->nullable()->after('seccion');
            $table->string('descripcion_local', 300)->nullable()->after('local_votacion');
            $table->string('mesa', 10)->nullable()->after('descripcion_local');
            $table->integer('orden')->nullable()->after('mesa');
            $table->date('fecha_afiliacion')->nullable()->after('fecha_nacimiento');
            
            // Índices para optimización
            $table->index('nro_registro');
            $table->index(['codigo_departamento', 'codigo_distrito']);
            $table->index('mesa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('votantes', function (Blueprint $table) {
            // Eliminar campos en orden inverso
            $table->dropIndex(['mesa']);
            $table->dropIndex(['codigo_departamento', 'codigo_distrito']);
            $table->dropIndex(['nro_registro']);
            
            $table->dropColumn([
                'fecha_afiliacion',
                'orden',
                'mesa',
                'descripcion_local',
                'local_votacion',
                'seccion',
                'codigo_seccion',
                'codigo_distrito',
                'departamento',
                'codigo_departamento',
                'nro_registro',
            ]);
        });
    }
};
