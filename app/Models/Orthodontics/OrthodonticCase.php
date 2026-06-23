<?php

namespace App\Models\Orthodontics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrthodonticCase extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'orthodontic_cases'; // Table name in the database

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'diagnosis', 
        'plan',
        'total_amount', 
        'installment_amount', 
        'status',
    ];

    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(\App\Models\Clinic\Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(\App\Models\Clinic\Doctor::class);
    }

    public function sessions()
    {
        return $this->hasMany(OrthodonticSession::class, 'case_id');
    }
}
