<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\EvolutionController;
use App\Http\Controllers\ClinicalRecordController;


// Página de inicio
Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

// Home
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// ================== ADMIN ==================
Route::middleware(['auth', 'admin'])->group(function () {
    // Rutas Especialidades
    Route::get('/especialidades', [App\Http\Controllers\admin\SpecialtyController::class, 'index']);
    Route::get('/especialidades/create', [App\Http\Controllers\admin\SpecialtyController::class, 'create']);
    Route::get('/especialidades/{specialty}/edit', [App\Http\Controllers\admin\SpecialtyController::class, 'edit']);
    Route::post('/especialidades', [App\Http\Controllers\admin\SpecialtyController::class, 'store']);
    Route::put('/especialidades/{specialty}', [App\Http\Controllers\admin\SpecialtyController::class, 'update']);
    Route::delete('/especialidades/{specialty}', [App\Http\Controllers\admin\SpecialtyController::class, 'destroy']);

    // Rutas Médicos
    Route::resource('medicos', 'App\Http\Controllers\admin\DoctorController');

    // Rutas Pacientes
    Route::resource('pacientes', 'App\Http\Controllers\admin\PatientController');

    // Rutas Reportes
    Route::get('/reportes/citas/line', [App\Http\Controllers\admin\ChartController::class, 'appointments']);
    Route::get('/reportes/doctors/column', [App\Http\Controllers\admin\ChartController::class, 'doctors']);
    Route::get('/reportes/doctors/column/data', [App\Http\Controllers\admin\ChartController::class, 'doctorsJson']);

       // Reportes
       Route::get('/reportes/citas/line', [App\Http\Controllers\Admin\ChartController::class, 'appointments']);
       Route::get('/reportes/doctors/column', [App\Http\Controllers\Admin\ChartController::class, 'doctors']);
       Route::get('/reportes/doctors/column/data', [App\Http\Controllers\Admin\ChartController::class, 'doctorsJson']);
   



});

// ================== DOCTOR ==================
Route::middleware(['auth', 'doctor'])->group(function () {
    Route::get('/horario', [App\Http\Controllers\doctor\HorarioController::class, 'edit']);
    Route::post('/horario', [App\Http\Controllers\doctor\HorarioController::class, 'store']);
});

// ================== CITAS ==================
Route::middleware('auth')->group(function () {
    // Reservar y gestionar citas
    Route::get('/reservarcitas/create', [App\Http\Controllers\AppointmentController::class, 'create']);
    Route::post('/reservarcitas', [App\Http\Controllers\AppointmentController::class, 'store']);
    Route::get('/miscitas', [App\Http\Controllers\AppointmentController::class, 'index']);
    Route::get('/miscitas/{appointment}', [App\Http\Controllers\AppointmentController::class, 'show']);
    Route::post('/miscitas/{appointment}/cancel', [App\Http\Controllers\AppointmentController::class, 'cancel']);
    Route::post('/miscitas/{appointment}/confirm', [App\Http\Controllers\AppointmentController::class, 'confirm']);

    // JSON dinámico
    Route::get('/especialidades/{specialty}/medicos', [App\Http\Controllers\Api\SpecialtyController::class, 'doctors']);
    Route::get('/horario/horas', [App\Http\Controllers\Api\HorarioController::class, 'hours']);
});

// ================== ANTECEDENTES MÉDICOS ==================

// 1. Rutas de visualización (para pacientes, doctores y admins)
Route::middleware(['auth'])->group(function () {
    Route::get('/medical-histories', [MedicalHistoryController::class, 'index'])->name('medical_histories.index');
    Route::get('/medical-histories/{medical_history}', [MedicalHistoryController::class, 'show'])->name('medical_histories.show'); // Cambiado
});

// 2. Rutas de gestión (solo doctor puede crear/editar)
Route::middleware(['auth', 'doctor'])->group(function () { // 👈 CAMBIADO 'role:doctor' por 'doctor'
    Route::get('/medical-histories/create/{appointment}', [MedicalHistoryController::class, 'create'])->name('medical_histories.create');
    Route::post('/medical-histories', [MedicalHistoryController::class, 'store'])->name('medical_histories.store');
    Route::get('/medical-histories/{medical_history}/edit', [MedicalHistoryController::class, 'edit'])->name('medical_histories.edit'); // Cambiado
    Route::put('/medical-histories/{medical_history}', [MedicalHistoryController::class, 'update'])->name('medical_histories.update'); // Cambiado
});


// REPROGRAMACIÓN DE CITAS
Route::middleware('auth')->group(function () {
    // Formulario de reprogramación
    Route::get('/appointments/{appointment}/reprogram', [AppointmentController::class, 'reprogramForm'])
         ->name('appointments.reprogram.form');
    
    // Procesar reprogramación
    Route::post('/appointments/{appointment}/reprogram', [AppointmentController::class, 'reprogram'])
         ->name('appointments.reprogram.store');
});




//////////////////////// SEGUNDO INCRMENTO ////////////////////////


// =======================================================
// 🩺 RUTAS DE EVOLUCIONES MÉDICAS
// =======================================================

// 🔒 Rutas exclusivas para DOCTORES (crear y editar evoluciones)
Route::middleware(['auth', 'doctor'])->group(function () {
    
    // Crear una nueva evolución médica (formulario)
    Route::get('/evolutions/create/{medical_history}', [EvolutionController::class, 'create'])
        ->name('evolutions.create');

    // Guardar la evolución médica en la BD
    Route::post('/evolutions', [EvolutionController::class, 'store'])
        ->name('evolutions.store');

    // Editar una evolución existente
    Route::get('/evolutions/{evolution}/edit', [EvolutionController::class, 'edit'])
        ->name('evolutions.edit');

    // Actualizar evolución en la BD
    Route::put('/evolutions/{evolution}', [EvolutionController::class, 'update'])
        ->name('evolutions.update');
});

// 🔓 Rutas accesibles para médicos y pacientes autenticados
Route::middleware(['auth'])->group(function () {

    // Listar evoluciones de un historial médico
    Route::get('/evolutions/{medical_history}', [EvolutionController::class, 'index'])
        ->name('evolutions.index');

    // Ver detalle de una evolución médica específica
    Route::get('/evolutions/show/{evolution}', [EvolutionController::class, 'show'])
        ->name('evolutions.show');
});



// ================== EXPEDIENTE CLÍNICO ==================
Route::middleware(['auth'])->group(function () {

    // 📌 Listar pacientes (solo admin y doctor)
    Route::get('/clinical-records', 
        [ClinicalRecordController::class, 'index'])
        ->name('clinical.records.index'); // ✅ corregido

    // 📌 Mostrar expediente del paciente seleccionado
    Route::get('/clinical-record/{patient}', 
        [ClinicalRecordController::class, 'show'])
        ->name('clinical.record.show');

    // 📌 Ver mi propio expediente (paciente)
    Route::get('/my-clinical-record',
        [ClinicalRecordController::class, 'myRecord'])
        ->name('clinical.record.my');
});



// ================== EXÁMENES MÉDICOS solo vista==================
Route::get('/medical-exams/create', function () {
    return view('medical_exams.create');
})->name('medical-exams.create');


// ================== NOTIFICACIONES MANUALES ==================
Route::middleware('auth')->group(function () {
    // Enviar recordatorio manual
    Route::post('/appointments/{appointment}/send-reminder', 
        [AppointmentController::class, 'sendReminder'])
        ->name('appointments.send.reminder');
});


// ================== NOTIFICAR RESULTADOS ==================
Route::middleware(['auth', 'doctor'])->group(function () {
    Route::get('/notificar-resultados', [AppointmentController::class, 'notificarResultados'])
        ->name('appointments.notify.results');
    
    Route::post('/appointments/{appointment}/send-results', 
        [AppointmentController::class, 'sendResults'])
        ->name('appointments.send.results');
});


// ================== REPORTES (Admin) ==================
Route::middleware(['auth', 'admin'])->group(function () {
    
    // Reporte de Citas (Gráfico + Nuevas Pestañas)
    Route::get('/reportes/citas/line', [App\Http\Controllers\Admin\ChartController::class, 'appointments'])
        ->name('reports.appointments.chart');
    
    // Reporte de Desempeño Médico  
    Route::get('/reportes/doctors/column', [App\Http\Controllers\Admin\ChartController::class, 'doctors'])
        ->name('reports.doctors.chart');
    
    // API para datos del gráfico de doctores
    Route::get('/reportes/doctors/column/data', [App\Http\Controllers\Admin\ChartController::class, 'doctorsJson'])
        ->name('reports.doctors.data');
});


// ================== DESCARGAS DE REPORTES ==================
Route::middleware(['auth', 'admin'])->group(function () {
    // ... rutas existentes ...
    
    Route::post('/reportes/descargar', [App\Http\Controllers\Admin\ChartController::class, 'downloadReport'])
        ->name('reports.download');
});


// En routes/web.php - agregar esta ruta
Route::post('/reportes/descargar', [App\Http\Controllers\Admin\ChartController::class, 'downloadReport'])
    ->name('reports.download')
    ->middleware(['auth', 'admin']);