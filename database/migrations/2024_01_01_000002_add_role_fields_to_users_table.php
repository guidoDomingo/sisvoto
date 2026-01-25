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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
            $table->string('telefono', 20)->nullable()->after('email');
            $table->string('ci', 20)->nullable()->unique()->after('telefono');
            $table->boolean('activo')->default(true)->after('ci');
            $table->timestamp('ultimo_acceso')->nullable()->after('activo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['role_id', 'telefono', 'ci', 'activo', 'ultimo_acceso']);
        });
    }
};
