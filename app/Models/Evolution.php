<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evolution extends Model
{
    use HasFactory;

    protected $fillable = [
        'medical_history_id',
        'doctor_id',
        'diagnosis',
        'treatment',
        'observations',
    ];

    public function medicalHistory()
    {
        return $this->belongsTo(MedicalHistory::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
