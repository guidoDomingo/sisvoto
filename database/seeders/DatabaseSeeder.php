<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            LiderSeeder::class,
            VotanteSeeder::class,
            ContactoVotanteSeeder::class,
            VehiculoSeeder::class,
            ChoferSeeder::class,
            ViajeSeeder::class,
            GastoSeeder::class,
        ]);

        $this->command->info('✅ Base de datos poblada exitosamente!');
        $this->command->info('📊 Resumen:');
        $this->command->info('   - Roles: 5');
        $this->command->info('   - Usuarios: ~17');
        $this->command->info('   - Líderes: 5');
        $this->command->info('   - Votantes: 250');
        $this->command->info('   - Vehículos: 8');
        $this->command->info('   - Choferes: 6');
        $this->command->info('   - Viajes: 15');
        $this->command->info('   - Gastos: 50');
        $this->command->newLine();
        $this->command->info('🔑 Credenciales de acceso:');
        $this->command->info('   Email: admin@campana.com | Password: password');
        $this->command->info('   Email: coordinador@campana.com | Password: password');
        $this->command->info('   Email: lider@campana.com | Password: password');
        $this->command->info('   Email: voluntario@campana.com | Password: password');
    }
}
