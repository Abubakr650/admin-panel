<?php

namespace App\Models\Orthodontics;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrthodonticSession extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'orthodontic_sessions'; // Table name in the database

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        'case_id', 
        'session_date', 
        'treatment',
        'teeth_changes', 
        'gum_changes',
        'wire_type_upper', 
        'wire_type_lower',
    ];

    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'deleted_at' => 'datetime',
        ];
    }

    public function orthodonticCase()
    {
        return $this->belongsTo(OrthodonticCase::class, 'case_id');
    }
}
