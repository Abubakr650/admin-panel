<?php

namespace App\Models\Clinic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Traits\ProtectsRelations;

class Patient extends Model
{
    use HasFactory, HasUuids, SoftDeletes, ProtectsRelations;

    protected array $protectedRelations = ['appointments', 'invoices', 'treatments', 'radiologies', 'orthodonticCases'];

    protected $table = 'patients';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'full_name',
        'gender',
        'phone',
        'address',
        'birth_date',
    ];

    protected $dates = ['deleted_at', 'birth_date'];

    protected function casts(): array
    {
        return [
            'deleted_at'  => 'datetime',
            'birth_date'  => 'date',
        ];
    }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->birth_date)->age;
    }

    // ─── Relationships ────────────────────────────────────────

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function invoices()
    {
        return $this->hasMany(\App\Models\Billing\Invoice::class);
    }

    public function radiologies()
    {
        return $this->hasMany(\App\Models\Radiology\Radiology::class);
    }

    public function orthodonticCases()
    {
        return $this->hasMany(\App\Models\Orthodontics\OrthodonticCase::class);
    }
}
