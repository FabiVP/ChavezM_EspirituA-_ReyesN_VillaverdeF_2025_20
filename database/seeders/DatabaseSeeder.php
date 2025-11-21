<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // 1. Usuarios: Carga SOLO al administrador (ya modificado en UsersTableSeeder)
        $this->call(UsersTableSeeder::class);
        
        // 2. Tablas de configuración:
        $this->call(SpecialtiesTableSeeder::class);
        $this->call(HorariosTableSeeder::class);
        
        // 3. Citas: Comentar para EVITAR EL ERROR (ya no hay pacientes que usar)
        // $this->call(AppointmentsTableSeeder::class);
    }
}