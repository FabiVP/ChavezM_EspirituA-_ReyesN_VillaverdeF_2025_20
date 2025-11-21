<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\MedicalHistory;
use App\Models\Evolution;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'cedula',
        'address',
        'phone',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'pivot',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Especialidades del doctor
    public function specialties()
    {
        return $this->belongsToMany(Specialty::class)->withTimestamps();
    }

    // Alcance para pacientes
    public function scopePatients($query)
    {
        return $query->where('role', 'paciente');
    }

    // Alcance para doctores
    public function scopeDoctors($query)
    {
        return $query->where('role', 'doctor');
    }

    // Citas como doctor
    public function asDoctorAppointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function attendedAppointments()
    {
        return $this->asDoctorAppointments()->where('status', 'Atendida');
    }

    public function cancelledAppointments()
    {
        return $this->asDoctorAppointments()->where('status', 'Cancelada');
    }

    // Citas como paciente
    public function asPatientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    // ✅ Antecedentes del paciente
    public function medicalHistories()
    {
        return $this->hasMany(MedicalHistory::class, 'patient_id');
    }

    // ✅ Evoluciones del paciente a través de sus historias clínicas
    public function evolutions()
    {
        return $this->hasManyThrough(
            Evolution::class,
            MedicalHistory::class,
            'patient_id',        // Foreign key on MedicalHistory table
            'medical_history_id',// Foreign key on Evolution table
            'id',                // Local key on User table
            'id'                 // Local key on MedicalHistory table
        );
    }
}
