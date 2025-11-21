<?php

namespace App\Http\Controllers;

use App\Interfaces\HorarioServiceInterface;
use App\Models\Appointment;
use App\Models\CancelledAppointment;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

// ✅ Importamos la clase de la CAPA 4
use App\StoreProcedures\AppointmentSP;


// =============================================================
// CONTROLADOR: AppointmentController
// -------------------------------------------------------------
// Descripción:
// Gestiona la lógica de negocio de las citas médicas (listar,
// registrar, cancelar, confirmar, reprogramar, etc.).
//
// En esta versión, ya NO consulta directamente a la base de
// datos mediante Eloquent, sino que utiliza los procedimientos
// almacenados definidos en MySQL (capa 4).
// =============================================================

class AppointmentController extends Controller
{
    // =========================================================
    // 📋 MÉTODO INDEX
    // ---------------------------------------------------------
    // Muestra todas las citas médicas según el rol del usuario.
    // Este método llama a los procedimientos almacenados de la
    // base de datos a través de la clase AppointmentSP.
    // =========================================================
    public function index()
    {
        // Instancia de la capa 4 (comunicador con la BD)
        $sp = new AppointmentSP();

        // Obtener rol y ID del usuario autenticado
        $role = auth()->user()->role;
        $userId = auth()->id();

        // 🔔 ACTUALIZADO: Usar el nuevo SP con notificaciones
        $allAppointments = $sp->listWithNotifications($userId, $role);

        // Filtramos los distintos estados usando colecciones
        $confirmedAppointments = $allAppointments->where('status', 'Confirmada');
        $pendingAppointments   = $allAppointments->where('status', 'Reservada');
        $oldAppointments       = $allAppointments->whereIn('status', ['Atendida', 'Cancelada', 'Reprogramada']);

        return view('appointments.index', compact(
            'confirmedAppointments',
            'pendingAppointments',
            'oldAppointments',
            'role'
        ));     
    }


    // =========================================================
// 📧 MÉTODO: Enviar recordatorio manual (SIMULACIÓN MEJORADA)
// =========================================================
public function sendReminder(Appointment $appointment, Request $request)
{
    $patientName = $appointment->patient->name;
    $appointmentDate = $appointment->scheduled_date;
    $appointmentTime = \Carbon\Carbon::parse($appointment->scheduled_time)->format('g:i A');
    $channel = $request->input('channel', 'email');
    
    // Mensajes según el canal
    $channelNames = [
        'whatsapp' => 'WhatsApp',
        'email' => 'Email', 
        'sms' => 'SMS'
    ];
    
    $channelName = $channelNames[$channel] ?? 'Email';
    
    $notification = "✅ Recordatorio enviado por {$channelName} a {$patientName} para la cita del {$appointmentDate} a las {$appointmentTime}";
    
    return redirect('/miscitas')->with([
        'notification' => $notification,
        'notification_type' => 'success'
    ]);
}








    // =========================================================
    // 🔔 MÉTODOS DE NOTIFICACIÓN - CAPA 4
    // ---------------------------------------------------------
    // Estos métodos utilizan el campo 'hours_to_appointment'
    // que es calculado directamente en el Store Procedure de MySQL
    // =========================================================

    // =========================================================
    // 🔔 MÉTODO: Verificar si se puede cancelar (para usar en vistas)
    // =========================================================
    public static function canCancelAppointment($appointment)
    {
        // ✅ CAPA 4: Usa directamente el campo calculado en MySQL
        $hours = $appointment->hours_to_appointment ?? 999;
        return $hours >= 4;
    }

    // =========================================================
    // 🔔 MÉTODO: Obtener texto de notificación (para usar en vistas)
    // =========================================================
    public static function getNotificationText($appointment)
    {
        // ✅ CAPA 4: Usa directamente el campo calculado en MySQL
        $hours = $appointment->hours_to_appointment ?? 999;
        
        if ($hours <= 0) return '✅ Cita completada';
        if ($hours <= 4) return '🔔 URGENTE: Cita hoy';
        if ($hours <= 24) return '⏰ Recordatorio: Cita mañana';
        
        return '📋 Confirmada';
    }

    // =========================================================
    // 🔔 MÉTODO: Obtener clase CSS para notificación (para usar en vistas)
    // =========================================================
    public static function getNotificationClass($appointment)
    {
        // ✅ CAPA 4: Usa directamente el campo calculado en MySQL
        $hours = $appointment->hours_to_appointment ?? 999;
        
        if ($hours <= 0) return 'bg-secondary';
        if ($hours <= 4) return 'bg-danger';
        if ($hours <= 24) return 'bg-warning text-dark';
        
        return 'bg-success';
    }

    // =========================================================
    // 🏥 CREATE - Muestra formulario de nueva cita
    // =========================================================
    public function create(HorarioServiceInterface $horarioServiceInterface)
    {
        $specialties = Specialty::all();

        $specialtyId = old('specialty_id');
        $doctors = $specialtyId ? Specialty::find($specialtyId)->users : collect();

        $date     = old('scheduled_date');
        $doctorId = old('doctor_id');
        $intervals = ($date && $doctorId)
            ? $horarioServiceInterface->getAvailableIntervals($date, $doctorId)
            : null;

        return view('appointments.create', compact('specialties', 'doctors', 'intervals'));
    }

    // =========================================================
    // 💾 STORE - Guarda una nueva cita en la BD
    // =========================================================
    public function store(Request $request, HorarioServiceInterface $horarioServiceInterface)
    {
        $rules = [
            'scheduled_time' => 'required',
            'type'           => 'required',
            'description'    => 'required',
            'doctor_id'      => 'exists:users,id',
            'specialty_id'   => 'exists:specialties,id'
        ];

        $messages = [
            'scheduled_time.required' => 'Debe seleccionar una hora para su cita.',
            'type.required'           => 'Debe seleccionar el tipo de consulta.',
            'description.required'    => 'Debe ingresar sus síntomas.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // Verifica si la hora está disponible
        $validator->after(function ($validator) use ($request, $horarioServiceInterface) {
            $date = $request->input('scheduled_date');
            $doctorId = $request->input('doctor_id');
            $scheduledTime = $request->input('scheduled_time');

            if ($date && $doctorId && $scheduledTime) {
                $start = new Carbon($scheduledTime);
                if (!$horarioServiceInterface->isAvailableInterval($date, $doctorId, $start)) {
                    $validator->errors()->add('available_time', 'La hora seleccionada ya está reservada.');
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Guardar datos
        $data = $request->only([
            'scheduled_date',
            'scheduled_time',
            'type',
            'description',
            'doctor_id',
            'specialty_id'
        ]);
        $data['patient_id'] = auth()->id();

        $carbonTime = Carbon::createFromFormat('g:i A', $data['scheduled_time']);
        $data['scheduled_time'] = $carbonTime->format('H:i:s');

        Appointment::create($data);

        $notification = '✅ La cita se ha registrado correctamente.';
        return redirect('/miscitas')->with(compact('notification'));
    }

    // =========================================================
    // ❌ CANCEL - Cancela una cita
    // =========================================================
    public function cancel(Appointment $appointment, Request $request)
    {
        $user = auth()->user();
        $role = $user->role;
    
        // Validaciones por rol (estas se mantienen)
        if ($role === 'paciente') {
            if ($appointment->patient_id !== $user->id) {
                return redirect('/miscitas')->withErrors('No puedes cancelar una cita que no es tuya.');
            }
    
            $appointmentDateTime = Carbon::parse($appointment->scheduled_date . ' ' . $appointment->scheduled_time);
            $hoursToAppointment = Carbon::now()->diffInHours($appointmentDateTime, false);
    
            if ($hoursToAppointment < 24) {
                return redirect('/miscitas')->withErrors('Solo puedes cancelar con al menos 24h de anticipación.');
            }
        }
    
        if ($role === 'doctor') {
            if ($appointment->doctor_id !== $user->id) {
                return redirect('/miscitas')->withErrors('No puedes cancelar una cita que no te corresponde.');
            }
    
            $request->validate(['justification' => 'required|string|min:5']);
        }
    
        // AHORA SÍ: LLAMAMOS LA CAPA 4 (NO ELOQUENT)
        $sp = new AppointmentSP();
        $sp->cancelarCita(
            $appointment->id,
            $request->input('justification', 'Cancelado sin motivo.'),
            $user->id
        );
    
        return redirect('/miscitas')->with([
            'notification' => '❌ La cita se ha cancelado correctamente.'
        ]);
    }
    

    // =========================================================
    // ✅ CONFIRMAR CITA
    // =========================================================
    public function confirm(Appointment $appointment)
    {
        $appointment->status = 'Confirmada';
        $appointment->save();

        $notification = '✅ La cita se ha confirmado correctamente.';
        return redirect('/miscitas')->with(compact('notification'));
    }

    // =========================================================
    // 📄 FORMULARIO DE CANCELACIÓN
    // =========================================================
    public function formCancel(Appointment $appointment)
    {
        if ($appointment->status == 'Confirmada' || $appointment->status == 'Reservada') {
            $role = auth()->user()->role;
            return view('appointments.cancel', compact('appointment', 'role'));
        }
        return redirect('/miscitas');
    }

    // =========================================================
    // 🔍 VER DETALLE DE CITA
    // =========================================================
    public function show(Appointment $appointment)
    {
        $role = auth()->user()->role;
        return view('appointments.show', compact('appointment', 'role'));
    }

    // =========================================================
    // 🔁 FORMULARIO Y LÓGICA DE REPROGRAMACIÓN
    // =========================================================
    public function reprogramForm(Appointment $appointment, HorarioServiceInterface $horarioService)
    {
        $user = auth()->user();

        if (!$this->canReprogram($appointment, $user)) {
            return redirect('/miscitas')->withErrors('No tienes permisos para reprogramar esta cita.');
        }

        if (!$appointment->canBeReprogrammed()) {
            return redirect('/miscitas')->withErrors('Esta cita no puede ser reprogramada.');
        }

        $specialties = Specialty::all();
        $specialtyId = $appointment->specialty_id;
        $doctors = $specialtyId ? Specialty::find($specialtyId)->users : collect();

        return view('appointments.reprogram', compact('appointment', 'specialties', 'doctors', 'horarioService'));
    }

    public function reprogram(Request $request, Appointment $appointment, HorarioServiceInterface $horarioService)
    {
        $user = auth()->user();

        if (!$this->canReprogram($appointment, $user)) {
            return redirect('/miscitas')->withErrors('No tienes permisos para reprogramar esta cita.');
        }

        $rules = [
            'scheduled_date' => 'required|date',
            'scheduled_time' => 'required',
            'reprogramming_reason' => 'required|min:10|max:500'
        ];

        $validator = Validator::make($request->all(), $rules);

        $validator->after(function ($validator) use ($request, $horarioService) {
            $date = $request->input('scheduled_date');
            $doctorId = $request->input('doctor_id');
            $scheduledTime = $request->input('scheduled_time');

            if ($date && $doctorId && $scheduledTime) {
                $start = new Carbon($scheduledTime);
                if (!$horarioService->isAvailableInterval($date, $doctorId, $start)) {
                    $validator->errors()->add('available_time', 'La hora seleccionada ya está reservada.');
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Nueva cita
        $newAppointment = Appointment::create([
            'scheduled_date' => $request->scheduled_date,
            'scheduled_time' => Carbon::createFromFormat('g:i A', $request->scheduled_time)->format('H:i:s'),
            'type' => $appointment->type,
            'description' => $appointment->description,
            'doctor_id' => $request->doctor_id,
            'specialty_id' => $request->specialty_id,
            'patient_id' => $appointment->patient_id,
            'status' => 'Reservada',
            'reprogrammed_from' => $appointment->id,
            'reprogrammed_by' => $user->id,
            'reprogramming_reason' => $request->reprogramming_reason
        ]);

        $appointment->update(['status' => 'Reprogramada']);

        $notification = '🔁 Cita reprogramada exitosamente.';
        return redirect('/miscitas')->with(compact('notification'));
    }

    // =========================================================
    // 🔒 Validación de permisos
    // =========================================================
    private function canReprogram(Appointment $appointment, $user)
    {
        if ($user->role == 'admin') return true;
        if ($user->role == 'doctor' && $appointment->doctor_id == $user->id) return true;
        if ($user->role == 'paciente' && $appointment->patient_id == $user->id) return true;

        return false;
    }



        // =========================================================
// 📋 MÉTODO: Notificar Resultados (Solo citas atendidas)
// =========================================================
public function notificarResultados()
{
    // Solo el doctor puede ver sus citas atendidas
    $doctorId = auth()->id();
    
    // Usar Store Procedure para obtener citas atendidas
    $sp = new AppointmentSP();
    $allAppointments = $sp->listWithNotifications($doctorId, 'doctor');
    
    // Filtrar solo las citas atendidas
    $appointments = $allAppointments->where('status', 'Atendida');

    return view('appointments.notify-results', compact('appointments'));
}

// =========================================================
// 📧 MÉTODO: Enviar Resultados (SIMULACIÓN)
// =========================================================
public function sendResults(Appointment $appointment, Request $request)
{
    // Verificar que el doctor solo pueda notificar sus propias citas
    if ($appointment->doctor_id !== auth()->id()) {
        abort(403, 'No autorizado');
    }

    // Verificar que la cita esté atendida
    if ($appointment->status !== 'Atendida') {
        return redirect()->route('appointments.notify.results')->with([
            'notification' => '❌ Solo se pueden notificar resultados de citas atendidas',
            'notification_type' => 'error'
        ]);
    }

    $patientName = $appointment->patient->name;
    $appointmentDate = $appointment->scheduled_date;
    
    $notification = "✅ Resultados enviados a {$patientName} para la cita del {$appointmentDate}";

    return redirect()->route('appointments.notify.results')->with([
        'notification' => $notification,
        'notification_type' => 'success'
    ]);
}

}