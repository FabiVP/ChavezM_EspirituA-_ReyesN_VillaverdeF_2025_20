<?php

namespace Database\Seeders;

use App\Models\Horarios;
use App\Models\User; // <-- IMPORTANTE: Necesitas el modelo User
use Illuminate\Database\Seeder;

class HorariosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. OBTENER el ID del primer usuario que tenga el rol 'doctor'.
        // Usar where('role', 'doctor') es más seguro que un ID fijo como 3.
        $doctorId = User::where('role', 'doctor')->value('id');

        // 2. Comprobar que existe un doctor para evitar el fallo (medida de seguridad)
        if (is_null($doctorId)) {
            // Si no se encuentra un doctor, podemos asignar el horario al Admin (ID 1), 
            // o simplemente detener la ejecución (depende de tu lógica).
            // Usaremos el ID del Admin (que es 1) como fallback si lo prefieres, 
            // o simplemente saldremos. Por ahora, saldremos si no hay doctores.
            echo "ADVERTENCIA: No se encontró ningún usuario con rol 'doctor'. No se insertarán horarios.";
            return;
        }

        for ($i=0; $i<7; ++$i){
            Horarios::create([
                'day' => $i,
                'active' => ($i==0),
                'morning_start' => ($i==0 ? '08:00:00' : '07:00:00'),
                'morning_end' => ($i==0 ? '10:00:00' : '07:00:00'),
                'afternoon_start' => ($i==0 ? '15:00:00' : '14:00:00'),
                'afternoon_end' => ($i==0 ? '17:00:00' : '14:00:00'),
                'user_id' => $doctorId // <-- ¡CORREGIDO! Usamos el ID dinámico del doctor
            ]);
        }
    }
}
