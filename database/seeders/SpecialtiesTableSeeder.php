<?php

namespace Database\Seeders;

use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;

class SpecialtiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $specialties = [
            'Neurología',
            'Quirúrgica',
            'Pediatría',
            'Cardiología',
            'Urología',
            'Medicina forense',
            'Dermatología'
        ];
        
        // 1. Obtener los IDs de los doctores fijos que existen (solo el Admin por ahora)
        // Para esto necesitamos que tengas al menos un doctor fijo creado en UsersTableSeeder.
        
        // Si tienes al menos un doctor fijo, puedes obtener su ID.
        // Como ahora solo tienes al Admin, vamos a vincular al Admin (ID 1)
        // O mejor: si no tienes doctores fijos, simplemente COMENTAMOS la vinculación.
        
        foreach ($specialties as $specialtyName) {
            $specialty = Specialty::create([
                'name' => $specialtyName
            ]);

            // ¡¡¡COMENTAR O ELIMINAR ESTE BLOQUE!!!
            /*
            $specialty->users()->saveMany(
                User::factory(4)->state(['role' => 'doctor'])->make()
            );
            */
            // Fin del bloque a comentar
        }

        // Esta línea intenta vincular al usuario con ID 3 (que antes era 'Medico 1')
        // Si solo tienes al Admin (ID 1), esto fallará o no hará nada útil.
        // COMENTARÍA ESTA LÍNEA también, a menos que sepas qué usuario es el ID 3.
        // User::find(3)->specialties()->save($specialty);
    }
}