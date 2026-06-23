<?php

namespace App\Models\Clinic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'appointments';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'parent_appointment_id',
        'appointment_date',
        'appointment_time',
        'appointment_status',
        'appointment_notes',
    ];

    protected $dates = ['deleted_at', 'appointment_date'];

    protected function casts(): array
    {
        return [
            'deleted_at'       => 'datetime',
            'appointment_date' => 'date',
        ];
    }

    // ─── Relationships ────────────────────────────────────────

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    // Self Relationship (Return Visit)
    // 🔹 الموعد الأصلي (لو هذا موعد مراجعة)
    public function parent()
    {
        return $this->belongsTo(Appointment::class, 'parent_appointment_id');
    }

    // 🔹 جميع المراجعات التابعة لهذا الموعد
    public function returns()
    {
        return $this->hasMany(Appointment::class, 'parent_appointment_id');
    }
}
