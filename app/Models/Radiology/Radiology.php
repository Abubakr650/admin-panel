<?php

namespace App\Models\Radiology;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Radiology extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'radiologies'; // Table name in the database

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
       'patient_id', 
       'doctor_id', 
       'service_id',
        'radiology_type', 
        'diagnosis',
        'image_path',
        'ai_analysis',
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

    public function service()
    {
        return $this->belongsTo(\App\Models\Billing\Service::class);
    }

    public function images()
    {
        return $this->hasMany(RadiologyImage::class);
    }
}

