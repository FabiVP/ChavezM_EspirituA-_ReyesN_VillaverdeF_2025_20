<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. USUARIO FIJO: Admin (ID será 1)
        User::create([
            'name' => 'Marcos Chavez',
            'email' => 'admin@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'), // Contraseña: 12345678
            'cedula' => '76252483',
            'address' => 'Av. Universitaria',
            'phone' => '996410860',
            'role' => 'admin',
        ]);

        // 2. USUARIO FIJO: Doctor (ID será 2) - Necesario para Horarios
        User::create([
            'name' => 'Ruth Mallma',
            'email' => 'medicogeneral@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('12345678'), // Contraseña: 12345678
            'cedula' => '76252482', // Cédula ligeramente diferente
            'address' => 'Clínica Central',
            'phone' => '996410861',
            'role' => 'doctor',
        ]);
        
        // Bloques de Paciente1, Médico 1 y Factory aleatoria se mantienen comentados
    }
}