<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleSuperAdmin = Role::where('slug', 'superadmin')->first();
        $roleCoordinador = Role::where('slug', 'coordinador')->first();
        $roleLider = Role::where('slug', 'lider')->first();
        $roleVoluntario = Role::where('slug', 'voluntario')->first();
        $roleAuditor = Role::where('slug', 'auditor')->first();

        // Super Admin
        User::create([
            'name' => 'Administrador Sistema',
            'email' => 'admin@campana.com',
            'password' => Hash::make('password'),
            'role_id' => $roleSuperAdmin->id,
            'telefono' => '0981-123456',
            'ci' => '1000000',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        // Coordinadores
        User::create([
            'name' => 'Carlos Coordinador',
            'email' => 'coordinador@campana.com',
            'password' => Hash::make('password'),
            'role_id' => $roleCoordinador->id,
            'telefono' => '0981-234567',
            'ci' => '2000000',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'María González',
            'email' => 'maria.coordinador@campana.com',
            'password' => Hash::make('password'),
            'role_id' => $roleCoordinador->id,
            'telefono' => '0981-345678',
            'ci' => '2000001',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        // Líderes
        $lideres = [
            ['name' => 'Juan Líder', 'email' => 'lider@campana.com', 'ci' => '3000000', 'telefono' => '0981-456789'],
            ['name' => 'Ana Martínez', 'email' => 'ana.lider@campana.com', 'ci' => '3000001', 'telefono' => '0981-567890'],
            ['name' => 'Pedro Sánchez', 'email' => 'pedro.lider@campana.com', 'ci' => '3000002', 'telefono' => '0981-678901'],
            ['name' => 'Lucía Benítez', 'email' => 'lucia.lider@campana.com', 'ci' => '3000003', 'telefono' => '0981-789012'],
            ['name' => 'Roberto Flores', 'email' => 'roberto.lider@campana.com', 'ci' => '3000004', 'telefono' => '0981-890123'],
        ];

        foreach ($lideres as $lider) {
            User::create([
                'name' => $lider['name'],
                'email' => $lider['email'],
                'password' => Hash::make('password'),
                'role_id' => $roleLider->id,
                'telefono' => $lider['telefono'],
                'ci' => $lider['ci'],
                'activo' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Voluntarios
        $voluntarios = [
            ['name' => 'José Voluntario', 'email' => 'voluntario@campana.com', 'ci' => '4000000', 'telefono' => '0981-901234'],
            ['name' => 'Carmen López', 'email' => 'carmen.vol@campana.com', 'ci' => '4000001', 'telefono' => '0981-012345'],
            ['name' => 'Miguel Torres', 'email' => 'miguel.vol@campana.com', 'ci' => '4000002', 'telefono' => '0981-112345'],
            ['name' => 'Rosa Giménez', 'email' => 'rosa.vol@campana.com', 'ci' => '4000003', 'telefono' => '0981-212345'],
            ['name' => 'Antonio Ramírez', 'email' => 'antonio.vol@campana.com', 'ci' => '4000004', 'telefono' => '0981-312345'],
            ['name' => 'Isabel Núñez', 'email' => 'isabel.vol@campana.com', 'ci' => '4000005', 'telefono' => '0981-412345'],
            ['name' => 'Diego Acosta', 'email' => 'diego.vol@campana.com', 'ci' => '4000006', 'telefono' => '0981-512345'],
            ['name' => 'Patricia Vera', 'email' => 'patricia.vol@campana.com', 'ci' => '4000007', 'telefono' => '0981-612345'],
        ];

        foreach ($voluntarios as $voluntario) {
            User::create([
                'name' => $voluntario['name'],
                'email' => $voluntario['email'],
                'password' => Hash::make('password'),
                'role_id' => $roleVoluntario->id,
                'telefono' => $voluntario['telefono'],
                'ci' => $voluntario['ci'],
                'activo' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Auditor
        User::create([
            'name' => 'Auditor Sistema',
            'email' => 'auditor@campana.com',
            'password' => Hash::make('password'),
            'role_id' => $roleAuditor->id,
            'telefono' => '0981-712345',
            'ci' => '5000000',
            'activo' => true,
            'email_verified_at' => now(),
        ]);
    }
}
