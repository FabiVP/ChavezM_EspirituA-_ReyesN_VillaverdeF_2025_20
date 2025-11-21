<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_date',
        'scheduled_time',
        'type',
        'description',
        'doctor_id',
        'patient_id',
        'specialty_id',
        'status', // 
        'reprogrammed_from', //  
         'reprogrammed_by', // 
        'reprogramming_reason' // 
    ];

    public function specialty() {
        return $this->belongsTo(Specialty::class);
    }

    public function doctor(){
        return $this->belongsTo(User::class);
    }

    public function patient(){
        return $this->belongsTo(User::class);
    }

    public function getScheduledTime12Attribute(){
        return (new Carbon($this->scheduled_time))
            ->format('g:i A');
    }

    public function cancellation() {
        return $this->hasOne(CancelledAppointment::class);
    }

        
    // Relación: una cita tiene un antecedente médico
    public function medicalHistory()
{
    return $this->hasOne(MedicalHistory::class);
}

            ////////////////////////////////////////////////////////
    /**
     * Cita original que fue reprogramada (para citas NUEVAS)
     */
    public function originalAppointment()
    {
        return $this->belongsTo(Appointment::class, 'reprogrammed_from');
    }

    /**
     * Citas que fueron reprogramadas desde esta (para citas ORIGINALES)
     */
    public function reprogrammedAppointments()
    {
        return $this->hasMany(Appointment::class, 'reprogrammed_from');
    }

    /**
     * Usuario que realizó la reprogramación
     */
    public function reprogrammedBy()
    {
        return $this->belongsTo(User::class, 'reprogrammed_by');
    }

    /**
     * Scope para citas canceladas que pueden ser reprogramadas
     */
    public function scopeCanBeReprogrammed($query)
    {
        return $query->where('status', 'Cancelada')
                    ->whereDoesntHave('reprogrammedAppointments');
    }

    /**
     * Verificar si la cita puede ser reprogramada
     */
    public function canBeReprogrammed()
    {
        return $this->status === 'Cancelada' && 
               $this->reprogrammedAppointments->isEmpty();
    }

    /**
     * Verificar si esta cita fue reprogramada
     */
    public function wasReprogrammed()
    {
        return !is_null($this->reprogrammed_from);
    }

    /**
     * Verificar si esta cita tiene reprogramaciones
     */
    public function hasReprogrammedVersions()
    {
        return $this->reprogrammedAppointments->isNotEmpty();
    }
}



