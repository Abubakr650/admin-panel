<?php

namespace App\Models\Clinic;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\ProtectsRelations;

class Doctor extends Model
{
    use HasFactory, HasUuids, SoftDeletes, ProtectsRelations;

    protected array $protectedRelations = ['appointments', 'invoices', 'treatments', 'radiologies', 'orthodonticCases'];

    protected $table = 'doctors';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'specialty',
        'degree',
        'is_active',
        'user_id',
    ];

    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    // ─── Relationships ────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

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
