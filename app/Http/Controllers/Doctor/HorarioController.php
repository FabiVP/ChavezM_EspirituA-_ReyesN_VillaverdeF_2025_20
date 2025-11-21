<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\StoreProcedures\HorarioSP;

class HorarioController extends Controller
{
    private $days = [
        'Lunes', 'Martes', 'Miércoles', 'Jueves',
        'Viernes', 'Sábado', 'Domingo'
    ];

    public function edit()
    {
        $horarios = HorarioSP::list(auth()->id());

        // Si no existen registros, crear 7 slots vacíos
        if ($horarios->count() === 0) {
            $horarios = collect();
            for ($i = 0; $i < 7; $i++) {
                $horarios->push((object)[
                    "day" => $i,
                    "active" => 0,
                    "morning_start" => null,
                    "morning_end" => null,
                    "afternoon_start" => null,
                    "afternoon_end" => null
                ]);
            }
        }

        return view('horario', [
            'days' => $this->days,
            'horarios' => $horarios
        ]);
    }


    public function store(Request $request)
{
    $active_input     = $request->input('active', []);
    $morning_start    = $request->input('morning_start', []);
    $morning_end      = $request->input('morning_end', []);
    $afternoon_start  = $request->input('afternoon_start', []);
    $afternoon_end    = $request->input('afternoon_end', []);

    // Normalizar flags 1/0
    $active_flags = [];
    for ($i = 0; $i < 7; $i++) {
        $active_flags[$i] = in_array("$i", $active_input) ? 1 : 0;
    }

    // Validaciones
    $errors = [];

    for ($i = 0; $i < 7; $i++) {
        $ms = $morning_start[$i] ?? null;
        $me = $morning_end[$i] ?? null;
        $as = $afternoon_start[$i] ?? null;
        $ae = $afternoon_end[$i] ?? null;

        if ($ms && $me && new Carbon($ms) > new Carbon($me)) {
            $errors[] = "Inconsistencia en el turno mañana del día {$this->days[$i]}.";
        }
        if ($as && $ae && new Carbon($as) > new Carbon($ae)) {
            $errors[] = "Inconsistencia en el turno tarde del día {$this->days[$i]}.";
        }
    }

    if (!empty($errors)) {
        return back()->with(compact('errors'))->withInput();
    }

    // 🔥 Llamada correcta a SP: 7 veces (una por día)
    $userId = auth()->id();

    for ($i = 0; $i < 7; $i++) {
        HorarioSP::save(
            $userId,
            $i,
            $active_flags[$i],
            $morning_start[$i] ?? null,
            $morning_end[$i] ?? null,
            $afternoon_start[$i] ?? null,
            $afternoon_end[$i] ?? null
        );
    }

    return back()->with([
        'notification' => 'Los cambios se han guardado correctamente.'
    ]);
}

}
